<?php

namespace App\Http\Controllers;
use App\Models\MaintenanceRequest;
use App\Models\Task;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
public function store(Request $request)
{
    $validated = $request->validate([
        'description' => 'required|string|max:1000',
        'is_major' => 'boolean',
    ]);

    $tenant = $request->user()->tenant;
    $occupancy = $tenant->occupancies()->whereNull('end_date')->latest()->first();
    if (! $occupancy) {
        return response()->json(['message' => 'You are not assigned to a unit.'], 422);
    }
    $unit = $occupancy->unit;

    // Restriction #9: block duplicate OPEN request for same description on same unit
    $existing = MaintenanceRequest::where('unit_id', $unit->id)
        ->where('description', $validated['description'])
        ->whereIn('status', ['pending', 'approved', 'in_progress'])
        ->first();
    if ($existing) {
        return response()->json(['message' => 'An open request for this issue already exists.'], 422);
    }

    $req = MaintenanceRequest::create([
        'tenant_id' => $tenant->id,
        'unit_id' => $unit->id,
        'description' => $validated['description'],
        'is_major' => $validated['is_major'] ?? false,
        'status' => 'pending',
    ]);

    return response()->json(['message' => 'Maintenance request submitted', 'request' => $req], 201);
}
}
