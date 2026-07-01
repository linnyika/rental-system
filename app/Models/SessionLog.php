<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionLog extends Model
{
    use HasFactory;

    /**
     * Device types
     */
    public const DEVICE_WEB = 'web';
    public const DEVICE_MOBILE = 'mobile';
    public const DEVICE_API = 'api';

    public const DEVICES = [
        self::DEVICE_WEB,
        self::DEVICE_MOBILE,
        self::DEVICE_API,
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'device_type',
        'location',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'login_time' => 'datetime',
        'logout_time' => 'datetime',
        'is_active' => 'boolean',
        'session_duration' => 'integer',
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

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByDate($query, $startDate, $endDate = null)
    {
        $query->where('login_time', '>=', $startDate);
        if ($endDate) {
            $query->where('login_time', '<=', $endDate);
        }
        return $query;
    }

    /**
     * Methods
     */
    public function closeSession(): void
    {
        if (!$this->is_active) {
            throw new \Exception('This session is already closed.');
        }

        $this->is_active = false;
        $this->logout_time = now();
        $this->session_duration = $this->login_time->diffInSeconds($this->logout_time);
        $this->save();
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function getDuration(): ?string
    {
        if (!$this->logout_time) {
            return null;
        }

        $seconds = $this->session_duration ?? $this->login_time->diffInSeconds($this->logout_time);
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        }

        return sprintf('%02d:%02d', $minutes, $secs);
    }

    public function getDeviceLabel(): string
    {
        return ucfirst($this->device_type ?? 'unknown');
    }

    /**
     * Boot method
     */
    protected static function booted(): void
    {
        static::creating(function (self $log): void {
            if (!isset($log->is_active)) {
                $log->is_active = true;
            }
            if (empty($log->login_time)) {
                $log->login_time = now();
            }
            if (empty($log->device_type)) {
                $log->device_type = self::DEVICE_WEB;
            }
            if (empty($log->session_token)) {
                $log->session_token = hash('sha256', $log->user_id . '|' . now()->timestamp . '|' . bin2hex(random_bytes(16)));
            }
        });
    }
}
