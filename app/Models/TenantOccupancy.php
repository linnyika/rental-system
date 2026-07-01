<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantOccupancy extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tenant_id',
        'unit_id',
        'start_date',
        'end_date',
        'is_current',
        'rent_amount_at_start',
        'lease_agreement_path',
        'deposit_paid',
        'deposit_amount',
        'deposit_refunded',
        'termination_reason',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'rent_amount_at_start' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'deposit_paid' => 'boolean',
        'deposit_refunded' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scopes
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeByTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_current', true)
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Methods
     */
    public function isActive(): bool
    {
        return $this->is_current &&
               ($this->end_date === null || $this->end_date->isFuture());
    }

    public function getDuration(): ?string
    {
        if (!$this->start_date) {
            return null;
        }

        $endDate = $this->end_date ?? now();
        return $this->start_date->diff($endDate)->format('%y years, %m months, %d days');
    }

    public function getPaymentsDuringTenancy(): array
    {
        return $this->tenant->payments()
            ->whereBetween('payment_date', [
                $this->start_date,
                $this->end_date ?? now()
            ])
            ->orderBy('payment_date', 'desc')
            ->get()
            ->toArray();
    }

    public function closeTenancy(?string $reason = null): void
    {
        $this->is_current = false;
        $this->end_date = now();
        $this->termination_reason = $reason;
        $this->save();

        // Update unit status
        $this->unit->status = Unit::STATUS_VACANT;
        $this->unit->save();

        // Update tenant status
        $this->tenant->is_active = false;
        $this->tenant->moved_out_date = now();
        $this->tenant->save();

        // Update property occupancy
        $this->unit->property->updateOccupancy();
    }

    /**
     * Boot method
     */
    protected static function booted(): void
    {
        static::creating(function (self $occupancy): void {
            // Ensure only one current occupancy per unit
            if ($occupancy->is_current) {
                $existing = self::where('unit_id', $occupancy->unit_id)
                    ->where('is_current', true)
                    ->first();

                if ($existing) {
                    throw new \Exception('This unit already has a current tenant.');
                }
            }

            // Ensure only one current occupancy per tenant
            if ($occupancy->is_current) {
                $existing = self::where('tenant_id', $occupancy->tenant_id)
                    ->where('is_current', true)
                    ->first();

                if ($existing) {
                    throw new \Exception('This tenant already has a current occupancy.');
                }
            }

            $overlapExists = self::query()
                ->where('tenant_id', $occupancy->tenant_id)
                ->where(function ($query) use ($occupancy) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', $occupancy->start_date);
                })
                ->where('start_date', '<=', $occupancy->end_date ?? now()->addYears(50))
                ->exists();

            if ($overlapExists) {
                throw new \RuntimeException('Tenant has overlapping tenancy dates.');
            }
        });

        static::saved(function (self $occupancy): void {
            if ($occupancy->is_current) {
                // Update unit status
                $occupancy->unit->status = Unit::STATUS_OCCUPIED;
                $occupancy->unit->save();

                // Update tenant status
                $occupancy->tenant->is_active = true;
                $occupancy->tenant->moved_in_date = $occupancy->start_date;
                $occupancy->tenant->save();

                // Update property occupancy
                $occupancy->unit->property->updateOccupancy();
            }
        });
    }
}
