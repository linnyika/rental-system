<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * Activity types
     */
    public const TYPE_INSPECTION = 'inspection';
    public const TYPE_CLEANING = 'cleaning';
    public const TYPE_REPAIR = 'repair';
    public const TYPE_MEETING = 'meeting';
    public const TYPE_REPORTING = 'reporting';
    public const TYPE_OTHER = 'other';

    public const TYPES = [
        self::TYPE_INSPECTION,
        self::TYPE_CLEANING,
        self::TYPE_REPAIR,
        self::TYPE_MEETING,
        self::TYPE_REPORTING,
        self::TYPE_OTHER,
    ];

    /**
     * Statuses
     */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_REVIEWED = 'reviewed';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_SUBMITTED,
        self::STATUS_REVIEWED,
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'caretaker_id',
        'property_id',
        'log_date',
        'log_time',
        'activity_type',
        'description',
        'duration_minutes',
        'status',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'location',
        'photo_attachment',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'log_date' => 'date',
        'log_time' => 'datetime',
        'duration_minutes' => 'integer',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function caretaker(): BelongsTo
    {
        return $this->belongsTo(Caretaker::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scopes
     */
    public function scopeByCaretaker($query, $caretakerId)
    {
        return $query->where('caretaker_id', $caretakerId);
    }

    public function scopeByDate($query, $startDate, $endDate = null)
    {
        $query->where('log_date', '>=', $startDate);
        if ($endDate) {
            $query->where('log_date', '<=', $endDate);
        }
        return $query;
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    /**
     * Methods
     */
    public function isEditable(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isDeletable(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function submitLog(): void
    {
        if ($this->status === self::STATUS_SUBMITTED) {
            throw new \Exception('This log is already submitted.');
        }

        $this->status = self::STATUS_SUBMITTED;
        $this->submitted_at = now();
        $this->save();
    }

    public function reviewLog(int $reviewedBy): void
    {
        if ($this->status !== self::STATUS_SUBMITTED) {
            throw new \Exception('Only submitted logs can be reviewed.');
        }

        $this->status = self::STATUS_REVIEWED;
        $this->reviewed_by = $reviewedBy;
        $this->reviewed_at = now();
        $this->save();
    }

    public function getTypeLabel(): string
    {
        return ucfirst($this->activity_type);
    }

    public function getStatusLabel(): string
    {
        return ucfirst($this->status);
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_SUBMITTED => 'warning',
            self::STATUS_REVIEWED => 'success',
            default => 'info',
        };
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            if (empty($log->status)) {
                $log->status = self::STATUS_DRAFT;
            }
            if (empty($log->log_date)) {
                $log->log_date = now();
            }
            if (empty($log->log_time)) {
                $log->log_time = now();
            }
        });

        static::updating(function ($log) {
            // Prevent editing submitted logs
            if ($log->isDirty() && $log->getOriginal('status') === self::STATUS_SUBMITTED) {
                throw new \Exception('Submitted logs cannot be edited.');
            }
        });

        static::deleting(function ($log) {
            // Prevent deleting submitted logs
            if ($log->status === self::STATUS_SUBMITTED) {
                throw new \Exception('Submitted logs cannot be deleted.');
            }
        });
    }
}
