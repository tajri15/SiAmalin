<?php

// File: app/Http/Controllers/Komandan/KomandanLaporanKinerjaController.php
// Tambahkan method baru `cetak`

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

        $laporanKinerjaData = [];

        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        Log::info("Laporan Kinerja - Periode: {$startDate->toDateString()} s/d {$endDate->toDateString()} untuk Fakultas: {$fakultasKomandan}");

        foreach ($petugasList as $petugas) {
            Log::info("Memproses Kinerja untuk NIK: {$petugas->nik} - Nama: {$petugas->nama_lengkap}");
            $totalJamKerjaAktualDetik = 0;
            $totalJamKerjaJadwalDetik = 0;
            $jumlahHariHadir = 0;
            $jumlahHariKerjaTerjadwal = 0;

            $presensiBulanIni = Presensi::where('nik', $petugas->nik)
                ->where('tgl_presensi', '>=', new UTCDateTime($startDate->copy()->timestamp * 1000))
                ->where('tgl_presensi', '<=', new UTCDateTime($endDate->copy()->timestamp * 1000))
                ->get()
                ->keyBy(function ($item) {
                    return $item->tgl_presensi ? Carbon::parse($item->tgl_presensi)->format('Y-m-d') : null;
                })->filter();

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

                if ($jadwalHari && strtoupper($jadwalHari->shift_nama) !== 'LIBUR') {
                    if (!empty($jadwalHari->jam_mulai) && !empty($jadwalHari->jam_selesai)) {
                        $jumlahHariKerjaTerjadwal++;
                        try {
                            $jamMulaiJadwal = Carbon::parse($date->format('Y-m-d') . ' ' . $jadwalHari->jam_mulai, config('app.timezone'));
                            $jamSelesaiJadwal = Carbon::parse($date->format('Y-m-d') . ' ' . $jadwalHari->jam_selesai, config('app.timezone'));

                            if ($jamSelesaiJadwal->lt($jamMulaiJadwal)) {
                                $jamSelesaiJadwal->addDay();
                            }

                            $durasiJadwalDetik = $jamMulaiJadwal->diffInSeconds($jamSelesaiJadwal);
                            
                            if ($durasiJadwalDetik < 0) { 
                                Log::warning("Durasi jadwal negatif untuk NIK {$petugas->nik} pada {$tanggalFormat}. Mulai: {$jadwalHari->jam_mulai}, Selesai: {$jadwalHari->jam_selesai}. Durasi dihitung: {$durasiJadwalDetik}. Direset ke 0.");
                                $durasiJadwalDetik = 0;
                            }
                            $totalJamKerjaJadwalDetik += $durasiJadwalDetik;
                            Log::debug("[NIK: {$petugas->nik}, Tgl: {$tanggalFormat}] Durasi Jadwal Hari Ini (detik): {$durasiJadwalDetik}. Total Terjadwal (detik): {$totalJamKerjaJadwalDetik}");

                        } catch (\Exception $e) {
                            Log::error("Error parsing jadwal shift time for NIK {$petugas->nik} on {$tanggalFormat}: Jam Mulai '{$jadwalHari->jam_mulai}', Jam Selesai '{$jadwalHari->jam_selesai}'. Error: {$e->getMessage()}");
                        }
                    } else {
                        Log::info("Shift {$jadwalHari->shift_nama} untuk NIK {$petugas->nik} pada {$tanggalFormat} tidak memiliki jam mulai/selesai yang lengkap, tidak dihitung sebagai hari kerja terjadwal.");
                    }
                }

                $presensiHari = $presensiBulanIni->get($tanggalFormat);
                if ($presensiHari && $presensiHari->jam_in && $presensiHari->jam_out) {
                    $jumlahHariHadir++;
                    try {
                        $tglPresensiCarbon = Carbon::parse($presensiHari->tgl_presensi)->setTimezone(config('app.timezone'));
                        $jamMasuk = Carbon::parse($tglPresensiCarbon->format('Y-m-d') . ' ' . $presensiHari->jam_in, config('app.timezone'));
                        $jamPulang = Carbon::parse($tglPresensiCarbon->format('Y-m-d') . ' ' . $presensiHari->jam_out, config('app.timezone'));

                        if ($jamPulang->lt($jamMasuk)) {
                            $jamPulang->addDay();
                        }
                        $durasiAktualDetik = $jamMasuk->diffInSeconds($jamPulang);
                        if ($durasiAktualDetik < 0) $durasiAktualDetik = 0;
                        $totalJamKerjaAktualDetik += $durasiAktualDetik;
                    } catch (\Exception $e) {
                         Log::error("Error parsing presensi time for NIK {$petugas->nik} on {$tanggalFormat}: Jam In '{$presensiHari->jam_in}', Jam Out '{$presensiHari->jam_out}'. Error: {$e->getMessage()}");
                    }
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

        return view('komandan.laporan_kinerja.index', compact(
            'fakultasKomandan',
            'laporanKinerjaData',
            'bulan',
            'tahun',
            'namaBulan'
        ));
    }

    /**
     * Menyiapkan data untuk halaman cetak laporan kinerja.
     */
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

        $laporanKinerjaData = [];
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        foreach ($petugasList as $petugas) {
            $totalJamKerjaAktualDetik = 0;
            $totalJamKerjaJadwalDetik = 0;
            $jumlahHariHadir = 0;
            $jumlahHariKerjaTerjadwal = 0;

            $presensiBulanIni = Presensi::where('nik', $petugas->nik)
                ->where('tgl_presensi', '>=', new UTCDateTime($startDate->copy()->timestamp * 1000))
                ->where('tgl_presensi', '<=', new UTCDateTime($endDate->copy()->timestamp * 1000))
                ->get()
                ->keyBy(function ($item) {
                    return $item->tgl_presensi ? Carbon::parse($item->tgl_presensi)->format('Y-m-d') : null;
                })->filter();

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

                if ($jadwalHari && strtoupper($jadwalHari->shift_nama) !== 'LIBUR' && !empty($jadwalHari->jam_mulai) && !empty($jadwalHari->jam_selesai)) {
                    $jumlahHariKerjaTerjadwal++;
                    try {
                        $jamMulaiJadwal = Carbon::parse($date->format('Y-m-d') . ' ' . $jadwalHari->jam_mulai, config('app.timezone'));
                        $jamSelesaiJadwal = Carbon::parse($date->format('Y-m-d') . ' ' . $jadwalHari->jam_selesai, config('app.timezone'));
                        if ($jamSelesaiJadwal->lt($jamMulaiJadwal)) $jamSelesaiJadwal->addDay();
                        $totalJamKerjaJadwalDetik += $jamMulaiJadwal->diffInSeconds($jamSelesaiJadwal);
                    } catch (\Exception $e) {}
                }

                $presensiHari = $presensiBulanIni->get($tanggalFormat);
                if ($presensiHari && $presensiHari->jam_in && $presensiHari->jam_out) {
                    $jumlahHariHadir++;
                    try {
                        $tglPresensiCarbon = Carbon::parse($presensiHari->tgl_presensi)->setTimezone(config('app.timezone'));
                        $jamMasuk = Carbon::parse($tglPresensiCarbon->format('Y-m-d') . ' ' . $presensiHari->jam_in, config('app.timezone'));
                        $jamPulang = Carbon::parse($tglPresensiCarbon->format('Y-m-d') . ' ' . $presensiHari->jam_out, config('app.timezone'));
                        if ($jamPulang->lt($jamMasuk)) $jamPulang->addDay();
                        $totalJamKerjaAktualDetik += $jamMasuk->diffInSeconds($jamPulang);
                    } catch (\Exception $e) {}
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
        }

        // Mengirim data ke view cetak yang baru
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