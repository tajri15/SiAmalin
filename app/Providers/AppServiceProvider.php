<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FaceRecognitionService;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(FaceRecognitionService::class, function ($app) {
            return new FaceRecognitionService();
        });
    }

    public function boot()
    {
        //
    }
}