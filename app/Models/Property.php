<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory;

    /**
     * Property types
     */
    public const TYPE_APARTMENT = 'apartment';
    public const TYPE_HOUSE = 'house';
    public const TYPE_COMMERCIAL = 'commercial';
    public const TYPE_OFFICE = 'office';

    public const TYPES = [
        self::TYPE_APARTMENT,
        self::TYPE_HOUSE,
        self::TYPE_COMMERCIAL,
        self::TYPE_OFFICE,
    ];

    /**
     * Property statuses
     */
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_MAINTENANCE = 'maintenance';

    public const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
        self::STATUS_MAINTENANCE,
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'landlord_id',
        'property_name',
        'property_type',
        'address',
        'total_units',
        'occupied_units',
        'latitude',
        'longitude',
        'status',
        'description',
        'image_url',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'total_units' => 'integer',
        'occupied_units' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function landlord(): BelongsTo
    {
        return $this->belongsTo(Landlord::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function caretakers(): HasMany
    {
        return $this->hasMany(Caretaker::class);
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeByLandlord($query, $landlordId)
    {
        return $query->where('landlord_id', $landlordId);
    }

    /**
     * Methods
     */
    public function updateOccupancy(): void
    {
        $occupied = $this->units()->where('status', Unit::STATUS_OCCUPIED)->count();

        if ((int) $this->occupied_units !== $occupied) {
            $this->occupied_units = $occupied;
            $this->saveQuietly();
        }
    }

    public function getAvailableUnits(): int
    {
        return $this->units()->where('status', Unit::STATUS_VACANT)->count();
    }

    public function getCaretakerNames(): array
    {
        return $this->caretakers()
            ->with('user')
            ->get()
            ->map(function ($caretaker) {
                return $caretaker->user->full_name;
            })
            ->toArray();
    }

    public function getTenantCount(): int
    {
        return $this->units()
            ->with('tenantOccupancy')
            ->get()
            ->filter(function ($unit) {
                return $unit->tenantOccupancy && $unit->tenantOccupancy->is_current;
            })
            ->count();
    }

    public function isFull(): bool
    {
        return $this->total_units > 0 && $this->occupied_units >= $this->total_units;
    }

    public function getOccupancyPercentage(): float
    {
        if ($this->total_units === 0) {
            return 0;
        }
        return round(($this->occupied_units / $this->total_units) * 100, 2);
    }

    public function getAvailableUnitsCount(): int
    {
        return $this->total_units - $this->occupied_units;
    }

    /**
     * Boot method
     */
}
