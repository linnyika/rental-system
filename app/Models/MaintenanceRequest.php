<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceRequest extends Model
{
    use HasFactory;

    /**
     * Categories
     */
    public const CATEGORY_PLUMBING = 'plumbing';
    public const CATEGORY_ELECTRICAL = 'electrical';
    public const CATEGORY_STRUCTURAL = 'structural';
    public const CATEGORY_APPLIANCE = 'appliance';
    public const CATEGORY_PEST = 'pest';
    public const CATEGORY_SECURITY = 'security';
    public const CATEGORY_OTHER = 'other';

    public const CATEGORIES = [
        self::CATEGORY_PLUMBING,
        self::CATEGORY_ELECTRICAL,
        self::CATEGORY_STRUCTURAL,
        self::CATEGORY_APPLIANCE,
        self::CATEGORY_PEST,
        self::CATEGORY_SECURITY,
        self::CATEGORY_OTHER,
    ];

    /**
     * Priorities
     */
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_EMERGENCY = 'emergency';

    public const PRIORITIES = [
        self::PRIORITY_LOW,
        self::PRIORITY_MEDIUM,
        self::PRIORITY_HIGH,
        self::PRIORITY_EMERGENCY,
    ];

    /**
     * Statuses
     */
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_REJECTED = 'rejected';

    public const STATUSES = [
        self::STATUS_SUBMITTED,
        self::STATUS_ASSIGNED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_RESOLVED,
        self::STATUS_REJECTED,
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tenant_id',
        'unit_id',
        'property_id',
        'category',
        'priority',
        'subject',
        'description',
        'is_major',
        'cost_estimate',
        'before_photo',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'submitted_at' => 'datetime',
        'assigned_at' => 'datetime',
        'resolved_at' => 'datetime',
        'approved_at' => 'datetime',
        'is_major' => 'boolean',
        'approved_by_landlord' => 'boolean',
        'cost_estimate' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Caretaker::class, 'assigned_to');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', [self::STATUS_SUBMITTED, self::STATUS_ASSIGNED]);
    }

    public function scopeByTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeMajor($query)
    {
        return $query->where('is_major', true);
    }

    /**
     * Methods
     */
    public function assignToCaretaker(Caretaker $caretaker, ?int $assignedBy = null): void
    {
        if ($this->status === self::STATUS_RESOLVED) {
            throw new \Exception('Cannot assign a resolved request.');
        }

        if ($this->property && $caretaker->landlord_id !== $this->property->landlord_id) {
            throw new \Exception('Caretaker must belong to the same landlord as the property.');
        }

        $caretakerKey = $caretaker->getKey();

        $this->assigned_to = $caretakerKey;
        $this->status = self::STATUS_ASSIGNED;
        $this->assigned_at = now();
        $this->save();

        // Create one open task per request/caretaker pair.
        $openTaskExists = $this->tasks()
            ->where('assigned_to', $caretakerKey)
            ->whereIn('status', [Task::STATUS_ASSIGNED, Task::STATUS_IN_PROGRESS])
            ->exists();

        if (!$openTaskExists) {
            Task::create([
                'maintenance_request_id' => $this->id,
                'assigned_to' => $caretakerKey,
                'assigned_by' => $assignedBy,
                'task_description' => $this->subject,
                'priority' => $this->priority,
                'due_date' => now()->addDays(3),
                'status' => Task::STATUS_ASSIGNED,
            ]);
        }
    }

    public function resolveRequest(string $notes, ?string $photo = null): void
    {
        if ($this->status === self::STATUS_RESOLVED) {
            throw new \Exception('This request is already resolved.');
        }

        $this->status = self::STATUS_RESOLVED;
        $this->resolved_at = now();
        $this->resolution_notes = $notes;
        if ($photo) {
            $this->after_photo = $photo;
        }
        $this->save();
    }

    public function rejectRequest(string $reason): void
    {
        if ($this->status === self::STATUS_RESOLVED) {
            throw new \Exception('Cannot reject a resolved request.');
        }

        $this->status = self::STATUS_REJECTED;
        $this->resolution_notes = $reason;
        $this->save();
    }

    public function approveMajorRequest(): void
    {
        if (!$this->is_major) {
            throw new \Exception('This is not a major request.');
        }

        $this->approved_by_landlord = true;
        $this->approved_at = now();
        $this->save();
    }

    public function isDuplicate(): bool
    {
        if (empty($this->description)) {
            return false;
        }

        $fingerprint = mb_strtolower(trim(mb_substr($this->description, 0, 120)));

        return self::where('tenant_id', $this->tenant_id)
            ->where('unit_id', $this->unit_id)
            ->where('category', $this->category)
            ->whereRaw('LOWER(TRIM(description)) LIKE ?', ['%' . $fingerprint . '%'])
            ->whereIn('status', [self::STATUS_SUBMITTED, self::STATUS_ASSIGNED, self::STATUS_IN_PROGRESS])
            ->where('created_at', '>=', now()->subDays(14))
            ->where('id', '!=', $this->id ?? 0)
            ->exists();
    }

    public function getDuplicates(): array
    {
        return self::where('tenant_id', $this->tenant_id)
            ->where('unit_id', $this->unit_id)
            ->where('category', $this->category)
            ->whereIn('status', [self::STATUS_SUBMITTED, self::STATUS_ASSIGNED, self::STATUS_IN_PROGRESS])
            ->where('id', '!=', $this->id ?? 0)
            ->with(['tenant.user', 'assignedTo.user'])
            ->get()
            ->toArray();
    }

    public function getTimeToResolution(): ?string
    {
        if (!$this->resolved_at) {
            return null;
        }

        return $this->submitted_at->diff($this->resolved_at)->format('%d days, %h hours');
    }

    public function getPriorityLabel(): string
    {
        return ucfirst($this->priority);
    }

    public function getStatusLabel(): string
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getPriorityColor(): string
    {
        return match ($this->priority) {
            self::PRIORITY_LOW => 'success',
            self::PRIORITY_MEDIUM => 'warning',
            self::PRIORITY_HIGH => 'danger',
            self::PRIORITY_EMERGENCY => 'danger',
            default => 'info',
        };
    }

    /**
     * Boot method
     */
    protected static function booted(): void
    {
        static::creating(function (self $request): void {
            if (empty($request->submitted_at)) {
                $request->submitted_at = now();
            }
            if (empty($request->status)) {
                $request->status = self::STATUS_SUBMITTED;
            }
        });

        static::saving(function (self $request): void {
            // Check for duplicates before saving
            if ($request->isDuplicate()) {
                throw new \Exception('A similar maintenance request already exists.');
            }

            if ($request->property_id && $request->unit_id && $request->property) {
                $requestUnit = Unit::query()
                    ->whereKey($request->unit_id)
                    ->where('property_id', $request->property_id)
                    ->exists();

                if (!$requestUnit) {
                    throw new \Exception('The selected unit does not belong to the selected property.');
                }
            }
        });
    }
}
