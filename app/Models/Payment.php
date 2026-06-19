<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
protected $fillable = [
    'tenant_id',
    'unit_id',
    'landlord_id',
    'amount',
    'method',
    'reference',
    'payment_date',
    'status',
    'verified_by_caretaker',
    'verified_by_landlord',
    'verified_at',
];

public function tenant()
{
    return $this->belongsTo(Tenant::class);
}

public function unit()
{
    return $this->belongsTo(Unit::class);
}

public function landlord()
{
    return $this->belongsTo(Landlord::class);
}}
