<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caretaker extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'landlord_id',
        'id_number',
        'emergency_contact',
        'emergency_phone',
        'skills',
        'is_active',
        'hire_date',
        'termination_date',
        'rating',
        'salary',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'skills' => 'array',
        'is_active' => 'boolean',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'rating' => 'decimal:1',
        'salary' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(Landlord::class);
    }

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'caretaker_properties');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'assigned_to');
    }

    public function handledRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'assessed_by_caretaker_id');
    }

    public function dailyActivityLogs(): HasMany
    {
        return $this->hasMany(DailyActivityLog::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByLandlord($query, $landlordId)
    {
        return $query->where('landlord_id', $landlordId);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->whereHas('properties', function ($q) use ($propertyId) {
            $q->where('properties.id', $propertyId);
        });
    }

    /**
     * Methods
     */
    public function getProperties(): array
    {
        return $this->properties()
            ->with('landlord.user')
            ->get()
            ->toArray();
    }

    public function getTasksByStatus(): array
    {
        return [
            'pending' => $this->tasks()->where('status', Task::STATUS_ASSIGNED)->count(),
            'in_progress' => $this->tasks()->where('status', 'in_progress')->count(),
            'completed' => $this->tasks()->where('status', 'completed')->count(),
            'cancelled' => $this->tasks()->where('status', 'cancelled')->count(),
        ];
    }

    public function getActiveTasks(): array
    {
        return $this->tasks()
            ->whereIn('status', [Task::STATUS_ASSIGNED, Task::STATUS_IN_PROGRESS])
            ->with('maintenanceRequest')
            ->get()
            ->toArray();
    }

    public function getCompletedTasks(): array
    {
        return $this->tasks()
            ->where('status', Task::STATUS_COMPLETED)
            ->with('maintenanceRequest')
            ->orderBy('completed_at', 'desc')
            ->get()
            ->toArray();
    }

    public function getActivityLogs(): array
    {
        return $this->activityLogs()
            ->with('property')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function assignToProperty(Property $property): void
    {
        if (!$this->properties()->where('properties.id', $property->id)->exists()) {
            $this->properties()->attach($property->id);
        }
    }

    public function canPerformTask(Task $task): bool
    {
        // Check if caretaker is assigned to the task's property
        $maintenanceRequest = $task->maintenanceRequest;
        if (!$maintenanceRequest) {
            return false;
        }

        return $this->properties()
            ->where('properties.id', $maintenanceRequest->property_id)
            ->exists();
    }

    public function getRatingAverage(): float
    {
        return round($this->rating ?? 0, 1);
    }

    public function isAssignedToProperty(int $propertyId): bool
    {
        return $this->properties()->where('properties.id', $propertyId)->exists();
    }

    public function getFullName(): string
    {
        return $this->user->full_name ?? 'N/A';
    }

    /**
     * Boot method
     */
    protected static function booted(): void
    {
        static::creating(function (self $caretaker): void {
            if (self::query()->where('user_id', $caretaker->user_id)->exists()) {
                throw new \RuntimeException('This user is already registered as a caretaker.');
            }
        });

        static::deleting(function (self $caretaker): void {
            // Check if caretaker has active tasks
            $activeTasks = $caretaker->tasks()
                ->whereIn('status', [Task::STATUS_ASSIGNED, Task::STATUS_IN_PROGRESS])
                ->count();

            if ($activeTasks > 0) {
                throw new \Exception('Cannot delete a caretaker with active tasks.');
            }
        });
    }
}
