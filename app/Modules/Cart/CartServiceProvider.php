<?php

namespace App\Modules\Cart;

use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'Cart');

        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }

    public function register(): void
    {
        //
    }
}
