<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
protected $fillable = ['tenant_id', 'unit_id', 'description', 'status', 'is_major'];
public function tenant(){ return $this->belongsTo(Tenant::class); }
public function unit(){ return $this->belongsTo(Unit::class); }
public function task(){ return $this->hasOne(Task::class); }
}
