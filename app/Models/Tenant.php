<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'int';

    /**
     * Gender enum
     */
    public const GENDER_MALE = 'male';
    public const GENDER_FEMALE = 'female';
    public const GENDER_OTHER = 'other';

    public const GENDERS = [
        self::GENDER_MALE,
        self::GENDER_FEMALE,
        self::GENDER_OTHER,
    ];

    /**
     * Employment statuses
     */
    public const EMPLOYMENT_EMPLOYED = 'employed';
    public const EMPLOYMENT_SELF_EMPLOYED = 'self-employed';
    public const EMPLOYMENT_STUDENT = 'student';
    public const EMPLOYMENT_RETIRED = 'retired';

    public const EMPLOYMENT_STATUSES = [
        self::EMPLOYMENT_EMPLOYED,
        self::EMPLOYMENT_SELF_EMPLOYED,
        self::EMPLOYMENT_STUDENT,
        self::EMPLOYMENT_RETIRED,
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'landlord_id',
        'id_number',
        'nationality',
        'date_of_birth',
        'gender',
        'emergency_contact',
        'emergency_phone',
        'employment_status',
        'employer_name',
        'employer_phone',
        'is_active',
        'moved_in_date',
        'moved_out_date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'moved_in_date' => 'date',
        'moved_out_date' => 'date',
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

    public function tenantOccupancies(): HasMany
    {
        return $this->hasMany(TenantOccupancy::class);
    }

    public function currentOccupancy(): HasOne
    {
        return $this->hasOne(TenantOccupancy::class)->where('is_current', true);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Accessors
     */
    public function getCurrentUnitAttribute(): ?Unit
    {
        return $this->currentOccupancy?->unit;
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
        return $query->whereHas('currentOccupancy.unit', function ($q) use ($propertyId) {
            $q->where('property_id', $propertyId);
        });
    }

    /**
     * Methods
     */
    public function getCurrentUnit(): ?Unit
    {
        return $this->currentOccupancy?->unit;
    }

    public function getPaymentStatus(): string
    {
        $currentMonth = now()->startOfMonth();
        $payment = $this->payments()
            ->where('status', Payment::STATUS_COMPLETED)
            ->whereYear('payment_date', $currentMonth->year)
            ->whereMonth('payment_date', $currentMonth->month)
            ->first();

        return $payment ? 'paid' : 'unpaid';
    }

    public function getOutstandingBalance(): float
    {
        $currentMonth = now()->startOfMonth();
        $unit = $this->getCurrentUnit();

        if (!$unit) {
            return 0;
        }

        $paid = $this->payments()
            ->where('status', Payment::STATUS_COMPLETED)
            ->whereYear('payment_date', $currentMonth->year)
            ->whereMonth('payment_date', $currentMonth->month)
            ->sum('amount');

        return max(0, $unit->rent_amount - $paid);
    }

    public function getPaymentHistory(): array
    {
        return $this->payments()
            ->with('unit')
            ->orderBy('payment_date', 'desc')
            ->get()
            ->toArray();
    }

    public function getMaintenanceRequests(): array
    {
        return $this->maintenanceRequests()
            ->with(['unit.property', 'assignedTo.user', 'tasks'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function hasActiveLease(): bool
    {
        if (!$this->is_active || !$this->currentOccupancy) {
            return false;
        }

        return $this->currentOccupancy->end_date === null ||
               $this->currentOccupancy->end_date->isFuture();
    }

    public function getTenancyDuration(): ?string
    {
        if (!$this->moved_in_date) {
            return null;
        }

        $endDate = $this->moved_out_date ?? now();
        return $this->moved_in_date->diff($endDate)->format('%m months, %d days');
    }

    public function getRentDueDate(): ?string
    {
        if (!$this->hasActiveLease() || !$this->getCurrentUnit()) {
            return null;
        }

        // Rent due on 5th of each month
        $dueDate = now()->startOfMonth()->addDays(4);
        if (now()->day > 5) {
            $dueDate = $dueDate->addMonth();
        }

        return $dueDate->toDateString();
    }

    /**
     * Boot method
     */
    protected static function booted(): void
    {
        static::deleting(function (self $tenant): void {
            if ($tenant->hasActiveLease()) {
                throw new \Exception('Cannot delete a tenant with an active lease.');
            }
        });

        static::creating(function (self $tenant): void {
            if (self::query()->where('user_id', $tenant->user_id)->exists()) {
                throw new \RuntimeException('This user is already registered as a tenant.');
            }

            if (empty($tenant->landlord_id)) {
                // Prefer deriving landlord from current occupancy if provided.
                if ($tenant->relationLoaded('currentOccupancy') && $tenant->currentOccupancy?->unit?->property) {
                    $tenant->landlord_id = $tenant->currentOccupancy->unit->property->landlord_id;
                }
            }
        });
    }
}
