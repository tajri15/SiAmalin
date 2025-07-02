<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class ClearRegistrationSession
{
    public function handle($request, Closure $next)
    {
        // Hanya bersihkan session jika mengakses halaman registrasi awal (GET)
        if ($request->is('register') && $request->isMethod('GET')) {
            Log::info('Membersihkan session registrasi.');
            $request->session()->forget(['register_data', 'register_face']);
        }

        return $next($request);
    }
}