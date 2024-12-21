<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::domain(parse_url(config('app.url'), PHP_URL_HOST))
                ->middleware('web')
                ->group(base_path('routes/web.php'));
                
            // ... route lainnya
        });
    }
} 