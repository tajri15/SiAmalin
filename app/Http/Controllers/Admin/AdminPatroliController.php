<?php
// File: app/Http/Controllers/Admin/AdminPatroliController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Patrol;
use App\Models\PatrolPoint;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime;

class AdminPatroliController extends Controller
{
    /**
     * Display a listing of the patrol records.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Patrol::with('karyawan')
                       ->where('status', 'selesai');

        if ($request->filled('nik_karyawan')) {
            $query->where('karyawan_nik', 'like', '%' . $request->nik_karyawan . '%');
        }

        if ($request->filled('nama_karyawan')) {
            $niksFromName = Karyawan::where('nama_lengkap', 'regexp', "/.*{$request->nama_karyawan}.*/i")
                                      ->pluck('nik')
                                      ->toArray();
            
            if (empty($niksFromName)) {
                $query->whereRaw(fn($q) => $q->where('_id', '=', '0'));
            } else {
                $query->whereIn('karyawan_nik', $niksFromName);
            }
        }

        if ($request->filled('tanggal_mulai')) {
            $tanggalMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
            $query->where('start_time', '>=', new UTCDateTime($tanggalMulai->timestamp * 1000));
        }

        if ($request->filled('tanggal_akhir')) {
            $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->endOfDay();
            $query->where('start_time', '<=', new UTCDateTime($tanggalAkhir->timestamp * 1000));
        }

        $patrols = $query->orderBy('start_time', 'desc')->paginate(15);
        $karyawans = Karyawan::orderBy('nama_lengkap')->get(['nik', 'nama_lengkap']);

        return view('admin.patroli.index', compact('patrols', 'karyawans'));
    }

    /**
     * Display the specified patrol record.
     * MODIFIED: Eager load karyawan and pass office location/radius to view.
     */
    public function show($patrolId)
    {
        // --- PERBAIKAN: Menggunakan with() untuk eager loading ---
        $patrol = Patrol::with(['karyawan', 'points'])->find($patrolId);

        if (!$patrol) {
            return redirect()->route('admin.patroli.index')->with('error', 'Data patroli tidak ditemukan.');
        }

        $pathForMap = collect($patrol->path)->map(function ($point) {
            if (is_array($point) && count($point) >= 2) {
                return [$point[1], $point[0]];
            }
            return null; 
        })->filter()->toArray();

        // --- PERBAIKAN: Mengambil data lokasi dan radius dari relasi karyawan ---
        $officeLocation = $patrol->karyawan->office_location ?? null;
        $officeRadius = $patrol->karyawan->office_radius ?? null;

        return view('admin.patroli.show', compact('patrol', 'pathForMap', 'officeLocation', 'officeRadius'));
    }

    /**
     * Remove the specified patrol record from storage.
     *
     * @param  string  $patrolId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($patrolId)
    {
        try {
            $patrol = Patrol::findOrFail($patrolId);
            $patrol->delete();

            return redirect()->route('admin.patroli.index')->with('success', 'Data patroli berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting patrol: ' . $e->getMessage());
            return redirect()->route('admin.patroli.index')->with('error', 'Gagal menghapus data patroli.');
        }
    }
}