<?php

namespace App\Http\Controllers;

use App\Models\Patrol;
use App\Models\PatrolPoint;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; 
use MongoDB\BSON\UTCDateTime;


class PatroliController extends Controller
{
    /**
     * Display the patrol page for employees.
     *
     * @return \Illuminate\View\View
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
                'path' => $activePatrol->points()->orderBy('timestamp', 'asc')->get(['latitude', 'longitude'])->map(function($point) {
                    return [$point->latitude, $point->longitude]; 
                })->toArray()
            ];
        }

        return view('patroli.index', compact('karyawan', 'patrolData'));
    }

    /**
     * Start a new patrol session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function startPatrol(Request $request)
    {
        $karyawan = Auth::guard('karyawan')->user();

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
     * Store a new patrol point.
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

            return response()->json(['success' => true, 'message' => 'Titik patroli disimpan.']);
        } catch (\Exception $e) {
            Log::error('Error storePoint: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan titik patroli: ' . $e->getMessage()], 500);
        }
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
    public function historiPatroli(Request $request) // KOREKSI 2: Tambahkan Request $request
    {
        // KOREKSI 3: Hapus paksa pesan error lama dari sesi
        // Ini memastikan halaman histori selalu bersih dari flash message sebelumnya.
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
            // Tempat di mana flash message error diset
            return redirect()->route('patroli.histori')->with('error', 'Data patroli tidak ditemukan.');
        }
        
        $pathForMap = collect($patrol->path)->map(function ($point) {
            if (is_array($point) && count($point) >= 2) {
                return [$point[1], $point[0]]; // Balik urutan menjadi [lat, lng]
            }
            return null;
        })->filter()->toArray();


        return view('patroli.detail', compact('patrol', 'karyawan', 'pathForMap'));
    }
}