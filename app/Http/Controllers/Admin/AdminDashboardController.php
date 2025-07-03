<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\Laporan;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    public function index()
    {
        try {
            // Hitung jumlah total karyawan
            $jumlahKaryawan = Karyawan::count();
            
            // Data presensi hari ini dengan filter yang benar
            $hariIni = Carbon::now()->format('Y-m-d');
            $startOfDay = Carbon::now()->startOfDay()->timestamp * 1000;
            $endOfDay = Carbon::now()->endOfDay()->timestamp * 1000;
            
            $presensiHariIni = Presensi::where('tgl_presensi', '>=', new UTCDateTime($startOfDay))
                                      ->where('tgl_presensi', '<=', new UTCDateTime($endOfDay))
                                      ->whereNotNull('jam_in') // Hanya yang benar-benar hadir
                                      ->get();
            
            $hadirHariIni = $presensiHariIni->count();
            
            // Laporan yang belum ditinjau
            $laporanBelumDitinjau = Laporan::whereNull('status_admin')->count();

            // Data untuk chart rekap presensi bulanan
            $bulanIni = Carbon::now()->month;
            $tahunIni = Carbon::now()->year;
            $hariTerakhirBulan = Carbon::create($tahunIni, $bulanIni, 1)->daysInMonth;
            
            $rekapPresensiBulanan = Presensi::raw(function ($collection) use ($tahunIni, $bulanIni, $hariTerakhirBulan) {
                return $collection->aggregate([
                    [
                        '$match' => [
                            'tgl_presensi' => [
                                '$gte' => new UTCDateTime(Carbon::create($tahunIni, $bulanIni, 1)->startOfDay()->timestamp * 1000),
                                '$lte' => new UTCDateTime(Carbon::create($tahunIni, $bulanIni, $hariTerakhirBulan)->endOfDay()->timestamp * 1000)
                            ]
                        ]
                    ],
                    [
                        '$group' => [
                            '_id' => null,
                            'totalHadir' => ['$sum' => ['$cond' => [['$ne' => ['$jam_in', null]], 1, 0]]],
                            'totalTerlambat' => ['$sum' => ['$cond' => [['$eq' => ['$is_terlambat', true]], 1, 0]]],
                            'totalPulangAwal' => ['$sum' => ['$cond' => [['$eq' => ['$is_pulang_awal', true]], 1, 0]]]
                        ]
                    ]
                ]);
            })->first();

            return view('admin.dashboard', compact(
                'jumlahKaryawan',
                'hadirHariIni',
                'laporanBelumDitinjau',
                'rekapPresensiBulanan'
            ));

        } catch (\Exception $e) {
            Log::error('Error in AdminDashboardController: ' . $e->getMessage());
            
            // Fallback values if there's an error
            return view('admin.dashboard', [
                'jumlahKaryawan' => 0,
                'hadirHariIni' => 0,
                'laporanBelumDitinjau' => 0,
                'rekapPresensiBulanan' => (object)['totalHadir' => 0, 'totalTerlambat' => 0, 'totalPulangAwal' => 0]
            ]);
        }
    }
}