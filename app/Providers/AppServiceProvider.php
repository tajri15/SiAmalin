<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FaceRecognitionService;
use Illuminate\Support\Facades\URL; // Diperlukan untuk forceScheme
use Illuminate\Pagination\Paginator; // Diperlukan untuk paginasi

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
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
        // Pengaturan dari kode Anda untuk memaksa HTTPS di production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // PERBAIKAN: Menambahkan baris ini untuk mengatur gaya paginasi
        // agar sesuai dengan Bootstrap 5 yang digunakan di panel admin Anda.
        Paginator::useBootstrapFive();
    }
}
