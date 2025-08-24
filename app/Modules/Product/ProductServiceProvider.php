<?php

namespace App\Modules\Product;

use Illuminate\Support\ServiceProvider;

class ProductServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__ . '/Views', 'product');
        
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }
}
