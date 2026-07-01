<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandlordController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UnitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', static fn () => redirect()->route('login'))->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', static function () {
        $user = Auth::user();

        return match ($user?->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'landlord' => redirect()->route('landlord.dashboard'),
            'caretaker' => redirect()->route('caretaker.dashboard'),
            'tenant' => redirect()->route('tenant.dashboard'),
            default => redirect()->route('home'),
        };
    })->name('dashboard');

    Route::prefix('profile')->name('profile.')->group(function (): void {
        Route::get('/', static function () {
            $user = Auth::user();
            return view('profile.show', compact('user'));
        })->name('show');

        Route::put('/', static function () {
            $user = Auth::user();

            if ($user?->role === 'landlord') {
                return app(LandlordController::class)->updateProfile(request());
            }
            if ($user?->role === 'caretaker') {
                return app(\App\Http\Controllers\CaretakerController::class)->updateProfile(request());
            }
            if ($user?->role === 'tenant') {
                return app(TenantController::class)->updateProfile(request());
            }

            return back();
        })->name('update');
    });

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function (): void {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::get('/landlords', [AdminController::class, 'landlords'])->name('landlords');
        Route::get('/landlords/{landlord}', [AdminController::class, 'landlordShow'])->name('landlords.show');

        Route::get('/tenants', [AdminController::class, 'tenants'])->name('tenants');
        Route::get('/tenants/{tenant}', [AdminController::class, 'tenantShow'])->name('tenants.show');

        Route::get('/caretakers', [AdminController::class, 'caretakers'])->name('caretakers');
        Route::get('/caretakers/{caretaker}', [AdminController::class, 'caretakerShow'])->name('caretakers.show');

        Route::get('/properties', [AdminController::class, 'properties'])->name('properties');
        Route::get('/properties/{property}', [AdminController::class, 'propertyShow'])->name('properties.show');

        Route::get('/payments', static fn () => view('admin.payments'))->name('payments');
        Route::get('/maintenance', static fn () => view('admin.maintenance'))->name('maintenance');
        Route::get('/reports', static fn () => view('admin.reports'))->name('reports');
        Route::get('/users', static fn () => view('admin.users'))->name('users');
        Route::get('/units', static fn () => view('admin.units'))->name('units');

        Route::prefix('oversight')->name('oversight.')->group(function (): void {
            Route::get('/', static fn () => view('admin.oversight.oversight'))->name('index');
        });

        Route::prefix('settings')->name('settings.')->group(function (): void {
            Route::get('/', static fn () => view('admin.settings.settings'))->name('index');
            Route::get('/profile', static fn () => view('admin.settings.profile'))->name('profile');
            Route::get('/notifications', static fn () => view('admin.settings.notifications'))->name('notifications');
        });

        Route::prefix('reports')->name('reports.')->group(function (): void {
            Route::get('/financial', [ReportController::class, 'financialSummary'])->name('financial');
            Route::get('/occupancy', [ReportController::class, 'occupancyReport'])->name('occupancy');
        });

        Route::prefix('notifications')->name('notifications.')->group(function (): void {
            Route::get('/', [NotificationsController::class, 'index'])->name('index');
            Route::put('/{notification}/read', [NotificationsController::class, 'markAsRead'])->name('read');
            Route::put('/read-all', [NotificationsController::class, 'markAllAsRead'])->name('read-all');
        });
    });

    Route::prefix('landlord')->name('landlord.')->middleware('role:landlord')->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'landlordDashboard'])->name('dashboard');

        Route::get('/properties', static fn () => view('landlord.properties.properties'))->name('properties');
        Route::get('/properties/create', static fn () => view('landlord.properties.create'))->name('properties.create');

        Route::resource('property-manager', PropertyController::class)
            ->parameters(['property-manager' => 'property'])
            ->names('properties.resource');

        Route::prefix('properties/{property}')->name('properties.')->group(function (): void {
            Route::resource('units', UnitController::class)->except(['create', 'edit']);
        });

        Route::get('/tenants', static fn () => view('landlord.tenants'))->name('tenants');
        Route::post('/tenants/register', [AuthController::class, 'registerTenant'])->name('tenants.register');

        Route::get('/caretakers', static fn () => view('landlord.caretakers'))->name('caretakers');
        Route::post('/caretakers/register', [AuthController::class, 'registerCaretaker'])->name('caretakers.register');

        Route::get('/payments', static fn () => view('landlord.payments'))->name('payments');
        Route::post('/payments/cash', [PaymentController::class, 'storeCash'])->name('payments.cash');
        Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

        Route::get('/maintenance', static fn () => view('landlord.maintenance'))->name('maintenance');
        Route::get('/maintenance/requests', [MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::patch('/maintenance/requests/{maintenanceRequest}/status', [MaintenanceController::class, 'updateStatus'])->name('maintenance.update');

        Route::get('/reports', static fn () => view('landlord.reports'))->name('reports');
        Route::get('/reports/financial', [ReportController::class, 'financialSummary'])->name('reports.financial');
        Route::get('/reports/occupancy', [ReportController::class, 'occupancyReport'])->name('reports.occupancy');

        Route::get('/caretaker-logs', static fn () => view('landlord.caretakers-logs'))->name('caretaker-logs');
        Route::get('/register', static fn () => view('landlord.register'))->name('register');

        Route::prefix('oversight')->name('oversight.')->group(function (): void {
            Route::get('/', static fn () => view('landlord.reports'))->name('index');
        });

        Route::prefix('settings')->name('settings.')->group(function (): void {
            Route::get('/', static fn () => view('landlord.settings.settings'))->name('index');
            Route::get('/profile', static fn () => view('landlord.settings.profile'))->name('profile');
            Route::get('/notifications', static fn () => view('landlord.settings.notifications'))->name('notifications');
        });

        Route::prefix('notifications')->name('notifications.')->group(function (): void {
            Route::get('/', [NotificationsController::class, 'index'])->name('index');
            Route::put('/{notification}/read', [NotificationsController::class, 'markAsRead'])->name('read');
            Route::put('/read-all', [NotificationsController::class, 'markAllAsRead'])->name('read-all');
        });
    });

    Route::prefix('caretaker')->name('caretaker.')->middleware('role:caretaker')->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'caretakerDashboard'])->name('dashboard');

        Route::get('/properties', static fn () => view('caretaker.properties'))->name('properties');
        Route::get('/tasks', static fn () => view('caretaker.tasks'))->name('tasks');
        Route::get('/maintenance', static fn () => view('caretaker.maintenance'))->name('maintenance');
        Route::get('/payments', static fn () => view('caretaker.payments'))->name('payments');
        Route::get('/activity', static fn () => view('caretaker.daily-activity-logs'))->name('activity');
        Route::get('/reports', static fn () => view('caretaker.maintenance'))->name('reports');

        Route::prefix('maintenance')->name('maintenance.')->group(function (): void {
            Route::get('/', [TaskController::class, 'index'])->name('index');
            Route::get('/pending', [TaskController::class, 'pending'])->name('pending');
            Route::get('/in-progress', [TaskController::class, 'inProgress'])->name('in-progress');
            Route::get('/completed', [TaskController::class, 'completed'])->name('completed');
            Route::get('/report', [TaskController::class, 'report'])->name('report');
            Route::get('/{task}', [TaskController::class, 'show'])->name('show');
            Route::post('/{task}/start', [TaskController::class, 'start'])->name('start');
            Route::post('/{task}/complete', [TaskController::class, 'complete'])->name('complete');
            Route::post('/{task}/confirm', [TaskController::class, 'confirm'])->name('confirm');
        });

        Route::get('/activity-logs', [MaintenanceController::class, 'activityLogs'])->name('activity.logs');
        Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');

        Route::prefix('settings')->name('settings.')->group(function (): void {
            Route::get('/', static fn () => view('caretaker.settings.settings'))->name('index');
            Route::get('/profile', static fn () => view('caretaker.settings.profile'))->name('profile');
            Route::get('/notifications', static fn () => view('caretaker.settings.notifications'))->name('notifications');
        });

        Route::prefix('notifications')->name('notifications.')->group(function (): void {
            Route::get('/', [NotificationsController::class, 'index'])->name('index');
            Route::put('/{notification}/read', [NotificationsController::class, 'markAsRead'])->name('read');
            Route::put('/read-all', [NotificationsController::class, 'markAllAsRead'])->name('read-all');
        });
    });

    Route::prefix('tenant')->name('tenant.')->middleware('role:tenant')->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'tenantDashboard'])->name('dashboard');

        Route::get('/unit', static fn () => view('tenant.unit'))->name('unit');
        Route::get('/payments', static fn () => view('tenant.payments'))->name('payments');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

        Route::get('/maintenance', static fn () => view('tenant.maintenance-request'))->name('maintenance');
        Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
        Route::post('/tasks/{task}/confirm', [MaintenanceController::class, 'confirmCompletion'])->name('maintenance.confirm');

        Route::prefix('settings')->name('settings.')->group(function (): void {
            Route::get('/', static fn () => view('tenant.settings.settings'))->name('index');
            Route::get('/profile', static fn () => view('tenant.settings.profile'))->name('profile');
            Route::get('/notifications', static fn () => view('tenant.settings.notifications'))->name('notifications');
        });

        Route::prefix('notifications')->name('notifications.')->group(function (): void {
            Route::get('/', [NotificationsController::class, 'index'])->name('index');
            Route::put('/{notification}/read', [NotificationsController::class, 'markAsRead'])->name('read');
            Route::put('/read-all', [NotificationsController::class, 'markAllAsRead'])->name('read-all');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Sanctum API Auth Mirror Routes
|--------------------------------------------------------------------------
| NOTE: Core API resources remain in routes/api.php.
| These mirror auth/token routes are provided for integration compatibility.
*/
Route::prefix('api')->name('api.')->group(function (): void {
    Route::post('/login', [AuthController::class, 'apiLogin'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'apiLogout'])->name('logout');
        Route::get('/user', [AuthController::class, 'user'])->name('user');

        Route::get('/notifications', [NotificationsController::class, 'index'])->name('notifications.index');
        Route::put('/notifications/{notification}/read', [NotificationsController::class, 'markAsRead'])->name('notifications.read');
        Route::put('/notifications/read-all', [NotificationsController::class, 'markAllAsRead'])->name('notifications.read-all');

        Route::middleware('role:landlord')->group(function (): void {
            Route::apiResource('properties', PropertyController::class);
            Route::apiResource('properties.units', UnitController::class);
            Route::post('/payments/cash', [PaymentController::class, 'storeCash'])->name('payments.cash');
            Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
            Route::post('/caretakers', [AuthController::class, 'registerCaretaker'])->name('caretakers.store');
            Route::post('/tenants', [AuthController::class, 'registerTenant'])->name('tenants.store');
            Route::get('/maintenance-requests', [MaintenanceController::class, 'index'])->name('maintenance.index');
            Route::patch('/maintenance-requests/{maintenanceRequest}/status', [MaintenanceController::class, 'updateStatus'])->name('maintenance.update');
        });

        Route::middleware('role:caretaker')->group(function (): void {
            Route::get('/caretaker/tasks', [MaintenanceController::class, 'caretakerTasks'])->name('caretaker.tasks');
            Route::put('/tasks/{task}/start', [MaintenanceController::class, 'startWork'])->name('tasks.start');
            Route::put('/tasks/{task}/complete', [MaintenanceController::class, 'markWorkDone'])->name('tasks.complete');
            Route::get('/caretaker/activity-logs', [MaintenanceController::class, 'activityLogs'])->name('caretaker.activity');
        });

        Route::middleware('role:tenant')->group(function (): void {
            Route::post('/payments', [PaymentController::class, 'store'])->name('tenant.payments.store');
            Route::get('/payments', [PaymentController::class, 'history'])->name('tenant.payments.history');
            Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('tenant.maintenance.store');
            Route::put('/tasks/{task}/confirm', [MaintenanceController::class, 'confirmCompletion'])->name('tenant.tasks.confirm');
        });

        Route::middleware('role:admin')->group(function (): void {
            Route::get('/admin/stats', [DashboardController::class, 'stats'])->name('admin.stats');
            Route::get('/reports/financial', [ReportController::class, 'financialSummary'])->name('reports.financial');
            Route::get('/reports/occupancy', [ReportController::class, 'occupancyReport'])->name('reports.occupancy');
        });
    });
});

Route::fallback(static fn () => abort(404, 'Page not found.'));
