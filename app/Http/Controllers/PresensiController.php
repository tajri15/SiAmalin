<?php
// File: C:\Users\dafii\OneDrive\Desktop\SiAmalin-EROR\SiAmalin\app\Http\Controllers\PresensiController.php

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
use App\Services\FaceRecognitionService; // Pastikan ini di-import
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;

class PresensiController extends Controller
{
    protected $faceRecognitionService;

    // PERUBAHAN: Tambahkan constructor untuk dependency injection
    public function __construct(FaceRecognitionService $faceRecognitionService)
    {
        $this->faceRecognitionService = $faceRecognitionService;
    }

    public function create()
    {
        $user = Auth::guard('karyawan')->user();
        $pesanJadwal = null; 

        if ($user->jabatan === 'Petugas Keamanan') {
            if (empty($user->office_location)) {
                session()->flash('error_presensi_create', 'Lokasi kantor Anda belum ditentukan. Harap hubungi Komandan atau Admin.');
            }
            $hariini = Carbon::today();
            $jadwalHariIni = JadwalShift::where('karyawan_nik', $user->nik)
                                        ->where('tanggal', new UTCDateTime($hariini->copy()->startOfDay()->timestamp * 1000))
                                        ->first();
            if (!$jadwalHariIni) {
                $pesanJadwal = "Anda tidak memiliki jadwal shift untuk hari ini. Presensi tidak dapat dilakukan.";
            } elseif (strtoupper($jadwalHariIni->shift_nama) === 'LIBUR') {
                $pesanJadwal = "Anda dijadwalkan LIBUR hari ini. Presensi tidak dapat dilakukan.";
            }
        }
        
        $hariini = Carbon::today();
        $tgl_presensi_start = new UTCDateTime($hariini->copy()->startOfDay()->timestamp * 1000);
        $tgl_presensi_end = new UTCDateTime($hariini->copy()->endOfDay()->timestamp * 1000);

        $cek = Presensi::where('nik', $user->nik)
                        ->whereBetween('tgl_presensi', [$tgl_presensi_start, $tgl_presensi_end])
                        ->first();

        return view('presensi.create', compact('cek', 'user', 'pesanJadwal'));
    }

    public function store(Request $request)
    {
        // PERUBAHAN: Validasi untuk menerima face_descriptor
        $validator = Validator::make($request->all(), [
            'lokasi' => 'required|string',
            'image' => 'required|string',
            'nik' => 'required|string|exists:karyawans,nik',
            'face_descriptor' => 'sometimes|required|array', // 'sometimes' agar tidak error jika jabatan bukan petugas
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $user = Karyawan::where('nik', $request->nik)->firstOrFail();

        if ($user->jabatan === 'Petugas Keamanan') {
            if (empty($user->face_embedding)) {
                return response()->json(['error' => 'Data wajah Anda belum terdaftar.'], 403);
            }

            // PERUBAHAN: Menggunakan service yang sudah diperbarui
            $faceVerification = $this->faceRecognitionService->verifyFaceDescriptor(
                $request->face_descriptor,
                $user->nik
            );

            if (!$faceVerification['success'] || !$faceVerification['match']) {
                $errorMessage = "Verifikasi wajah gagal. " . ($faceVerification['message'] ?? '');
                // Memberi info tambahan tentang jarak untuk debugging
                $errorMessage .= " (Jarak: " . round($faceVerification['distance'], 2) . ", Maks: " . $faceVerification['threshold'] . ")";
                return response()->json(['error' => $errorMessage], 401);
            }
            
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

        $hariini = Carbon::today();
        $jam_sekarang_str = date("H:i:s");
        
        $startOfDay = $hariini->copy()->startOfDay();
        $endOfDay = $hariini->copy()->endOfDay();
        
        Log::info("Mencari presensi untuk NIK: {$user->nik} pada tanggal: {$hariini->toDateString()}");
        
        $presensiHariIni = Presensi::where('nik', $user->nik)
            ->whereBetween('tgl_presensi', [new UTCDateTime($startOfDay->timestamp * 1000), new UTCDateTime($endOfDay->timestamp * 1000)])
            ->first();

        if ($presensiHariIni && !empty($presensiHariIni->jam_out) && $presensiHariIni->jam_out !== '00:00:00') {
             Log::warning("Presensi hari ini sudah lengkap untuk NIK: {$user->nik}");
             return response()->json(['error' => 'Anda sudah melakukan presensi masuk dan pulang hari ini.'], 400);
        }

        $isClockOut = $presensiHariIni && !empty($presensiHariIni->jam_in);
        $actionType = $isClockOut ? "out" : "in";
        Log::info("Aksi presensi terdeteksi: {$actionType} untuk NIK {$user->nik}");

        $folderPath = "uploads/absensi/";
        $fileName = "{$user->nik}-{$hariini->format('Y-m-d')}-{$actionType}.png";
        $fullPath = $folderPath . $fileName;

        $image_parts = explode(";base64,", $request->image);
        if(count($image_parts) < 2){ return response()->json(['error' => 'Format gambar tidak valid.'], 400); }
        $image_base64 = base64_decode($image_parts[1]);
        
        try {
            $success = Storage::disk('public')->put($fullPath, $image_base64);
            if (!$success) throw new \Exception("Gagal menyimpan file gambar.");
            
            $savedPath = $fullPath;

            if ($isClockOut) {
                Log::info("Melakukan update presensi pulang untuk NIK: {$user->nik}");
                $presensiHariIni->update([
                    "jam_out" => $jam_sekarang_str,
                    "foto_out" => $savedPath,
                    "lokasi_out" => $request->lokasi
                ]);
                return response()->json(['success' => 'Terimakasih, hati-hati di jalan!', 'status' => 'out', 'redirect_url' => url('/dashboard')]);
            } else {
                Log::info("Membuat data presensi masuk baru untuk NIK: {$user->nik}");
                if ($user->jabatan === 'Petugas Keamanan') {
                    $jadwalHariIni = JadwalShift::where('karyawan_nik', $user->nik)
                                                 ->where('tanggal', new UTCDateTime($startOfDay->timestamp * 1000))
                                                 ->first();
                    if (!$jadwalHariIni || strtoupper($jadwalHariIni->shift_nama) === 'LIBUR') {
                        return response()->json(['error' => 'Anda tidak memiliki jadwal shift atau sedang libur hari ini.'], 403);
                    }
                }
                
                Presensi::create([
                    'nik' => $user->nik,
                    'tgl_presensi' => new UTCDateTime($startOfDay->timestamp * 1000),
                    'jam_in' => $jam_sekarang_str,
                    'foto_in' => $savedPath,
                    'lokasi_in' => $request->lokasi
                ]);
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