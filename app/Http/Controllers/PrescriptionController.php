<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [CustomerController::class, 'show'])->name('profile');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::view('/customers', 'coming-soon')->name('customers');
    Route::view('/inventory', 'coming-soon')->name('inventory');
    Route::view('/sales', 'coming-soon')->name('sales');
    Route::view('/payments', 'coming-soon')->name('payments');
});