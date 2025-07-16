<?php
// File: app/Http/Controllers/PresensiController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Presensi;
use App\Models\Karyawan;
use App\Models\JadwalShift;
use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Helpers\FaceRecognitionHelper;
use App\Services\FaceRecognitionService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;

class PresensiController extends Controller
{
    protected $faceRecognitionService;

    public function __construct(FaceRecognitionService $faceRecognitionService)
    {
        $this->faceRecognitionService = $faceRecognitionService;
    }

    private function getJadwalAktif($nik)
    {
        $waktuSekarang = Carbon::now();
        
        // Cek jadwal kemarin untuk shift malam yang masih berlanjut
        $jadwalKemarin = JadwalShift::where('karyawan_nik', $nik)
                                    ->where('tanggal', new UTCDateTime($waktuSekarang->copy()->subDay()->startOfDay()->timestamp * 1000))
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
                                    ->where('tanggal', new UTCDateTime($waktuSekarang->copy()->startOfDay()->timestamp * 1000))
                                    ->first();
        
        return $jadwalHariIni;
    }

    public function create()
    {
        $user = Auth::guard('karyawan')->user();
        $pesanJadwal = null; 
        $faceDescriptor = null;
        $waktuSekarang = Carbon::now();

        if ($user && !empty($user->face_embedding['embedding'])) {
            $faceDescriptor = json_encode($user->face_embedding['embedding']);
        }

        // PERBAIKAN: Cari presensi aktif dengan rentang waktu yang lebih luas (48 jam)
        // untuk menangani shift malam yang bisa berlangsung lintas hari
        $presensiAktif = Presensi::where('nik', $user->nik)
                        ->whereNotNull('jam_in')
                        ->whereNull('jam_out')
                        ->where('tgl_presensi', '>=', new UTCDateTime($waktuSekarang->copy()->subHours(48)->timestamp * 1000))
                        ->orderBy('tgl_presensi', 'desc')
                        ->first();
        
        // Jika tidak ada presensi aktif, cek apakah ada jadwal yang sudah selesai hari ini
        if (!$presensiAktif) {
            $jadwalHariIni = $this->getJadwalAktif($user->nik);
            if ($jadwalHariIni) {
                $tanggalJadwalUTC = new UTCDateTime($jadwalHariIni->tanggal->copy()->startOfDay()->timestamp * 1000);
                $presensiSelesaiHariIni = Presensi::where('nik', $user->nik)
                    ->where('tgl_presensi', $tanggalJadwalUTC)
                    ->whereNotNull('jam_out')
                    ->first();
                
                if ($presensiSelesaiHariIni) {
                    $presensiAktif = $presensiSelesaiHariIni;
                }
            }
        }

        if ($user->jabatan === 'Petugas Keamanan') {
            if (empty($user->office_location)) {
                session()->flash('error_presensi_create', 'Lokasi kantor Anda belum ditentukan. Harap hubungi Komandan atau Admin.');
            }
            
            if (!$presensiAktif || !$presensiAktif->jam_out) {
                // PERBAIKAN: Tentukan jadwal yang tepat untuk validasi
                if ($presensiAktif) {
                    // Jika ada presensi aktif, gunakan jadwal dari tanggal presensi tersebut
                    $jadwalUntukValidasi = JadwalShift::where('karyawan_nik', $user->nik)
                        ->where('tanggal', $presensiAktif->tgl_presensi)
                        ->first();
                } else {
                    // Jika tidak ada presensi aktif, cari jadwal yang sedang berlaku
                    $jadwalUntukValidasi = $this->getJadwalAktif($user->nik);
                }

                if (!$jadwalUntukValidasi) {
                    if (!$presensiAktif) {
                        $pesanJadwal = "Anda tidak memiliki jadwal shift aktif saat ini. Presensi tidak dapat dilakukan.";
                    }
                } elseif (strtoupper($jadwalUntukValidasi->shift_nama) === 'LIBUR') {
                    if (!$presensiAktif) {
                        $pesanJadwal = "Anda dijadwalkan LIBUR hari ini. Presensi tidak dapat dilakukan.";
                    }
                } else {
                    $tanggalJadwal = Carbon::parse($jadwalUntukValidasi->tanggal);
                    $jamMulai = Carbon::parse($tanggalJadwal->format('Y-m-d') . ' ' . $jadwalUntukValidasi->jam_mulai);
                    $jamSelesai = Carbon::parse($tanggalJadwal->format('Y-m-d') . ' ' . $jadwalUntukValidasi->jam_selesai);

                    // Handle shift malam (jam selesai < jam mulai)
                    if ($jamSelesai->lt($jamMulai)) {
                        $jamSelesai->addDay();
                    }

                    if ($presensiAktif) {
                        // Jika sudah ada presensi aktif, berarti ini untuk absen pulang
                        if ($waktuSekarang->lt($jamSelesai)) {
                            $pesanJadwal = "Anda baru dapat melakukan absen pulang setelah shift berakhir pada pukul " . $jamSelesai->format('H:i') . ".";
                        }
                    } else { 
                        // Jika belum ada presensi aktif, ini untuk absen masuk
                        // PERBAIKAN: Untuk shift malam, izinkan absen masuk dari jam mulai hingga jam selesai (lintas hari)
                        $sekarangDalamShift = $waktuSekarang->between($jamMulai, $jamSelesai);
                        
                        if (!$sekarangDalamShift) {
                            $pesanJadwal = "Absen masuk hanya bisa dilakukan selama jam shift Anda ({$jamMulai->format('H:i')} - {$jamSelesai->format('H:i')}).";
                        }
                    }
                }
            } else {
                $pesanJadwal = "Anda sudah menyelesaikan presensi untuk shift ini.";
            }
        }

        return view('presensi.create', compact('presensiAktif', 'user', 'pesanJadwal', 'faceDescriptor'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lokasi' => 'required|string',
            'image' => 'required|string',
            'nik' => 'required|string|exists:karyawans,nik',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $user = Karyawan::where('nik', $request->nik)->firstOrFail();
        $waktuSekarang = Carbon::now();
        
        // PERBAIKAN: Cari presensi aktif dengan rentang waktu yang lebih luas (48 jam)
        $presensiAktif = Presensi::where('nik', $user->nik)
            ->whereNotNull('jam_in')
            ->whereNull('jam_out')
            ->where('tgl_presensi', '>=', new UTCDateTime($waktuSekarang->copy()->subHours(48)->timestamp * 1000))
            ->orderBy('tgl_presensi', 'desc')
            ->first();
        
        $isClockOutAction = !is_null($presensiAktif);
        
        // PERBAIKAN: Tentukan jadwal yang tepat untuk validasi
        if ($isClockOutAction) {
            // Jika ada presensi aktif, gunakan jadwal dari tanggal presensi tersebut
            $jadwalUntukValidasi = JadwalShift::where('karyawan_nik', $user->nik)
                ->where('tanggal', $presensiAktif->tgl_presensi)
                ->first();
        } else {
            // Jika tidak ada presensi aktif, cari jadwal yang sedang berlaku
            $jadwalUntukValidasi = $this->getJadwalAktif($user->nik);
        }

        if ($user->jabatan === 'Petugas Keamanan') {
            if (!$jadwalUntukValidasi) {
                return response()->json(['error' => 'Presensi ditolak. Jadwal shift Anda tidak ditemukan.'], 403);
            }
            if (strtoupper($jadwalUntukValidasi->shift_nama) === 'LIBUR') {
                return response()->json(['error' => 'Presensi ditolak. Anda dijadwalkan LIBUR.'], 403);
            }

            $tanggalJadwal = Carbon::parse($jadwalUntukValidasi->tanggal);
            $jamMulai = Carbon::parse($tanggalJadwal->format('Y-m-d') . ' ' . $jadwalUntukValidasi->jam_mulai);
            $jamSelesai = Carbon::parse($tanggalJadwal->format('Y-m-d') . ' ' . $jadwalUntukValidasi->jam_selesai);

            // Handle shift malam
            if ($jamSelesai->lt($jamMulai)) {
                $jamSelesai->addDay();
            }

            if ($isClockOutAction) {
                // Validasi waktu absen pulang - harus setelah jam selesai shift
                if ($waktuSekarang->lt($jamSelesai)) {
                    return response()->json(['error' => "Absen pulang hanya bisa dilakukan setelah shift berakhir pada pukul {$jamSelesai->format('H:i')}."], 403);
                }
            } else {
                // Validasi waktu absen masuk - harus dalam rentang shift
                $sekarangDalamShift = $waktuSekarang->between($jamMulai, $jamSelesai);
                
                if (!$sekarangDalamShift) {
                    return response()->json(['error' => "Absen masuk hanya bisa dilakukan selama jam shift Anda ({$jamMulai->format('H:i')} - {$jamSelesai->format('H:i')})."], 403);
                }
            }
        }

        if ($user->jabatan === 'Petugas Keamanan') {
            if (empty($user->office_location)) {
                return response()->json(['error' => 'Lokasi kantor Anda belum ditentukan.'], 400);
            }
            $lokasiuser = explode(",", $request->lokasi);
            if (count($lokasiuser) < 2 || !is_numeric($lokasiuser[0]) || !is_numeric($lokasiuser[1])) {
                return response()->json(['error' => 'Format lokasi tidak valid.'], 400);
            }
            $jarakResult = FaceRecognitionHelper::isWithinOfficeRadius(floatval($lokasiuser[0]), floatval($lokasiuser[1]), $user);
            if (!$jarakResult['within']) {
                return response()->json(['error' => "Anda berada di luar radius kantor. Jarak: " . round($jarakResult['distance']) . "m."], 400);
            }
        }
        
        $jam_sekarang_str = $waktuSekarang->format("H:i:s");
        
        if ($isClockOutAction && !empty($presensiAktif->jam_out) && $presensiAktif->jam_out !== '00:00:00') {
             return response()->json(['error' => 'Anda sudah melakukan presensi masuk dan pulang untuk shift ini.'], 400);
        }

        $tanggalFile = $isClockOutAction ? Carbon::parse($presensiAktif->tgl_presensi)->format('Y-m-d') : Carbon::parse($jadwalUntukValidasi->tanggal)->format('Y-m-d');
        $actionType = $isClockOutAction ? "out" : "in";
        $fileName = "{$user->nik}-{$tanggalFile}-{$actionType}.png";
        $folderPath = "uploads/absensi/";
        $fullPath = $folderPath . $fileName;

        $image_parts = explode(";base64,", $request->image);
        if(count($image_parts) < 2){ return response()->json(['error' => 'Format gambar tidak valid.'], 400); }
        $image_base64 = base64_decode($image_parts[1]);
        
        try {
            Storage::disk('public')->put($fullPath, $image_base64);
            $savedPath = $fullPath;

            if ($isClockOutAction) {
                $presensiAktif->update([
                    "jam_out" => $jam_sekarang_str,
                    "foto_out" => $savedPath,
                    "lokasi_out" => $request->lokasi
                ]);
                return response()->json(['success' => 'Terimakasih, hati-hati di jalan!', 'status' => 'out', 'redirect_url' => url('/dashboard')]);
            } else {
                Presensi::updateOrCreate(
                    [
                        'nik' => $user->nik,
                        'tgl_presensi' => new UTCDateTime(Carbon::parse($jadwalUntukValidasi->tanggal)->startOfDay()->timestamp * 1000)
                    ],
                    [
                        'jam_in' => $jam_sekarang_str,
                        'foto_in' => $savedPath,
                        'lokasi_in' => $request->lokasi,
                        'jam_out' => null,
                        'foto_out' => null,
                        'lokasi_out' => null
                    ]
                );
                return response()->json(['success' => 'Terimakasih, selamat bekerja!', 'status' => 'in', 'redirect_url' => url('/dashboard')]);
            }
        } catch (\Exception $e) {
            Log::error('Kesalahan saat menyimpan presensi: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Gagal menyimpan presensi.'], 500);
        }
    }
    
    public function updateprofile(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $karyawan = Karyawan::where('nik', $nik)->firstOrFail();
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'no_hp' => 'required|string|max:15',
            'email' => 'nullable|email|max:255|unique:karyawans,email,' . $karyawan->_id . ',_id',
            'password' => 'nullable|string|min:6|confirmed',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        if ($validator->fails()) { return Redirect::back()->withErrors($validator)->withInput(); }
        $dataToUpdate = ['nama_lengkap' => $request->nama_lengkap, 'no_hp' => $request->no_hp, 'email' => $request->email];
        if ($request->filled('password')) { $dataToUpdate['password'] = Hash::make($request->password); }
        if ($request->hasFile('foto')) {
            if ($karyawan->foto) { Storage::disk('public')->delete($karyawan->foto); }
            $dataToUpdate['foto'] = $request->file('foto')->store('uploads/karyawan', 'public');
        }
        $karyawan->update($dataToUpdate);
        return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
    }

    public function histori()
    {
        $namabulan = ["","Januari","Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presensi.histori', compact('namabulan'));
    }

    public function gethistori(Request $request)
    {
        $bulan = (int) $request->bulan;
        $tahun = (int) $request->tahun;
        $nik = Auth::guard('karyawan')->user()->nik;
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        $histori = Presensi::where('nik', $nik)
            ->whereBetween('tgl_presensi', [new UTCDateTime($startDate->timestamp * 1000), new UTCDateTime($endDate->timestamp * 1000)])
            ->orderBy('tgl_presensi', 'asc')
            ->get();
        return view('presensi.gethistori', compact('histori'));
    }
}