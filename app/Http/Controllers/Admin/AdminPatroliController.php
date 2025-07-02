<?php

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
            $query->where('karyawan_nik', $request->nik_karyawan);
        }

        if ($request->filled('nama_karyawan')) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->nama_karyawan . '%');
            });
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
     *
     * @param  string  $patrolId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($patrolId)
    {
        $patrol = Patrol::with('karyawan', 'points')->find($patrolId);

        if (!$patrol) {
            return redirect()->route('admin.patroli.index')->with('error', 'Data patroli tidak ditemukan.');
        }

        $pathForMap = collect($patrol->path)->map(function ($point) {
            if (is_array($point) && count($point) >= 2) {
                return [$point[1], $point[0]];
            }
            return null; 
        })->filter()->toArray();

        return view('admin.patroli.show', compact('patrol', 'pathForMap'));
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
