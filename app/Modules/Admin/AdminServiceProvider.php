<?php

namespace App\Modules\Admin;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
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


        Route::prefix('api')
         ->middleware('api')
         ->group(__DIR__.'/Routes/api.php');
    }


}
