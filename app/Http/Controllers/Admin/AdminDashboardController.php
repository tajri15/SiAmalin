<?php
// File: C:\Users\dafii\OneDrive\Desktop\SiAmalin-BARU\SiAmalin\app\Http/Controllers/Admin/AdminDashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\Laporan;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $jumlahKaryawan = Karyawan::count();

        $hariIni = date("Y-m-d");
        $presensiHariIni = Presensi::where('tgl_presensi', '>=', new UTCDateTime(strtotime($hariIni . " 00:00:00") * 1000))
                                    ->where('tgl_presensi', '<', new UTCDateTime(strtotime($hariIni . " 23:59:59") * 1000))
                                    ->get();
        $hadirHariIni = $presensiHariIni->count();
        // $terlambatHariIni = $presensiHariIni->where('jam_in', '>', '07:00')->count(); // Dihapus

        $laporanBelumDitinjau = Laporan::whereNull('status_admin')->count();

        // Data untuk chart (contoh sederhana)
        $rekapPresensiBulanan = Presensi::raw(function ($collection) {
            $bulanIni = date("m");
            $tahunIni = date("Y");
            return $collection->aggregate([
                [
                    '$match' => [
                        'tgl_presensi' => [
                            '$gte' => new UTCDateTime(strtotime("$tahunIni-$bulanIni-01") * 1000),
                            '$lt' => new UTCDateTime(strtotime("$tahunIni-$bulanIni-" . date('t', strtotime("$tahunIni-$bulanIni-01")) . " 23:59:59") * 1000)
                        ]
                    ]
                ],
                [
                    '$group' => [
                        '_id' => null,
                        'totalHadir' => ['$sum' => ['$cond' => [['$ne' => ['$jam_in', null]], 1, 0]]],
                        // 'totalTerlambat' => ['$sum' => ['$cond' => [['$gt' => ['$jam_in', '07:00']], 1, 0]]], // Dihapus
                        // Anda bisa tambahkan kalkulasi lain seperti izin, sakit jika ada datanya
                    ]
                ]
            ]);
        })->first();


        return view('admin.dashboard', compact(
            'jumlahKaryawan',
            'hadirHariIni',
            // 'terlambatHariIni', // Dihapus
            'laporanBelumDitinjau',
            'rekapPresensiBulanan'
        ));
    }
}