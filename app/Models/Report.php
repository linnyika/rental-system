<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    /**
     * Report types
     */
    public const TYPE_PAYMENT = 'payment';
    public const TYPE_MAINTENANCE = 'maintenance';
    public const TYPE_OCCUPANCY = 'occupancy';
    public const TYPE_FINANCIAL = 'financial';
    public const TYPE_ACTIVITY = 'activity';
    public const TYPE_CUSTOM = 'custom';

    public const TYPES = [
        self::TYPE_PAYMENT,
        self::TYPE_MAINTENANCE,
        self::TYPE_OCCUPANCY,
        self::TYPE_FINANCIAL,
        self::TYPE_ACTIVITY,
        self::TYPE_CUSTOM,
    ];

    /**
     * Report formats
     */
    public const FORMAT_PDF = 'pdf';
    public const FORMAT_EXCEL = 'excel';
    public const FORMAT_CSV = 'csv';

    public const FORMATS = [
        self::FORMAT_PDF,
        self::FORMAT_EXCEL,
        self::FORMAT_CSV,
    ];

    /**
     * Schedule frequencies
     */
    public const FREQUENCY_DAILY = 'daily';
    public const FREQUENCY_WEEKLY = 'weekly';
    public const FREQUENCY_MONTHLY = 'monthly';

    public const FREQUENCIES = [
        self::FREQUENCY_DAILY,
        self::FREQUENCY_WEEKLY,
        self::FREQUENCY_MONTHLY,
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'generated_by',
        'report_type',
        'title',
        'description',
        'date_range_start',
        'date_range_end',
        'filters',
        'data',
        'file_path',
        'format',
        'generated_at',
        'is_scheduled',
        'schedule_frequency',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date_range_start' => 'date',
        'date_range_end' => 'date',
        'filters' => 'array',
        'data' => 'array',
        'generated_at' => 'datetime',
        'is_scheduled' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Scopes
     */
    public function scopeByType($query, $type)
    {
        return $query->where('report_type', $type);
    }

    public function scopeByDate($query, $startDate, $endDate = null)
    {
        $query->where('generated_at', '>=', $startDate);
        if ($endDate) {
            $query->where('generated_at', '<=', $endDate);
        }
        return $query;
    }

    public function scopeScheduled($query)
    {
        return $query->where('is_scheduled', true);
    }

    /**
     * Methods
     */
    public function generateData(): array
    {
        // This method would compile report data based on report_type
        // Implementation would depend on the specific report type
        return $this->data ?? [];
    }

    public function hasData(): bool
    {
        return !empty($this->data) &&
               (is_array($this->data) && count($this->data) > 0);
    }

    public function exportPdf(): string
    {
        // Implementation would generate PDF and return file path
        // This is a placeholder
        return $this->file_path ?? '';
    }

    public function exportExcel(): string
    {
        // Implementation would generate Excel and return file path
        // This is a placeholder
        return $this->file_path ?? '';
    }

    public function getTypeLabel(): string
    {
        return ucfirst($this->report_type);
    }

    public function getFormatLabel(): string
    {
        return strtoupper($this->format);
    }

    public function getFrequencyLabel(): string
    {
        return ucfirst($this->schedule_frequency ?? '');
    }

    public function getDateRange(): string
    {
        if (!$this->date_range_start || !$this->date_range_end) {
            return 'N/A';
        }

        return $this->date_range_start->format('M d, Y') . ' - ' .
               $this->date_range_end->format('M d, Y');
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($report) {
            if (empty($report->generated_at)) {
                $report->generated_at = now();
            }
            if (empty($report->format)) {
                $report->format = self::FORMAT_PDF;
            }
        });

        static::saving(function ($report) {
            // Validate that report has data before saving
            if (empty($report->data) && !$report->is_scheduled) {
                throw new \Exception('Cannot generate report with no data.');
            }
        });
    }
}
