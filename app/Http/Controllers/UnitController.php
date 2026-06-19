<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Unit;
class UnitController extends Controller
{
    public function store(Request $request, $propertyId)
{
    $validated = $request->validate([
        'unit_number' => 'required|string|max:255',
        'rent_amount' => 'required|numeric|min:0',
    ]);

    $landlord = $request->user()->landlord;

    // Ownership check: the property must belong to THIS landlord
    $property = $landlord->properties()->findOrFail($propertyId);

    $unit = $property->units()->create([
        'unit_number' => $validated['unit_number'],
        'rent_amount' => $validated['rent_amount'],
    ]);

    return response()->json([
        'message' => 'Unit created successfully',
        'unit' => $unit,
    ], 201);
}

public function index(Request $request, $propertyId)
{
    $landlord = $request->user()->landlord;

    $property = $landlord->properties()->findOrFail($propertyId);

    $units = $property->units()->get();

    return response()->json([
        'units' => $units,
    ]);
}
}
