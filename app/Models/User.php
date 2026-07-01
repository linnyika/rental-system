<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'phone',
        'full_name',
        'role',
        'is_active',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * User roles constants
     */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_LANDLORD = 'landlord';
    public const ROLE_CARETAKER = 'caretaker';
    public const ROLE_TENANT = 'tenant';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_LANDLORD,
        self::ROLE_CARETAKER,
        self::ROLE_TENANT,
    ];

    /**
     * Relationships
     */
    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class);
    }

    public function landlord(): HasOne
    {
        return $this->hasOne(Landlord::class);
    }

    public function caretaker(): HasOne
    {
        return $this->hasOne(Caretaker::class);
    }

    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class);
    }

    public function sessionLogs(): HasMany
    {
        return $this->hasMany(SessionLog::class);
    }

    public function auditTrails(): HasMany
    {
        return $this->hasMany(AuditTrail::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function createdReports(): HasMany
    {
        return $this->hasMany(Report::class, 'generated_by');
    }

    public function createdSettings(): HasMany
    {
        return $this->hasMany(Settings::class, 'created_by');
    }

    public function updatedSettings(): HasMany
    {
        return $this->hasMany(Settings::class, 'updated_by');
    }

    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_by');
    }

    public function reviewedActivityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'reviewed_by');
    }

    public function verifiedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'verified_by');
    }

    public function createdTenantOccupancies(): HasMany
    {
        return $this->hasMany(TenantOccupancy::class, 'created_by');
    }

    /**
     * Scopes
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole(Builder $query, string $role): Builder
    {
        return $query->where('role', $role);
    }

    public function scopeAdmin(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_ADMIN);
    }

    public function scopeLandlord(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_LANDLORD);
    }

    public function scopeCaretaker(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_CARETAKER);
    }

    public function scopeTenant(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_TENANT);
    }

    /**
     * Accessors
     */
    public function getRoleNameAttribute(): string
    {
        return ucfirst($this->role);
    }

    public function getFullNameAttribute(): string
    {
        return $this->full_name;
    }

    /**
     * Methods
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->currentAccessToken() && $this->tokenCan($permission)) {
            return true;
        }

        // Implement permission checking logic
        // For now, admins have all permissions
        if ($this->role === self::ROLE_ADMIN) {
            return true;
        }

        // Add role-specific permission logic here
        return false;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isLandlord(): bool
    {
        return $this->role === self::ROLE_LANDLORD;
    }

    public function isCaretaker(): bool
    {
        return $this->role === self::ROLE_CARETAKER;
    }

    public function isTenant(): bool
    {
        return $this->role === self::ROLE_TENANT;
    }

    public function getProfile()
    {
        return match ($this->role) {
            self::ROLE_ADMIN => $this->admin,
            self::ROLE_LANDLORD => $this->landlord,
            self::ROLE_CARETAKER => $this->caretaker,
            self::ROLE_TENANT => $this->tenant,
            default => null,
        };
    }

    /**
     * Boot method
     */
    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (empty($user->username)) {
                $user->username = strtolower(explode('@', $user->email)[0]);
            }

            $baseUsername = $user->username;
            $counter = 1;
            while (self::where('username', $user->username)->exists()) {
                $user->username = $baseUsername . $counter;
                $counter++;
            }

            if (!in_array($user->role, self::ROLES, true)) {
                $user->role = self::ROLE_TENANT;
            }
        });
    }
}
