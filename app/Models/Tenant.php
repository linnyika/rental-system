<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = ['user_id'];

public function user()
{
    return $this->belongsTo(User::class);
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
