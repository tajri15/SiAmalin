<?php
// File: app/Http/Controllers/Komandan/KomandanDashboardController.php

namespace App\Http\Controllers\Komandan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\Laporan;
use App\Models\Patrol;
use App\Models\PatrolPoint;
use MongoDB\BSON\UTCDateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class KomandanDashboardController extends Controller
{
    private function getScopedKaryawanQuery()
    {
        $komandan = Auth::guard('karyawan')->user();
        return Karyawan::where('unit', $komandan->unit)
                        ->where('jabatan', 'Petugas Keamanan')
                        ->where('is_admin', false)
                        ->where('is_komandan', false);
    }

    public function index()
    {
        $komandan = Auth::guard('karyawan')->user();
        $fakultasKomandan = $komandan->unit;

        if (!$fakultasKomandan) {
            Auth::guard('karyawan')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('admin.login.form')->with('error', 'Fakultas tidak terdefinisi untuk akun Komandan Anda.');
        }

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

        return view('komandan.dashboard', compact(
            'fakultasKomandan', 'jumlahPetugas', 'hadirHariIni',
            'laporanBelumDitinjau', 'totalLaporanBulanIni', 'totalPatroliBulanIni', 'rekapPresensiBulanan'
        ));
    }
    
    public function dataKaryawan(Request $request)
    {
        $komandan = Auth::guard('karyawan')->user();
        $fakultasKomandan = $komandan->unit;

        $query = $this->getScopedKaryawanQuery();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_lengkap', 'regexp', "/.*$searchTerm.*/i")
                  ->orWhere('nik', 'like', '%' . $searchTerm . '%');
            });
        }
        $karyawans = $query->orderBy('nama_lengkap')->paginate(10);
        return view('komandan.karyawan.index', compact('karyawans', 'fakultasKomandan'));
    }

    public function rekapPresensi(Request $request)
    {
        $komandan = Auth::guard('karyawan')->user();
        $fakultasKomandan = $komandan->unit;

        $bulanIni = $request->input('bulan', date('m'));
        $tahunIni = $request->input('tahun', date('Y'));
        $searchNik = $request->input('nik');
        $searchNama = $request->input('nama');

        $karyawanQuery = $this->getScopedKaryawanQuery();

        if ($searchNik) {
            $karyawanQuery->where('nik', 'like', '%' . $searchNik . '%');
        }
        if ($searchNama) {
            $karyawanQuery->where('nama_lengkap', 'regexp', "/.*$searchNama.*/i");
        }

        $petugasNiksToSearch = $karyawanQuery->pluck('nik')->toArray();
        
        $query = Presensi::query()->with('karyawan');

        if (empty($petugasNiksToSearch) && ($searchNik || $searchNama)) {
            $query->whereRaw(fn($q) => $q->where('_id', '=', '0'));
        } else {
            $query->whereIn('nik', $petugasNiksToSearch);
        }

        $startDate = Carbon::createFromDate($tahunIni, $bulanIni, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahunIni, $bulanIni, 1)->endOfMonth();
        $query->whereBetween('tgl_presensi', [new UTCDateTime($startDate->timestamp * 1000), new UTCDateTime($endDate->timestamp * 1000)]);
        
        $presensiData = $query->orderBy('tgl_presensi', 'desc')->paginate(15);
        
        $namaBulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        return view('komandan.presensi.rekapitulasi', compact('presensiData', 'bulanIni', 'tahunIni', 'namaBulan', 'searchNik', 'searchNama', 'fakultasKomandan'));
    }

    public function laporanHarianPresensi(Request $request)
    {
        $komandan = Auth::guard('karyawan')->user();
        $fakultasKomandan = $komandan->unit;
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        
        $petugasNiksInFakultas = $this->getScopedKaryawanQuery()->pluck('nik')->toArray();
        
        $query = Presensi::query()->with('karyawan');
        
        if (empty($petugasNiksInFakultas)) {
            $query->where('nik', 'mustahil-ditemukan');
        } else {
            $tglStart = new UTCDateTime(Carbon::parse($tanggal)->startOfDay()->timestamp * 1000);
            $tglEnd = new UTCDateTime(Carbon::parse($tanggal)->endOfDay()->timestamp * 1000);
            $query->whereIn('nik', $petugasNiksInFakultas)
                  ->whereBetween('tgl_presensi', [$tglStart, $tglEnd]);
        }
        
        $presensiHarian = $query->orderBy('jam_in', 'asc')->get();

        return view('komandan.presensi.harian', compact('presensiHarian', 'tanggal', 'fakultasKomandan'));
    }

    public function detailPresensiKaryawan(Request $request, $nik)
    {
        $komandan = Auth::guard('karyawan')->user();
        $fakultasKomandan = $komandan->unit;
        $karyawan = $this->getScopedKaryawanQuery()->where('nik', $nik)->firstOrFail();
        
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        $historiPresensi = Presensi::where('nik', $nik)
            ->whereBetween('tgl_presensi', [new UTCDateTime($startDate->timestamp * 1000), new UTCDateTime($endDate->timestamp * 1000)])
            ->orderBy('tgl_presensi', 'asc')->get();
        
        $namaBulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('komandan.presensi.detail_karyawan', compact('karyawan', 'historiPresensi', 'bulan', 'tahun', 'namaBulan', 'fakultasKomandan'));
    }

    public function editPresensi($id)
    {
        $komandan = Auth::guard('karyawan')->user();
        $fakultasKomandan = $komandan->unit;

        $presensi = Presensi::where('_id', $id)
            ->whereHas('karyawan', fn($q) => $q->where('unit', $fakultasKomandan)->where('jabatan', 'Petugas Keamanan'))
            ->with('karyawan')->firstOrFail();
                                
        return view('komandan.presensi.edit', compact('presensi', 'fakultasKomandan'));
    }

    public function updatePresensi(Request $request, $id)
    {
        $komandan = Auth::guard('karyawan')->user();
        $fakultasKomandan = $komandan->unit;

        $presensi = Presensi::where('_id', $id)
            ->whereHas('karyawan', fn($q) => $q->where('unit', $fakultasKomandan)->where('jabatan', 'Petugas Keamanan'))
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'tgl_presensi_edit' => 'required|date',
            'jam_in_edit' => 'nullable|date_format:H:i:s',
            'jam_out_edit' => 'nullable|date_format:H:i:s',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $presensi->update([
                'tgl_presensi' => new UTCDateTime(Carbon::parse($request->tgl_presensi_edit)->startOfDay()->utc()->getTimestamp() * 1000),
                'jam_in' => $request->jam_in_edit ?: null,
                'jam_out' => $request->jam_out_edit ?: null,
            ]);
            return redirect()->route('komandan.presensi.rekapitulasi')->with('success', 'Data presensi berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data presensi: ' . $e->getMessage())->withInput();
        }
    }

    public function laporanKaryawan(Request $request)
    {
        $komandan = Auth::guard('karyawan')->user();
        $fakultasKomandan = $komandan->unit;

        $karyawanQuery = $this->getScopedKaryawanQuery();

        if ($request->filled('nik_karyawan')) {
            $karyawanQuery->where('nik', 'like', '%' . $request->nik_karyawan . '%');
        }
        if ($request->filled('nama_karyawan')) {
            $karyawanQuery->where('nama_lengkap', 'regexp', "/.*{$request->nama_karyawan}.*/i");
        }

        $petugasNiksToSearch = $karyawanQuery->pluck('nik')->toArray();
        
        $query = Laporan::with('karyawan');

        if (empty($petugasNiksToSearch) && ($request->filled('nik_karyawan') || $request->filled('nama_karyawan'))) {
            $query->whereRaw(fn($q) => $q->where('_id', '=', '0'));
        } else {
            $query->whereIn('nik', $petugasNiksToSearch);
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $tanggalMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
            $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->endOfDay();
            $query->whereBetween('created_at', [ new UTCDateTime($tanggalMulai->utc()->getTimestamp() * 1000), new UTCDateTime($tanggalAkhir->utc()->getTimestamp() * 1000)]);
        } elseif ($request->filled('tanggal_mulai')) {
            $tanggalMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
            $query->where('created_at', '>=', new UTCDateTime($tanggalMulai->utc()->getTimestamp() * 1000));
        } elseif ($request->filled('tanggal_akhir')) {
            $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->endOfDay();
            $query->where('created_at', '<=', new UTCDateTime($tanggalAkhir->utc()->getTimestamp() * 1000));
        }

        if ($request->filled('jenis_laporan')) $query->where('jenis_laporan', $request->jenis_laporan);
        if ($request->filled('status_laporan')) { 
            if ($request->status_laporan == 'belum_ditinjau') $query->whereNull('status_admin');
            else $query->where('status_admin', $request->status_laporan);
        }
        
        $laporans = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $petugasFakultas = $this->getScopedKaryawanQuery()->orderBy('nama_lengkap')->get(['nik', 'nama_lengkap']);

        return view('komandan.laporan.index', compact('laporans', 'petugasFakultas', 'fakultasKomandan'));
    }

    public function showLaporanKaryawan($id)
    {
        $komandan = Auth::guard('karyawan')->user();
        $fakultasKomandan = $komandan->unit;
        $laporan = Laporan::with('karyawan')->find($id);

        if (!$laporan || !$laporan->karyawan || $laporan->karyawan->unit !== $fakultasKomandan) {
            return redirect()->route('komandan.laporan.index')->with('error', 'Anda tidak berhak mengakses detail laporan ini atau laporan tidak valid.');
        }
        
        return view('komandan.laporan.show', compact('laporan', 'fakultasKomandan'));
    }

    public function updateStatusLaporan(Request $request, $id)
    {
        $komandan = Auth::guard('karyawan')->user();
        $fakultasKomandan = $komandan->unit;
        $laporan = Laporan::with('karyawan')->find($id);

        if (!$laporan || !$laporan->karyawan || $laporan->karyawan->unit !== $fakultasKomandan) {
            return redirect()->route('komandan.laporan.show', $id)->with('error', 'Anda tidak berhak memperbarui status laporan ini.');
        }
        
        $request->validate(['status_admin' => 'required|string|in:Diterima,Ditolak', 'catatan_admin' => 'nullable|string|max:1000']);
        
        $laporan->update([
            'status_admin' => $request->status_admin,
            'catatan_admin' => $request->catatan_admin,
            'admin_peninjau_id' => $komandan->nik,
            'tanggal_peninjauan_admin' => new UTCDateTime(now()->timestamp * 1000)
        ]);

        return redirect()->route('komandan.laporan.show', $id)->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function patroliKaryawan(Request $request)
    {
        $komandan = Auth::guard('karyawan')->user();
        $fakultasKomandan = $komandan->unit;

        $karyawanQuery = $this->getScopedKaryawanQuery();

        if ($request->filled('nik_karyawan')) {
            $karyawanQuery->where('nik', 'like', '%' . $request->nik_karyawan . '%');
        }
        if ($request->filled('nama_karyawan')) {
            $karyawanQuery->where('nama_lengkap', 'regexp', "/.*{$request->nama_karyawan}.*/i");
        }

        $petugasNiksToSearch = $karyawanQuery->pluck('nik')->toArray();
        
        $query = Patrol::with('karyawan')->where('status', 'selesai'); 

        if (empty($petugasNiksToSearch) && ($request->filled('nik_karyawan') || $request->filled('nama_karyawan'))) {
            $query->whereRaw(fn($q) => $q->where('_id', '=', '0'));
        } else {
            $query->whereIn('karyawan_nik', $petugasNiksToSearch);
        }

        if ($request->filled('tanggal_mulai')) $query->where('start_time', '>=', new UTCDateTime(Carbon::parse($request->tanggal_mulai)->startOfDay()->timestamp * 1000));
        if ($request->filled('tanggal_akhir')) $query->where('start_time', '<=', new UTCDateTime(Carbon::parse($request->tanggal_akhir)->endOfDay()->timestamp * 1000));

        $patrols = $query->orderBy('start_time', 'desc')->paginate(15);
        
        $petugasFakultas = $this->getScopedKaryawanQuery()->orderBy('nama_lengkap')->get(['nik', 'nama_lengkap']);

        return view('komandan.patroli.index', compact('patrols', 'petugasFakultas', 'fakultasKomandan'));
    }

    /**
     * Display the specified patrol record for Komandan.
     * MODIFIED: Eager load karyawan and pass office location/radius to view.
     */
    public function showPatroliKaryawan($patrolId)
    {
        try {
            $komandan = Auth::guard('karyawan')->user();
            $fakultasKomandan = $komandan->unit;
            
            // Debug log
            Log::info('Komandan accessing patrol detail', [
                'patrol_id' => $patrolId,
                'komandan_nik' => $komandan->nik,
                'fakultas' => $fakultasKomandan
            ]);
            
            // Cari patrol dengan eager loading
            $patrol = Patrol::with('karyawan')->find($patrolId);
            
            if (!$patrol) {
                Log::warning('Patrol not found', ['patrol_id' => $patrolId]);
                return redirect()->route('komandan.patroli.index')->with('error', 'Data patroli tidak ditemukan.');
            }
            
            if (!$patrol->karyawan) {
                Log::warning('Patrol karyawan not found', ['patrol_id' => $patrolId]);
                return redirect()->route('komandan.patroli.index')->with('error', 'Data karyawan patroli tidak ditemukan.');
            }
            
            if ($patrol->karyawan->unit !== $fakultasKomandan) {
                Log::warning('Komandan accessing patrol outside faculty', [
                    'patrol_id' => $patrolId,
                    'patrol_faculty' => $patrol->karyawan->unit,
                    'komandan_faculty' => $fakultasKomandan
                ]);
                return redirect()->route('komandan.patroli.index')->with('error', 'Anda tidak berhak mengakses detail patroli ini.');
            }
            
            // Proses path untuk map
            $pathForMap = collect($patrol->path)->map(function($point) {
                if (is_array($point) && count($point) >= 2 && is_numeric($point[0]) && is_numeric($point[1])) {
                    return [$point[1], $point[0]]; // [lat, lng]
                }
                return null;
            })->filter()->toArray();
            
            // Ambil data lokasi dan radius dari relasi karyawan
            $officeLocation = $patrol->karyawan->office_location ?? null;
            $officeRadius = $patrol->karyawan->office_radius ?? null;
            
            Log::info('Patrol detail loaded successfully', [
                'patrol_id' => $patrolId,
                'has_path' => !empty($pathForMap),
                'has_office_location' => !is_null($officeLocation)
            ]);
            
            return view('komandan.patroli.show', compact(
                'patrol', 
                'pathForMap', 
                'fakultasKomandan', 
                'officeLocation', 
                'officeRadius'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error in showPatroliKaryawan', [
                'patrol_id' => $patrolId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('komandan.patroli.index')->with('error', 'Terjadi kesalahan saat memuat detail patroli.');
        }
    }

    public function monitoring()
    {
        return view('admin.patroli.monitoring');
    }

    public function getLivePatrolData()
    {
        $komandan = Auth::guard('karyawan')->user();
        $fakultasKomandan = $komandan->unit;
        $petugasNiks = $this->getScopedKaryawanQuery()->pluck('nik')->toArray();

        $activePatrols = Patrol::with('karyawan:nik,nama_lengkap,foto')
            ->whereIn('status', ['aktif', 'jeda'])
            ->whereIn('karyawan_nik', $petugasNiks)
            ->get(['_id', 'karyawan_nik', 'start_time', 'status']);

        $liveData = [];
        foreach ($activePatrols as $patrol) {
            $lastPoint = PatrolPoint::where('patrol_id', $patrol->_id)->orderBy('timestamp', 'desc')->first();
            if ($lastPoint && $patrol->karyawan) {
                $liveData[] = [
                    'patrol_id' => $patrol->_id,
                    'nik' => $patrol->karyawan_nik,
                    'nama_lengkap' => $patrol->karyawan->nama_lengkap,
                    'foto_url' => $patrol->karyawan->foto ? asset('storage/' . $patrol->karyawan->foto) : asset('assets/img/sample/avatar/avatar1.jpg'),
                    'latitude' => $lastPoint->latitude,
                    'longitude' => $lastPoint->longitude,
                    'status' => $patrol->status,
                    'last_update' => Carbon::parse($lastPoint->timestamp)->diffForHumans(),
                    'start_time' => Carbon::parse($patrol->start_time)->format('H:i:s'),
                ];
            }
        }
        return response()->json($liveData);
    }
}