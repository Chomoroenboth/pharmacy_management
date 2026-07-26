<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
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
