<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    // -------------------- LIST (Web & API) --------------------
    public function index(Request $request)
    {
        $landlord = $request->user()->landlord;
        $properties = $landlord->properties()->with('units')->get();

        if ($request->expectsJson()) {
            return $this->successResponse($properties);
        }
        return view('landlord.properties', compact('properties'));
    }

    // -------------------- STORE --------------------
    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);
        if ($validation) return $validation;

        $landlord = $request->user()->landlord;
        $property = $landlord->properties()->create($request->only(['name', 'address']));

        return $this->successResponse($property, 'Property created', 201);
    }

    // -------------------- SHOW --------------------
    public function show(Request $request, Property $property)
    {
        $this->authorizeProperty($request->user(), $property);
        $property->load(['units', 'caretaker.user', 'landlord.user']);
        return $this->successResponse($property);
    }

    // -------------------- UPDATE --------------------
    public function update(Request $request, Property $property)
    {
        $this->authorizeProperty($request->user(), $property);
        $validation = $this->validateRequest($request, [
            'name' => 'sometimes|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);
        if ($validation) return $validation;

        $property->update($request->only(['name', 'address']));
        return $this->successResponse($property, 'Property updated');
    }

    // -------------------- DELETE --------------------
    public function destroy(Request $request, Property $property)
    {
        $this->authorizeProperty($request->user(), $property);
        $property->delete();
        return $this->successResponse(null, 'Property deleted');
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
