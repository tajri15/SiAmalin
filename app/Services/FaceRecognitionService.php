<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Karyawan;
use Carbon\Carbon;
use Exception;

class FaceRecognitionService
{
    protected $threshold;
    protected $enableAntiSpoofing;
    protected $embeddingVersion;
    
    public function __construct()
    {
        $this->threshold = config('face_recognition.threshold', 0.85);
        $this->enableAntiSpoofing = config('face_recognition.enable_anti_spoofing', false);
        $this->embeddingVersion = config('face_recognition.embedding_version', 'v1');
    }
    
    public function verifyFace($imageData, $nik)
    {
        try {
            $startTime = microtime(true);
            
            // Validasi input
            if (empty($imageData) || empty($nik)) {
                throw new Exception("Data gambar dan NIK harus diisi");
            }

            // Extract base64 image data
            $imageParts = explode(";base64,", $imageData);
            if (count($imageParts) !== 2) {
                throw new Exception("Format gambar tidak valid");
            }

            $imageBase64 = base64_decode($imageParts[1]);
            if (!$imageBase64) {
                throw new Exception("Gagal mendekode gambar");
            }

            // Get employee data
            $karyawan = Karyawan::where('nik', $nik)->first();
            
            if (!$karyawan) {
                throw new Exception("Karyawan tidak ditemukan");
            }
            
            if (empty($karyawan->face_embedding)) {
                return [
                    'success' => false,
                    'message' => 'Wajah belum terdaftar',
                    'similarity' => 0,
                    'threshold' => $this->threshold,
                    'match' => false
                ];
            }

            // Check for spoofing if enabled
            if (config('face_recognition.enable_anti_spoofing')) {
                $isReal = $this->checkAntiSpoofing($imageBase64);
                if (!$isReal) {
                    return [
                        'success' => false,
                        'message' => 'Deteksi upaya spoofing (wajah tidak asli)',
                        'similarity' => 0,
                        'threshold' => $this->threshold,
                        'match' => false
                    ];
                }
            }

            // Extract embedding from uploaded image
            $uploadedEmbedding = $this->generateFaceEmbedding($imageBase64);

            // Calculate similarity score
            $similarity = $this->calculateSimilarity(
                $uploadedEmbedding['embedding'],
                $karyawan->face_embedding['embedding']
            );

            $isMatch = $similarity >= $this->threshold;

            // Log verification result if enabled
            if (config('face_recognition.log_verification_results')) {
                $this->logVerificationResult([
                    'nik' => $nik,
                    'similarity' => $similarity,
                    'threshold' => $this->threshold,
                    'is_match' => $isMatch,
                    'processing_time' => microtime(true) - $startTime,
                    'timestamp' => now()->toDateTimeString()
                ]);
            }

            return [
                'success' => true,
                'similarity' => $similarity,
                'threshold' => $this->threshold,
                'match' => $isMatch,
                'message' => $isMatch ? 'Wajah terverifikasi' : 'Wajah tidak cocok'
            ];
            
        } catch (Exception $e) {
            Log::error("Face verification failed: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'similarity' => 0,
                'threshold' => $this->threshold,
                'match' => false
            ];
        }
    }
    
    /**
     * Generate face embedding from image data
     *
     * @param string $imageData Raw image data
     * @return array
     */

    public function generateFaceEmbedding($imageData, $checkSpoofing = false)
    {
        try {
            if ($checkSpoofing && !$this->checkAntiSpoofing($imageData)) {
                throw new \Exception("Deteksi wajah palsu!");
            }

            // Generate face embedding (128-dimensional vector)
            $embedding = [];
            for ($i = 0; $i < 128; $i++) {
                $embedding[] = mt_rand() / mt_getrandmax(); // Nilai random 0-1
            }

            return [
                'embedding' => $embedding,
                'version' => config('face_recognition.embedding_version'),
                'created_at' => now()->toDateTimeString()
            ];
        } catch (\Exception $e) {
            Log::error('Error generating face embedding: ' . $e->getMessage());
            throw $e;
        }
    }
    protected function generateEmbedding($imageData)
    {
        // NOTE: Ini adalah implementasi dummy
        // Pada implementasi nyata, ini akan memanggil library/model ML
        
        $embedding = [];
        for ($i = 0; $i < 128; $i++) {
            $embedding[] = mt_rand() / mt_getrandmax();
        }
        
        // Normalize the vector
        $norm = sqrt(array_sum(array_map(function($x) { 
            return $x * $x; 
        }, $embedding)));
        
        $normalizedEmbedding = array_map(function($x) use ($norm) { 
            return $x / $norm; 
        }, $embedding);
        
        return [
            'embedding' => $normalizedEmbedding,
            'version' => $this->embeddingVersion,
            'created_at' => Carbon::now()->toDateTimeString()
        ];
    }
    
    /**
     * Calculate cosine similarity between two embeddings
     *
     * @param array $embedding1
     * @param array $embedding2
     * @return float
     */
    public function calculateSimilarity($embedding1, $embedding2)
    {
        if (count($embedding1) !== count($embedding2)) {
            return 0;
        }
        
        $dotProduct = 0;
        $normA = 0;
        $normB = 0;

        for ($i = 0; $i < count($embedding1); $i++) {
            $dotProduct += $embedding1[$i] * $embedding2[$i];
            $normA += $embedding1[$i] * $embedding1[$i];
            $normB += $embedding2[$i] * $embedding2[$i];
        }

        $normA = sqrt($normA);
        $normB = sqrt($normB);

        return $dotProduct / ($normA * $normB);
    }
    
    /**
     * Check for face spoofing/liveness
     *
     * @param string $imageData Raw image data
     * @return bool
     */
    protected function checkAntiSpoofing($imageData)
    {
        // NOTE: Ini adalah implementasi dummy
        // Pada implementasi nyata, ini akan memanggil library/model anti-spoofing
        return true;
    }
    
    /**
     * Log verification result
     *
     * @param array $data
     * @return void
     */
    protected function logVerificationResult($data)
    {
        try {
            $logPath = storage_path('logs/face_verification.log');
            $logData = json_encode($data) . PHP_EOL;
            
            file_put_contents($logPath, $logData, FILE_APPEND);
        } catch (Exception $e) {
            Log::error("Failed to log verification result: " . $e->getMessage());
        }
    }
    
    /**
     * Compare two face images directly
     *
     * @param string $image1 Base64 encoded image
     * @param string $image2 Base64 encoded image
     * @return array
     */
    public function compareFaces($image1, $image2)
    {
        try {
            // Generate embeddings for both images
            $embedding1 = $this->generateEmbedding($image1);
            $embedding2 = $this->generateEmbedding($image2);
            
            // Calculate similarity
            $similarity = $this->calculateSimilarity(
                $embedding1['embedding'],
                $embedding2['embedding']
            );
            
            return [
                'success' => true,
                'similarity' => $similarity,
                'is_match' => $similarity >= $this->threshold,
                'threshold' => $this->threshold
            ];
        } catch (Exception $e) {
            Log::error("Face comparison failed: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat membandingkan wajah'
            ];
        }
    }
}