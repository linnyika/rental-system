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

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $stats = [
            'total_properties' => Property::count(),
            'total_units' => Unit::count(),
            'total_tenants' => Tenant::count(),
            'total_caretakers' => Caretaker::count(),
            'total_payments' => Payment::count(),
            'total_maintenance' => MaintenanceRequest::count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }

    public function landlordDashboard()
    {
        $user = Auth::user();
        $landlord = $user->landlord;

        $properties = Property::where('landlord_id', $landlord->id)->get();
        $units = Unit::whereIn('property_id', $properties->pluck('id'))->get();
        $payments = Payment::whereIn('property_id', $properties->pluck('id'))->get();
        $maintenance = MaintenanceRequest::whereIn('property_id', $properties->pluck('id'))->get();

        return view('landlord.dashboard', compact('properties', 'units', 'payments', 'maintenance'));
    }

    public function caretakerDashboard()
    {
        $user = Auth::user();
        $caretaker = $user->caretaker;

        $maintenanceRequests = MaintenanceRequest::where('assigned_to', $caretaker->id)
                                                ->orderBy('created_at', 'desc')
                                                ->get();
        $pendingTasks = $maintenanceRequests->where('status', 'pending')->count();

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

        $currentUnit = Unit::where('tenant_id', $tenant->id)->first();

        return view('tenant.dashboard', compact('payments', 'maintenanceRequests', 'currentUnit'));
    }
}
