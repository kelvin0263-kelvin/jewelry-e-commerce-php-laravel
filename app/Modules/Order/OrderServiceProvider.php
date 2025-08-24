<?php

namespace App\Modules\Order;

use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load views
        $this->loadViewsFrom(__DIR__ . '/Views', 'order');
        
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }
}
