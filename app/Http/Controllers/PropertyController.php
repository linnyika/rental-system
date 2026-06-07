<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;


class PropertyController extends Controller
{
   public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'address' => 'nullable|string|max:255',
    ]);

    $landlord = $request->user()->landlord;

    $property = $landlord->properties()->create([
        'name' => $validated['name'],
        'address' => $validated['address'] ?? null,
    ]);

    return response()->json([
        'message' => 'Property created successfully',
        'property' => $property,
    ], 201);
}

public function index(Request $request)
{
    $landlord = $request->user()->landlord;

    $properties = $landlord->properties()->get();

    return response()->json([
        'properties' => $properties,
    ]);
}
}
