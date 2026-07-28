<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MedicineController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\PriceHistoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PrescriptionController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AllergyController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Auth\StaffAuthController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Api\DashboardController;

// Feature 1: Authentication API endpoints (Public)
Route::post('/auth/register', [UserAuthController::class, 'register']);
Route::post('/auth/login', [UserAuthController::class, 'login']);
Route::post('/auth/reset-password', [UserAuthController::class, 'resetPassword']);

// Staff Authentication (Public)
Route::post('/staff/login', [StaffAuthController::class, 'staffLogin']);

// Protected API Routes (Requires Sanctum Auth)
Route::middleware('auth:sanctum')->group(function () {

    // Auth Logout
    Route::post('/auth/logout', [UserAuthController::class, 'logout']);
    Route::post('/staff/logout', [StaffAuthController::class, 'logout']);

    // Feature 2: Customer Management (self-service)
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::post('/allergies', [AllergyController::class, 'store']);
        Route::get('/allergies', [AllergyController::class, 'index']);
        Route::delete('/allergies/{allergy}', [AllergyController::class, 'destroy']);
    });

    // Staff-side Customer Management
    Route::prefix('staff/customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index']);
        Route::get('/{id}', [CustomerController::class, 'show']);
        Route::post('/', [CustomerController::class, 'store']);
        Route::put('/{id}', [CustomerController::class, 'update']);
        Route::delete('/{id}', [CustomerController::class, 'destroy']);
    });

    // Feature 3: Inventory Management
    Route::prefix('inventory')->group(function () {
        Route::get('/medicines', [MedicineController::class, 'index']);
        Route::post('/medicines', [MedicineController::class, 'store']);
        Route::get('/medicines/low-stock', [MedicineController::class, 'lowStockAlert']);
        Route::post('/stock', [StockController::class, 'store']);

        Route::get('/medicines/{medicine}/price-history', [PriceHistoryController::class, 'index']);
        Route::put('/medicines/{medicine}/price', [MedicineController::class, 'updatePrice']);
        Route::delete('/medicines/{id}', [MedicineController::class, 'destroy']);

        Route::get('/medicines/{id}', [MedicineController::class, 'show']);
        Route::put('/medicines/{id}', [MedicineController::class, 'update']);
    });

    // Feature 4: Prescription Management
    Route::prefix('prescriptions')->group(function () {
        Route::get('/', [PrescriptionController::class, 'index']);
        Route::get('/{id}', [PrescriptionController::class, 'show']);
        Route::post('/', [PrescriptionController::class, 'store']);
        Route::put('/{id}', [PrescriptionController::class, 'update']);
        Route::delete('/{id}', [PrescriptionController::class, 'destroy']);

        Route::post('/{id}/medicines', [PrescriptionController::class, 'addMedicine']);
        Route::put('/medicines/{detailId}', [PrescriptionController::class, 'updateMedicine']);
        Route::delete('/medicines/{detailId}', [PrescriptionController::class, 'removeMedicine']);

        Route::post('/{prescription}/dispense', [PrescriptionController::class, 'dispense']);
    });

    // Feature 5: Sales & Billing
    Route::prefix('shop')->group(function () {
        Route::post('/checkout', [SaleController::class, 'checkout']);
        Route::post('/sales/{sale}/payment', [PaymentController::class, 'process']);
        Route::get('/sales', [SaleController::class, 'index']);
        Route::get('/sales/{id}', [SaleController::class, 'show']);
        Route::get('/payments', [PaymentController::class, 'index']);
    });

    // Feature 6: Cart Routes
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/', [CartController::class, 'store']);
        Route::delete('/{id}', [CartController::class, 'destroy']);
        Route::put('/{id}', [CartController::class, 'update']);
    });
    Route::get('/dashboard', [DashboardController::class, 'index']);

});
