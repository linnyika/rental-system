<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = ['landlord_id', 'name', 'address'];

public function landlord()
{
    return $this->belongsTo(Landlord::class);
}

public function units()
{
    return $this->hasMany(Unit::class);
}
}
