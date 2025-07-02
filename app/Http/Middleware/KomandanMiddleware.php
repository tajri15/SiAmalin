<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class KomandanMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan pengguna sudah login dan merupakan komandan
        if (Auth::guard('karyawan')->check() && Auth::guard('karyawan')->user()->is_komandan) {
            // Periksa juga apakah Komandan memiliki fakultas yang ditugaskan
            if (empty(Auth::guard('karyawan')->user()->unit)) {
                // Jika Komandan tidak memiliki fakultas, logout dan redirect dengan pesan error
                Auth::guard('karyawan')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('admin.login.form')->with('error', 'Akun Komandan Anda belum ditugaskan ke fakultas. Harap hubungi Administrator.');
            }
            return $next($request);
        }

        // Jika bukan komandan atau belum login, redirect ke halaman login admin
        // atau berikan pesan error yang sesuai.
        if (Auth::guard('karyawan')->check() && Auth::guard('karyawan')->user()->is_admin) {
            // Jika admin mencoba akses halaman komandan, redirect ke dashboard admin
            return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak. Anda adalah Admin, bukan Komandan.');
        }

        return redirect()->route('admin.login.form')->with('error', 'Akses ditolak. Anda bukan Komandan atau sesi Anda telah berakhir.');
    }
}
