<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
  protected $fillable = ['property_id', 'unit_number', 'rent_amount', 'is_occupied'];

protected function casts(): array
{
    return [
        'rent_amount' => 'decimal:2',
        'is_occupied' => 'boolean',
    ];
}

public function property()
{
    return $this->belongsTo(Property::class);
}  

public function occupancies()
{
    return $this->hasMany(TenantOccupancy::class);
}

public function payments()
{
    return $this->hasMany(Payment::class);
}
}
