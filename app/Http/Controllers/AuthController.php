<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
class AuthController extends Controller
{
  public function register(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|unique:users,phone',
        'email' => 'nullable|email|unique:users,email',
        'role' => ['required', Rule::in(['admin', 'landlord', 'caretaker', 'tenant'])],
        'password' => 'required|string|min:6|confirmed',
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'phone' => $validated['phone'],
        'email' => $validated['email'] ?? null,
        'role' => $validated['role'],
        'password' => $validated['password'],
    ]);

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
}
