<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Pastikan pengguna sudah login dan merupakan admin
        // Sesuaikan 'is_admin' dengan field yang Anda gunakan di model Karyawan
        if (Auth::guard('karyawan')->check() && Auth::guard('karyawan')->user()->is_admin) {
            return $next($request);
        }

        // Jika bukan admin, redirect atau berikan error
        // Anda bisa redirect ke halaman login karyawan atau halaman error khusus
        return redirect('/')->with('error', 'Akses ditolak. Anda bukan admin.');
    }
}