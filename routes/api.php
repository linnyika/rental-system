<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\PaymentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Available to any logged-in user
    Route::post('/logout', [AuthController::class, 'logout']);

    // Landlord-only actions
    Route::middleware('role:landlord')->group(function () {
        Route::post('/properties', [PropertyController::class, 'store']);
        Route::get('/properties', [PropertyController::class, 'index']);

        Route::post('/properties/{property}/units', [UnitController::class, 'store']);
        Route::get('/properties/{property}/units', [UnitController::class, 'index']);

        Route::post('/caretakers', [AuthController::class, 'registerCaretaker']);
        Route::post('/tenants', [AuthController::class, 'registerTenant']);
        Route::get('/available-units', [UnitController::class, 'availableUnits']);
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy']);
    });

    // Tenant-only actions
    Route::middleware('role:tenant')->group(function () {
        Route::post('/payments', [PaymentController::class, 'store']);
        Route::middleware('role:tenant')->group(function () {
        Route::post('/payments', [PaymentController::class, 'store']);
        Route::get('/payments', [PaymentController::class, 'history']);
        Route::post('/maintenance-requests', [MaintenanceController::class, 'store']);
    });
    });
    // Payment verification — caretaker (normal) or landlord (fallback)
    Route::middleware('role:caretaker,landlord')->group(function () {
        Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify']);
        Route::middleware('role:caretaker,landlord')->group(function () {
        Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify']);
        Route::post('/payments/cash', [PaymentController::class, 'storeCash']);
        
    });
    });

});