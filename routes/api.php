<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController; // 1. Is this 'use' statement here?
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 2. Is your GET route defined correctly?
//Route::get('/customers/segmentation', [CustomerController::class, 'segmentation']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Broadcast::routes();

use App\Http\Controllers\Api\ChatController;

Route::get('/admin/chat/conversations/{id}/messages', [ChatController::class, 'messages']);
Route::post('/admin/chat/messages', [ChatController::class, 'store']);