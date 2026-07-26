<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/customers', [AdminController::class, 'customers'])->name('customers');
    Route::get('/customers/create', [AdminController::class, 'customerCreate'])->name('customers.create');
    Route::get('/customers/{id}', [AdminController::class, 'customerShow'])->name('customers.show');
    Route::view('/prescriptions', 'coming-soon')->name('prescriptions');
    Route::view('/inventory', 'coming-soon')->name('inventory');
    Route::view('/sales', 'coming-soon')->name('sales');
    Route::view('/payments', 'coming-soon')->name('payments');
});

Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [CustomerController::class, 'profile'])->name('profile');
});
