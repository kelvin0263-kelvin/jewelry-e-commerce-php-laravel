<?php

namespace App\Modules\Support;

use Illuminate\Support\ServiceProvider;

class SupportServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register services
        $this->app->singleton(
            \App\Modules\Support\Services\ChatQueueService::class,
            \App\Modules\Support\Services\ChatQueueService::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load views
        $this->loadViewsFrom(__DIR__ . '/Views', 'support');
        
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }
}