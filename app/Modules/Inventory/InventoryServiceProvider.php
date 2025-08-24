<?php

namespace App\Modules\Inventory;

use Illuminate\Support\ServiceProvider;

class InventoryServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__ . '/Views', 'inventory');
        
        // Load migrations (when created)
        // $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }
}
