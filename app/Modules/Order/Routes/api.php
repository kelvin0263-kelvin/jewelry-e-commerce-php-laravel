<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Order\Controllers\OrderApiController;

Route::middleware('auth:sanctum')->prefix('orders')->group(function () {
    Route::get('/', [OrderApiController::class, 'index']);                    // GET /api/orders
    Route::get('/{id}', [OrderApiController::class, 'show']);                 // GET /api/orders/{id}
    Route::get('/status/{status}', [OrderApiController::class, 'getOrdersByStatus']); // GET /api/orders/status/{status}
    Route::get('/{id}/items', [OrderApiController::class, 'getOrderItems']);  // GET /api/orders/{id}/items
    Route::get('/{id}/tracking', [OrderApiController::class, 'getTracking']); // GET /api/orders/{id}/tracking
    Route::patch('/{id}/complete', [OrderApiController::class, 'markAsCompleted']); // PATCH /api/orders/{id}/complete
    Route::patch('/{id}/refund', [OrderApiController::class, 'markAsRefund']); // PATCH /api/orders/{id}/refund
    Route::post('/{id}/refund-reason', [OrderApiController::class, 'submitRefundReason']); // POST /api/orders/{id}/refund-reason
});

