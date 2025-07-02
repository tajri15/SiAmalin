<?php

namespace App\Http\Controllers\KetuaDepartemen;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Komandan\KomandanJadwalShiftController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\Laporan;
use App\Models\Patrol;
use App\Models\PatrolPoint;
use App\Models\JadwalShift;
use MongoDB\BSON\UTCDateTime;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class KetuaDepartemenDashboardController extends Controller
{
    /**
     * Get base query for employees under the Head of Department's supervision.
     */
    private function getScopedKaryawanQuery()
    {
        $ketua = Auth::guard('karyawan')->user();
        return Karyawan::where('unit', $ketua->unit)
                        ->where('departemen', $ketua->departemen)
                        ->where('jabatan', 'Petugas Keamanan');
    }

    /**
     * Display the main dashboard for the Head of Department.
     */
    public function index()
    {
        $ketua = Auth::guard('karyawan')->user();
        $fakultas = $ketua->unit;
        $departemen = $ketua->departemen;

        $petugasNiks = $this->getScopedKaryawanQuery()->pluck('nik')->toArray();

        $jumlahPetugas = count($petugasNiks);
        
        $tglHariIniStart = new UTCDateTime(Carbon::today()->startOfDay()->timestamp * 1000);
        $tglHariIniEnd = new UTCDateTime(Carbon::today()->endOfDay()->timestamp * 1000);
        $hadirHariIni = !empty($petugasNiks) ? Presensi::whereIn('nik', $petugasNiks)->whereBetween('tgl_presensi', [$tglHariIniStart, $tglHariIniEnd])->count() : 0;

        $laporanBelumDitinjau = !empty($petugasNiks) ? Laporan::whereIn('nik', $petugasNiks)->whereNull('status_admin')->count() : 0;

        $startOfMonth = new UTCDateTime(Carbon::now()->startOfMonth()->startOfDay()->utc()->getTimestamp() * 1000);
        $endOfMonth = new UTCDateTime(Carbon::now()->endOfMonth()->endOfDay()->utc()->getTimestamp() * 1000);
        
        $totalLaporanBulanIni = !empty($petugasNiks) ? Laporan::whereIn('nik', $petugasNiks)->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count() : 0;

        $totalPatroliBulanIni = !empty($petugasNiks) ? Patrol::whereIn('karyawan_nik', $petugasNiks)->where('status', 'selesai')->whereBetween('start_time', [$startOfMonth, $endOfMonth])->count() : 0;

        $rekapPresensiBulanan = null;
        if (!empty($petugasNiks)) {
            $rekapPresensiBulanan = Presensi::raw(function ($collection) use ($petugasNiks, $startOfMonth, $endOfMonth) {
                return $collection->aggregate([
                    ['$match' => ['nik' => ['$in' => $petugasNiks], 'tgl_presensi' => ['$gte' => $startOfMonth, '$lte' => $endOfMonth]]],
                    ['$group' => ['_id' => null, 'totalHadir' => ['$sum' => ['$cond' => [['$ne' => ['$jam_in', null]], 1, 0]]]]]
                ]);
            })->first();
        }

        return view('ketua-departemen.dashboard', compact(
            'fakultas', 'departemen', 'jumlahPetugas', 'hadirHariIni', 'laporanBelumDitinjau',
            'totalLaporanBulanIni', 'totalPatroliBulanIni', 'rekapPresensiBulanan'
        ));
    }
    
    /**
     * Display list of employees under supervision. (VIEW ONLY)
     */
    public function dataKaryawan(Request $request)
    {
        $ketua = Auth::guard('karyawan')->user();
        $fakultas = $ketua->unit;
        $departemen = $ketua->departemen;
        
        $query = $this->getScopedKaryawanQuery();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_lengkap', 'like', '%' . $searchTerm . '%')
                    ->orWhere('nik', 'like', '%' . $searchTerm . '%');
            });
        }
        $karyawans = $query->orderBy('nama_lengkap')->paginate(10);
        
        return view('ketua-departemen.karyawan.index', compact('karyawans', 'fakultas', 'departemen'));
    }
    
    /**
     * Display attendance recapitulation. (VIEW ONLY)
     */
    public function rekapPresensi(Request $request)
    {
        $ketua = Auth::guard('karyawan')->user();
        $fakultas = $ketua->unit;
        $departemen = $ketua->departemen;

        $bulanIni = $request->input('bulan', date('m'));
        $tahunIni = $request->input('tahun', date('Y'));
        
        $petugasNiks = $this->getScopedKaryawanQuery()->pluck('nik')->toArray();

        $presensiData = collect();
        if (!empty($petugasNiks)) {
            $query = Presensi::query()->with('karyawan')->whereIn('nik', $petugasNiks);
            
            $startDate = Carbon::createFromDate($tahunIni, $bulanIni, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($tahunIni, $bulanIni, 1)->endOfMonth();
            $query->whereBetween('tgl_presensi', [new UTCDateTime($startDate->timestamp * 1000), new UTCDateTime($endDate->timestamp * 1000)]);
            
            if ($request->filled('nik')) $query->where('nik', 'like', '%' . $request->nik . '%');
            if ($request->filled('nama')) $query->whereHas('karyawan', fn($q) => $q->where('nama_lengkap', 'like', '%' . $request->nama . '%'));

            $presensiData = $query->orderBy('tgl_presensi', 'desc')->paginate(15);
        }
        
        $namaBulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        return view('ketua-departemen.presensi.rekapitulasi', compact('presensiData', 'bulanIni', 'tahunIni', 'namaBulan', 'fakultas', 'departemen'));
    }

    /**
     * Display daily attendance report. (VIEW ONLY)
     */
    public function laporanHarianPresensi(Request $request)
    {
        $ketua = Auth::guard('karyawan')->user();
        $fakultas = $ketua->unit;
        $departemen = $ketua->departemen;
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        $petugasNiks = $this->getScopedKaryawanQuery()->pluck('nik')->toArray();

        $presensiHarian = collect();
        if (!empty($petugasNiks)) {
            $tglStart = new UTCDateTime(Carbon::parse($tanggal)->startOfDay()->timestamp * 1000);
            $tglEnd = new UTCDateTime(Carbon::parse($tanggal)->endOfDay()->timestamp * 1000);
            $presensiHarian = Presensi::whereIn('nik', $petugasNiks)
                ->whereBetween('tgl_presensi', [$tglStart, $tglEnd])
                ->with('karyawan')->orderBy('jam_in', 'asc')->get();
        }

        return view('ketua-departemen.presensi.harian', compact('presensiHarian', 'tanggal', 'fakultas', 'departemen'));
    }
    
    /**
     * Display attendance detail for a specific employee. (VIEW ONLY)
     */
    public function detailPresensiKaryawan(Request $request, $nik)
    {
        $ketua = Auth::guard('karyawan')->user();
        $fakultas = $ketua->unit;
        $departemen = $ketua->departemen;

        $karyawan = $this->getScopedKaryawanQuery()->where('nik', $nik)->firstOrFail();
        
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        
        $historiPresensi = Presensi::where('nik', $nik)
            ->whereBetween('tgl_presensi', [new UTCDateTime($startDate->timestamp * 1000), new UTCDateTime($endDate->timestamp * 1000)])
            ->orderBy('tgl_presensi', 'asc')->get();
            
        $namaBulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        return view('ketua-departemen.presensi.detail_karyawan', compact('karyawan', 'historiPresensi', 'bulan', 'tahun', 'namaBulan', 'fakultas', 'departemen'));
    }
    
    public function laporanKaryawan(Request $request)
    {
        $ketua = Auth::guard('karyawan')->user();
        $petugasNiks = $this->getScopedKaryawanQuery()->pluck('nik')->toArray();
        
        $query = Laporan::with('karyawan');
        if (!empty($petugasNiks)) {
            $query->whereIn('nik', $petugasNiks);
        } else {
            $query->whereRaw(fn($q) => $q->where('nik', '=', '-1'));
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $tanggalMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
            $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->endOfDay();
            $query->whereBetween('created_at', [new UTCDateTime($tanggalMulai->timestamp * 1000), new UTCDateTime($tanggalAkhir->timestamp * 1000)]);
        }
        
        if ($request->filled('nik_karyawan')) $query->where('nik', $request->nik_karyawan);
        if ($request->filled('jenis_laporan')) $query->where('jenis_laporan', $request->jenis_laporan);
        
        if ($request->filled('status_laporan')) {
            if ($request->status_laporan == 'belum_ditinjau') $query->whereNull('status_admin');
            else $query->where('status_admin', $request->status_laporan);
        }

        $laporans = $query->orderBy('created_at', 'desc')->paginate(15);
        $petugasFakultas = $this->getScopedKaryawanQuery()->get(['nik', 'nama_lengkap']);

        return view('ketua-departemen.laporan.index', [
            'laporans' => $laporans,
            'petugasFakultas' => $petugasFakultas,
            'fakultas' => $ketua->unit,
            'departemen' => $ketua->departemen
        ]);
    }

    public function showLaporanKaryawan($id)
    {
        $ketua = Auth::guard('karyawan')->user();
        $laporan = Laporan::with('karyawan')->findOrFail($id);

        if (!$laporan->karyawan || $laporan->karyawan->unit !== $ketua->unit || $laporan->karyawan->departemen !== $ketua->departemen) {
            abort(403, 'Anda tidak berhak mengakses laporan ini.');
        }

        return view('ketua-departemen.laporan.show', ['laporan' => $laporan, 'fakultas' => $ketua->unit, 'departemen' => $ketua->departemen]);
    }
    
    public function updateStatusLaporan(Request $request, $id)
    {
        $ketua = Auth::guard('karyawan')->user();
        $laporan = Laporan::with('karyawan')->findOrFail($id);
        
        if (!$laporan->karyawan || $laporan->karyawan->unit !== $ketua->unit || $laporan->karyawan->departemen !== $ketua->departemen) {
            abort(403, 'Anda tidak berhak mengubah status laporan ini.');
        }
        
        $request->validate([
            'status_admin' => 'required|string|in:Diterima,Ditolak',
            'catatan_admin' => 'nullable|string|max:1000',
        ]);

        $laporan->status_admin = $request->status_admin;
        $laporan->catatan_admin = $request->catatan_admin;
        $laporan->admin_peninjau_id = $ketua->nik; 
        $laporan->tanggal_peninjauan_admin = new UTCDateTime(now()->timestamp * 1000);
        $laporan->save();
        
        return redirect()->route('ketua-departemen.laporan.show', $id)->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function patroliKaryawan(Request $request)
    {
        $ketua = Auth::guard('karyawan')->user();
        $petugasNiks = $this->getScopedKaryawanQuery()->pluck('nik')->toArray();
        
        $query = Patrol::with('karyawan')->where('status', 'selesai');
        if (!empty($petugasNiks)) {
            $query->whereIn('karyawan_nik', $petugasNiks);
        } else {
            $query->whereRaw(fn($q) => $q->where('karyawan_nik', '=', '-1'));
        }

        if ($request->filled('nik_karyawan')) $query->where('karyawan_nik', $request->nik_karyawan);
        if ($request->filled('tanggal_mulai')) $query->where('start_time', '>=', new UTCDateTime(Carbon::parse($request->tanggal_mulai)->startOfDay()->timestamp * 1000));
        if ($request->filled('tanggal_akhir')) $query->where('start_time', '<=', new UTCDateTime(Carbon::parse($request->tanggal_akhir)->endOfDay()->timestamp * 1000));
        
        $patrols = $query->orderBy('start_time', 'desc')->paginate(15);
        $petugasFakultas = $this->getScopedKaryawanQuery()->get(['nik', 'nama_lengkap']);

        return view('ketua-departemen.patroli.index', [
            'patrols' => $patrols,
            'petugasFakultas' => $petugasFakultas,
            'fakultas' => $ketua->unit,
            'departemen' => $ketua->departemen
        ]);
    }

    public function showPatroliKaryawan($patrolId)
    {
        $ketua = Auth::guard('karyawan')->user();
        $patrol = Patrol::with('karyawan')->findOrFail($patrolId);

        if (!$patrol->karyawan || $patrol->karyawan->unit !== $ketua->unit || $patrol->karyawan->departemen !== $ketua->departemen) {
            abort(403, 'Anda tidak berhak mengakses detail patroli ini.');
        }

        $pathForMap = collect($patrol->path)->map(fn ($point) => (is_array($point) && count($point) >= 2) ? [$point[1], $point[0]] : null)->filter()->toArray();

        return view('ketua-departemen.patroli.show', ['patrol' => $patrol, 'pathForMap' => $pathForMap, 'fakultas' => $ketua->unit, 'departemen' => $ketua->departemen]);
    }
    
    public function monitoring()
    {
        return view('admin.patroli.monitoring');
    }

    public function getLivePatrolData()
    {
        $ketua = Auth::guard('karyawan')->user();
        $petugasNiks = $this->getScopedKaryawanQuery()->pluck('nik')->toArray();

        // PERBAIKAN: Mengambil status 'aktif' dan 'jeda'
        $activePatrols = Patrol::with('karyawan:nik,nama_lengkap,foto')
                               ->whereIn('status', ['aktif', 'jeda'])
                               ->whereIn('karyawan_nik', $petugasNiks)
                               ->get(['_id', 'karyawan_nik', 'start_time', 'status']);

        $liveData = [];

        foreach ($activePatrols as $patrol) {
            $lastPoint = PatrolPoint::where('patrol_id', $patrol->_id)
                                    ->orderBy('timestamp', 'desc')
                                    ->first();

            if ($lastPoint && $patrol->karyawan) {
                $liveData[] = [
                    'patrol_id' => $patrol->_id,
                    'nik' => $patrol->karyawan_nik,
                    'nama_lengkap' => $patrol->karyawan->nama_lengkap,
                    'foto_url' => $patrol->karyawan->foto ? asset('storage/' . $patrol->karyawan->foto) : asset('assets/img/sample/avatar/avatar1.jpg'),
                    'latitude' => $lastPoint->latitude,
                    'longitude' => $lastPoint->longitude,
                    'status' => $patrol->status, // Mengirim status ke frontend
                    'last_update' => Carbon::parse($lastPoint->timestamp)->diffForHumans(),
                    'start_time' => Carbon::parse($patrol->start_time)->format('H:i:s'),
                ];
            }
        }

        return response()->json($liveData);
    }
    
    public function jadwalShift(Request $request)
    {
        $ketua = Auth::guard('karyawan')->user();
        $selectedDate = $request->input('tanggal') ? Carbon::parse($request->input('tanggal')) : Carbon::now();
        $startOfWeek = $selectedDate->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $selectedDate->copy()->endOfWeek(Carbon::SUNDAY);
        
        $period = CarbonPeriod::create($startOfWeek, $endOfWeek);
        $datesOfWeek = collect($period)->map(fn($date) => $date->copy());

        $petugasKeamanan = $this->getScopedKaryawanQuery()->orderBy('nama_lengkap', 'asc')->get(['nik', 'nama_lengkap']);
        
        $jadwalMingguan = [];
        foreach ($petugasKeamanan as $petugas) {
            $shifts = JadwalShift::where('karyawan_nik', $petugas->nik)
                            ->whereBetween('tanggal', [
                                new UTCDateTime($startOfWeek->copy()->startOfDay()->timestamp * 1000),
                                new UTCDateTime($endOfWeek->copy()->startOfDay()->timestamp * 1000)
                            ])->get()->keyBy(fn($item) => $item->tanggal ? Carbon::parse($item->tanggal)->format('Y-m-d') : null);

            $jadwalMingguan[$petugas->nik] = ['nama_lengkap' => $petugas->nama_lengkap, 'shifts' => $shifts];
        }
        
        $definedShifts = (new \App\Http\Controllers\Komandan\KomandanJadwalShiftController)->definedShifts;
        $namaBulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        return view('ketua-departemen.jadwal_shift.index', compact(
            'jadwalMingguan', 'datesOfWeek', 'startOfWeek', 'endOfWeek', 'selectedDate',
            'definedShifts', 'namaBulan', 'ketua'
        ));
    }

    private function getLaporanKinerjaData(Request $request)
    {
        $ketua = Auth::guard('karyawan')->user();
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        
        $petugasList = $this->getScopedKaryawanQuery()->orderBy('nama_lengkap', 'asc')->get(['nik', 'nama_lengkap']);
        
        $laporanKinerjaData = [];
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        foreach ($petugasList as $petugas) {
            $totalJamKerjaAktualDetik = 0;
            $totalJamKerjaJadwalDetik = 0;
            $jumlahHariHadir = 0;
            $jumlahHariKerjaTerjadwal = 0;

            $presensiBulanIni = Presensi::where('nik', $petugas->nik)->whereBetween('tgl_presensi', [new UTCDateTime($startDate->copy()->timestamp * 1000), new UTCDateTime($endDate->copy()->timestamp * 1000)])->get()->keyBy(fn ($item) => Carbon::parse($item->tgl_presensi)->format('Y-m-d'));
            $jadwalBulanIni = JadwalShift::where('karyawan_nik', $petugas->nik)->whereBetween('tanggal', [new UTCDateTime($startDate->copy()->startOfDay()->utc()->getTimestamp() * 1000), new UTCDateTime($endDate->copy()->startOfDay()->utc()->getTimestamp() * 1000)])->get()->keyBy(fn ($item) => $item->tanggal ? $item->tanggal->format('Y-m-d') : null);

            foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
                $tanggalFormat = $date->format('Y-m-d');
                $jadwalHari = $jadwalBulanIni->get($tanggalFormat);

                if ($jadwalHari && strtoupper($jadwalHari->shift_nama) !== 'LIBUR' && !empty($jadwalHari->jam_mulai) && !empty($jadwalHari->jam_selesai)) {
                    $jumlahHariKerjaTerjadwal++;
                    try {
                        $jamMulaiJadwal = Carbon::parse($date->format('Y-m-d') . ' ' . $jadwalHari->jam_mulai);
                        $jamSelesaiJadwal = Carbon::parse($date->format('Y-m-d') . ' ' . $jadwalHari->jam_selesai);
                        if ($jamSelesaiJadwal->lt($jamMulaiJadwal)) $jamSelesaiJadwal->addDay();
                        $totalJamKerjaJadwalDetik += $jamMulaiJadwal->diffInSeconds($jamSelesaiJadwal);
                    } catch (\Exception $e) {}
                }

                $presensiHari = $presensiBulanIni->get($tanggalFormat);
                if ($presensiHari && $presensiHari->jam_in && $presensiHari->jam_out) {
                    $jumlahHariHadir++;
                    try {
                        $tglPresensiCarbon = Carbon::parse($presensiHari->tgl_presensi);
                        $jamMasuk = Carbon::parse($tglPresensiCarbon->format('Y-m-d') . ' ' . $presensiHari->jam_in);
                        $jamPulang = Carbon::parse($tglPresensiCarbon->format('Y-m-d') . ' ' . $presensiHari->jam_out);
                        if ($jamPulang->lt($jamMasuk)) $jamPulang->addDay();
                        $totalJamKerjaAktualDetik += $jamMasuk->diffInSeconds($jamPulang);
                    } catch (\Exception $e) {}
                }
            }

            $laporanKinerjaData[] = [
                'nik' => $petugas->nik,
                'nama_lengkap' => $petugas->nama_lengkap,
                'total_jam_kerja_aktual_format' => gmdate('H\j i\m s\d', $totalJamKerjaAktualDetik),
                'total_jam_kerja_jadwal_format' => gmdate('H\j i\m s\d', $totalJamKerjaJadwalDetik),
                'jumlah_hari_hadir' => $jumlahHariHadir,
                'jumlah_hari_kerja_terjadwal' => $jumlahHariKerjaTerjadwal,
                'persentase_kehadiran' => $jumlahHariKerjaTerjadwal > 0 ? round(($jumlahHariHadir / $jumlahHariKerjaTerjadwal) * 100, 2) : 0,
            ];
        }
        
        return [
            'laporanKinerjaData' => $laporanKinerjaData,
            'fakultas' => $ketua->unit,
            'departemen' => $ketua->departemen,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"]
        ];
    }

    public function laporanKinerja(Request $request)
    {
        $data = $this->getLaporanKinerjaData($request);
        return view('ketua-departemen.laporan_kinerja.index', $data);
    }

    public function cetakLaporanKinerja(Request $request)
    {
        $data = $this->getLaporanKinerjaData($request);
        $data['ketua'] = Auth::guard('karyawan')->user();
        return view('ketua-departemen.laporan_kinerja.cetak', $data);
    }
}
