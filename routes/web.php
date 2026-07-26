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

// Staff/admin authentication
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showAdminLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'adminLoginSubmit'])->name('login.submit');
    Route::get('/forgot-password', [AuthController::class, 'showAdminForgotPassword'])->name('forgot-password');
    Route::post('/forgot-password', [AuthController::class, 'adminForgotPasswordSubmit'])->name('forgot-password.submit');
    Route::get('/reset-password', [AuthController::class, 'showAdminResetPassword'])->name('reset-password');
    Route::post('/reset-password', [AuthController::class, 'adminResetPasswordSubmit'])->name('reset-password.submit');
});
