<?php

namespace App\Modules\Cart;

use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Load views from the Views folder
        $this->loadViewsFrom(__DIR__ . '/Views', 'cart');

        // Load migrations (if you add migrations in this module later)
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }
}
