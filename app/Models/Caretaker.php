<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caretaker extends Model
{
protected $fillable = ['user_id', 'landlord_id'];

public function user()
{
    return $this->belongsTo(User::class);
}

public function landlord()
{
    return $this->belongsTo(Landlord::class);
}

public function properties()
{
    return $this->hasMany(Property::class);
}

public function tasks()
{
    return $this->hasMany(Task::class);
}

public function activityLogs()
{
    return $this->hasMany(ActivityLog::class);
}
}
