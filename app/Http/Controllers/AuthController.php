<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Karyawan;
use App\Services\FaceRecognitionService; // Assuming this is still used elsewhere or for Petugas
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AuthController extends Controller
{
    protected $faceRecognitionService;

    public function __construct(FaceRecognitionService $faceRecognitionService)
    {
        $this->faceRecognitionService = $faceRecognitionService;
    }

    // Menampilkan form login KARYAWAN (Petugas Keamanan)
    public function showLoginForm()
    {
        if (Auth::guard('karyawan')->check()) {
            $user = Auth::guard('karyawan')->user();
            if ($user->is_admin) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->is_komandan) {
                return redirect()->route('komandan.dashboard');
            } elseif ($user->is_ketua_departemen) {
                return redirect()->route('ketua-departemen.dashboard');
            }
            return redirect()->route('dashboard'); // Dashboard Petugas Keamanan
        }
        return view('auth.login'); // View untuk login karyawan (Petugas)
    }

    // Proses login KARYAWAN (Petugas Keamanan)
    public function proseslogin(Request $request)
    {
        $request->validate([
            'nik' => 'required|string',
            'password' => 'required|string',
        ]);

        $karyawan = Karyawan::where('nik', $request->nik)->first();

        if ($karyawan && Hash::check($request->password, $karyawan->password)) {
            // Prevent Admin, Komandan, or Ketua Departemen from logging in via the regular employee form
            if ($karyawan->is_admin || $karyawan->is_komandan || $karyawan->is_ketua_departemen) {
                return redirect()->route('login')->withErrors([
                    'login' => 'Akun Anda harus login melalui halaman login panel.',
                ]);
            }

            Auth::guard('karyawan')->login($karyawan);
            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        }

        return redirect()->route('login')->withErrors([
            'login' => 'NIK atau password salah!',
        ]);
    }

    /**
     * Menampilkan form login ADMIN / KOMANDAN / KETUA DEPARTEMEN.
     */
    public function showAdminLoginForm(Request $request)
    {
        if (Auth::guard('karyawan')->check()) {
            $user = Auth::guard('karyawan')->user();
            if ($user->is_admin) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->is_komandan) {
                return redirect()->route('komandan.dashboard');
            } elseif ($user->is_ketua_departemen) {
                return redirect()->route('ketua-departemen.dashboard');
            }
            // If a regular Petugas Keamanan is logged in and tries to access admin/komandan login,
            // log them out first.
            Auth::guard('karyawan')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login.form')->with('info', 'Anda telah logout. Silakan login kembali.');
        }
        return view('auth.admin-login');
    }

    /**
     * Memproses login ADMIN / KOMANDAN / KETUA DEPARTEMEN.
     */
    public function prosesAdminLogin(Request $request)
    {
        $request->validate([
            'nik' => 'required|string',
            'password' => 'required|string',
        ]);

        $karyawan = Karyawan::where('nik', $request->nik)->first();

        if ($karyawan && Hash::check($request->password, $karyawan->password)) {
            if ($karyawan->is_admin) {
                Auth::guard('karyawan')->login($karyawan);
                return redirect()->route('admin.dashboard')->with('success', 'Login sebagai Admin berhasil!');
            } elseif ($karyawan->is_komandan) {
                Auth::guard('karyawan')->login($karyawan);
                return redirect()->route('komandan.dashboard')->with('success', 'Login sebagai Komandan berhasil!');
            } elseif ($karyawan->is_ketua_departemen) {
                Auth::guard('karyawan')->login($karyawan);
                return redirect()->route('ketua-departemen.dashboard')->with('success', 'Login sebagai Ketua Departemen berhasil!');
            }
             else {
                // NIK and password are correct, but the user does not have a panel role
                return redirect()->route('admin.login.form')->withErrors([
                    'login' => 'Akun ini tidak memiliki akses ke panel.',
                ]);
            }
        }

        return redirect()->route('admin.login.form')->withErrors([
            'login' => 'NIK atau password salah!',
        ]);
    }

    // Proses logout
    public function proseslogout(Request $request)
    {
        $user = Auth::guard('karyawan')->user();
        $isPanelUser = $user && ($user->is_admin || $user->is_komandan || $user->is_ketua_departemen);

        Auth::guard('karyawan')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($isPanelUser) {
            return redirect()->route('admin.login.form')->with('success', 'Anda berhasil logout.');
        }
        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }

    // Verifikasi wajah (API) - unchanged, assuming for Petugas Keamanan
    public function verifyFace(Request $request)
    {
        // Ambil data dan decode descriptor jika perlu
        $data = $request->all();
        if (isset($data['face_descriptor']) && is_string($data['face_descriptor'])) {
            $decodedDescriptor = json_decode($data['face_descriptor'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['face_descriptor'] = $decodedDescriptor;
            }
        }

        $validator = Validator::make($data, [
            'nik' => 'required|string|exists:karyawans,nik',
            'face_descriptor' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Input tidak valid',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Panggil service dengan descriptor
            $verificationResult = $this->faceRecognitionService->verifyFaceDescriptor(
                $data['face_descriptor'],
                $data['nik']
            );
            
            // Kembalikan hasil dari service
            return response()->json($verificationResult);

        } catch (\Exception $e) {
            Log::error('Face verification API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'match' => false,
                'message' => 'Terjadi kesalahan internal saat verifikasi wajah.'
            ], 500);
        }
    }

    /**
     * Menyimpan status verifikasi wajah ke session.
     */
    public function setFaceVerifiedSession(Request $request)
    {
        $request->validate([
            'verified' => 'required|boolean',
            'timestamp' => 'required|numeric'
        ]);

        Session::put('face_verified_status', $request->verified);
        Session::put('face_verified_timestamp', $request->timestamp);

        return response()->json(['success' => true, 'message' => 'Status verifikasi wajah disimpan.']);
    }

    /**
     * Memeriksa status verifikasi wajah dari session.
     */
    public function checkFaceVerifiedSession(Request $request)
    {
        $verified = Session::get('face_verified_status', false);
        $timestamp = Session::get('face_verified_timestamp', 0);

        // Tentukan batas waktu validitas verifikasi (misalnya 5 menit)
        $validityDuration = 5 * 60 * 1000; // 5 menit dalam milidetik

        if ($verified && (microtime(true) * 1000 - $timestamp) < $validityDuration) {
            return response()->json(['verified' => true]);
        }

        // Jika tidak valid atau sudah kadaluarsa, hapus session
        Session::forget(['face_verified_status', 'face_verified_timestamp']);
        return response()->json(['verified' => false, 'message' => 'Verifikasi wajah tidak valid atau sudah kadaluarsa. Silakan verifikasi ulang.'], 401);
    }
}
