<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardApiController;

Route::prefix('api')->middleware('auth:sanctum')->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardApiController::class, 'admin']);
    });

    Route::middleware('role:landlord')->group(function () {
        Route::get('/landlord/dashboard', [DashboardApiController::class, 'landlord']);
    });

    Route::middleware('role:caretaker')->group(function () {
        Route::get('/caretaker/dashboard', [DashboardApiController::class, 'caretaker']);
    });

    Route::middleware('role:tenant')->group(function () {
        Route::get('/tenant/dashboard', [DashboardApiController::class, 'tenant']);
    });
});
