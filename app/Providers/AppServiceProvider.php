<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FaceRecognitionService;
use Illuminate\Support\Facades\URL; // KOREKSI: Tambahkan ini

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(FaceRecognitionService::class, function ($app) {
            return new FaceRecognitionService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // KOREKSI UTAMA: Tambahkan blok kode ini
        // Cek jika lingkungan aplikasi adalah 'production' (sesuai file .env Anda)
        // dan paksa semua URL yang dihasilkan oleh helper asset() dan url() untuk menggunakan HTTPS.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}