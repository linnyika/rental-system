<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Landlord;
use App\Models\Tenant;
use App\Models\Caretaker;
use App\Models\Unit;
use App\Models\TenantOccupancy;
class AuthController extends Controller
{
  public function register(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|unique:users,phone',
        'email' => 'nullable|email|unique:users,email',
        'password' => 'required|string|min:6|confirmed',
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'phone' => $validated['phone'],
        'email' => $validated['email'] ?? null,
        'role' => 'landlord',
        'password' => $validated['password'],
    ]);

    // Every public signup is a landlord, so create their landlord record
    Landlord::create(['user_id' => $user->id]);

    $token = $user->createToken('mobile')->plainTextToken;

    return response()->json([
        'message' => 'Registration successful',
        'user' => $user,
        'token' => $token,
    ], 201);
}

public function login(Request $request)
{
    $validated = $request->validate([
        'phone' => 'required|string',
        'password' => 'required|string',
    ]);

    $user = User::where('phone', $validated['phone'])->first();

    if (! $user || ! Hash::check($validated['password'], $user->password)) {
        return response()->json([
            'message' => 'The provided credentials are incorrect.',
        ], 401);
    }

    $token = $user->createToken('mobile')->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'user' => $user,
        'token' => $token,
    ]);
}
public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Logged out successfully',
    ]);
}
public function registerCaretaker(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|unique:users,phone',
        'email' => 'nullable|email|unique:users,email',
        'password' => 'required|string|min:6|confirmed',
    ]);

    $landlord = $request->user()->landlord;

    $user = User::create([
        'name' => $validated['name'],
        'phone' => $validated['phone'],
        'email' => $validated['email'] ?? null,
        'role' => 'caretaker',
        'password' => $validated['password'],
    ]);

    $caretaker = Caretaker::create([
        'user_id' => $user->id,
        'landlord_id' => $landlord->id,
    ]);

    return response()->json([
        'message' => 'Caretaker registered successfully',
        'caretaker' => $caretaker->load('user'),
    ], 201);
}
public function registerTenant(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|unique:users,phone',
        'email' => 'nullable|email|unique:users,email',
        'password' => 'required|string|min:6|confirmed',
        'unit_id' => 'required|exists:units,id',
        'start_date' => 'required|date',
    ]);

    $landlord = $request->user()->landlord;

    // The unit must belong to one of THIS landlord's properties
    $unit = Unit::where('id', $validated['unit_id'])
        ->whereHas('property', function ($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        })
        ->first();

    if (! $unit) {
        return response()->json([
            'message' => 'Unit not found or does not belong to you.',
        ], 404);
    }

    // Restriction #4: cannot assign a tenant to an occupied unit
    if ($unit->is_occupied) {
        return response()->json([
            'message' => 'This unit is already occupied.',
        ], 422);
    }

    $user = User::create([
        'name' => $validated['name'],
        'phone' => $validated['phone'],
        'email' => $validated['email'] ?? null,
        'role' => 'tenant',
        'password' => $validated['password'],
    ]);

    $tenant = Tenant::create(['user_id' => $user->id]);

    // Link tenant to the unit
    TenantOccupancy::create([
        'tenant_id' => $tenant->id,
        'unit_id' => $unit->id,
        'start_date' => $validated['start_date'],
    ]);

    // Mark the unit occupied
    $unit->update(['is_occupied' => true]);

    return response()->json([
        'message' => 'Tenant registered successfully',
        'tenant' => $tenant->load('user'),
        'unit' => $unit,
    ], 201);
}
}
