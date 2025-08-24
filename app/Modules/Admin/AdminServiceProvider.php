<?php

namespace App\Modules\Admin;

use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register services
        $this->app->singleton(
            \App\Modules\Admin\Services\CustomerSegmentationService::class,
            \App\Modules\Admin\Services\CustomerSegmentationService::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load views
        $this->loadViewsFrom(__DIR__ . '/Views', 'admin');
    }
}
