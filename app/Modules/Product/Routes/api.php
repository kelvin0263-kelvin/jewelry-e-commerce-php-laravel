<?php
/**
 * Author: SIA XIAO HUI
 * Date: 2025-09-15
 */

use Illuminate\Support\Facades\Route;
use App\Modules\Product\Controllers\ProductApiController;
use App\Modules\Product\Controllers\ProductController;

// Public AJAX endpoints served by web ProductController
Route::get('/products/sku-details', [ProductController::class, 'getSkuDetails'])->name('products.sku-details');
Route::get('/products/check-published/{inventoryId}', [ProductController::class, 'checkPublished'])->name('products.check-published');

// Public product routes (no authentication required)
Route::prefix('products')
    ->name('api.products.')
    ->controller(ProductApiController::class)
    ->group(function () {
        // Public product listing (only published products)
        Route::get('/', 'index')->name('index');
        
        // Public product search
        Route::get('/search', 'search')->name('search');
        
        // Public product details (only published products)
        Route::get('/{product}', 'show')->name('show');
        
        // Public product statistics
        Route::get('/stats/overview', 'getStats')->name('stats');
        
        // Get products by inventory
        Route::get('/inventory/{inventoryId}', 'getByInventory')->name('by-inventory');
    });

// Protected product routes (require authentication)
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::prefix('products')
        ->name('api.products.')
        ->controller(ProductApiController::class)
        ->group(function () {
            // Create new product
            Route::post('/', 'store')->name('store');
            
            // Update product
            Route::put('/{product}', 'update')->name('update');
            
            // Delete product
            Route::delete('/{product}', 'destroy')->name('destroy');

            
        });

    
});


// Admin-only product routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::prefix('admin/products')
        ->name('api.admin.products.')
        ->controller(ProductApiController::class)
        ->group(function () {
            // Admin product listing (all products including unpublished)
            Route::get('/', 'index')->name('index');
            
            // Admin product search (all products)
            Route::get('/search', 'search')->name('search');
            
            // Admin product details (all products)
            Route::get('/{product}', 'show')->name('show');
            
            // Admin product statistics
            Route::get('/stats/overview', 'getStats')->name('stats');
            
            // Admin create product
            Route::post('/', 'store')->name('store');
            
            // Admin update product
            Route::put('/{product}', 'update')->name('update');
            
            // Admin delete product
            Route::delete('/{product}', 'destroy')->name('destroy');
            
            // Get products by inventory (admin view)
            Route::get('/inventory/{inventoryId}', 'getByInventory')->name('by-inventory');
        });
});
