<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function landlord()
{
    return $this->hasOne(Landlord::class);
}

public function tenant()
{
    return $this->hasOne(Tenant::class);
}

public function caretaker()
{
    return $this->hasOne(Caretaker::class);
}

public function notifications()
{
    return $this->hasMany(Notification::class);
}


 // Role helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isLandlord()
    {
        return $this->role === 'landlord';
    }

    public function isCaretaker()
    {
        return $this->role === 'caretaker';
    }

    public function isTenant()
    {
        return $this->role === 'tenant';
    }
}
