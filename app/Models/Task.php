<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['maintenance_request_id', 'caretaker_id', 'status', 'tenant_confirmed', 'completed_at'];
public function request(){ return $this->belongsTo(MaintenanceRequest::class, 'maintenance_request_id'); }
public function caretaker(){ return $this->belongsTo(Caretaker::class); }
}
