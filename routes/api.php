<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// User authentication routes
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Include module API routes
// Admin API routes
if (file_exists(app_path('Modules/Admin/Routes/api.php'))) {
    include app_path('Modules/Admin/Routes/api.php');
}

// Support API routes
if (file_exists(app_path('Modules/Support/Routes/api.php'))) {
    include app_path('Modules/Support/Routes/api.php');
}