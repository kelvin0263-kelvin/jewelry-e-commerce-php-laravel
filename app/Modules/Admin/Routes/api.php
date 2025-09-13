<?php
// app/Modules/Admin/Routes/api.php

use Illuminate\Support\Facades\Route;
use App\Modules\Admin\Controllers\CustomerController;
use App\Modules\Admin\Controllers\DashboardController;

// Admin protected routes - require admin authentication
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    
    // Dashboard routes
    Route::get('/admin/dashboard', [DashboardController::class, 'index']);
    Route::get('/admin/dashboard/stats', [DashboardController::class, 'getStats']);
    
    // Customer management routes
    Route::prefix('admin/customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index']);          // GET /api/admin/customers
        Route::get('/{customer}', [CustomerController::class, 'show']);  // GET /api/admin/customers/{id}
        Route::put('/{customer}', [CustomerController::class, 'update']); // PUT /api/admin/customers/{id}
        Route::delete('/{customer}', [CustomerController::class, 'destroy']); // DELETE /api/admin/customers/{id}
        Route::get('/{customer}/orders', [CustomerController::class, 'orders']); // GET /api/admin/customers/{id}/orders
        Route::post('/{customer}/block', [CustomerController::class, 'block']); // POST /api/admin/customers/{id}/block
        Route::post('/{customer}/unblock', [CustomerController::class, 'unblock']); // POST /api/admin/customers/{id}/unblock
    });
    
    
    
});
