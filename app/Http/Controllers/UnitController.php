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

    if (! $request->is('api/*')) {
        return view('landlord.units', compact('property', 'units'));
    }

    return response()->json([
        'units' => $units,
    ]);
}

public function availableUnits(Request $request)
{
    $landlord = $request->user()->landlord;

    $units = Unit::with('property')
        ->where('is_occupied', false)
        ->whereHas('property', function ($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        })
        ->orderBy('unit_number')
        ->get();

    return response()->json([
        'units' => $units,
    ]);
}
}
