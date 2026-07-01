<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Model
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
        'admin_level',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'admin_level' => 'integer',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Methods
     */
    public function canManageUsers(): bool
    {
        return $this->admin_level >= 1;
    }

    public function canViewLogs(): bool
    {
        return $this->admin_level >= 1;
    }

    public function canConfigureSystem(): bool
    {
        return $this->admin_level >= 2;
    }

    public function hasPermission(string $permission): bool
    {
        if (empty($this->permissions)) {
            return false;
        }
        return in_array($permission, $this->permissions, true);
    }

    public function getLevelName(): string
    {
        return match ($this->admin_level) {
            1 => 'Basic Admin',
            2 => 'Super Admin',
            3 => 'System Admin',
            default => 'Admin Level ' . $this->admin_level,
        };
    }

    protected static function booted(): void
    {
        static::creating(function (self $admin): void {
            if (self::query()->where('user_id', $admin->user_id)->exists()) {
                throw new \RuntimeException('This user is already registered as an admin.');
            }
        });
    }
}
