<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    /**
     * Task statuses
     */
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_ASSIGNED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'maintenance_request_id',
        'assigned_to',
        'assigned_by',
        'task_description',
        'priority',
        'due_date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'due_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_completed_by_caretaker' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function maintenanceRequest(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Caretaker::class, 'assigned_to');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Scopes
     */
    public function scopeByCaretaker($query, $caretakerId)
    {
        return $query->where('assigned_to', $caretakerId);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereIn('status', [self::STATUS_ASSIGNED, self::STATUS_IN_PROGRESS]);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [self::STATUS_ASSIGNED, self::STATUS_IN_PROGRESS]);
    }

    /**
     * Methods
     */
    public function completeTask(string $notes, ?string $photo = null): void
    {
        if ($this->status === self::STATUS_COMPLETED) {
            throw new \Exception('This task is already completed.');
        }

        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = now();
        $this->completion_notes = $notes;
        if ($photo) {
            $this->completion_photo = $photo;
        }
        $this->is_completed_by_caretaker = true;
        $this->save();

        // Check if all tasks for the maintenance request are completed
        $maintenanceRequest = $this->maintenanceRequest;
        $pendingTasks = $maintenanceRequest->tasks()
            ->whereIn('status', [self::STATUS_ASSIGNED, self::STATUS_IN_PROGRESS])
            ->count();

        if ($pendingTasks === 0) {
            $maintenanceRequest->status = MaintenanceRequest::STATUS_RESOLVED;
            $maintenanceRequest->resolved_at = now();
            $maintenanceRequest->save();
        }
    }

    public function startTask(): void
    {
        if ($this->status !== self::STATUS_ASSIGNED) {
            throw new \Exception('Only assigned tasks can be started.');
        }

        $this->status = self::STATUS_IN_PROGRESS;
        $this->started_at = now();
        $this->save();

        // Update maintenance request status
        $this->maintenanceRequest->status = MaintenanceRequest::STATUS_IN_PROGRESS;
        $this->maintenanceRequest->save();
    }

    public function isOverdue(): bool
    {
        return $this->due_date->isPast() &&
               in_array($this->status, [self::STATUS_ASSIGNED, self::STATUS_IN_PROGRESS]);
    }

    public function getTimeToComplete(): ?string
    {
        if (!$this->completed_at) {
            return null;
        }

        return $this->started_at->diff($this->completed_at)->format('%h hours, %i minutes');
    }

    public function getStatusLabel(): string
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getPriorityLabel(): string
    {
        return ucfirst($this->priority);
    }

    public function getPriorityColor(): string
    {
        return match ($this->priority) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            default => 'info',
        };
    }

    /**
     * Boot method
     */
    protected static function booted(): void
    {
        static::creating(function (self $task): void {
            if (empty($task->status)) {
                $task->status = self::STATUS_ASSIGNED;
            }
            if (empty($task->priority)) {
                $task->priority = $task->maintenanceRequest->priority ?? 'medium';
            }

            $alreadyAssigned = self::query()
                ->where('maintenance_request_id', $task->maintenance_request_id)
                ->where('assigned_to', $task->assigned_to)
                ->whereIn('status', [self::STATUS_ASSIGNED, self::STATUS_IN_PROGRESS])
                ->exists();

            if ($alreadyAssigned) {
                throw new \RuntimeException('An open task for this caretaker already exists on this request.');
            }

            $request = $task->maintenanceRequest;
            $assignedCaretaker = Caretaker::query()->find($task->assigned_to);
            if ($request?->property && $assignedCaretaker && $assignedCaretaker->landlord_id !== $request->property->landlord_id) {
                throw new \RuntimeException('Caretaker assignment does not match property landlord.');
            }
        });
    }
}
