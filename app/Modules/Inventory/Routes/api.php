<?php

namespace App\Modules\Inventory\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Models\InventoryVariation;
use App\Modules\Product\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Modules\Inventory\Controllers\InventoryController;

Route::prefix('inventory')->group(function () {
    Route::get('/', [InventoryController::class, 'apiIndex']);       // List inventories
    Route::get('/{id}', [InventoryController::class, 'apiShow']);    // Show single inventory
    Route::post('/', [InventoryController::class, 'apiStore']);      // Create inventory
    Route::put('/{id}', [InventoryController::class, 'apiUpdate']);  // Update inventory
    Route::delete('/{id}', [InventoryController::class, 'apiDestroy']); // Delete inventory
    Route::put('/{id}/toggle-status', [InventoryController::class, 'apiToggleStatus']); // Toggle published/draft
});
