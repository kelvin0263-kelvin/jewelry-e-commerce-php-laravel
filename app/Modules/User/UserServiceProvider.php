<?php

namespace App\Modules\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
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
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/auth.php');
        // Register routes
        Route::prefix('api')
        ->middleware('api')
        ->group(__DIR__.'/Routes/api.php');
                
        // Load views
        $this->loadViewsFrom(__DIR__ . '/Views', 'user');
        
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }
}
