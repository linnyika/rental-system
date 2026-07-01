<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TenantController extends Controller
{
    /**
     * Get tenant profile.
     */
    public function profile(Request $request)
    {
        $tenant = $request->user()->tenant;
        return $this->successResponse($tenant->load('user'));
    }

    /**
     * Update tenant profile (user name/phone).
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
     * Get current unit/occupancy details for the tenant.
     */
    public function currentUnit(Request $request)
    {
        $tenant = $request->user()->tenant;
        $occupancy = $tenant->occupancies()
            ->whereNull('end_date')
            ->with('unit.property')
            ->latest()
            ->first();

        if (!$occupancy) {
            return $this->errorResponse('No current unit assigned.', 404);
        }

        return $this->successResponse([
            'occupancy' => $occupancy,
            'unit' => $occupancy->unit,
            'property' => $occupancy->unit->property,
        ]);
    }

    /**
     * List payment history for this tenant.
     */
    public function payments(Request $request)
    {
        $tenant = $request->user()->tenant;
        $payments = $tenant->payments()->with('unit.property')->latest()->get();
        return $this->successResponse($payments);
    }

    /**
     * List maintenance requests for this tenant.
     */
    public function maintenanceRequests(Request $request)
    {
        $tenant = $request->user()->tenant;
        $requests = $tenant->maintenanceRequests()->with('unit.property')->latest()->get();
        return $this->successResponse($requests);
    }
}
