<?php

namespace App\Http\Controllers;

use App\Models\Landlord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LandlordController extends Controller
{
    /**
     * Get landlord profile (for the authenticated user).
     */
    public function profile(Request $request)
    {
        $landlord = $request->user()->landlord;
        return $this->successResponse($landlord->load('user'));
    }

    /**
     * Update landlord profile.
     */
    public function updateProfile(Request $request)
    {
        $landlord = $request->user()->landlord;

        $validator = Validator::make($request->all(), [
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $landlord->update($request->only(['company_name', 'phone']));
        return $this->successResponse($landlord->fresh()->load('user'), 'Profile updated');
    }

    /**
     * List all properties owned by this landlord (with units).
     */
    public function properties(Request $request)
    {
        $landlord = $request->user()->landlord;
        $properties = $landlord->properties()->with('units')->get();
        return $this->successResponse($properties);
    }

    /**
     * List all tenants under this landlord's properties.
     */
    public function tenants(Request $request)
    {
        $landlord = $request->user()->landlord;
        $tenants = \App\Models\Tenant::whereHas('occupancies.unit.property', function ($q) use ($landlord) {
            $q->where('landlord_id', $landlord->id);
        })->with('user')->get()->unique('id')->values();

        return $this->successResponse($tenants);
    }

    /**
     * List all caretakers under this landlord.
     */
    public function caretakers(Request $request)
    {
        $landlord = $request->user()->landlord;
        $caretakers = $landlord->caretakers()->with('user')->get();
        return $this->successResponse($caretakers);
    }
}
