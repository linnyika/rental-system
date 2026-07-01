<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyActivityLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'caretaker_id',
        'log_date',
        'activities_performed',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'log_date' => 'date',
        'submitted_at' => 'datetime',
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

    /**
     * Scopes
     */
    public function scopeByCaretaker($query, $caretakerId)
    {
        return $query->where('caretaker_id', $caretakerId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('log_date', $date);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('log_date', [$startDate, $endDate]);
    }

    /**
     * Methods
     */
    public function isEditable(): bool
    {
        // Logs cannot be edited after submission
        return $this->submitted_at === null;
    }

    public function isDeletable(): bool
    {
        // Logs cannot be deleted after submission
        return $this->submitted_at === null;
    }

    public function submit(): void
    {
        if ($this->submitted_at) {
            throw new \Exception('This log has already been submitted.');
        }

        $this->submitted_at = now();
        $this->save();
    }

    public function getActivitiesList(): array
    {
        return explode("\n", $this->activities_performed);
    }

    public function getFormattedActivities(): string
    {
        $activities = $this->getActivitiesList();
        return implode('<br>', array_map(function ($activity) {
            return '• ' . trim($activity);
        }, $activities));
    }

    /**
     * Boot method
     */
    protected static function booted(): void
    {
        static::creating(function (self $log): void {
            if (empty($log->log_date)) {
                $log->log_date = now();
            }

            $exists = self::query()
                ->where('caretaker_id', $log->caretaker_id)
                ->whereDate('log_date', $log->log_date)
                ->exists();

            if ($exists) {
                throw new \RuntimeException('A daily activity log already exists for this caretaker and date.');
            }
        });

        static::updating(function (self $log): void {
            // Prevent editing submitted logs
            if ($log->isDirty() && $log->getOriginal('submitted_at') !== null) {
                throw new \Exception('Submitted logs cannot be edited.');
            }
        });

        static::deleting(function (self $log): void {
            // Prevent deleting submitted logs
            if ($log->submitted_at !== null) {
                throw new \Exception('Submitted logs cannot be deleted.');
            }
        });
    }
}
