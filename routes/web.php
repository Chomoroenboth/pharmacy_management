<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return response()->json(['message' => 'Pharmacy Management API is running']);
});

// Customer authentication
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/login', [AuthController::class, 'showCustomerLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'customerLoginSubmit'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showCustomerRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'customerRegisterSubmit'])->name('register.submit');
    Route::get('/forgot-password', [AuthController::class, 'showCustomerForgotPassword'])->name('forgot-password');
    Route::post('/forgot-password', [AuthController::class, 'customerForgotPasswordSubmit'])->name('forgot-password.submit');
});

// OTC Shop and Cart
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/shop', [CartController::class, 'otcShop'])->name('otc-shop');
    Route::get('/search', [CartController::class, 'search'])->name('search');

    Route::get('/cart', [CartController::class, 'viewCart'])->name('cart');
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/{id}/update', [CartController::class, 'updateQuantity'])->name('cart.update');
    Route::delete('/cart/{id}/remove', [CartController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    Route::get('/purchases/{id}', [CartController::class, 'purchaseDetail'])->name('purchase.detail');
});

// Staff/admin authentication
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showAdminLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'adminLoginSubmit'])->name('login.submit');
    Route::get('/forgot-password', [AuthController::class, 'showAdminForgotPassword'])->name('forgot-password');
    Route::post('/forgot-password', [AuthController::class, 'adminForgotPasswordSubmit'])->name('forgot-password.submit');
    Route::get('/reset-password', [AuthController::class, 'showAdminResetPassword'])->name('reset-password');
    Route::post('/reset-password', [AuthController::class, 'adminResetPasswordSubmit'])->name('reset-password.submit');
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