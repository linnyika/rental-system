<?php

namespace App\Http\Controllers;
use App\Models\MaintenanceRequest;
use App\Models\Task;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

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

    $landlordUser = $unit->property->landlord?->user;
    if ($landlordUser) {
        $this->createNotification(
            $landlordUser->id,
            'New Maintenance Request',
            'A new maintenance request has been submitted.',
            'maintenance'
        );
    }

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
public function caretakerTasks(Request $request)
{
    $caretaker = $request->user()->caretaker;

    $tasks = Task::where(
        'caretaker_id',
        $caretaker->id
    )
    ->with([
        'request',
        'request.tenant',
        'request.unit',
        'request.unit.property'
    ])
    ->latest()
    ->get();

    return response()->json([
        'tasks' => $tasks
    ]);
}
public function updateStatus(
    Request $request,
    MaintenanceRequest $maintenanceRequest
)
{
    $request->validate([
        'status' => 'required|in:approved,rejected'
    ]);

    $landlord = $request->user()->landlord;

    if (
        $maintenanceRequest->unit->property->landlord_id != $landlord->id
    ) {
        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }

    // Prevent updating already processed requests
    if (
        in_array(
            $maintenanceRequest->status,
            ['approved', 'rejected', 'completed']
        )
    ) {
        return response()->json([
            'message' => 'Request has already been processed'
        ], 422);
    }

    $maintenanceRequest->update([
        'status' => $request->status
    ]);

    if ($request->status === 'approved') {

        $caretakerId = $maintenanceRequest
            ->unit
            ->property
            ->caretaker_id;

        if (!$caretakerId) {
            return response()->json([
                'message' => 'No caretaker assigned to this property'
            ], 422);
        }

        // Prevent duplicate task creation
        if (!$maintenanceRequest->task) {

            Task::create([
                'maintenance_request_id' => $maintenanceRequest->id,
                'caretaker_id' => $caretakerId,
                'status' => 'assigned'
            ]);

            $caretakerUser = $maintenanceRequest->unit->property->caretaker?->user;
            if ($caretakerUser) {
                $this->createNotification(
                    $caretakerUser->id,
                    'New Task Assigned',
                    'A maintenance task has been assigned to you.',
                    'maintenance'
                );
            }
        }
    } elseif ($request->status === 'rejected') {
        $tenantUser = $maintenanceRequest->tenant?->user;
        if ($tenantUser) {
            $this->createNotification(
                $tenantUser->id,
                'Maintenance Request Rejected',
                'Your maintenance request has been rejected.',
                'maintenance'
            );
        }
    }

    return response()->json([
        'message' => 'Request updated successfully',
        'request' => $maintenanceRequest->fresh()
    ]);
}
public function startWork(Task $task)
{
    $task->update([
        'status' => 'in_progress'
    ]);

    ActivityLog::create([
        'caretaker_id' => $task->caretaker_id,
        'description' => 'Started maintenance work: ' .
            $task->request->description,
        'activity_date' => now()->toDateString(),
    ]);

    $tenantUser = $task->request?->tenant?->user;
    if ($tenantUser) {
        $this->createNotification(
            $tenantUser->id,
            'Maintenance Started',
            'Your maintenance request is now being worked on.',
            'maintenance'
        );
    }

    return response()->json([
        'message' => 'Task started',
        'task' => $task
    ]);
}
public function markWorkDone(Task $task)
{
    $task->update([
        'status' => 'done',
        'completed_at' => now()
    ]);

    ActivityLog::create([
        'caretaker_id' => $task->caretaker_id,
        'description' => 'Completed maintenance work: ' .
            $task->request->description,
        'activity_date' => now()->toDateString(),
    ]);

    $tenantUser = $task->request?->tenant?->user;
    if ($tenantUser) {
        $this->createNotification(
            $tenantUser->id,
            'Maintenance Completed',
            'Please confirm whether the maintenance work has been completed.',
            'maintenance'
        );
    }

    return response()->json([
        'message' => 'Task completed',
        'task' => $task
    ]);
}
public function confirmCompletion(Task $task)
{
    $task->update([
        'tenant_confirmed' => true
    ]);

    $task->request->update([
        'status' => 'completed'
    ]);

    $landlordUser = $task->request?->unit?->property?->landlord?->user;
    if ($landlordUser) {
        $this->createNotification(
            $landlordUser->id,
            'Maintenance Confirmed',
            'The tenant has confirmed the maintenance work.',
            'maintenance'
        );
    }

    return response()->json([
        'message' => 'Work confirmed'
    ]);
}

public function activityLogs(Request $request)
{
    $caretaker = $request->user()->caretaker;

    $logs = ActivityLog::where(
        'caretaker_id',
        $caretaker->id
    )
    ->latest()
    ->get();

    return response()->json([
        'logs' => $logs
    ]);
}
public function landlordActivityLogs(Request $request)
{
    $landlord = $request->user()->landlord;

    $logs = ActivityLog::whereHas(
        'caretaker.properties',
        function ($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        }
    )
    ->with('caretaker')
    ->latest()
    ->get();

    return response()->json([
        'logs' => $logs
    ]);
}

private function createNotification(int $userId, string $title, string $message, string $type): void
{
    Notification::create([
        'user_id' => $userId,
        'title' => $title,
        'message' => $message,
        'type' => $type,
        'is_read' => false,
    ]);
}
}
