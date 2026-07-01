<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    /**
     * Payment methods
     */
    public const METHOD_MPESA = 'mpesa';
    public const METHOD_BANK = 'bank';
    public const METHOD_CASH = 'cash';
    public const METHOD_CHEQUE = 'cheque';

    public const METHODS = [
        self::METHOD_MPESA,
        self::METHOD_BANK,
        self::METHOD_CASH,
        self::METHOD_CHEQUE,
    ];

    /**
     * Payment statuses
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_COMPLETED,
        self::STATUS_FAILED,
        self::STATUS_REFUNDED,
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tenant_id',
        'amount',
        'due_date',
        'payment_date',
        'payment_method',
        'transaction_id',
        'receipt_url',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'payment_date' => 'date',
        'verified_at' => 'datetime',
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
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeByTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByDateRange($query, $start, $end)
    {
        return $query->whereBetween('payment_date', [$start, $end]);
    }

    public function scopeByUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    /**
     * Methods
     */
    public function verifyPayment(?int $userId = null): void
    {
        if ($this->status === self::STATUS_COMPLETED) {
            throw new \Exception('This payment is already verified.');
        }

        $this->status = self::STATUS_COMPLETED;
        $this->verified_by = $userId;
        $this->verified_at = now();
        $this->receipt_number = $this->generateReceiptNumber();
        $this->save();
    }

    public function isVerified(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function generateReceiptNumber(): string
    {
        $prefix = 'RCP';
        $year = now()->year;
        $month = str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        $sequence = str_pad($this->id, 6, '0', STR_PAD_LEFT);

        return "{$prefix}-{$year}{$month}-{$sequence}";
    }

    public function getTenantName(): string
    {
        return $this->tenant->user->full_name ?? 'N/A';
    }

    public function getUnitNumber(): string
    {
        return $this->unit->unit_number ?? 'N/A';
    }

    public function getPaymentPeriod(): string
    {
        if (!$this->due_date || !$this->payment_date) {
            return 'N/A';
        }

        return $this->due_date->format('M d, Y') . ' - ' .
               $this->payment_date->format('M d, Y');
    }

    public function isLate(): bool
    {
        if (!$this->due_date) {
            return false;
        }

        return $this->due_date->isPast() && $this->status !== self::STATUS_COMPLETED;
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_FAILED => 'danger',
            self::STATUS_REFUNDED => 'secondary',
            default => 'info',
        };
    }

    public function getStatusBadge(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-warning',
            self::STATUS_COMPLETED => 'bg-success',
            self::STATUS_FAILED => 'bg-danger',
            self::STATUS_REFUNDED => 'bg-secondary',
            default => 'bg-info',
        };
    }

    /**
     * Boot method
     */
    protected static function booted(): void
    {
        static::creating(function (self $payment): void {
            // Set receipt number if not set
            if (empty($payment->receipt_number)) {
                $payment->receipt_number = 'RCP-' . strtoupper(uniqid());
            }

            // Set default status if not set
            if (empty($payment->status)) {
                $payment->status = self::STATUS_PENDING;
            }
        });

        static::updating(function (self $payment): void {
            // Prevent modifying verified payments
            if ($payment->isDirty() && $payment->getOriginal('status') === self::STATUS_COMPLETED) {
                throw new \Exception('Verified payments cannot be modified.');
            }
        });

        static::deleting(function (self $payment): void {
            // Prevent deleting verified payments
            if ($payment->status === self::STATUS_COMPLETED) {
                throw new \Exception('Verified payments cannot be deleted.');
            }
        });
    }
}
