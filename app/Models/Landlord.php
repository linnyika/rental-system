<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Landlord extends Model
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
        'company_name',
        'id_number',
        'kra_pin',
        'physical_address',
        'is_verified',
        'verification_date',
        'max_properties',
        'registration_date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_verified' => 'boolean',
        'verification_date' => 'datetime',
        'registration_date' => 'datetime',
        'max_properties' => 'integer',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function caretakers(): HasMany
    {
        return $this->hasMany(Caretaker::class);
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'approved_by_landlord_id');
    }

    /**
     * Scopes
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    /**
     * Methods
     */
    public function getTotalProperties(): int
    {
        return $this->properties()->count();
    }

    public function getTotalUnits(): int
    {
        return $this->properties()->withCount('units')->get()->sum('units_count');
    }

    public function getOccupancyRate(): float
    {
        $totalUnits = $this->getTotalUnits();
        if ($totalUnits === 0) {
            return 0;
        }

        $occupiedUnits = $this->properties()
            ->with(['units' => function ($query) {
                $query->where('status', 'occupied');
            }])
            ->get()
            ->sum(function ($property) {
                return $property->units->count();
            });

        return round(($occupiedUnits / $totalUnits) * 100, 2);
    }

    public function getMonthlyRevenue(): float
    {
        return (float) Payment::query()
            ->where('status', Payment::STATUS_COMPLETED)
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->whereHas('tenant.currentOccupancy.unit.property', function ($query) {
                $query->where('landlord_id', $this->user_id);
            })
            ->sum('amount');
    }

    public function getActiveTenants(): int
    {
        return $this->tenants()
            ->where('is_active', true)
            ->count();
    }

    public function canAddProperty(): bool
    {
        return $this->getTotalProperties() < $this->max_properties;
    }

    public function getCaretakerByProperty(int $propertyId): ?Caretaker
    {
        return $this->caretakers()
            ->whereHas('properties', function ($query) use ($propertyId) {
                $query->where('properties.id', $propertyId);
            })
            ->first();
    }

    /**
     * Boot method
     */
    protected static function booted(): void
    {
        static::creating(function (self $landlord): void {
            if (empty($landlord->max_properties)) {
                $landlord->max_properties = 10;
            }
            if (empty($landlord->registration_date)) {
                $landlord->registration_date = now();
            }

            if (self::query()->where('user_id', $landlord->user_id)->exists()) {
                throw new \RuntimeException('This user is already registered as a landlord.');
            }
        });
    }
}
