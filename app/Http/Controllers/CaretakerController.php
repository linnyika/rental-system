<?php

namespace App\Http\Controllers;

use App\Models\Caretaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CaretakerController extends Controller
{
    /**
     * Get caretaker profile.
     */
    public function profile(Request $request)
    {
        $caretaker = $request->user()->caretaker;
        return $this->successResponse($caretaker->load('user'));
    }

    /**
     * Update caretaker profile (only name/phone via user).
     * Typically caretaker profile has no extra fields, but we allow updating user info.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $user->update($request->only(['name', 'phone']));
        return $this->successResponse($user->fresh(), 'Profile updated');
    }

    /**
     * List properties assigned to this caretaker.
     */
    public function properties(Request $request)
    {
        $caretaker = $request->user()->caretaker;
        $properties = $caretaker->properties()->with('landlord.user')->get();
        return $this->successResponse($properties);
    }

    /**
     * List units assigned to this caretaker (via properties).
     */
    public function units(Request $request)
    {
        $caretaker = $request->user()->caretaker;
        $units = \App\Models\Unit::whereHas('property', function ($q) use ($caretaker) {
            $q->where('caretaker_id', $caretaker->id);
        })->with('property')->get();

        return $this->successResponse($units);
    }

    /**
     * List tenants under the properties this caretaker manages.
     */
    public function tenants(Request $request)
    {
        $caretaker = $request->user()->caretaker;
        $tenants = \App\Models\Tenant::whereHas('occupancies.unit.property', function ($q) use ($caretaker) {
            $q->where('caretaker_id', $caretaker->id);
        })->with('user')->get()->unique('id')->values();

        return $this->successResponse($tenants);
    }
}
