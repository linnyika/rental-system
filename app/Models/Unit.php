<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Unit extends Model
{
    use HasFactory;

    /**
     * Unit types
     */
    public const TYPE_STUDIO = 'studio';
    public const TYPE_1BED = '1bed';
    public const TYPE_2BED = '2bed';
    public const TYPE_3BED = '3bed';
    public const TYPE_COMMERCIAL = 'commercial';

    public const TYPES = [
        self::TYPE_STUDIO,
        self::TYPE_1BED,
        self::TYPE_2BED,
        self::TYPE_3BED,
        self::TYPE_COMMERCIAL,
    ];

    /**
     * Unit statuses
     */
    public const STATUS_VACANT = 'vacant';
    public const STATUS_OCCUPIED = 'occupied';
    public const STATUS_RESERVED = 'reserved';
    public const STATUS_MAINTENANCE = 'maintenance';

    public const STATUSES = [
        self::STATUS_VACANT,
        self::STATUS_OCCUPIED,
        self::STATUS_RESERVED,
        self::STATUS_MAINTENANCE,
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'property_id',
        'unit_number',
        'floor_number',
        'unit_type',
        'rent_amount',
        'deposit_amount',
        'status',
        'is_active',
        'size_sqm',
        'description',
        'features',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'rent_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'size_sqm' => 'decimal:2',
        'is_active' => 'boolean',
        'features' => 'array',
        'floor_number' => 'integer',
    ];

    /**
     * Relationships
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function tenantOccupancy(): HasOne
    {
        return $this->hasOne(TenantOccupancy::class)->where('is_current', true);
    }

    public function tenantOccupancies(): HasMany
    {
        return $this->hasMany(TenantOccupancy::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function getCurrentTenantAttribute(): ?Tenant
    {
        return $this->tenantOccupancy?->tenant;
    }

    public function getIsOccupiedAttribute(): bool
    {
        return $this->status === self::STATUS_OCCUPIED;
    }

    /**
     * Scopes
     */
    public function scopeVacant($query)
    {
        return $query->where('status', self::STATUS_VACANT);
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', self::STATUS_OCCUPIED);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Methods
     */
    public function getCurrentTenant(): ?Tenant
    {
        return $this->tenantOccupancy?->tenant;
    }

    public function isOccupied(): bool
    {
        return $this->status === self::STATUS_OCCUPIED;
    }

    public function getPaymentHistory(): array
    {
        return $this->payments()
            ->with('tenant.user')
            ->orderBy('payment_date', 'desc')
            ->get()
            ->toArray();
    }

    public function getTotalRentCollected(): float
    {
        return $this->payments()
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getLastPaymentDate(): ?string
    {
        $payment = $this->payments()
            ->where('status', 'completed')
            ->orderBy('payment_date', 'desc')
            ->first();

        return $payment?->payment_date?->toDateString();
    }

    public function getOccupancyHistory(): array
    {
        return $this->tenantOccupancies()
            ->with('tenant.user')
            ->orderBy('start_date', 'desc')
            ->get()
            ->toArray();
    }

    public function getRentDueDate(): ?string
    {
        $tenant = $this->getCurrentTenant();
        if (!$tenant) {
            return null;
        }

        // Assuming rent is due on the 5th of each month
        return now()->startOfMonth()->addDays(4)->toDateString();
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($unit) {
            if ($unit->isOccupied()) {
                throw new \Exception('Cannot delete an occupied unit.');
            }
        });

        static::updating(function ($unit) {
            if ($unit->isDirty('status') && $unit->getOriginal('status') === 'occupied') {
                throw new \Exception('Cannot change status of occupied unit directly.');
            }
        });
    }
}
