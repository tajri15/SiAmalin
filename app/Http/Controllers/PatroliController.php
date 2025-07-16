<?php
// File: app/Http/Controllers/PatroliController.php

namespace App\Http\Controllers;

use App\Models\Patrol;
use App\Models\PatrolPoint;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Storage;
use MongoDB\BSON\UTCDateTime;
use App\Helpers\FaceRecognitionHelper;

class PatroliController extends Controller
{
    /**
     * Display the patrol page for employees.
     * MODIFIED: Pass office location and radius to the view.
     */
    public function index()
    {
        $karyawan = Auth::guard('karyawan')->user();
        $activePatrol = Patrol::where('karyawan_nik', $karyawan->nik)
                               ->whereIn('status', ['aktif', 'jeda'])
                               ->orderBy('start_time', 'desc')
                               ->first();
        
        $patrolData = null;
        if ($activePatrol) {
            $currentDurationSeconds = $activePatrol->duration_seconds ?? 0;
            if ($activePatrol->status === 'aktif' && $activePatrol->updated_at) {
                $currentDurationSeconds += (now()->timestamp - $activePatrol->updated_at->timestamp);
            }

            $patrolData = [
                'id' => $activePatrol->_id,
                'status' => $activePatrol->status,
                'start_time' => $activePatrol->start_time->timestamp * 1000, 
                'total_distance_meters' => $activePatrol->total_distance_meters ?? 0,
                'duration_seconds' => $currentDurationSeconds, 
                'face_verified' => $activePatrol->face_verified ?? false, // NEW: Face verification status
                'path' => $activePatrol->points()->orderBy('timestamp', 'asc')->get(['latitude', 'longitude'])->map(function($point) {
                    return [$point->latitude, $point->longitude]; 
                })->toArray()
            ];
        }

        // Get office location and radius for the view
        $officeLocation = $karyawan->office_location;
        $officeRadius = $karyawan->office_radius;

        // NEW: Pass face descriptor for verification
        $faceDescriptor = null;
        if ($karyawan && !empty($karyawan->face_embedding['embedding'])) {
            $faceDescriptor = json_encode($karyawan->face_embedding['embedding']);
        }

        return view('patroli.index', compact('karyawan', 'patrolData', 'officeLocation', 'officeRadius', 'faceDescriptor'));
    }

    /**
     * Start a new patrol session.
     * MODIFIED: Add radius validation before starting a patrol.
     */
    public function startPatrol(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $karyawan = Auth::guard('karyawan')->user();

        // --- PERBAIKAN: Validasi Radius Lokasi ---
        if (empty($karyawan->office_location)) {
            return response()->json(['success' => false, 'message' => 'Patroli gagal dimulai. Lokasi kerja Anda belum diatur oleh Admin.'], 400);
        }

        $jarakResult = FaceRecognitionHelper::isWithinOfficeRadius($request->latitude, $request->longitude, $karyawan);

        if (!$jarakResult['within']) {
            return response()->json([
                'success' => false, 
                'message' => 'Patroli tidak dapat dimulai. Anda berada di luar radius lokasi kerja Anda. Jarak: ' . round($jarakResult['distance']) . 'm.'
            ], 403);
        }
        // --- AKHIR PERBAIKAN ---

        // Batalkan patroli sebelumnya yang mungkin masih aktif
        Patrol::where('karyawan_nik', $karyawan->nik)
            ->whereIn('status', ['aktif', 'jeda'])
            ->update([
                'status' => 'dibatalkan', 
                'end_time' => new UTCDateTime(now()->timestamp * 1000)
            ]);

        try {
            $patrol = Patrol::create([
                'karyawan_nik' => $karyawan->nik,
                'start_time' => new UTCDateTime(now()->timestamp * 1000),
                'status' => 'aktif',
                'total_distance_meters' => 0,
                'duration_seconds' => 0,
                'face_verified' => false, // NEW: Face verification status
                'face_verification_image' => null, // NEW: Face verification image
                'path' => []
            ]);

            return response()->json([
                'success' => true,
                'patrol_id' => $patrol->_id,
                'start_time' => $patrol->start_time->timestamp * 1000, 
                'message' => 'Patroli berhasil dimulai.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error startPatrol: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memulai patroli: ' . $e->getMessage()], 500);
        }
    }

