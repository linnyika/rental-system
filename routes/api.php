<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/landlord/signup', [AuthController::class, 'apiLandlordSignup']);
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

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/landlords', [AuthController::class, 'adminLandlords']);
        Route::patch('/admin/landlords/{landlord}', [AuthController::class, 'adminUpdateLandlord']);
        Route::delete('/admin/landlords/{landlord}', [AuthController::class, 'adminDeleteLandlord']);
    });

    /*
    |--------------------------------------------------------------------------
    | LANDLORD ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:landlord')->group(function () {
        Route::post('/properties', [PropertyController::class, 'store']);
        Route::get('/properties', [PropertyController::class, 'index']);

        Route::post('/properties/{property}/units', [UnitController::class, 'store']);
        Route::get('/properties/{property}/units', [UnitController::class, 'index']);

        Route::post('/caretakers', [AuthController::class, 'registerCaretaker']);
        Route::post('/tenants', [AuthController::class, 'registerTenant']);

        Route::get('/available-units', [UnitController::class, 'availableUnits']);

        Route::get('/maintenance-requests', [MaintenanceController::class, 'index']);
        Route::patch('/maintenance-requests/{maintenanceRequest}/status', [MaintenanceController::class, 'updateStatus']);

        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy']);
        Route::get('/activity-logs', [MaintenanceController::class, 'landlordActivityLogs']);
    });

    /*
    |--------------------------------------------------------------------------
    | TENANT ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:tenant')->group(function () {
        Route::post('/payments', [PaymentController::class, 'store']);
        Route::get('/payments', [PaymentController::class, 'history']);

        Route::post('/maintenance', [MaintenanceController::class, 'store']);
        Route::post('/maintenance-requests', [MaintenanceController::class, 'store']);
        Route::get('/tenant/maintenance-requests', [MaintenanceController::class, 'tenantRequests']);

        Route::get('/tenant/tasks/completed', [MaintenanceController::class, 'tenantCompletedTasks']);
        Route::put('/tasks/{task}/confirm', [MaintenanceController::class, 'confirmCompletion']);
    });

    /*
    |--------------------------------------------------------------------------
    | CARETAKER ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:caretaker')->group(function () {
        Route::get('/caretaker/tasks', [MaintenanceController::class, 'caretakerTasks']);
        Route::put('/tasks/{task}/start', [MaintenanceController::class, 'startWork']);
        Route::put('/tasks/{task}/complete', [MaintenanceController::class, 'markWorkDone']);
        Route::get('/caretaker/activity-logs', [MaintenanceController::class, 'activityLogs']);
    });

    /*
    |--------------------------------------------------------------------------
    | LANDLORD + CARETAKER SHARED ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:caretaker,landlord')->group(function () {
        Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify']);
        Route::post('/payments/cash', [PaymentController::class, 'storeCash']);
    });
});
