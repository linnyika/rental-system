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
public function index(Request $request)
{
    $landlord = $request->user()->landlord;
        $landlord = $request->user()->landlord;

    
 $requests = MaintenanceRequest::whereHas(
        'unit.property',
        function ($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        }
    )
    ->with(['tenant', 'unit'])
    ->latest()
    ->get();

    return response()->json([
        'requests' => $requests 
    ]);
    
}
public function caretakerRequests(Request $request)
{
     $caretaker = $request->user()->caretaker;

    
    $caretaker = $request->user()->caretaker;

    if (!$caretaker) {
        return response()->json([
            'message' => 'Caretaker profile not found'
        ], 404);
    }

    $requests = MaintenanceRequest::whereHas(
        'unit.property',
        function ($query) use ($caretaker) {
            $query->where('caretaker_id', $caretaker->id);
        }
    )
    ->with([
        'tenant',
        'unit',
        'unit.property'
    ])
    ->latest()
    ->get();

    return response()->json([
        'requests' => $requests
    ]);
}
public function updateStatus(Request $request, MaintenanceRequest $maintenanceRequest)
{
    $request->validate([
        'status' => 'required|in:pending,approved,rejected,in progress,completed'
    ]);

    $landlord = $request->user()->landlord;

    if (
        $maintenanceRequest->unit->property->landlord_id != $landlord->id
    ) {
        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }

    $maintenanceRequest->update([
        'status' => $request->status
    ]);

    return response()->json([
        'message' => 'Status updated successfully',
        'request' => $maintenanceRequest
    ]);
}
public function assignCaretaker(Request $request, MaintenanceRequest $maintenanceRequest)
{
    $request->validate([
        'caretaker_id' => 'required|exists:caretakers,id'
    ]);

    $landlord = $request->user()->landlord;

    if (
        $maintenanceRequest->unit->property->landlord_id != $landlord->id
    ) {
        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }

    $maintenanceRequest->update([
        'caretaker_id' => $request->caretaker_id,
        'status' => 'approved'
    ]);

    return response()->json([
        'message' => 'Caretaker assigned successfully',
        'request' => $maintenanceRequest
    ]);
}

}

