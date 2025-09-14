<?php
/**
 * Author: TAN CHUN KEAT
 * Date: 2025-09-15
 */
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
