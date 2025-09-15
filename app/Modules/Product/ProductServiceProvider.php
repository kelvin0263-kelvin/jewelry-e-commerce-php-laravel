<?php
/**
 * Author: SIA XIAO HUI
 * Date: 2025-09-15
 */

namespace App\Modules\Product;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Modules\Product\Middleware\SecureErrorHandling;
use App\Modules\Product\Middleware\DatabaseSecurity;

class ProductServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        
        $this->mergeConfigFrom(__DIR__ . '/Config/security.php', 'product.security');
    }

    public function boot(): void
    {
        
        $this->loadViewsFrom(__DIR__ . '/Views', 'product');
        
        
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
        
        
        $this->registerMiddleware();
    }

    protected function registerMiddleware(): void
    {
        
        $this->app['router']->aliasMiddleware('product.secure_error_handling', SecureErrorHandling::class);
        $this->app['router']->aliasMiddleware('product.database_security', DatabaseSecurity::class);
    }
}
