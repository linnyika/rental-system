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
        $landlord = $request->user()->landlord;
        $requests = MaintenanceRequest::whereHas('unit.property', function ($q) use ($landlord) {
            $q->where('landlord_id', $landlord->id);
        })->with(['tenant.user', 'unit'])->latest()->get();

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
    }
}
