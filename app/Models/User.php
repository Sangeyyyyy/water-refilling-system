<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * User Roles
     */
    const ROLE_ADMIN = 'admin';       // TSSU admin
    const ROLE_DIRECTOR = 'director'; // Charlo
    const ROLE_MANAGER = 'manager';
    const ROLE_STAFF = 'staff';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'office_id',
        'contact_number',
        'gallon_count',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
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
     * Get the user's full name.
     */
    public function getNameAttribute($value)
    {
        if ($this->first_name && $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
        }
        return $value;
    }
}
