<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Caretaker;
use App\Models\Landlord;
use App\Models\MaintenanceRequest;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\TenantOccupancy;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function admin(Request $request): JsonResponse
    {
        return response()->json([
            'stats' => [
                'total_properties' => Property::count(),
                'total_landlords' => Landlord::count(),
                'total_tenants' => Tenant::count(),
                'total_caretakers' => Caretaker::count(),
            ],
            'recent_registrations' => $this->recentUsers(),
            'recent_payments' => $this->recentPayments(),
            'recent_activity' => $this->recentActivity(),
        ]);
    }

    public function landlord(Request $request): JsonResponse
    {
        $landlord = $request->user()->landlord;
        $propertyIds = Property::where('landlord_id', $landlord?->id)->pluck('id');
        $totalUnits = Unit::whereIn('property_id', $propertyIds)->count();
        $occupiedUnits = Unit::whereIn('property_id', $propertyIds)->where('is_occupied', true)->count();

        return response()->json([
            'stats' => [
                'total_properties' => Property::where('landlord_id', $landlord?->id)->count(),
                'total_units' => $totalUnits,
                'occupied_units' => $occupiedUnits,
                'vacant_units' => max($totalUnits - $occupiedUnits, 0),
                'pending_maintenance_requests' => MaintenanceRequest::whereIn('unit_id', function ($query) use ($propertyIds) {
                    $query->select('id')->from('units')->whereIn('property_id', $propertyIds);
                })->where('status', 'pending')->count(),
            ],
            'recent_payments' => $this->recentPayments($propertyIds, 5),
            'maintenance_requests' => $this->recentMaintenanceRequests($propertyIds, 5),
            'notifications' => $this->recentNotifications($request->user()->id, 5),
        ]);
    }

    public function caretaker(Request $request): JsonResponse
    {
        $caretaker = $request->user()->caretaker;

        return response()->json([
            'stats' => [
                'assigned_tasks' => Task::where('caretaker_id', $caretaker?->id)->count(),
                'tasks_in_progress' => Task::where('caretaker_id', $caretaker?->id)->where('status', 'in_progress')->count(),
                'completed_tasks' => Task::where('caretaker_id', $caretaker?->id)->where('status', 'done')->count(),
                'activity_logs' => ActivityLog::where('caretaker_id', $caretaker?->id)->count(),
            ],
            'tasks' => $this->recentTasks($caretaker?->id, 5),
            'activity_logs' => $this->recentActivityLogs($caretaker?->id, 5),
            'notifications' => $this->recentNotifications($request->user()->id, 5),
        ]);
    }

    public function tenant(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        $occupancy = TenantOccupancy::with(['unit.property'])
            ->where('tenant_id', $tenant?->id)
            ->whereNull('end_date')
            ->latest('start_date')
            ->first();

        if ($occupancy === null) {
            $occupancy = TenantOccupancy::with(['unit.property'])
                ->where('tenant_id', $tenant?->id)
                ->latest('start_date')
                ->first();
        }

        $payments = Payment::with(['unit.property'])
            ->where('tenant_id', $tenant?->id)
            ->latest('payment_date')
            ->get();

        return response()->json([
            'current_unit' => $occupancy && $occupancy->unit ? [
                'unit_number' => $occupancy->unit->unit_number,
                'property_name' => optional($occupancy->unit->property)->name,
                'rent_amount' => $occupancy->unit->rent_amount,
            ] : null,
            'rent_status' => optional($payments->first())->status ?? 'pending',
            'payment_summary' => [
                'total_properties' => 0,
                'total_landlords' => 0,
                'total_tenants' => 0,
                'total_caretakers' => 0,
                'total_units' => 0,
                'occupied_units' => 0,
                'vacant_units' => 0,
                'pending_maintenance_requests' => MaintenanceRequest::where('tenant_id', $tenant?->id)->where('status', 'pending')->count(),
                'assigned_tasks' => 0,
                'tasks_in_progress' => 0,
                'completed_tasks' => 0,
                'activity_logs' => 0,
                'total_paid' => (float) $payments->sum('amount'),
                'pending_payments' => (int) $payments->where('status', 'pending')->count(),
                'verified_payments' => (int) $payments->where('status', 'verified')->count(),
                'payment_count' => (int) $payments->count(),
            ],
            'recent_payments' => $this->mapPayments($payments->take(5)),
            'maintenance_requests' => $this->recentMaintenanceRequestsForTenant($tenant?->id, 5),
            'notifications' => $this->recentNotifications($request->user()->id, 5),
        ]);
    }

    private function recentUsers(): array
    {
        return User::whereIn('role', ['landlord', 'tenant', 'caretaker'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function (User $user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                    'contact' => $user->phone ?? $user->email ?? '-',
                    'created_at' => optional($user->created_at)->toDateTimeString(),
                ];
            })
            ->values()
            ->all();
    }

    private function recentPayments($propertyIds = null, int $limit = 5): array
    {
        $query = Payment::with(['tenant.user', 'unit.property'])->latest('payment_date');

        if ($propertyIds !== null) {
            $query->whereIn('unit_id', function ($subQuery) use ($propertyIds) {
                $subQuery->select('id')->from('units')->whereIn('property_id', $propertyIds);
            });
        }

        return $this->mapPayments($query->limit($limit)->get());
    }

    private function recentMaintenanceRequests($propertyIds = null, int $limit = 5): array
    {
        $query = MaintenanceRequest::with(['tenant.user', 'unit.property'])->latest();

        if ($propertyIds !== null) {
            $query->whereIn('unit_id', function ($subQuery) use ($propertyIds) {
                $subQuery->select('id')->from('units')->whereIn('property_id', $propertyIds);
            });
        }

        return $this->mapRequests($query->limit($limit)->get());
    }

    private function recentMaintenanceRequestsForTenant($tenantId, int $limit = 5): array
    {
        return $this->mapRequests(
            MaintenanceRequest::with(['tenant.user', 'unit.property'])
                ->where('tenant_id', $tenantId)
                ->latest()
                ->limit($limit)
                ->get()
        );
    }

    private function recentTasks($caretakerId, int $limit = 5): array
    {
        return Task::with(['request.tenant.user', 'request.unit.property'])
            ->where('caretaker_id', $caretakerId)
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (Task $task) {
                return [
                    'id' => $task->id,
                    'status' => $task->status,
                    'description' => data_get($task, 'request.description', 'Maintenance task'),
                    'tenant_name' => data_get($task, 'request.tenant.user.name', '-'),
                    'property_name' => data_get($task, 'request.unit.property.name', '-'),
                    'unit_number' => data_get($task, 'request.unit.unit_number', '-'),
                    'created_at' => optional($task->created_at)->toDateTimeString(),
                ];
            })
            ->values()
            ->all();
    }

    private function recentActivityLogs($caretakerId, int $limit = 5): array
    {
        return ActivityLog::where('caretaker_id', $caretakerId)
            ->latest('activity_date')
            ->limit($limit)
            ->get()
            ->map(function (ActivityLog $log) {
                return [
                    'id' => $log->id,
                    'description' => $log->description,
                    'activity_date' => optional($log->activity_date)->toDateString(),
                ];
            })
            ->values()
            ->all();
    }

    private function recentActivity(): array
    {
        return ActivityLog::latest('activity_date')
            ->limit(5)
            ->get()
            ->map(function (ActivityLog $log) {
                return [
                    'id' => $log->id,
                    'description' => $log->description,
                    'activity_date' => optional($log->activity_date)->toDateString(),
                ];
            })
            ->values()
            ->all();
    }

    private function recentNotifications($userId, int $limit = 5): array
    {
        return Notification::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (Notification $notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'is_read' => (bool) $notification->is_read,
                    'created_at' => optional($notification->created_at)->toDateTimeString(),
                ];
            })
            ->values()
            ->all();
    }

    private function mapPayments($payments): array
    {
        return $payments->map(function (Payment $payment) {
            return [
                'id' => $payment->id,
                'amount' => (float) $payment->amount,
                'method' => $payment->method,
                'status' => $payment->status,
                'payment_date' => optional($payment->payment_date)->toDateString(),
                'tenant_name' => data_get($payment, 'tenant.user.name', '-'),
                'property_name' => data_get($payment, 'unit.property.name', '-'),
                'unit_number' => data_get($payment, 'unit.unit_number', '-'),
            ];
        })->values()->all();
    }

    private function mapRequests($requests): array
    {
        return $requests->map(function (MaintenanceRequest $request) {
            return [
                'id' => $request->id,
                'description' => $request->description,
                'status' => $request->status,
                'tenant_name' => data_get($request, 'tenant.user.name', '-'),
                'property_name' => data_get($request, 'unit.property.name', '-'),
                'unit_number' => data_get($request, 'unit.unit_number', '-'),
                'created_at' => optional($request->created_at)->toDateTimeString(),
            ];
        })->values()->all();
    }
}
