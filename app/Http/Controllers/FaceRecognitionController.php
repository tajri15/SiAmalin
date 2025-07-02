<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\FaceRecognitionService;
use App\Models\Karyawan;
use Carbon\Carbon;

class FaceRecognitionController extends Controller
{
    protected $faceRecognitionService;

    public function __construct(FaceRecognitionService $faceRecognitionService)
    {
        $this->faceRecognitionService = $faceRecognitionService;
    }

    public function verifyFace(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'face_image' => 'required|string',
            'nik' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $startTime = microtime(true);
            $karyawan = Karyawan::where('nik', $request->nik)->first();

            if (!$karyawan || empty($karyawan->face_embedding)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wajah belum terdaftar'
                ], 404);
            }

            // Check for spoofing if enabled
            if (config('face_recognition.enable_anti_spoofing')) {
                $isReal = $this->faceRecognitionService->checkAntiSpoofing($request->face_image);
                if (!$isReal) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Deteksi upaya spoofing (wajah tidak asli)'
                    ], 403);
                }
            }

            // Extract embedding from uploaded image
            $image_parts = explode(";base64,", $request->face_image);
            $image_base64 = base64_decode($image_parts[1]);
            $uploadedEmbedding = $this->faceRecognitionService->generateFaceEmbedding($image_base64);

            // Calculate similarity score
            $similarity = $this->faceRecognitionService->calculateSimilarity(
                $uploadedEmbedding['embedding'],
                $karyawan->face_embedding['embedding']
            );

            $threshold = config('face_recognition.threshold');
            $isMatch = $similarity >= $threshold;

            // Log verification result if enabled
            if (config('face_recognition.log_verification_results')) {
                $this->faceRecognitionService->logVerificationResult([
                    'nik' => $request->nik,
                    'similarity' => $similarity,
                    'threshold' => $threshold,
                    'is_match' => $isMatch,
                    'processing_time' => microtime(true) - $startTime,
                    'timestamp' => Carbon::now()
                ]);
            }

            return response()->json([
                'success' => true,
                'match' => $isMatch,
                'similarity' => $similarity,
                'threshold' => $threshold,
                'message' => $isMatch ? 'Wajah terverifikasi' : 'Wajah tidak cocok'
            ]);
        } catch (\Exception $e) {
            Log::error('Face verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat verifikasi wajah'
            ], 500);
        }
    }

    /**
     * Generate face embedding using FaceRecognitionService
     *
     * @param string $imageData Raw image data
     * @param bool $checkSpoofing Whether to perform anti-spoofing check
     * @return array
     */
    public function generateFaceEmbedding($imageData, $checkSpoofing = false)
    {
        return $this->faceRecognitionService->generateFaceEmbedding($imageData, $checkSpoofing);
    }

    /**
     * Calculate similarity between two embeddings (delegated to service)
     */
    private function calculateSimilarity($embedding1, $embedding2)
    {
        return $this->faceRecognitionService->calculateSimilarity($embedding1, $embedding2);
    }

    /**
     * Check for face spoofing (delegated to service)
     */
    private function checkAntiSpoofing($imageData)
    {
        return $this->faceRecognitionService->checkAntiSpoofing($imageData);
    }

    /**
     * Log verification result (delegated to service)
     */
    private function logVerificationResult($data)
    {
        $this->faceRecognitionService->logVerificationResult($data);
    }
}