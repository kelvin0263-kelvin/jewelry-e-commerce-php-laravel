<?php

namespace App\Modules\Product;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Modules\Product\Middleware\SecureErrorHandling;
use App\Modules\Product\Middleware\DatabaseSecurity;

class ProductServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register security configuration
        $this->mergeConfigFrom(__DIR__ . '/Config/security.php', 'product.security');
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
        
        // Register middleware
        $this->registerMiddleware();
    }

    /**
     * Register Product Module middleware.
     */
    protected function registerMiddleware(): void
    {
        // Register as global middleware aliases
        $this->app['router']->aliasMiddleware('product.secure_error_handling', SecureErrorHandling::class);
        $this->app['router']->aliasMiddleware('product.database_security', DatabaseSecurity::class);
    }
}
