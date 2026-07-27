<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;

Route::get('/', function () {
    return view('welcome');
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
