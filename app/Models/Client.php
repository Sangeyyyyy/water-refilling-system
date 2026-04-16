<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Client extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'office_id',
        'contact_number',
        'gallon_count',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the client's full name.
     */
    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
