<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
  protected $fillable = ['property_id', 'unit_number', 'rent_amount', 'is_occupied'];

public function property()
{
    return $this->belongsTo(Property::class);
}  
}