    /**
     * NEW: Verify face for patrol
     */
    public function verifyFace(Request $request)
    {
        $request->validate([
            'patrol_id' => 'required|string',
            'face_image' => 'required|string',
        ]);

        $karyawan = Auth::guard('karyawan')->user();

        try {
            $patrol = Patrol::where('_id', $request->patrol_id)
                            ->where('karyawan_nik', $karyawan->nik)
                            ->whereIn('status', ['aktif', 'jeda'])
                            ->first();

            if (!$patrol) {
                return response()->json(['success' => false, 'message' => 'Patroli tidak ditemukan atau sudah selesai.'], 404);
            }

            if ($patrol->face_verified) {
                return response()->json(['success' => true, 'message' => 'Wajah sudah terverifikasi untuk patroli ini.']);
            }

            // Save face verification image
            $currentDate = now();
            $yearMonth = $currentDate->format('Y/m');
            
            $faceImagePath = $this->processBase64Image(
                $request->face_image,
                "patroli/{$yearMonth}/verification",
                'patrol_faceverify_' . $karyawan->nik . '_' . time()
            );

            // Update patrol with face verification
            $patrol->update([
                'face_verified' => true,
                'face_verification_image' => $faceImagePath['storage_path'],
                'face_verification_time' => new UTCDateTime($currentDate->timestamp * 1000)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Verifikasi wajah berhasil.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error verifyFace: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memverifikasi wajah: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a new patrol point.
     * MODIFIED: Add radius validation before storing point.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storePoint(Request $request)
    {
        $request->validate([
            'patrol_id' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'timestamp' => 'required|numeric', 
            'accuracy' => 'nullable|numeric',
            'speed' => 'nullable|numeric',
        ]);

        $karyawan = Auth::guard('karyawan')->user();

        try {
            $patrol = Patrol::where('_id', $request->patrol_id)
                            ->where('karyawan_nik', $karyawan->nik)
                            ->where('status', 'aktif') 
                            ->first();

            if (!$patrol) {
                return response()->json(['success' => false, 'message' => 'Patroli tidak aktif atau tidak ditemukan.'], 404);
            }

            // --- TAMBAHAN: Validasi Radius Real-time ---
            $jarakResult = FaceRecognitionHelper::isWithinOfficeRadius($request->latitude, $request->longitude, $karyawan);

            if (!$jarakResult['within']) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Titik patroli tidak disimpan. Anda berada di luar radius lokasi kerja.',
                    'outside_radius' => true,
                    'distance' => round($jarakResult['distance'])
                ], 403);
            }
            // --- AKHIR TAMBAHAN ---

            PatrolPoint::create([
                'patrol_id' => $request->patrol_id,
                'karyawan_nik' => $karyawan->nik,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy,
                'speed' => $request->speed,
                'timestamp' => new UTCDateTime($request->timestamp), 
            ]);
            
            $patrol->touch(); 

            return response()->json([
                'success' => true, 
                'message' => 'Titik patroli disimpan.',
                'outside_radius' => false
            ]);
        } catch (\Exception $e) {
            Log::error('Error storePoint: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan titik patroli: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Check if current location is within office radius.
     * NEW METHOD: For real-time radius checking.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkRadius(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $karyawan = Auth::guard('karyawan')->user();

        if (empty($karyawan->office_location)) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi kantor belum diatur.',
                'within_radius' => false,
                'distance' => 0
            ]);
        }

        $jarakResult = FaceRecognitionHelper::isWithinOfficeRadius($request->latitude, $request->longitude, $karyawan);

        return response()->json([
            'success' => true,
            'within_radius' => $jarakResult['within'],
            'distance' => round($jarakResult['distance']),
            'message' => $jarakResult['within'] ? 'Dalam radius kerja' : 'Di luar radius kerja'
        ]);
    }

    /**
     * Pause an active patrol session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pausePatrol(Request $request)
    {
        $request->validate(['patrol_id' => 'required|string']);
        $karyawan = Auth::guard('karyawan')->user();

        try {
            $patrol = Patrol::where('_id', $request->patrol_id)
                            ->where('karyawan_nik', $karyawan->nik)
                            ->where('status', 'aktif')
                            ->first();

            if (!$patrol) {
                return response()->json(['success' => false, 'message' => 'Patroli tidak aktif atau tidak ditemukan untuk dijeda.'], 404);
            }

            $currentSegmentDuration = 0;
            if ($patrol->updated_at) { 
                $currentSegmentDuration = now()->timestamp - $patrol->updated_at->timestamp;
            } elseif ($patrol->start_time) { 
                $currentSegmentDuration = now()->timestamp - $patrol->start_time->timestamp;
            }
            
            $newDuration = ($patrol->duration_seconds ?? 0) + $currentSegmentDuration;

            $patrol->update([
                'status' => 'jeda',
                'duration_seconds' => $newDuration
            ]);

            return response()->json(['success' => true, 'message' => 'Patroli dijeda.']);
        } catch (\Exception $e) {
            Log::error('Error pausePatrol: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menjeda patroli: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Resume a paused patrol session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resumePatrol(Request $request)
    {
        $request->validate(['patrol_id' => 'required|string']);
        $karyawan = Auth::guard('karyawan')->user();

        try {
            $patrol = Patrol::where('_id', $request->patrol_id)
                            ->where('karyawan_nik', $karyawan->nik)
                            ->where('status', 'jeda')
                            ->first();

            if (!$patrol) {
                return response()->json(['success' => false, 'message' => 'Patroli tidak dijeda atau tidak ditemukan untuk dilanjutkan.'], 404);
            }
            $patrol->update(['status' => 'aktif']); 

            return response()->json(['success' => true, 'message' => 'Patroli dilanjutkan.']);
        } catch (\Exception $e) {
            Log::error('Error resumePatrol: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal melanjutkan patroli: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Stop an active or paused patrol session.
     * MODIFIED: Add face verification check before allowing stop
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function stopPatrol(Request $request)
    {
        $request->validate([
            'patrol_id' => 'required|string',
            'total_distance_meters' => 'required|numeric',
            'duration_seconds' => 'required|integer',
        ]);
        $karyawan = Auth::guard('karyawan')->user();

        try {
            $patrol = Patrol::where('_id', $request->patrol_id)
                            ->where('karyawan_nik', $karyawan->nik)
                            ->whereIn('status', ['aktif', 'jeda'])
                            ->first();

            if (!$patrol) {
                return response()->json(['success' => false, 'message' => 'Patroli tidak ditemukan atau sudah selesai.'], 404);
            }

            // NEW: Check if face verification is required
            if (!$patrol->face_verified) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Patroli tidak dapat dihentikan. Anda harus melakukan verifikasi wajah terlebih dahulu.',
                    'face_verification_required' => true
                ], 403);
            }

            $points = PatrolPoint::where('patrol_id', $patrol->_id)
                                ->orderBy('timestamp', 'asc')
                                ->get(['longitude', 'latitude']); 

            $pathCoordinates = $points->map(function ($point) {
                return [$point->longitude, $point->latitude]; 
            })->toArray();

            $finalDurationSeconds = (int) $request->duration_seconds;
            
            $updateData = [
                'status' => 'selesai',
                'end_time' => new UTCDateTime(now()->timestamp * 1000),
                'total_distance_meters' => (float) $request->total_distance_meters,
                'duration_seconds' => $finalDurationSeconds,
                'path' => $pathCoordinates 
            ];
            
            $patrol->update($updateData);

            return response()->json(['success' => true, 'message' => 'Patroli berhasil dihentikan dan disimpan.']);
        } catch (\Exception $e) {
            Log::error('Error stopPatrol: ' . $e->getMessage() . ' Line: ' . $e->getLine() . ' File: ' . $e->getFile() );
            return response()->json(['success' => false, 'message' => 'Gagal menghentikan patroli: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display patrol history for the logged-in employee.
     *
     * @return \Illuminate\Http\Response
     */
    public function historiPatroli(Request $request)
    {
        $request->session()->forget('error');

        $karyawan = Auth::guard('karyawan')->user();
        $patrols = Patrol::where('karyawan_nik', $karyawan->nik)
                         ->where('status', 'selesai') 
                         ->orderBy('start_time', 'desc')
                         ->paginate(10); 

        return view('patroli.histori', compact('patrols', 'karyawan'));
    }

    /**
     * Display detail of a specific patrol.
     *
     * @param  string  $patrolId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function detailHistoriPatroli($patrolId)
    {
        $karyawan = Auth::guard('karyawan')->user();
        $patrol = Patrol::where('_id', $patrolId)
                        ->where('karyawan_nik', $karyawan->nik)
                        ->first();

        if (!$patrol) {
            return redirect()->route('patroli.histori')->with('error', 'Data patroli tidak ditemukan.');
        }
        
        $pathForMap = collect($patrol->path)->map(function ($point) {
            if (is_array($point) && count($point) >= 2) {
                return [$point[1], $point[0]];
            }
            return null;
        })->filter()->toArray();

        return view('patroli.detail', compact('patrol', 'karyawan', 'pathForMap'));
    }

    /**
     * NEW: Process base64 image for face verification
     */
    private function processBase64Image($base64Image, $folderPath, $fileNamePrefix = 'img_')
    {
        try {
            if (!preg_match('/^data:image\/(jpeg|png|jpg);base64,/', $base64Image, $typeMatch)) {
                throw new \Exception('Format gambar tidak valid. Hanya menerima JPEG/JPG/PNG');
            }

            $imageType = $typeMatch[1]; 
            $data = explode(',', $base64Image)[1];
            $decodedImage = base64_decode($data);

            if (!$decodedImage) {
                throw new \Exception('Gagal mendekode gambar base64');
            }

            $storageRelativePath = "{$folderPath}/{$fileNamePrefix}" . uniqid() . '.' . $imageType;
            
            Storage::disk('public')->put($storageRelativePath, $decodedImage);

            return [
                'storage_path' => $storageRelativePath, 
                'full_path' => storage_path("app/public/{$storageRelativePath}"), 
                'mime_type' => 'image/'.$imageType
            ];

        } catch (\Exception $e) {
            Log::error('Error proses gambar base64: '.$e->getMessage());
            throw $e; 
        }
    }
}