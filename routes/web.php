<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;

Route::get('/', function () {
    return view('welcome');
});

// Inventory Management
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::get('/inventory/{id}', [InventoryController::class, 'show'])->name('inventory.show');
    Route::put('/inventory/{id}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    Route::post('/inventory/{id}/restock', [InventoryController::class, 'restock'])->name('inventory.restock');
    Route::get('/inventory/{id}/price', [InventoryController::class, 'editPrice'])->name('inventory.price');
    Route::post('/inventory/{id}/price', [InventoryController::class, 'updatePrice'])->name('inventory.price.update');
});
