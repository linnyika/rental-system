<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Payment;
use App\Models\MaintenanceRequest;
use App\Models\Tenant;
use App\Models\Caretaker;
use App\Models\Landlord;
use App\Models\TenantOccupancy;
use App\Models\Task;

class DashboardController extends Controller
{
    // Admin dashboard is handled in AdminController, so we redirect
    public function adminDashboard()
    {
        return redirect()->route('admin.dashboard');
    }

    // -------------------- LANDLORD DASHBOARD (Web) --------------------
    public function landlordDashboard()
    {
        $user = Auth::user();
        $landlord = $user->landlord;

        $properties = Property::where('landlord_id', $landlord->id)->get();
        $unitIds = Unit::whereIn('property_id', $properties->pluck('id'))->pluck('id');
        $payments = Payment::whereIn('unit_id', $unitIds)->get();
        $maintenance = MaintenanceRequest::whereIn('unit_id', $unitIds)->get();

        return view('landlord.dashboard', compact('properties', 'payments', 'maintenance'));
    }

    // -------------------- CARETAKER DASHBOARD (Web) --------------------
    public function caretakerDashboard()
    {
        $user = Auth::user();
        $caretaker = $user->caretaker;

        $tasks = Task::where('caretaker_id', $caretaker->id)
            ->with(['request.tenant.user', 'request.unit.property'])
            ->latest()->take(10)->get();

        $total = Task::where('caretaker_id', $caretaker->id)->count();
        $pending = Task::where('caretaker_id', $caretaker->id)->where('status', 'assigned')->count();
        $inProgress = Task::where('caretaker_id', $caretaker->id)->where('status', 'in_progress')->count();
        $completed = Task::where('caretaker_id', $caretaker->id)->where('status', 'done')->count();

        $pendingRequests = MaintenanceRequest::whereHas('unit.property', function ($q) use ($caretaker) {
            $q->where('caretaker_id', $caretaker->id);
        })->where('status', 'approved')->whereDoesntHave('task')->count();

        return view('caretaker.dashboard', compact('tasks', 'total', 'pending', 'inProgress', 'completed', 'pendingRequests'));
    }

    // -------------------- TENANT DASHBOARD (Web) --------------------
    public function tenantDashboard()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $payments = Payment::where('tenant_id', $tenant->id)->latest()->get();
        $maintenance = MaintenanceRequest::where('tenant_id', $tenant->id)->latest()->get();

        $currentOccupancy = TenantOccupancy::where('tenant_id', $tenant->id)
            ->whereNull('end_date')->latest()->first();
        $currentUnit = $currentOccupancy?->unit;

        return view('tenant.dashboard', compact('payments', 'maintenance', 'currentUnit'));
    }

    // -------------------- API: GET DASHBOARD STATS FOR ANY ROLE --------------------
    public function stats(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            $stats = $this->adminStats();
        } elseif ($user->role === 'landlord') {
            $stats = $this->landlordStats($user->landlord);
        } elseif ($user->role === 'caretaker') {
            $stats = $this->caretakerStats($user->caretaker);
        } elseif ($user->role === 'tenant') {
            $stats = $this->tenantStats($user->tenant);
        } else {
            return $this->errorResponse('Unauthorized', 403);
        }

        return $this->successResponse($stats);
    }

    private function adminStats()
    {
        $totalUnits = Unit::count();
        $occupied = Unit::where('is_occupied', true)->count();

        return [
            'total_users' => User::count(),
            'total_landlords' => Landlord::count(),
            'total_tenants' => Tenant::count(),
            'total_caretakers' => Caretaker::count(),
            'total_properties' => Property::count(),
            'total_units' => $totalUnits,
            'occupied_units' => $occupied,
            'vacant_units' => $totalUnits - $occupied,
            'total_active_leases' => TenantOccupancy::whereNull('end_date')->count(),
            'total_rent_collected' => Payment::where('status', 'verified')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->count(),
        ];
    }

    private function landlordStats($landlord)
    {
        $properties = Property::where('landlord_id', $landlord->id)->get();
        $unitIds = Unit::whereIn('property_id', $properties->pluck('id'))->pluck('id');
        $totalUnits = Unit::whereIn('id', $unitIds)->count();
        $occupied = Unit::whereIn('id', $unitIds)->where('is_occupied', true)->count();

        return [
            'total_properties' => $properties->count(),
            'total_units' => $totalUnits,
            'occupied_units' => $occupied,
            'vacant_units' => $totalUnits - $occupied,
            'total_tenants' => Tenant::whereHas('occupancies.unit', function ($q) use ($unitIds) {
                $q->whereIn('unit_id', $unitIds);
            })->count(),
            'total_payments' => Payment::whereIn('unit_id', $unitIds)->count(),
            'total_rent_collected' => Payment::whereIn('unit_id', $unitIds)->where('status', 'verified')->sum('amount'),
            'pending_maintenance' => MaintenanceRequest::whereIn('unit_id', $unitIds)->where('status', 'pending')->count(),
        ];
    }

    private function caretakerStats($caretaker)
    {
        $taskStats = [
            'total' => Task::where('caretaker_id', $caretaker->id)->count(),
            'assigned' => Task::where('caretaker_id', $caretaker->id)->where('status', 'assigned')->count(),
            'in_progress' => Task::where('caretaker_id', $caretaker->id)->where('status', 'in_progress')->count(),
            'done' => Task::where('caretaker_id', $caretaker->id)->where('status', 'done')->count(),
        ];

        $pendingRequests = MaintenanceRequest::whereHas('unit.property', function ($q) use ($caretaker) {
            $q->where('caretaker_id', $caretaker->id);
        })->where('status', 'approved')->whereDoesntHave('task')->count();

        return array_merge($taskStats, ['pending_requests' => $pendingRequests]);
    }

    private function tenantStats($tenant)
    {
        $payments = Payment::where('tenant_id', $tenant->id)->get();
        $maintenance = MaintenanceRequest::where('tenant_id', $tenant->id)->get();

        return [
            'total_payments' => $payments->count(),
            'verified_payments' => $payments->where('status', 'verified')->count(),
            'pending_payments' => $payments->where('status', 'pending')->count(),
            'total_maintenance_requests' => $maintenance->count(),
            'open_maintenance' => $maintenance->whereIn('status', ['pending', 'approved', 'in_progress'])->count(),
        ];
    }
}