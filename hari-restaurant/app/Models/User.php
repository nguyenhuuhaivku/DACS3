<?php

namespace App\Models;

use App\Models\user\Cart;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'roles'
    ];
    public function hasRole($role)
    {
        return $this->roles === $role;
    }
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function cart()
    {
        return $this->hasMany(Cart::class, 'user_id');
    }
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'UserID');
    }
    public function payments()
    {
        return $this->hasManyThrough(
            Payment::class,
            Reservation::class,
            'UserID', // Foreign key on reservations table
            'ReservationID', // Foreign key on payments table
            'id', // Local key on users table
            'ReservationID' // Local key on reservations table
        );
    }
}
