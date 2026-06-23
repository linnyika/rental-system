<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Payment;
use App\Models\MaintenanceRequest;
use App\Models\Tenant;
use App\Models\Caretaker;
use App\Models\Landlord;
use App\Models\TenantOccupancy;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $totalUnits = Unit::count();
        $occupiedUnits = Unit::where('is_occupied', true)->count();
        $verifiedPayments = Payment::where('status', 'verified')->sum('amount');
        $openMaintenance = MaintenanceRequest::whereIn('status', ['pending', 'in_progress'])->count();

        $stats = [
            'total_properties' => Property::count(),
            'total_units' => $totalUnits,
            'occupied_units' => $occupiedUnits,
            'occupancy_rate' => $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100) : 0,
            'total_tenants' => Tenant::count(),
            'total_landlords' => Landlord::count(),
            'total_caretakers' => Caretaker::count(),
            'total_payments' => Payment::count(),
            'verified_payment_amount' => $verifiedPayments,
            'total_maintenance' => MaintenanceRequest::count(),
            'open_maintenance' => $openMaintenance,
        ];

        $recentLandlords = Landlord::with('user')
            ->withCount(['properties'])
            ->latest()
            ->take(5)
            ->get();

        $recentPayments = Payment::with(['tenant.user', 'unit.property'])
            ->latest('payment_date')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentLandlords', 'recentPayments'));
    }

    public function landlordDashboard()
    {
        $user = Auth::user();
        $landlord = $user->landlord;

        $properties = Property::where('landlord_id', $landlord->id)->get();
        $units = Unit::whereIn('property_id', $properties->pluck('id'))->get();
        $unitIds = $units->pluck('id');
        $payments = Payment::whereIn('unit_id', $unitIds)->get();
        $maintenance = MaintenanceRequest::whereIn('unit_id', $unitIds)->get();

        return view('landlord.dashboard', compact('properties', 'units', 'payments', 'maintenance'));
    }

    public function caretakerDashboard()
    {
        $user = Auth::user();
        $caretaker = $user->caretaker;

        $maintenanceRequests = MaintenanceRequest::with(['tenant.user', 'unit', 'task'])
            ->whereHas('task', function ($query) use ($caretaker) {
                $query->where('caretaker_id', $caretaker->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingTasks = $maintenanceRequests->whereIn('status', ['pending', 'in_progress'])->count();

        return view('caretaker.dashboard', compact('maintenanceRequests', 'pendingTasks'));
    }

    public function tenantDashboard()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $payments = Payment::where('tenant_id', $tenant->id)
                          ->orderBy('created_at', 'desc')
                          ->get();
        $maintenanceRequests = MaintenanceRequest::where('tenant_id', $tenant->id)
                                                ->orderBy('created_at', 'desc')
                                                ->get();

        $currentUnit = TenantOccupancy::with('unit.property')
            ->where('tenant_id', $tenant->id)
            ->whereNull('end_date')
            ->latest()
            ->first()
            ?->unit;

        return view('tenant.dashboard', compact('payments', 'maintenanceRequests', 'currentUnit'));
    }
}
