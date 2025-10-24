<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ Force Laravel à générer toutes les URLs en HTTPS en production
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
    }
}
