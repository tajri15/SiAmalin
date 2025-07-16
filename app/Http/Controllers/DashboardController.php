<?php
// File: app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Presensi;
use App\Models\JadwalShift;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $nik = Auth::guard('karyawan')->user()->nik;

        // --- PERBAIKAN LOGIKA SHIFT MALAM & SHIFT BIASA v5 ---
        // 1. Cari presensi yang masih aktif (belum absen pulang) dalam 48 jam terakhir
        //    (diperluas untuk menangani shift malam yang lintas hari)
        $presensihariini = Presensi::where('nik', $nik)
            ->whereNull('jam_out')
            ->where('tgl_presensi', '>=', now()->subHours(48))
            ->orderBy('tgl_presensi', 'desc')
            ->first();

        // 2. Jika tidak ada yang aktif, cek jadwal yang berlaku dan lihat apakah sudah ada presensi selesai
        if (!$presensihariini) {
            $jadwalAktif = $this->getJadwalAktifUntukDashboard($nik);
            
            if ($jadwalAktif) {
                // Query berdasarkan tanggal jadwal yang tepat
                $tanggalJadwalUTC = new \MongoDB\BSON\UTCDateTime($jadwalAktif->tanggal->copy()->startOfDay()->timestamp * 1000);
                $presensihariini = Presensi::where('nik', $nik)
                    ->where('tgl_presensi', $tanggalJadwalUTC)
                    ->whereNotNull('jam_out')
                    ->first();
            }
        }
        // --- AKHIR PERBAIKAN ---

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

        $startOfDayForLeaderboard = Carbon::today()->startOfDay();
        $endOfDayForLeaderboard = Carbon::today()->endOfDay();
        $leaderboard = Presensi::whereBetween('tgl_presensi', [$startOfDayForLeaderboard, $endOfDayForLeaderboard])
            ->with('karyawan') // Eager load
            ->orderBy('jam_in', 'asc')
            ->get();

        return view('dashboard.dashboard', compact('presensihariini', 'historibulanini', 'rekappresensi', 'leaderboard'));
    }

    private function getJadwalAktifUntukDashboard($nik)
    {
        $waktuSekarang = Carbon::now();
        
        // Cek jadwal kemarin untuk shift malam yang masih berlanjut
        $jadwalKemarin = JadwalShift::where('karyawan_nik', $nik)
                                    ->where('tanggal', new \MongoDB\BSON\UTCDateTime($waktuSekarang->copy()->subDay()->startOfDay()->timestamp * 1000))
                                    ->first();

        if ($jadwalKemarin && $jadwalKemarin->jam_mulai && $jadwalKemarin->jam_selesai) {
            $jamMulaiKemarin = Carbon::parse($jadwalKemarin->tanggal->format('Y-m-d') . ' ' . $jadwalKemarin->jam_mulai);
            $jamSelesaiKemarin = Carbon::parse($jadwalKemarin->tanggal->format('Y-m-d') . ' ' . $jadwalKemarin->jam_selesai);
            
            // Jika jam selesai lebih kecil dari jam mulai, berarti shift melewati tengah malam
            if ($jamSelesaiKemarin->lt($jamMulaiKemarin)) {
                $jamSelesaiKemarin->addDay();
                // Jika waktu sekarang masih dalam rentang shift kemarin, return jadwal kemarin
                if ($waktuSekarang->lt($jamSelesaiKemarin)) {
                    return $jadwalKemarin;
                }
            }
        }

        // Jika tidak ada shift kemarin yang masih aktif, cek jadwal hari ini
        $jadwalHariIni = JadwalShift::where('karyawan_nik', $nik)
                                    ->where('tanggal', new \MongoDB\BSON\UTCDateTime($waktuSekarang->copy()->startOfDay()->timestamp * 1000))
                                    ->first();
        
        return $jadwalHariIni;
    }
}