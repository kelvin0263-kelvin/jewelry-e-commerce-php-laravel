<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Inventory\Controllers\InventoryApiController;

Route::prefix('inventory')
    ->name('api.inventory.')
    ->controller(InventoryApiController::class)
    ->group(function () {
        Route::get('/history', 'history')->name('history');
        Route::get('/', 'index')->name('index');
        Route::get('/{inventory}', 'show')->name('show');
        //Route::post('/', 'store')->name('store');
        //Route::put('/{inventory}', 'update')->name('update');
        //Route::delete('/{inventory}', 'destroy')->name('destroy');


    });




