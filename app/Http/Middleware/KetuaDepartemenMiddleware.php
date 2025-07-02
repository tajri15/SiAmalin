<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class KetuaDepartemenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan pengguna sudah login dan merupakan Ketua Departemen
        if (Auth::guard('karyawan')->check() && Auth::guard('karyawan')->user()->is_ketua_departemen) {
            // Periksa juga apakah Ketua Departemen memiliki fakultas dan departemen yang ditugaskan
            if (empty(Auth::guard('karyawan')->user()->unit) || empty(Auth::guard('karyawan')->user()->departemen)) {
                // Jika tidak lengkap, logout dan redirect dengan pesan error
                Auth::guard('karyawan')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('admin.login.form')->with('error', 'Akun Ketua Departemen Anda belum lengkap. Harap hubungi Administrator.');
            }
            return $next($request);
        }

        // Jika bukan Ketua Departemen atau belum login, redirect ke halaman login panel
        if (Auth::guard('karyawan')->check()) {
            if(Auth::guard('karyawan')->user()->is_admin) {
                 return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak. Anda adalah Admin.');
            }
             if(Auth::guard('karyawan')->user()->is_komandan) {
                 return redirect()->route('komandan.dashboard')->with('error', 'Akses ditolak. Anda adalah Komandan.');
            }
        }

        return redirect()->route('admin.login.form')->with('error', 'Akses ditolak. Anda bukan Ketua Departemen atau sesi Anda telah berakhir.');
    }
}
