<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Modules\User\Controllers\UserApiController;

Route::post('/register', [UserApiController::class, 'register']);
Route::post('/login', [UserApiController::class, 'login']);
Route::post('/password/request-code', [UserApiController::class, 'requestResetCode']);
Route::post('/password/verify-code', [UserApiController::class, 'verifyResetCode']);
Route::post('/password/reset', [UserApiController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserApiController::class, 'logout']);
    Route::get('/me', [UserApiController::class, 'me']);
    Route::put('/me', [UserApiController::class, 'updateProfile']);
    Route::put('/me/password', [UserApiController::class, 'updatePassword']);
    Route::delete('/me', [UserApiController::class, 'deleteAccount']);
});
