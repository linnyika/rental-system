<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Landlord;
use App\Models\Tenant;
use App\Models\Caretaker;
use App\Models\Unit;
use App\Models\TenantOccupancy;
<<<<<<< HEAD
use App\Models\Property;
use Illuminate\Support\Facades\DB;
=======
>>>>>>> 68c69cf5e34a6f8d87b126164bf53a9211d40f5e

class AuthController extends Controller
{
    // -------------------- WEB VIEWS --------------------
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // -------------------- WEB LOGIN --------------------
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();
            if (!$user instanceof User) {
                return back()->withErrors([
                    'email' => 'Unable to initialize authenticated session.',
                ]);
            }

            // Generate Sanctum token and store in session
            $token = $user->createToken('auth_token')->plainTextToken;
            session(['api_token' => $token]);
            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // -------------------- WEB REGISTER (Landlord only) --------------------
    public function register(Request $request)
    {
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

<<<<<<< HEAD
        $user = $this->createLandlordUser([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
=======
        $user = User::create([
            'full_name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'landlord',
        ]);

        Landlord::create([
            'user_id' => $user->id,
            'company_name' => $request->company_name ?? null,
            'phone' => $request->phone ?? null,
>>>>>>> 68c69cf5e34a6f8d87b126164bf53a9211d40f5e
        ]);

        Auth::login($user);
        $token = $user->createToken('auth_token')->plainTextToken;
        session(['api_token' => $token]);

        return redirect()->route('landlord.dashboard')
            ->with('success', 'Registration successful!');
    }

    // -------------------- WEB LOGOUT --------------------
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user instanceof User) {
            $user->tokens()->delete();
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session()->forget('api_token');
        return redirect('/login')->with('success', 'Logged out.');
    }

    // -------------------- API LOGIN (Sanctum) --------------------
    public function apiLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
<<<<<<< HEAD
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
            'email' => 'nullable|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'property_id' => 'required|integer|exists:properties,id',
=======
            'email' => 'required|email',
            'password' => 'required',
>>>>>>> 68c69cf5e34a6f8d87b126164bf53a9211d40f5e
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'role' => $user->role,
        ]);
    }

    // -------------------- API LOGOUT --------------------
    public function apiLogout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->successResponse(null, 'Logged out');
    }

    // -------------------- API CURRENT USER --------------------
    public function user(Request $request)
    {
        return $this->successResponse($request->user());
    }

    // -------------------- LANDLORD REGISTERS CARETAKER (API) --------------------
    public function registerCaretaker(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
        if ($validation) return $validation;

        $landlord = $request->user()->landlord;
        if (!$landlord) {
            return $this->errorResponse('Only landlords can register caretakers.', 403);
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
            'full_name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'caretaker',
        ]);

        $caretaker = Caretaker::create([
            'user_id' => $user->id,
            'landlord_id' => $landlord->id,
        ]);

<<<<<<< HEAD
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

=======
        return $this->successResponse($caretaker->load('user'), 'Caretaker registered', 201);
    }

    // -------------------- LANDLORD REGISTERS TENANT (API) --------------------
>>>>>>> 68c69cf5e34a6f8d87b126164bf53a9211d40f5e
    public function registerTenant(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'unit_id' => 'required|exists:units,id',
            'start_date' => 'required|date',
        ]);
        if ($validation) return $validation;

        $landlord = $request->user()->landlord;
        if (!$landlord) {
            return $this->errorResponse('Only landlords can register tenants.', 403);
        }

        // Ensure the unit belongs to this landlord
        $unit = Unit::where('id', $request->unit_id)
            ->whereHas('property', function ($query) use ($landlord) {
                $query->where('landlord_id', $landlord->id);
            })->first();

        if (!$unit) {
            return $this->errorResponse('Unit not found or not under your properties.', 404);
        }

        if ($unit->is_occupied) {
            return $this->errorResponse('Unit is already occupied.', 422);
        }

        $user = User::create([
            'full_name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'tenant',
        ]);

        $tenant = Tenant::create(['user_id' => $user->id]);

        TenantOccupancy::create([
            'tenant_id' => $tenant->id,
            'unit_id' => $unit->id,
            'start_date' => $request->start_date,
        ]);

        $unit->update(['is_occupied' => true]);

        return $this->successResponse([
            'tenant' => $tenant->load('user'),
            'unit' => $unit,
        ], 'Tenant registered', 201);
    }

    // -------------------- REDIRECT HELPER --------------------
    private function redirectBasedOnRole($user)
    {
        switch ($user->role) {
            case 'admin': return redirect()->intended(route('admin.dashboard'));
            case 'landlord': return redirect()->intended(route('landlord.dashboard'));
            case 'caretaker': return redirect()->intended(route('caretaker.dashboard'));
            case 'tenant': return redirect()->intended(route('tenant.dashboard'));
            default: return redirect('/');
        }
    }
<<<<<<< HEAD

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
=======
>>>>>>> 68c69cf5e34a6f8d87b126164bf53a9211d40f5e
}
