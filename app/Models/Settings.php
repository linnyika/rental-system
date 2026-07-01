<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Settings extends Model
{
    use HasFactory;

    /**
     * Setting types
     */
    public const TYPE_STRING = 'string';
    public const TYPE_INTEGER = 'integer';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_JSON = 'json';
    public const TYPE_ARRAY = 'array';

    public const TYPES = [
        self::TYPE_STRING,
        self::TYPE_INTEGER,
        self::TYPE_BOOLEAN,
        self::TYPE_JSON,
        self::TYPE_ARRAY,
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_group',
        'setting_type',
        'is_public',
        'description',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_public' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scopes
     */
    public function scopeByGroup($query, $group)
    {
        return $query->where('setting_group', $group);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByKey($query, $key)
    {
        return $query->where('setting_key', $key);
    }

    /**
     * Methods
     */
    public function getValue()
    {
        return match ($this->setting_type) {
            self::TYPE_INTEGER => (int) $this->setting_value,
            self::TYPE_BOOLEAN => (bool) $this->setting_value,
            self::TYPE_JSON => json_decode($this->setting_value, true),
            self::TYPE_ARRAY => explode(',', $this->setting_value),
            default => $this->setting_value,
        };
    }

    public function setValue($value): void
    {
        $this->setting_value = match ($this->setting_type) {
            self::TYPE_INTEGER => (string) (int) $value,
            self::TYPE_BOOLEAN => $value ? '1' : '0',
            self::TYPE_JSON => json_encode($value),
            self::TYPE_ARRAY => is_array($value) ? implode(',', $value) : (string) $value,
            default => (string) $value,
        };
        $this->save();
    }

    public static function getSystemSettings(): array
    {
        return self::all()->pluck('setting_value', 'setting_key')->toArray();
    }

    public static function getSetting(string $key, $default = null)
    {
        $setting = self::where('setting_key', $key)->first();
        return $setting ? $setting->getValue() : $default;
    }

    public static function setSetting(string $key, $value, ?string $group = 'general', ?string $type = null): void
    {
        $setting = self::where('setting_key', $key)->first();

        if (!$setting) {
            $setting = new self();
            $setting->setting_key = $key;
            $setting->setting_group = $group ?? 'general';
            $setting->setting_type = $type ?? self::TYPE_STRING;
        }

        $setting->setValue($value);
        $setting->save();
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($setting) {
            if (empty($setting->setting_type)) {
                $setting->setting_type = self::TYPE_STRING;
            }
            if (empty($setting->setting_group)) {
                $setting->setting_group = 'general';
            }
        });
    }
}
