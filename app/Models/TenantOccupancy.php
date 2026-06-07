<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantOccupancy extends Model
{
    protected $fillable = ['tenant_id', 'unit_id', 'start_date', 'end_date'];

public function tenant()
{
    return $this->belongsTo(Tenant::class);
}

public function unit()
{
    return $this->belongsTo(Unit::class);
}
}
