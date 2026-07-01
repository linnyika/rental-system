<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Property;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    // -------------------- LIST (under property) --------------------
    public function index(Request $request, Property $property)
    {
        $this->authorizeProperty($request->user(), $property);
        $units = $property->units()->get();
        return $this->successResponse($units);
    }

    // -------------------- STORE --------------------
    public function store(Request $request, Property $property)
    {
        $this->authorizeProperty($request->user(), $property);
        $validation = $this->validateRequest($request, [
            'unit_number' => 'required|string|max:50',
            'rent_amount' => 'required|numeric|min:0',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'size_sqft' => 'nullable|integer|min:0',
            'status' => 'nullable|in:available,occupied,maintenance',
        ]);
        if ($validation) return $validation;

        $unit = $property->units()->create($request->all());
        return $this->successResponse($unit, 'Unit created', 201);
    }

    // -------------------- SHOW --------------------
    public function show(Request $request, Property $property, Unit $unit)
    {
        $this->authorizeProperty($request->user(), $property);
        if ($unit->property_id !== $property->id) {
            abort(404);
        }
        return $this->successResponse($unit);
    }

    // -------------------- UPDATE --------------------
    public function update(Request $request, Property $property, Unit $unit)
    {
        $this->authorizeProperty($request->user(), $property);
        if ($unit->property_id !== $property->id) {
            abort(404);
        }

        $validation = $this->validateRequest($request, [
            'unit_number' => 'sometimes|string|max:50',
            'rent_amount' => 'sometimes|numeric|min:0',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'size_sqft' => 'nullable|integer|min:0',
            'status' => 'nullable|in:available,occupied,maintenance',
            'is_occupied' => 'sometimes|boolean',
        ]);
        if ($validation) return $validation;

        $unit->update($request->all());
        return $this->successResponse($unit, 'Unit updated');
    }

    // -------------------- DELETE --------------------
    public function destroy(Request $request, Property $property, Unit $unit)
    {
        $this->authorizeProperty($request->user(), $property);
        if ($unit->property_id !== $property->id) {
            abort(404);
        }
        // Prevent deletion if occupied
        if ($unit->is_occupied) {
            return $this->errorResponse('Cannot delete an occupied unit', 422);
        }
        $unit->delete();
        return $this->successResponse(null, 'Unit deleted');
    }

    // -------------------- AUTHORIZATION HELPER --------------------
    private function authorizeProperty($user, $property)
    {
        if ($user->role === 'admin') return;
        $landlord = $user->landlord;
        if (!$landlord || $property->landlord_id !== $landlord->id) {
            abort(403, 'Unauthorized');
        }
    }
}
