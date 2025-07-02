<?php

namespace App\Http\Controllers\Komandan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Karyawan;
use App\Models\JadwalShift;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class KomandanJadwalShiftController extends Controller
{
    // Diubah dari protected menjadi public agar bisa diakses oleh controller lain
    public $definedShifts = [
        'PAGI' => ['mulai' => '07:00', 'selesai' => '19:00', 'label' => 'Pagi (07:00-19:00)'],
        'MALAM' => ['mulai' => '19:00', 'selesai' => '07:00', 'label' => 'Malam (19:00-07:00)'],
        'LIBUR' => ['mulai' => null, 'selesai' => null, 'label' => 'Libur'],
    ];

    public function index(Request $request)
    {
        $komandan = Auth::guard('karyawan')->user();
        $fakultasKomandan = $komandan->unit;

        if (!$fakultasKomandan) {
            Auth::guard('karyawan')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login.form')->with('error', 'Fakultas tidak terdefinisi untuk akun Komandan Anda.');
        }

        $selectedDate = $request->input('tanggal') ? Carbon::parse($request->input('tanggal')) : Carbon::now();
        $startOfWeek = $selectedDate->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $selectedDate->copy()->endOfWeek(Carbon::SUNDAY);
        
        $period = CarbonPeriod::create($startOfWeek, $endOfWeek);
        $datesOfWeek = [];
        foreach ($period as $date) {
            $datesOfWeek[] = $date->copy();
        }

        $petugasKeamanan = Karyawan::where('unit', $fakultasKomandan)
            ->where('jabatan', 'Petugas Keamanan')
            ->where('is_admin', false)
            ->where('is_komandan', false)
            ->orderBy('nama_lengkap', 'asc')
            ->get(['nik', 'nama_lengkap']);

        $jadwalMingguan = [];
        foreach ($petugasKeamanan as $petugas) {
            $jadwalPetugas = [];
            foreach ($datesOfWeek as $tanggalCarbon) {
                $tanggalUtcQuery = new UTCDateTime($tanggalCarbon->copy()->startOfDay()->utc()->getTimestamp() * 1000);
                $shift = JadwalShift::where('karyawan_nik', $petugas->nik)
                    ->where('tanggal', $tanggalUtcQuery)
                    ->first();
                $jadwalPetugas[$tanggalCarbon->format('Y-m-d')] = $shift;
            }
            $jadwalMingguan[$petugas->nik] = [
                'nama_lengkap' => $petugas->nama_lengkap,
                'nik' => $petugas->nik,
                'shifts' => $jadwalPetugas,
            ];
        }
        
        $definedShifts = $this->definedShifts;
        $namaBulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        return view('komandan.jadwal_shift.index', compact(
            'fakultasKomandan',
            'jadwalMingguan',
            'datesOfWeek',
            'startOfWeek',
            'endOfWeek',
            'selectedDate',
            'definedShifts',
            'namaBulan'
        ));
    }

    public function storeOrUpdate(Request $request)
    {
        $komandan = Auth::guard('karyawan')->user();
        $fakultasKomandanDariAuth = $komandan->unit;

        Log::info('--- KOMANDAN JADWAL SHIFT: storeOrUpdate Attempt (Return Shift Data) ---');
        Log::info('Komandan NIK: ' . $komandan->nik . ', Fakultas Komandan (dari Auth): \'' . $fakultasKomandanDariAuth . '\'');
        Log::info('Request Data: ', $request->all());
        $nikKaryawanRequest = $request->karyawan_nik;

        $baseValidator = Validator::make($request->all(), [
            'karyawan_nik' => 'required|string|exists:karyawans,nik',
            'tanggal' => 'required|date_format:Y-m-d',
            'shift_nama' => 'required|string',
            'jam_mulai' => 'nullable|required_if:shift_nama,CUSTOM|date_format:H:i',
            'jam_selesai' => 'nullable|required_if:shift_nama,CUSTOM|date_format:H:i',
            'keterangan' => 'nullable|string|max:255',
        ]);

        if ($baseValidator->fails()) {
            Log::warning('KOMANDAN JADWAL SHIFT: Base validation failed. Errors:', $baseValidator->errors()->toArray());
            return response()->json(['success' => false, 'message' => 'Data input tidak valid.', 'errors' => $baseValidator->errors()], 422);
        }

        $targetKaryawan = Karyawan::where('nik', $nikKaryawanRequest)->first();

        if (!$targetKaryawan) {
            Log::warning('KOMANDAN JADWAL SHIFT: Manual Karyawan Check - NIK \'' . $nikKaryawanRequest . '\' NOT FOUND.');
             return response()->json(['success' => false, 'message' => 'NIK Petugas tidak ditemukan.', 'errors' => ['karyawan_nik' => ['NIK Petugas tidak valid.']]], 422);
        }
        
        $customErrorsKaryawan = [];
        if ($targetKaryawan->unit !== $fakultasKomandanDariAuth) {
            $customErrorsKaryawan[] = 'Petugas ini bukan dari fakultas Anda.';
        }
        if ($targetKaryawan->jabatan !== 'Petugas Keamanan') {
            $customErrorsKaryawan[] = 'Karyawan yang dipilih bukan berjabatan "Petugas Keamanan".';
        }
        if ($targetKaryawan->is_admin === true) {
            $customErrorsKaryawan[] = 'Karyawan yang dipilih memiliki role Admin.';
        }
        if ($targetKaryawan->is_komandan === true) {
            $customErrorsKaryawan[] = 'Karyawan yang dipilih memiliki role Komandan.';
        }

        if (!empty($customErrorsKaryawan)) {
            Log::warning('KOMANDAN JADWAL SHIFT: Manual Karyawan validation conditions not met for NIK: ' . $nikKaryawanRequest . '. Errors:', ['karyawan_nik' => $customErrorsKaryawan]);
            return response()->json(['success' => false, 'message' => 'Karyawan tidak memenuhi kriteria.', 'errors' => ['karyawan_nik' => $customErrorsKaryawan]], 422);
        }
        
        $shiftData = [
            'karyawan_nik' => $request->karyawan_nik,
            'nama_karyawan' => $targetKaryawan->nama_lengkap,
            'fakultas' => $fakultasKomandanDariAuth,
            'shift_nama' => strtoupper($request->shift_nama),
            'dibuat_oleh_nik' => $komandan->nik,
            'dibuat_oleh_nama' => $komandan->nama_lengkap,
            'keterangan' => $request->keterangan,
        ];

        if (strtoupper($request->shift_nama) === 'CUSTOM') {
            $shiftData['jam_mulai'] = $request->jam_mulai;
            $shiftData['jam_selesai'] = $request->jam_selesai;
        } elseif (isset($this->definedShifts[strtoupper($request->shift_nama)])) {
            $definedShift = $this->definedShifts[strtoupper($request->shift_nama)];
            $shiftData['jam_mulai'] = $definedShift['mulai'];
            $shiftData['jam_selesai'] = $definedShift['selesai'];
        } else {
            $shiftData['jam_mulai'] = null;
            $shiftData['jam_selesai'] = null;
            $shiftData['shift_nama'] = 'LIBUR'; 
        }
        
        if ($shiftData['shift_nama'] === 'LIBUR') {
            $shiftData['jam_mulai'] = null;
            $shiftData['jam_selesai'] = null;
        }
        $shiftData['tanggal'] = $request->tanggal;

        try {
            $jadwalShift = JadwalShift::updateOrCreate(
                [
                    'karyawan_nik' => $request->karyawan_nik,
                    'tanggal' => new UTCDateTime(Carbon::parse($request->tanggal)->startOfDay()->utc()->getTimestamp() * 1000)
                ],
                $shiftData
            );
            Log::info('KOMANDAN JADWAL SHIFT: Shift schedule successfully stored/updated for NIK: ' . $request->karyawan_nik . ' on ' . $request->tanggal);
            
            return response()->json([
                'success' => true, 
                'message' => 'Jadwal shift berhasil disimpan.',
                'shift' => [ // Data ini akan digunakan untuk update UI
                    'shift_nama' => $jadwalShift->shift_nama,
                    'jam_mulai' => $jadwalShift->jam_mulai,
                    'jam_selesai' => $jadwalShift->jam_selesai,
                    'keterangan' => $jadwalShift->keterangan,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("KOMANDAN JADWAL SHIFT: Error saving shift for NIK: " . $request->karyawan_nik . " on " . $request->tanggal . ". Error: " . $e->getMessage() . "\nStack Trace:\n" . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan jadwal shift: Terjadi kesalahan internal server.'], 500);
        }
    }
}
