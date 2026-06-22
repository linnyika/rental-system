<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MaintenanceController;

// Guest routes
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin Routes - Only admin can access
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        // Add other admin routes here
    });

    // Landlord Routes - Only landlords can access
    Route::middleware(['role:landlord'])->prefix('landlord')->name('landlord.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'landlordDashboard'])->name('dashboard');
        Route::resource('properties', PropertyController::class);
        Route::resource('units', UnitController::class);
        Route::resource('payments', PaymentController::class);
        Route::resource('maintenance', MaintenanceController::class);
    });

    // Caretaker Routes - Only caretakers can access
    Route::middleware(['role:caretaker'])->prefix('caretaker')->name('caretaker.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'caretakerDashboard'])->name('dashboard');
        // Add other caretaker routes here
    });

    // Tenant Routes - Only tenants can access
    Route::middleware(['role:tenant'])->prefix('tenant')->name('tenant.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'tenantDashboard'])->name('dashboard');
        // Add other tenant routes here
    });
});

// API Routes (for mobile/SPA with Sanctum tokens)
Route::prefix('api')->group(function () {
    Route::post('/login', [AuthController::class, 'apiLogin']);
    Route::post('/logout', [AuthController::class, 'apiLogout'])->middleware('auth:sanctum');
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
});
/*
Route::view('/admin/dashboard', 'admin.dashboard');
Route::view('/landlord/dashboard', 'landlord.dashboard');
Route::view(
    '/landlord/properties',
    'landlord.properties'
);
Route::get(
    '/landlord/units/{id}',
    function () {
        return view('landlord.units');
    }
);
Route::view(
    '/landlord/caretakers',
    'landlord.caretakers'
);
Route::view('/landlord/tenants', 'landlord.tenants');
Route::view('/landlord/tenants', 'landlord.tenants');
*/
