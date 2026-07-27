<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrescriptionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/customer/prescriptions', [PrescriptionController::class, 'prescriptions'])->name('customer.prescriptions');
Route::get('/customer/prescriptions/{id}', [PrescriptionController::class, 'prescriptionDetails'])->name('customer.prescriptions.details');

// Prescription Management
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/prescriptions', [PrescriptionController::class, 'index'])->name('prescriptions');
    Route::get('/prescriptions/create', [PrescriptionController::class, 'create'])->name('prescriptions.create');
    Route::post('/prescriptions', [PrescriptionController::class, 'store'])->name('prescriptions.store');
    Route::get('/prescriptions/{id}', [PrescriptionController::class, 'show'])->name('prescriptions.show');
    Route::put('/prescriptions/{id}', [PrescriptionController::class, 'update'])->name('prescriptions.update');
    Route::delete('/prescriptions/{id}', [PrescriptionController::class, 'destroy'])->name('prescriptions.destroy');
    Route::post('/prescriptions/{id}/medicines', [PrescriptionController::class, 'addMedicine'])->name('prescriptions.medicines.store');
    Route::put('/prescriptions/{id}/medicines/{medicineId}', [PrescriptionController::class, 'updateMedicine'])->name('prescriptions.medicines.update');
    Route::delete('/prescriptions/{id}/medicines/{medicineId}', [PrescriptionController::class, 'removeMedicine'])->name('prescriptions.medicines.remove');
});
