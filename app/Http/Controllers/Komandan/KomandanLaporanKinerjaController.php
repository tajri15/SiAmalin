<?php

namespace App\Http\Controllers\Komandan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\JadwalShift;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Facades\Log;

class KomandanLaporanKinerjaController extends Controller
{
    // Helper function to format total seconds into HHj MMm SSd
    private function formatDurationFromSeconds($totalSeconds) {
        if ($totalSeconds <= 0) {
            return '00j 00m 00d';
        }
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;
        return sprintf('%02dj %02dm %02dd', $hours, $minutes, $seconds);
    }

    /**
     * Helper function to calculate work duration for a day
     */
    private function calculateWorkDuration($date, $startTime, $endTime, $timezone = 'Asia/Jakarta')
    {
        try {
            $start = Carbon::parse($date->format('Y-m-d') . ' ' . $startTime, $timezone);
            $end = Carbon::parse($date->format('Y-m-d') . ' ' . $endTime, $timezone);

            if ($end <= $start) {
                $end->addDay(); // Handle overnight shifts
            }

            return $start->diffInSeconds($end);
        } catch (\Exception $e) {
            Log::error("Error calculating work duration: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get performance report data for a given period
     */
    private function getPerformanceReportData($petugasList, $startDate, $endDate)
    {
        $laporanKinerjaData = [];

        foreach ($petugasList as $petugas) {
            Log::info("Memproses Kinerja untuk NIK: {$petugas->nik} - Nama: {$petugas->nama_lengkap}");
            
            $totalJamKerjaAktualDetik = 0;
            $totalJamKerjaJadwalDetik = 0;
            $jumlahHariHadir = 0;
            $jumlahHariKerjaTerjadwal = 0;

            // Get presences for the period
            $presensiBulanIni = Presensi::where('nik', $petugas->nik)
                ->where('tgl_presensi', '>=', new UTCDateTime($startDate->copy()->timestamp * 1000))
                ->where('tgl_presensi', '<=', new UTCDateTime($endDate->copy()->timestamp * 1000))
                ->get()
                ->keyBy(function ($item) {
                    return $item->tgl_presensi ? Carbon::parse($item->tgl_presensi)->format('Y-m-d') : null;
                })->filter();

            // Get schedules for the period
            $jadwalBulanIni = JadwalShift::where('karyawan_nik', $petugas->nik)
                ->where('tanggal', '>=', new UTCDateTime($startDate->copy()->startOfDay()->utc()->getTimestamp() * 1000))
                ->where('tanggal', '<=', new UTCDateTime($endDate->copy()->startOfDay()->utc()->getTimestamp() * 1000))
                ->get()
                ->keyBy(function ($item) {
                    return $item->tanggal ? $item->tanggal->format('Y-m-d') : null;
                })->filter();

            $period = CarbonPeriod::create($startDate, $endDate);

            foreach ($period as $date) {
                $tanggalFormat = $date->format('Y-m-d');
                $jadwalHari = $jadwalBulanIni->get($tanggalFormat);

                // Calculate scheduled work duration
                if ($jadwalHari && strtoupper($jadwalHari->shift_nama) !== 'LIBUR') {
                    if (!empty($jadwalHari->jam_mulai) && !empty($jadwalHari->jam_selesai)) {
                        $jumlahHariKerjaTerjadwal++;
                        
                        $durasiJadwalDetik = $this->calculateWorkDuration(
                            $date,
                            $jadwalHari->jam_mulai,
                            $jadwalHari->jam_selesai,
                            config('app.timezone')
                        );
                        
                        $totalJamKerjaJadwalDetik += $durasiJadwalDetik;
                        Log::debug("[NIK: {$petugas->nik}, Tgl: {$tanggalFormat}] Durasi Jadwal Hari Ini (detik): {$durasiJadwalDetik}. Total Terjadwal (detik): {$totalJamKerjaJadwalDetik}");
                    }
                }

                // Calculate actual work duration from presence
                $presensiHari = $presensiBulanIni->get($tanggalFormat);
                if ($presensiHari && $presensiHari->jam_in && $presensiHari->jam_out) {
                    $jumlahHariHadir++;
                    
                    $durasiAktualDetik = $this->calculateWorkDuration(
                        Carbon::parse($presensiHari->tgl_presensi),
                        $presensiHari->jam_in,
                        $presensiHari->jam_out,
                        config('app.timezone')
                    );
                    
                    $totalJamKerjaAktualDetik += $durasiAktualDetik;
                }
            }

            $laporanKinerjaData[] = [
                'nik' => $petugas->nik,
                'nama_lengkap' => $petugas->nama_lengkap,
                'total_jam_kerja_aktual_format' => $this->formatDurationFromSeconds($totalJamKerjaAktualDetik),
                'total_jam_kerja_jadwal_format' => $this->formatDurationFromSeconds($totalJamKerjaJadwalDetik),
                'jumlah_hari_hadir' => $jumlahHariHadir,
                'jumlah_hari_kerja_terjadwal' => $jumlahHariKerjaTerjadwal,
                'persentase_kehadiran' => $jumlahHariKerjaTerjadwal > 0 ? round(($jumlahHariHadir / $jumlahHariKerjaTerjadwal) * 100, 2) : 0,
            ];
            
            Log::info("[NIK: {$petugas->nik}] Selesai Kinerja: Hadir={$jumlahHariHadir}/{$jumlahHariKerjaTerjadwal}, Jam Aktual={$totalJamKerjaAktualDetik}s, Jam Jadwal={$totalJamKerjaJadwalDetik}s");
        }

        return $laporanKinerjaData;
    }

    public function index(Request $request)
    {
        $komandan = Auth::guard('karyawan')->user();
        $fakultasKomandan = $komandan->unit;

        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $namaBulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        $petugasList = Karyawan::where('unit', $fakultasKomandan)
            ->where('jabatan', 'Petugas Keamanan')
            ->where('is_admin', false)
            ->where('is_komandan', false)
            ->orderBy('nama_lengkap', 'asc')
            ->get(['nik', 'nama_lengkap']);

        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        
        Log::info("Laporan Kinerja - Periode: {$startDate->toDateString()} s/d {$endDate->toDateString()} untuk Fakultas: {$fakultasKomandan}");

        $laporanKinerjaData = $this->getPerformanceReportData($petugasList, $startDate, $endDate);

        return view('komandan.laporan_kinerja.index', compact(
            'fakultasKomandan',
            'laporanKinerjaData',
            'bulan',
            'tahun',
            'namaBulan'
        ));
    }

    public function cetak(Request $request)
    {
        $komandan = Auth::guard('karyawan')->user();
        $fakultasKomandan = $komandan->unit;

        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $namaBulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        $petugasList = Karyawan::where('unit', $fakultasKomandan)
            ->where('jabatan', 'Petugas Keamanan')
            ->where('is_admin', false)
            ->where('is_komandan', false)
            ->orderBy('nama_lengkap', 'asc')
            ->get(['nik', 'nama_lengkap']);

        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        $laporanKinerjaData = $this->getPerformanceReportData($petugasList, $startDate, $endDate);

        return view('komandan.laporan_kinerja.cetak', compact(
            'fakultasKomandan',
            'laporanKinerjaData',
            'bulan',
            'tahun',
            'namaBulan',
            'komandan'
        ));
    }
}