<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Cart\Controllers\CartApiController;

Route::prefix('cart')->group(function () {
    Route::get('/', [CartApiController::class, 'index']);            // GET /api/cart
    Route::post('/add/{productId}', [CartApiController::class, 'add']);  // POST /api/cart/add/{productId}
    Route::put('/update/{id}', [CartApiController::class, 'update']);    // PUT /api/cart/update/{id}
    Route::delete('/remove/{id}', [CartApiController::class, 'remove']); // DELETE /api/cart/remove/{id}
    Route::delete('/clear', [CartApiController::class, 'clear']);        // DELETE /api/cart/clear
    Route::get('/checkout', [CartApiController::class, 'checkout']);     // GET /api/cart/checkout
    Route::post('/place-order', [CartApiController::class, 'placeOrder']); // POST /api/cart/place-order
});
