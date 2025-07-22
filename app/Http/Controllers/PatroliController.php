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
            // FIXED: Hitung durasi yang akurat
            $currentDurationSeconds = $this->calculateAccurateDuration($activePatrol);

            // FIXED: Hitung jarak berdasarkan points yang tersimpan
            $calculatedDistance = $this->calculateTotalDistanceFromPoints($activePatrol->_id);

            $patrolData = [
                'id' => $activePatrol->_id,
                'status' => $activePatrol->status,
                'start_time' => $activePatrol->start_time->timestamp * 1000, 
                'total_distance_meters' => $calculatedDistance,
                'duration_seconds' => $currentDurationSeconds, 
                'face_verified' => $activePatrol->face_verified ?? false,
                'path' => $activePatrol->points()->orderBy('timestamp', 'asc')->get(['latitude', 'longitude'])->map(function($point) {
                    return [$point->latitude, $point->longitude]; 
                })->toArray()
            ];
        }

        // Get office location and radius for the view
        $officeLocation = $karyawan->office_location;
        $officeRadius = $karyawan->office_radius;

        // Pass face descriptor for verification
        $faceDescriptor = null;
        if ($karyawan && !empty($karyawan->face_embedding['embedding'])) {
            $faceDescriptor = json_encode($karyawan->face_embedding['embedding']);
        }

        return view('patroli.index', compact('karyawan', 'patrolData', 'officeLocation', 'officeRadius', 'faceDescriptor'));
    }

    /**
     * NEW: Calculate accurate duration considering pause/resume
     */
    private function calculateAccurateDuration($patrol)
    {
        $totalDuration = $patrol->duration_seconds ?? 0;
        
        if ($patrol->status === 'aktif') {
            // Tambahkan waktu dari last resume/start sampai sekarang
            $lastActiveTime = $patrol->updated_at ?? $patrol->start_time;
            if ($lastActiveTime) {
                $totalDuration += (now()->timestamp - $lastActiveTime->timestamp);
            }
        }
        
        return $totalDuration;
    }

    /**
     * NEW: Calculate total distance from stored points using Haversine formula
     */
    private function calculateTotalDistanceFromPoints($patrolId)
    {
        $points = PatrolPoint::where('patrol_id', $patrolId)
                            ->orderBy('timestamp', 'asc')
                            ->get(['latitude', 'longitude']);
        
        if ($points->count() < 2) {
            return 0;
        }

        $totalDistance = 0;
        for ($i = 1; $i < $points->count(); $i++) {
            $totalDistance += $this->haversineDistance(
                $points[$i-1]->latitude, 
                $points[$i-1]->longitude,
                $points[$i]->latitude, 
                $points[$i]->longitude
            );
        }

        return $totalDistance;
    }

    /**
     * NEW: Calculate distance between two points using Haversine formula
     */
    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Earth radius in meters
        
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLatRad = deg2rad($lat2 - $lat1);
        $deltaLonRad = deg2rad($lon2 - $lon1);

        $a = sin($deltaLatRad / 2) * sin($deltaLatRad / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLonRad / 2) * sin($deltaLonRad / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
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

        // Validasi Radius Lokasi
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
                'face_verified' => false,
                'face_verification_image' => null,
                'path' => [],
                'pause_start_time' => null, // NEW: Track pause time
                'total_pause_duration' => 0, // NEW: Track total pause time
            ]);

            // FIXED: Simpan titik awal patroli
            PatrolPoint::create([
                'patrol_id' => $patrol->_id,
                'karyawan_nik' => $karyawan->nik,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => null,
                'speed' => null,
                'timestamp' => new UTCDateTime(now()->timestamp * 1000),
                'point_type' => 'start' // NEW: Mark as start point
            ]);

            Log::info('Patrol started:', [
                'patrol_id' => $patrol->_id,
                'karyawan_nik' => $karyawan->nik,
                'start_location' => [$request->latitude, $request->longitude],
                'start_time' => now()
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
     * Verify face for patrol
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

            Log::info('Face verified for patrol:', [
                'patrol_id' => $patrol->_id,
                'karyawan_nik' => $karyawan->nik,
                'verification_time' => $currentDate
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
     * MODIFIED: Add validation and better logging
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

            // Validasi Radius Real-time
            $jarakResult = FaceRecognitionHelper::isWithinOfficeRadius($request->latitude, $request->longitude, $karyawan);

            if (!$jarakResult['within']) {
                Log::warning('Point outside radius:', [
                    'patrol_id' => $request->patrol_id,
                    'karyawan_nik' => $karyawan->nik,
                    'location' => [$request->latitude, $request->longitude],
                    'distance' => $jarakResult['distance']
                ]);

                return response()->json([
                    'success' => false, 
                    'message' => 'Titik patroli tidak disimpan. Anda berada di luar radius lokasi kerja.',
                    'outside_radius' => true,
                    'distance' => round($jarakResult['distance'])
                ], 403);
            }

            // FIXED: Validasi jarak minimum untuk menghindari duplikasi point
            $lastPoint = PatrolPoint::where('patrol_id', $request->patrol_id)
                                   ->orderBy('timestamp', 'desc')
                                   ->first();

            if ($lastPoint) {
                $distanceFromLast = $this->haversineDistance(
                    $lastPoint->latitude, 
                    $lastPoint->longitude,
                    $request->latitude, 
                    $request->longitude
                );

                // Skip jika jarak terlalu dekat (kurang dari 5 meter)
                if ($distanceFromLast < 5) {
                    return response()->json([
                        'success' => true, 
                        'message' => 'Titik terlalu dekat dengan titik sebelumnya.',
                        'outside_radius' => false,
                        'skipped' => true
                    ]);
                }
            }

            PatrolPoint::create([
                'patrol_id' => $request->patrol_id,
                'karyawan_nik' => $karyawan->nik,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy,
                'speed' => $request->speed,
                'timestamp' => new UTCDateTime($request->timestamp),
                'point_type' => 'track' // NEW: Mark as tracking point
            ]);
            
            // FIXED: Update total distance in real-time
            $totalDistance = $this->calculateTotalDistanceFromPoints($request->patrol_id);
            $patrol->update([
                'total_distance_meters' => $totalDistance,
                'updated_at' => now()
            ]);

            Log::info('Point stored:', [
                'patrol_id' => $request->patrol_id,
                'location' => [$request->latitude, $request->longitude],
                'total_distance' => $totalDistance,
                'accuracy' => $request->accuracy,
                'speed' => $request->speed
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Titik patroli disimpan.',
                'outside_radius' => false,
                'total_distance' => $totalDistance
            ]);
        } catch (\Exception $e) {
            Log::error('Error storePoint: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan titik patroli: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Check if current location is within office radius.
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
     * FIXED: Better pause tracking
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

            // FIXED: Calculate accurate duration before pause
            $currentSegmentDuration = 0;
            $lastActiveTime = $patrol->updated_at ?? $patrol->start_time;
            
            if ($lastActiveTime) {
                $currentSegmentDuration = now()->timestamp - $lastActiveTime->timestamp;
            }
            
            $newDuration = ($patrol->duration_seconds ?? 0) + $currentSegmentDuration;

            $patrol->update([
                'status' => 'jeda',
                'duration_seconds' => $newDuration,
                'pause_start_time' => new UTCDateTime(now()->timestamp * 1000)
            ]);

            Log::info('Patrol paused:', [
                'patrol_id' => $patrol->_id,
                'duration_before_pause' => $newDuration,
                'pause_time' => now()
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Patroli dijeda.',
                'duration_seconds' => $newDuration
            ]);
        } catch (\Exception $e) {
            Log::error('Error pausePatrol: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menjeda patroli: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Resume a paused patrol session.
     * FIXED: Better resume tracking
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

            // FIXED: Calculate pause duration
            $pauseDuration = 0;
            if ($patrol->pause_start_time) {
                $pauseDuration = now()->timestamp - $patrol->pause_start_time->timestamp;
            }

            $totalPauseDuration = ($patrol->total_pause_duration ?? 0) + $pauseDuration;

            $patrol->update([
                'status' => 'aktif',
                'pause_start_time' => null,
                'total_pause_duration' => $totalPauseDuration
            ]);

            Log::info('Patrol resumed:', [
                'patrol_id' => $patrol->_id,
                'pause_duration' => $pauseDuration,
                'total_pause_duration' => $totalPauseDuration,
                'resume_time' => now()
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Patroli dilanjutkan.',
                'total_pause_duration' => $totalPauseDuration
            ]);
        } catch (\Exception $e) {
            Log::error('Error resumePatrol: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal melanjutkan patroli: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Stop an active or paused patrol session.
     * FIXED: Calculate accurate final duration and distance
     */
    public function stopPatrol(Request $request)
    {
        $request->validate([
            'patrol_id' => 'required|string',
            'latitude' => 'nullable|numeric', // NEW: End location
            'longitude' => 'nullable|numeric', // NEW: End location
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

            // Check if face verification is required
            if (!$patrol->face_verified) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Patroli tidak dapat dihentikan. Anda harus melakukan verifikasi wajah terlebih dahulu.',
                    'face_verification_required' => true
                ], 403);
            }

            // FIXED: Save end point if location provided
            if ($request->latitude && $request->longitude) {
                PatrolPoint::create([
                    'patrol_id' => $request->patrol_id,
                    'karyawan_nik' => $karyawan->nik,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'accuracy' => null,
                    'speed' => null,
                    'timestamp' => new UTCDateTime(now()->timestamp * 1000),
                    'point_type' => 'end' // NEW: Mark as end point
                ]);
            }

            // FIXED: Calculate accurate final values
            $finalDistance = $this->calculateTotalDistanceFromPoints($patrol->_id);
            $finalDuration = $this->calculateFinalDuration($patrol);

            $points = PatrolPoint::where('patrol_id', $patrol->_id)
                                ->orderBy('timestamp', 'asc')
                                ->get(['longitude', 'latitude']); 

            $pathCoordinates = $points->map(function ($point) {
                return [$point->longitude, $point->latitude]; 
            })->toArray();

            $updateData = [
                'status' => 'selesai',
                'end_time' => new UTCDateTime(now()->timestamp * 1000),
                'total_distance_meters' => $finalDistance,
                'duration_seconds' => $finalDuration,
                'path' => $pathCoordinates,
                'total_points' => $points->count() // NEW: Track total points
            ];
            
            $patrol->update($updateData);

            Log::info('Patrol completed:', [
                'patrol_id' => $patrol->_id,
                'final_distance' => $finalDistance,
                'final_duration' => $finalDuration,
                'total_points' => $points->count(),
                'start_time' => $patrol->start_time,
                'end_time' => now()
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Patroli berhasil dihentikan dan disimpan.',
                'final_distance' => $finalDistance,
                'final_duration' => $finalDuration,
                'total_points' => $points->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error stopPatrol: ' . $e->getMessage() . ' Line: ' . $e->getLine() . ' File: ' . $e->getFile());
            return response()->json(['success' => false, 'message' => 'Gagal menghentikan patroli: ' . $e->getMessage()], 500);
        }
    }

    /**
     * NEW: Calculate final duration considering all pause times
     */
    private function calculateFinalDuration($patrol)
    {
        $totalDuration = $patrol->duration_seconds ?? 0;
        
        // Add current segment if patrol is active
        if ($patrol->status === 'aktif') {
            $lastActiveTime = $patrol->updated_at ?? $patrol->start_time;
            if ($lastActiveTime) {
                $totalDuration += (now()->timestamp - $lastActiveTime->timestamp);
            }
        }
        
        // Alternative: Calculate from start to end time minus pause duration
        $startToEndDuration = now()->timestamp - $patrol->start_time->timestamp;
        $totalPauseDuration = $patrol->total_pause_duration ?? 0;
        
        // Use the more accurate calculation
        $calculatedDuration = $startToEndDuration - $totalPauseDuration;
        
        // Return the maximum of both calculations to ensure accuracy
        return max($totalDuration, $calculatedDuration);
    }

    /**
     * Display patrol history for the logged-in employee.
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
     * Process base64 image for face verification
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