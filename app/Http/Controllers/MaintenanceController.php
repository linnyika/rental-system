<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\Task;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Requests\Tenant\MaintenanceRequestRequest;
use App\Http\Requests\Landlord\ApproveMaintenanceRequest;

class MaintenanceController extends Controller
{
<<<<<<< HEAD
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

        $this->createActivityLog(
            $unit->property?->caretaker_id,
            'Maintenance request created: ' . $req->description
        );

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
        if (! $landlord) {
            return response()->json(['message' => 'Only landlords can view maintenance requests.'], 403);
        }

        $requests = MaintenanceRequest::whereHas('unit.property', function ($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        })
            ->with(['tenant.user', 'unit.property', 'task'])
            ->latest()
            ->get();

        return response()->json([
            'requests' => $requests,
        ]);
    }

    public function caretakerTasks(Request $request)
    {
        $caretaker = $request->user()->caretaker;
        if (! $caretaker) {
            return response()->json(['message' => 'Only caretakers can view tasks.'], 403);
        }

        $tasks = Task::where('caretaker_id', $caretaker->id)
            ->where(function ($query) {
                $query->whereIn('status', ['assigned', 'in_progress'])
                    ->orWhere(function ($doneQuery) {
                        $doneQuery->where('status', 'done')
                            ->where('tenant_confirmed', false);
                    });
            })
            ->with([
                'request',
                'request.tenant.user',
                'request.unit',
                'request.unit.property',
            ])
            ->latest()
            ->get();

        return response()->json([
            'tasks' => $tasks,
        ]);
    }

    public function updateStatus(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

=======
    // -------------------- TENANT SUBMITS REQUEST (API) --------------------
    public function store(MaintenanceRequestRequest $request)
    {
        $tenant = $request->user()->tenant;
        $occupancy = $tenant->occupancies()->whereNull('end_date')->latest()->first();
        if (!$occupancy) {
            return $this->errorResponse('You are not assigned to a unit.', 422);
        }
        $unit = $occupancy->unit;

        // Restriction: no duplicate open request for same description
        $existing = MaintenanceRequest::where('unit_id', $unit->id)
            ->where('description', $request->description)
            ->whereIn('status', ['pending', 'approved', 'in_progress'])
            ->first();
        if ($existing) {
            return $this->errorResponse('An open request for this issue already exists.', 422);
        }

        $request = MaintenanceRequest::create([
            'tenant_id' => $tenant->id,
            'unit_id' => $unit->id,
            'description' => $request->description,
            'is_major' => $request->is_major ?? false,
            'status' => 'pending',
        ]);

        // Notify landlord
        $landlordUser = $unit->property->landlord?->user;
        if ($landlordUser) {
            $this->createNotification($landlordUser->id, 'New Maintenance Request', 'A request has been submitted.', 'maintenance');
        }

        return $this->successResponse($request, 'Request submitted', 201);
    }

    // -------------------- LANDLORD LIST REQUESTS (API) --------------------
    public function index(Request $request)
    {
        $this->ensureRole($request, ['landlord', 'admin']);
>>>>>>> 68c69cf5e34a6f8d87b126164bf53a9211d40f5e
        $landlord = $request->user()->landlord;
        $requests = MaintenanceRequest::whereHas('unit.property', function ($q) use ($landlord) {
            $q->where('landlord_id', $landlord->id);
        })->with(['tenant.user', 'unit'])->latest()->get();

<<<<<<< HEAD
        if ($maintenanceRequest->unit->property->landlord_id != $landlord->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        if (in_array($maintenanceRequest->status, ['approved', 'rejected', 'completed'], true)) {
            return response()->json([
                'message' => 'Request has already been processed',
            ], 422);
        }

        if ($request->status === 'approved' && ! $maintenanceRequest->unit->property->caretaker_id) {
            return response()->json([
                'message' => 'No caretaker assigned to this property',
            ], 422);
        }

        $maintenanceRequest->update([
            'status' => $request->status,
        ]);

        if ($request->status === 'approved') {
            $task = $maintenanceRequest->task;
            if (! $task) {
                $task = Task::create([
                    'maintenance_request_id' => $maintenanceRequest->id,
                    'caretaker_id' => $maintenanceRequest->unit->property->caretaker_id,
                    'status' => 'assigned',
                ]);
            }

            $this->createActivityLog(
                $task->caretaker_id,
                'Maintenance request approved: ' . $maintenanceRequest->description
            );
            $this->createActivityLog(
                $task->caretaker_id,
                'Task generated and assigned: ' . $maintenanceRequest->description
            );

            $tenantUser = $maintenanceRequest->tenant?->user;
            if ($tenantUser) {
                $this->createNotification(
                    $tenantUser->id,
                    'Maintenance Request Approved',
                    'Your maintenance request has been approved and assigned to a caretaker.',
                    'maintenance'
                );
            }
=======
        return $this->successResponse($requests);
    }

    // -------------------- CARETAKER LIST TASKS (API) --------------------
    public function caretakerTasks(Request $request)
    {
        $this->ensureRole($request, 'caretaker');
        $caretaker = $request->user()->caretaker;
        $tasks = Task::where('caretaker_id', $caretaker->id)
            ->with(['request.tenant.user', 'request.unit.property'])
            ->latest()->get();

        return $this->successResponse($tasks);
    }

    // -------------------- LANDLORD APPROVES/REJECTS (API) --------------------
    public function updateStatus(ApproveMaintenanceRequest $request, MaintenanceRequest $maintenanceRequest)
    {
        $this->ensureRole($request, 'landlord');

        $landlord = $request->user()->landlord;
        if ($maintenanceRequest->unit->property->landlord_id !== $landlord->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        if (in_array($maintenanceRequest->status, ['approved', 'rejected', 'completed'])) {
            return $this->errorResponse('Request already processed.', 422);
        }

        $maintenanceRequest->update(['status' => $request->status]);
>>>>>>> 68c69cf5e34a6f8d87b126164bf53a9211d40f5e

        if ($request->status === 'approved') {
            $caretakerId = $maintenanceRequest->unit->property->caretaker_id;
            if (!$caretakerId) {
                return $this->errorResponse('No caretaker assigned to this property.', 422);
            }

            // Prevent duplicate task
            if (!$maintenanceRequest->task) {
                Task::create([
                    'maintenance_request_id' => $maintenanceRequest->id,
                    'caretaker_id' => $caretakerId,
                    'status' => 'assigned',
                ]);

                $caretakerUser = $maintenanceRequest->unit->property->caretaker?->user;
                if ($caretakerUser) {
                    $this->createNotification($caretakerUser->id, 'New Task Assigned', 'A task has been assigned to you.', 'maintenance');
                }
            }
        } elseif ($request->status === 'rejected') {
            $tenantUser = $maintenanceRequest->tenant?->user;
            if ($tenantUser) {
                $this->createNotification($tenantUser->id, 'Request Rejected', 'Your maintenance request was rejected.', 'maintenance');
            }
        }

<<<<<<< HEAD
        if ($request->status === 'rejected') {
            $this->createActivityLog(
                $maintenanceRequest->unit->property?->caretaker_id,
                'Maintenance request rejected: ' . $maintenanceRequest->description
            );

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
            'request' => $maintenanceRequest->fresh(['task']),
        ]);
    }

    public function startWork(Request $request, Task $task)
    {
        $caretaker = $request->user()->caretaker;
        if (! $caretaker || $task->caretaker_id !== $caretaker->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (! in_array($task->status, ['assigned'], true)) {
            return response()->json(['message' => 'Task cannot be started in its current status.'], 422);
        }

        $task->update([
            'status' => 'in_progress',
        ]);

        $this->createActivityLog(
            $task->caretaker_id,
            'Task started: ' . $task->request->description
        );

        $tenantUser = $task->request?->tenant?->user;
        if ($tenantUser) {
            $this->createNotification(
                $tenantUser->id,
                'Maintenance Started',
                'Your maintenance request is now being worked on.',
                'maintenance'
            );
        }

        $landlordUser = $task->request?->unit?->property?->landlord?->user;
        if ($landlordUser) {
            $this->createNotification(
                $landlordUser->id,
                'Maintenance Started',
                'The assigned caretaker has started maintenance work.',
                'maintenance'
            );
        }

        return response()->json([
            'message' => 'Task started',
            'task' => $task->fresh(['request', 'request.unit', 'request.unit.property']),
        ]);
    }

    public function markWorkDone(Request $request, Task $task)
    {
        $caretaker = $request->user()->caretaker;
        if (! $caretaker || $task->caretaker_id !== $caretaker->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (! in_array($task->status, ['in_progress'], true)) {
            return response()->json(['message' => 'Task cannot be completed in its current status.'], 422);
        }

        $task->update([
            'status' => 'done',
            'completed_at' => now(),
        ]);

        $this->createActivityLog(
            $task->caretaker_id,
            'Task completed: ' . $task->request->description
        );

        $tenantUser = $task->request?->tenant?->user;
        if ($tenantUser) {
            $this->createNotification(
                $tenantUser->id,
                'Maintenance Completed',
                'Please confirm whether the maintenance work has been completed.',
                'maintenance'
            );
        }

        $landlordUser = $task->request?->unit?->property?->landlord?->user;
        if ($landlordUser) {
            $this->createNotification(
                $landlordUser->id,
                'Maintenance Completed',
                'The assigned caretaker has marked a maintenance task as completed.',
                'maintenance'
            );
        }

        return response()->json([
            'message' => 'Task completed',
            'task' => $task->fresh(['request', 'request.unit', 'request.unit.property']),
        ]);
    }

    public function confirmCompletion(Request $request, Task $task)
    {
        $tenant = $request->user()->tenant;
        if (! $tenant || $task->request?->tenant_id !== $tenant->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($task->status !== 'done') {
            return response()->json(['message' => 'Only completed tasks can be confirmed.'], 422);
        }

        if ($task->tenant_confirmed) {
            return response()->json(['message' => 'Task already confirmed.'], 422);
        }

        $task->update([
            'tenant_confirmed' => true,
        ]);

        $task->request->update([
            'status' => 'completed',
        ]);

        $this->createActivityLog(
            $task->caretaker_id,
            'Tenant confirmed maintenance completion: ' . $task->request->description
        );

        $landlordUser = $task->request?->unit?->property?->landlord?->user;
        if ($landlordUser) {
            $this->createNotification(
                $landlordUser->id,
                'Maintenance Confirmed',
                'The tenant has confirmed the maintenance work.',
                'maintenance'
            );
        }

        $caretakerUser = $task->caretaker?->user;
        if ($caretakerUser) {
            $this->createNotification(
                $caretakerUser->id,
                'Maintenance Confirmed',
                'The tenant has confirmed your completed maintenance work.',
                'maintenance'
            );
        }

        return response()->json([
            'message' => 'Work confirmed',
        ]);
    }

    public function activityLogs(Request $request)
    {
        $caretaker = $request->user()->caretaker;

        $logs = ActivityLog::where('caretaker_id', $caretaker->id)
            ->latest()
            ->get();

        return response()->json([
            'logs' => $logs,
        ]);
    }

    public function tenantRequests(Request $request)
    {
        $tenant = $request->user()->tenant;

        $requests = MaintenanceRequest::where('tenant_id', $tenant->id)
            ->with(['unit', 'unit.property', 'task'])
            ->latest()
            ->get();

        return response()->json([
            'requests' => $requests,
        ]);
    }

    public function tenantCompletedTasks(Request $request)
    {
        $tenant = $request->user()->tenant;

        $tasks = Task::whereHas('request', function ($query) use ($tenant) {
            $query->where('tenant_id', $tenant->id);
        })
            ->where('status', 'done')
            ->where('tenant_confirmed', false)
            ->with(['request', 'request.unit', 'request.unit.property'])
            ->latest()
            ->get();

        return response()->json([
            'tasks' => $tasks,
        ]);
    }

    public function landlordActivityLogs(Request $request)
    {
        $landlord = $request->user()->landlord;

        $logs = ActivityLog::whereHas('caretaker.properties', function ($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        })
            ->with('caretaker')
            ->latest()
            ->get();

        return response()->json([
            'logs' => $logs,
        ]);
    }

    private function createActivityLog($caretakerId, string $description): void
    {
        if (! $caretakerId) {
            return;
        }

        ActivityLog::create([
            'caretaker_id' => $caretakerId,
            'description' => $description,
            'activity_date' => now()->toDateString(),
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
=======
        return $this->successResponse($maintenanceRequest, 'Request updated');
    }

    // -------------------- CARETAKER START WORK (API) --------------------
    public function startWork(Request $request, Task $task)
    {
        $this->ensureRole($request, 'caretaker');
        $caretaker = $request->user()->caretaker;
        if ($task->caretaker_id !== $caretaker->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $task->update(['status' => 'in_progress']);

        ActivityLog::create([
            'caretaker_id' => $task->caretaker_id,
            'description' => 'Started work: ' . $task->request->description,
            'activity_date' => now()->toDateString(),
        ]);

        $tenantUser = $task->request?->tenant?->user;
        if ($tenantUser) {
            $this->createNotification($tenantUser->id, 'Maintenance Started', 'Work has started on your request.', 'maintenance');
        }

        return $this->successResponse($task, 'Task started');
    }

    // -------------------- CARETAKER MARK DONE (API) --------------------
    public function markWorkDone(Request $request, Task $task)
    {
        $this->ensureRole($request, 'caretaker');
        $caretaker = $request->user()->caretaker;
        if ($task->caretaker_id !== $caretaker->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $task->update([
            'status' => 'done',
            'completed_at' => now(),
        ]);

        ActivityLog::create([
            'caretaker_id' => $task->caretaker_id,
            'description' => 'Completed work: ' . $task->request->description,
            'activity_date' => now()->toDateString(),
        ]);

        $tenantUser = $task->request?->tenant?->user;
        if ($tenantUser) {
            $this->createNotification($tenantUser->id, 'Maintenance Completed', 'Please confirm the work.', 'maintenance');
        }

        return $this->successResponse($task, 'Task completed');
    }

    // -------------------- TENANT CONFIRMS COMPLETION (API) --------------------
    public function confirmCompletion(Request $request, Task $task)
    {
        $this->ensureRole($request, 'tenant');
        $tenant = $request->user()->tenant;
        // Ensure the task belongs to this tenant's unit
        if ($task->request->tenant_id !== $tenant->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $task->update(['tenant_confirmed' => true]);
        $task->request->update(['status' => 'completed']);

        $landlordUser = $task->request?->unit?->property?->landlord?->user;
        if ($landlordUser) {
            $this->createNotification($landlordUser->id, 'Maintenance Confirmed', 'Tenant confirmed work completion.', 'maintenance');
        }

        return $this->successResponse(null, 'Work confirmed');
    }

    // -------------------- CARETAKER ACTIVITY LOGS (API) --------------------
    public function activityLogs(Request $request)
    {
        $this->ensureRole($request, 'caretaker');
        $caretaker = $request->user()->caretaker;
        $logs = ActivityLog::where('caretaker_id', $caretaker->id)->latest()->get();
        return $this->successResponse($logs);
    }

    // -------------------- LANDLORD VIEWS ALL ACTIVITY LOGS (API) --------------------
    public function landlordActivityLogs(Request $request)
    {
        $this->ensureRole($request, 'landlord');
        $landlord = $request->user()->landlord;
        $logs = ActivityLog::whereHas('caretaker.properties', function ($q) use ($landlord) {
            $q->where('landlord_id', $landlord->id);
        })->with('caretaker')->latest()->get();

        return $this->successResponse($logs);
>>>>>>> 68c69cf5e34a6f8d87b126164bf53a9211d40f5e
    }
}
