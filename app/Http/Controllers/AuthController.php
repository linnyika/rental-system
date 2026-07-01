<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Landlord;
use App\Models\Tenant;
use App\Models\Caretaker;
use App\Models\Unit;
use App\Models\TenantOccupancy;
use App\Models\Property;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Show registration form (only for landlords)
     */
    public function showRegisterForm()
    {
        return view('register');
    }

    /**
     * Handle login request with Sanctum token
     */
    public function login(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Attempt to login with email and password
        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Authentication passed
            $request->session()->regenerate();
            
            /** @var User $user */
            $user = Auth::user();
            
            // Generate Sanctum token for API access
            $token = $user->createToken('auth_token')->plainTextToken;
            
            // Store token in session for subsequent requests
            session(['api_token' => $token]);
            
            // Redirect based on user role
            return $this->redirectBasedOnRole($user);
        }

        // Authentication failed
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle landlord registration
     */
    public function register(Request $request)
    {
        // Only allow landlord registration
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'accepted',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = $this->createLandlordUser([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
        ]);

        // Log the user in
        Auth::login($user);
        
        // Generate token
        /** @var User $user */
        $token = $user->createToken('auth_token')->plainTextToken;
        session(['api_token' => $token]);

        return redirect()->route('landlord.dashboard')
            ->with('success', 'Registration successful! Welcome to the Rental System.');
    }

    public function registerCaretaker(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
            'email' => 'nullable|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'property_id' => 'required|integer|exists:properties,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $landlord = $request->user()->landlord;

        if (! $landlord) {
            return response()->json(['message' => 'Only landlords can register caretakers.'], 403);
        }

        $property = Property::where('id', $request->property_id)
            ->where('landlord_id', $landlord->id)
            ->first();

        if (! $property) {
            return response()->json([
                'message' => 'Selected property was not found or does not belong to you.',
            ], 404);
        }

        if ($property->caretaker_id) {
            return response()->json([
                'message' => 'Selected property already has a caretaker assigned.',
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'caretaker',
        ]);

        $caretaker = Caretaker::create([
            'user_id' => $user->id,
            'landlord_id' => $landlord->id,
        ]);

        $property->update([
            'caretaker_id' => $caretaker->id,
        ]);

        return response()->json([
            'message' => 'Caretaker registered successfully',
            'caretaker' => $caretaker->load('user'),
            'property' => $property,
        ], 201);
    }

    public function adminLandlords(Request $request)
    {
        $landlords = Landlord::with('user')
            ->withCount('properties')
            ->latest()
            ->get()
            ->map(function (Landlord $landlord) {
                return [
                    'id' => $landlord->id,
                    'user_id' => $landlord->user_id,
                    'name' => $landlord->user?->name,
                    'email' => $landlord->user?->email,
                    'phone' => $landlord->user?->phone,
                    'properties_count' => $landlord->properties_count,
                    'created_at' => optional($landlord->created_at)->toDateTimeString(),
                ];
            })
            ->values();

        return response()->json([
            'landlords' => $landlords,
        ]);
    }

    public function adminUpdateLandlord(Request $request, Landlord $landlord)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $landlord->user_id,
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $landlord->user;
        if (! $user) {
            return response()->json(['message' => 'Landlord user account not found.'], 404);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return response()->json([
            'message' => 'Landlord updated successfully',
            'landlord' => [
                'id' => $landlord->id,
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
        ]);
    }

    public function adminDeleteLandlord(Request $request, Landlord $landlord)
    {
        DB::transaction(function () use ($landlord) {
            $propertyIds = Property::where('landlord_id', $landlord->id)->pluck('id');
            $unitIds = Unit::whereIn('property_id', $propertyIds)->pluck('id');

            $tenantIds = TenantOccupancy::whereIn('unit_id', $unitIds)
                ->pluck('tenant_id')
                ->unique()
                ->values();

            $tenantUserIds = Tenant::whereIn('id', $tenantIds)->pluck('user_id');
            $caretakerUserIds = Caretaker::where('landlord_id', $landlord->id)->pluck('user_id');
            $landlordUserId = $landlord->user_id;

            $userIdsToDelete = $tenantUserIds
                ->merge($caretakerUserIds)
                ->push($landlordUserId)
                ->filter()
                ->unique()
                ->values();

            if ($userIdsToDelete->isNotEmpty()) {
                User::whereIn('id', $userIdsToDelete)->get()->each(function (User $user) {
                    $user->delete();
                });
            } else {
                $landlord->delete();
            }
        });

        return response()->json([
            'message' => 'Landlord and related records deleted successfully',
        ]);
    }

    public function registerTenant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
            'email' => 'nullable|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'unit_id' => 'required|exists:units,id',
            'start_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $landlord = $request->user()->landlord;

        if (! $landlord) {
            return response()->json(['message' => 'Only landlords can register tenants.'], 403);
        }

        $unit = Unit::where('id', $request->unit_id)
            ->whereHas('property', function ($query) use ($landlord) {
                $query->where('landlord_id', $landlord->id);
            })
            ->first();

        if (! $unit) {
            return response()->json([
                'message' => 'Unit not found or does not belong to you.',
            ], 404);
        }

        if ($unit->is_occupied) {
            return response()->json([
                'message' => 'This unit is already occupied.',
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'tenant',
        ]);

        $tenant = Tenant::create([
            'user_id' => $user->id,
        ]);

        TenantOccupancy::create([
            'tenant_id' => $tenant->id,
            'unit_id' => $unit->id,
            'start_date' => $request->start_date,
        ]);

        $unit->update(['is_occupied' => true]);

        return response()->json([
            'message' => 'Tenant registered successfully',
            'tenant' => $tenant->load('user'),
            'unit' => $unit,
        ], 201);
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        // Revoke all tokens for the user
        /** @var User|null $user */
        $user = Auth::user();
        if ($user) {
            $user->tokens()->delete();
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Clear the API token from session
        session()->forget('api_token');
        
        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Redirect user based on role
     */
    private function redirectBasedOnRole($user)
    {
        switch ($user->role) {
            case 'admin':
                return redirect()->intended(route('admin.dashboard'));
            case 'landlord':
                return redirect()->intended(route('landlord.dashboard'));
            case 'caretaker':
                return redirect()->intended(route('caretaker.dashboard'));
            case 'tenant':
                return redirect()->intended(route('tenant.dashboard'));
            default:
                return redirect()->intended('/');
        }
    }

    /**
     * Get authenticated user details with token (API)
     */
    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
            'token' => $request->bearerToken()
        ]);
    }

    /**
     * API Login (for mobile/SPA)
     */
    public function apiLogin(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'role' => $user->role
        ]);
    }

    public function apiLandlordSignup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $this->createLandlordUser([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'role' => $user->role,
            'message' => 'Landlord account created successfully',
        ], 201);
    }

    /**
     * API Logout
     */
    public function apiLogout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    private function createLandlordUser(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => 'landlord',
        ]);

        Landlord::create([
            'user_id' => $user->id,
        ]);

        return $user->fresh(['landlord']);
    }
}

/*
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
*/
