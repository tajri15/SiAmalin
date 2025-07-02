<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Presensi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $nik = Auth::guard('karyawan')->user()->nik;

        // --- PERBAIKAN UTAMA: Menggunakan today() dan endOfDay() dari Carbon ---
        // Cara ini secara otomatis menangani zona waktu dan lebih andal.
        $startOfDay = Carbon::today();
        $endOfDay = Carbon::today()->endOfDay();

        $presensihariini = Presensi::where('nik', $nik)
            ->whereBetween('tgl_presensi', [$startOfDay, $endOfDay])
            ->first();
        // --- AKHIR PERBAIKAN UTAMA ---

        $bulanini = date("m");
        $tahunini = date("Y");
        
        $historibulanini = Presensi::where('nik', $nik)
            ->whereMonth('tgl_presensi', $bulanini)
            ->whereYear('tgl_presensi', $tahunini)
            ->orderBy('tgl_presensi')
            ->get();
            
        $rekappresensi = Presensi::raw(function ($collection) use ($nik, $bulanini, $tahunini) {
            $startOfMonth = Carbon::createFromDate($tahunini, $bulanini, 1)->startOfMonth();
            $endOfMonth = Carbon::createFromDate($tahunini, $bulanini, 1)->endOfMonth();
            return $collection->aggregate([
                ['$match' => [
                    'nik' => $nik,
                    'tgl_presensi' => [
                        '$gte' => new \MongoDB\BSON\UTCDateTime($startOfMonth->timestamp * 1000),
                        '$lte' => new \MongoDB\BSON\UTCDateTime($endOfMonth->timestamp * 1000)
                    ]
                ]],
                ['$group' => ['_id' => null, 'jmlhadir' => ['$sum' => 1]]]
            ]);
        })->first();

        $leaderboard = Presensi::whereBetween('tgl_presensi', [$startOfDay, $endOfDay])
            ->with('karyawan') // Eager load
            ->orderBy('jam_in', 'asc')
            ->get();

        return view('dashboard.dashboard', compact('presensihariini', 'historibulanini', 'rekappresensi', 'leaderboard'));
    }
}
