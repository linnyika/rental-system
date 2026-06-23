<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'caretaker_id',
        'description',
        'activity_date',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
        ];
    }

    public function caretaker()
    {
        return $this->belongsTo(Caretaker::class);
    }
}
