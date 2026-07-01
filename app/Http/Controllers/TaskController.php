<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\Caretaker\TaskRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = $this->baseTaskQueryForUser($request->user())->latest()->get();
        return $this->taskListResponse($request, $tasks, 'Tasks fetched');
    }

    public function pending(Request $request)
    {
        $tasks = $this->baseTaskQueryForUser($request->user())
            ->whereIn('status', ['assigned', 'pending'])
            ->latest()
            ->get();

        return $this->taskListResponse($request, $tasks, 'Pending tasks fetched');
    }

    public function inProgress(Request $request)
    {
        $tasks = $this->baseTaskQueryForUser($request->user())
            ->where('status', 'in_progress')
            ->latest()
            ->get();

        return $this->taskListResponse($request, $tasks, 'In-progress tasks fetched');
    }

    public function completed(Request $request)
    {
        $tasks = $this->baseTaskQueryForUser($request->user())
            ->whereIn('status', ['completed', 'done'])
            ->latest()
            ->get();

        return $this->taskListResponse($request, $tasks, 'Completed tasks fetched');
    }

    public function show(Request $request, Task $task)
    {
        $this->authorizeTaskAccess($request->user(), $task);

        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->successResponse($task);
        }

        return view('caretaker.tasks', ['tasks' => collect([$task])]);
    }

    public function start(Request $request, Task $task)
    {
        return $this->updateStatusByRoute($request, $task, 'in_progress', 'Task started');
    }

    public function complete(Request $request, Task $task)
    {
        return $this->updateStatusByRoute($request, $task, 'completed', 'Task completed');
    }

    public function confirm(Request $request, Task $task)
    {
        $this->authorizeTaskAccess($request->user(), $task);

        $task->update(['tenant_confirmed' => true]);

        return $request->expectsJson() || $request->is('api/*')
            ? $this->successResponse($task, 'Task confirmed')
            : back()->with('success', 'Task confirmed');
    }

    public function report(Request $request)
    {
        $query = $this->baseTaskQueryForUser($request->user());
        $stats = [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->whereIn('status', ['assigned', 'pending'])->count(),
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            'completed' => (clone $query)->whereIn('status', ['completed', 'done'])->count(),
        ];

        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->successResponse($stats);
        }

        return view('caretaker.tasks', ['tasks' => collect(), 'stats' => $stats]);
    }

    public function updateStatus(TaskRequest $request, Task $task)
    {
        $this->authorizeTaskAccess($request->user(), $task);

        $task->update(['status' => $request->status]);

        if (in_array($request->status, ['done', 'completed'], true)) {
            $task->update(['completed_at' => now()]);
        }

        return $this->successResponse($task, 'Task status updated');
    }

    public function destroy(Request $request, Task $task)
    {
        $user = $request->user();
        if ($user->role === 'admin') {
            // admin can delete any
        } elseif ($user->role === 'landlord') {
            $landlordId = $user->landlord->id;
            if ($task->request->unit->property->landlord_id !== $landlordId) {
                return $this->errorResponse('Unauthorized', 403);
            }
        } else {
            return $this->errorResponse('Unauthorized', 403);
        }

        $task->delete();
        return $this->successResponse(null, 'Task deleted');
    }

    private function authorizeTaskAccess($user, Task $task)
    {
        if ($user->role === 'admin') return;
        if ($user->role === 'landlord') {
            $landlordId = $user->landlord?->id;
            if (!$landlordId || !$task->maintenanceRequest || $task->maintenanceRequest->property?->landlord_id !== $landlordId) {
                abort(403, 'Unauthorized');
            }
            return;
        }
        if ($user->role === 'caretaker') {
            $column = $this->taskAssigneeColumn();
            $assignee = $task->{$column} ?? null;
            $caretakerId = $user->caretaker?->getKey() ?? $user->caretaker?->id;

            if (!$caretakerId || (string) $assignee !== (string) $caretakerId) {
                abort(403, 'Unauthorized');
            }
            return;
        }
        abort(403, 'Unauthorized');
    }

    private function baseTaskQueryForUser($user)
    {
        $query = Task::query();

        if ($user->role === 'caretaker') {
            $column = $this->taskAssigneeColumn();
            $caretakerId = $user->caretaker?->getKey() ?? $user->caretaker?->id;
            return $query->where($column, $caretakerId);
        }

        if ($user->role === 'landlord') {
            $landlordId = $user->landlord?->id;
            return $query->whereHas('maintenanceRequest.property', function ($subQuery) use ($landlordId) {
                $subQuery->where('landlord_id', $landlordId);
            });
        }

        if ($user->role === 'admin') {
            return $query;
        }

        abort(403, 'Unauthorized');
    }

    private function taskListResponse(Request $request, $tasks, string $message)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->successResponse($tasks, $message);
        }

        return view('caretaker.tasks', ['tasks' => $tasks]);
    }

    private function updateStatusByRoute(Request $request, Task $task, string $status, string $message)
    {
        $this->authorizeTaskAccess($request->user(), $task);

        $task->status = $status;
        if ($status === 'completed') {
            $task->completed_at = now();
        }
        if ($status === 'in_progress' && empty($task->started_at)) {
            $task->started_at = now();
        }
        $task->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->successResponse($task, $message);
        }

        return back()->with('success', $message);
    }

    private function taskAssigneeColumn(): string
    {
        foreach (['caretaker_id', 'assigned_to', 'assigned_to_caretaker_id'] as $column) {
            if (Schema::hasColumn('tasks', $column)) {
                return $column;
            }
        }

        return 'assigned_to';
    }
}
