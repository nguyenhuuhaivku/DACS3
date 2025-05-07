<?php


namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;


class Admin extends Authenticatable
{
    use Notifiable;


    protected $guard = 'admin';
    protected $table = 'admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'last_login_ip',
        'last_login_at'
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'last_login_at' => 'datetime',
    ];
}
