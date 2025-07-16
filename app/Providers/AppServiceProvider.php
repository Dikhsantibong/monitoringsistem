<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\UserMiddleware;
use Illuminate\Pagination\Paginator;
use App\Models\WoBacklog;
use App\Observers\WoBacklogObserver;
use App\Helpers\TextFormatter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register the TextFormatter helper
        $this->app->singleton('text.formatter', function ($app) {
            return new TextFormatter();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app['router']->aliasMiddleware('admin', AdminMiddleware::class);
        $this->app['router']->aliasMiddleware('user', UserMiddleware::class);
        
        Paginator::useBootstrap();
        WoBacklog::observe(WoBacklogObserver::class);
    }
}