<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MaintenanceController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'apiLogin']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'apiLogout']);

    /*
    |--------------------------------------------------------------------------
    | LANDLORD ROUTES
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:landlord')->group(function () {

        // Properties
        Route::post('/properties', [PropertyController::class, 'store']);
        Route::get('/properties', [PropertyController::class, 'index']);

        // Units
        Route::post('/properties/{property}/units', [UnitController::class, 'store']);
        Route::get('/properties/{property}/units', [UnitController::class, 'index']);

        // Tenant & Caretaker Registration
        Route::post('/caretakers', [AuthController::class, 'registerCaretaker']);
        Route::post('/tenants', [AuthController::class, 'registerTenant']);

        // Available Units
        Route::get('/available-units', [UnitController::class, 'availableUnits']);

        // Maintenance Requests
        Route::get('/maintenance-requests', [MaintenanceController::class, 'index']);

        Route::patch(
            '/maintenance-requests/{maintenanceRequest}/status',
            [MaintenanceController::class, 'updateStatus']
        );

        Route::patch(
            '/maintenance-requests/{maintenanceRequest}/assign-caretaker',
            [MaintenanceController::class, 'assignCaretaker']
        );

        // Payments
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy']);
    });


    /*
    |--------------------------------------------------------------------------
    | TENANT ROUTES
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:tenant')->group(function () {

        // Payments
        Route::post('/payments', [PaymentController::class, 'store']);
        Route::get('/payments', [PaymentController::class, 'history']);

        // Maintenance Requests
        Route::post('/maintenance', [MaintenanceController::class, 'store']);

        Route::post(
            '/maintenance-requests',
            [MaintenanceController::class, 'store']
        );
    });


    /*
    |--------------------------------------------------------------------------
    | CARETAKER ROUTES
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:caretaker')->group(function () {

        Route::get(
            '/caretaker/maintenance-requests',
            [MaintenanceController::class, 'caretakerRequests']
        );
    });


    /*
    |--------------------------------------------------------------------------
    | LANDLORD + CARETAKER SHARED ROUTES
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:caretaker,landlord')->group(function () {

        Route::post(
            '/payments/{payment}/verify',
            [PaymentController::class, 'verify']
        );

        Route::post(
            '/payments/cash',
            [PaymentController::class, 'storeCash']
        );
    });

});