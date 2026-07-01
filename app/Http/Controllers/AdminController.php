<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Landlord;
use App\Models\Tenant;
use App\Models\Caretaker;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Payment;
use App\Models\MaintenanceRequest;
use App\Models\TenantOccupancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends \Illuminate\Routing\Controller
{
    // -------------------- DASHBOARD (Web) --------------------
    public function dashboard()
    {
        $totalUnits = Unit::count();
        $occupied = Unit::where('is_occupied', true)->count();

        $stats = [
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

        $recentRegistrations = User::with(['landlord', 'caretaker', 'tenant'])
            ->latest()->take(10)->get();

        $recentPayments = Payment::with(['tenant.user', 'unit.property', 'landlord.user'])
            ->latest('payment_date')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentRegistrations', 'recentPayments'));
    }

    // -------------------- LANDLORDS LIST (Web) --------------------
    public function landlords()
    {
        $landlords = Landlord::with('user')->get()->sortBy('user.name');
        return view('admin.reports.landlords.index', compact('landlords'));
    }

    public function landlordShow(Landlord $landlord)
    {
        $landlord->load(['user', 'properties.units', 'properties.caretaker.user']);
        $properties = $landlord->properties;
        $units = $properties->flatMap->units;
        $payments = Payment::where('landlord_id', $landlord->id)->with('tenant.user')->latest('payment_date')->get();
        $tenants = Tenant::whereHas('occupancies.unit.property', function ($q) use ($landlord) {
            $q->where('landlord_id', $landlord->id);
        })->with('user')->get()->unique('id');

        return view('admin.reports.landlords.show', compact('landlord', 'properties', 'units', 'payments', 'tenants'));
    }

    // -------------------- TENANTS LIST (Web) --------------------
    public function tenants()
    {
        $tenants = Tenant::with('user')->get()->sortBy('user.name');
        return view('admin.reports.tenants.index', compact('tenants'));
    }

    public function tenantShow(Tenant $tenant)
    {
        $tenant->load(['user', 'occupancies.unit.property', 'payments']);
        $currentOccupancy = $tenant->occupancies->whereNull('end_date')->first();
        $currentUnit = $currentOccupancy?->unit;
        $outstanding = $tenant->payments->whereIn('status', ['pending', 'rejected'])->sum('amount');
        return view('admin.reports.tenants.show', compact('tenant', 'currentOccupancy', 'currentUnit', 'outstanding'));
    }

    // -------------------- CARETAKERS LIST (Web) --------------------
    public function caretakers()
    {
        $caretakers = Caretaker::with('user')->get()->sortBy('user.name');
        return view('admin.reports.caretakers.index', compact('caretakers'));
    }

    public function caretakerShow(Caretaker $caretaker)
    {
        $caretaker->load(['user', 'properties.units', 'activityLogs']);
        return view('admin.reports.caretakers.show', compact('caretaker'));
    }

    // -------------------- PROPERTIES LIST (Web) --------------------
    public function properties()
    {
        $properties = Property::with('landlord.user')->get()->sortBy('name');
        return view('admin.reports.properties.index', compact('properties'));
    }

    public function propertyShow(Property $property)
    {
        $property->load(['landlord.user', 'caretaker.user', 'units']);
        $units = $property->units;
        $tenants = Tenant::whereHas('occupancies.unit', function ($q) use ($property) {
            $q->where('property_id', $property->id);
        })->with('user')->get()->unique('id');
        $payments = Payment::whereIn('unit_id', $units->pluck('id'))->latest('payment_date')->get();
        return view('admin.reports.properties.show', compact('property', 'units', 'tenants', 'payments'));
    }

    // -------------------- API: CRUD for Users (Admin Only) --------------------
    public function indexUsers(Request $request)
    {
        $users = User::with(['landlord', 'tenant', 'caretaker'])->paginate(20);
        return response()->json($users);
    }

    public function storeUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,landlord,caretaker,tenant',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Create corresponding profile if role is not admin
        if ($request->role === 'landlord') {
            Landlord::create(['user_id' => $user->id]);
        } elseif ($request->role === 'tenant') {
            Tenant::create(['user_id' => $user->id]);
        } elseif ($request->role === 'caretaker') {
            Caretaker::create(['user_id' => $user->id]);
        }

        return response()->json(['message' => 'User created', 'user' => $user], 201);
    }

    public function updateUser(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'sometimes|in:admin,landlord,caretaker,tenant',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update($request->only(['name', 'email', 'phone', 'role']));
        return response()->json(['message' => 'User updated', 'user' => $user]);
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }
}