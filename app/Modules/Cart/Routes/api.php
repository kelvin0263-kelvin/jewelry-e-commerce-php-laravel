<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Cart\Controllers\CartController;

Route::middleware('auth:sanctum')->prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index']);            // GET /api/cart
    Route::post('/add/{productId}', [CartController::class, 'add']);
    Route::put('/update/{id}', [CartController::class, 'update']);
    Route::delete('/remove/{id}', [CartController::class, 'remove']);
    Route::delete('/clear', [CartController::class, 'clear']);
    Route::get('/checkout', [CartController::class, 'checkout']);
    Route::post('/place-order', [CartController::class, 'placeOrder']);
});
