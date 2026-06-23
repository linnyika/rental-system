<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminReportController;
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
        Route::get('/dashboard', [AdminReportController::class, 'dashboard'])->name('dashboard');
        Route::get('/landlords', [AdminReportController::class, 'landlords'])->name('landlords.index');
        Route::get('/landlords/{landlord}', [AdminReportController::class, 'landlordShow'])->name('landlords.show');
        Route::get('/tenants', [AdminReportController::class, 'tenants'])->name('tenants.index');
        Route::get('/tenants/{tenant}', [AdminReportController::class, 'tenantShow'])->name('tenants.show');
        Route::get('/caretakers', [AdminReportController::class, 'caretakers'])->name('caretakers.index');
        Route::get('/caretakers/{caretaker}', [AdminReportController::class, 'caretakerShow'])->name('caretakers.show');
        Route::get('/properties', [AdminReportController::class, 'properties'])->name('properties.index');
        Route::get('/properties/{property}', [AdminReportController::class, 'propertyShow'])->name('properties.show');
    });

    // Landlord Routes - Only landlords can access
    Route::middleware(['role:landlord'])->prefix('landlord')->name('landlord.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'landlordDashboard'])->name('dashboard');
        Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
        Route::get('/units/{property}', [UnitController::class, 'index'])->name('units.index');
        Route::view('/caretakers', 'landlord.caretakers')->name('caretakers.create');
        Route::view('/tenants', 'landlord.tenants')->name('tenants.create');
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

/*
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
