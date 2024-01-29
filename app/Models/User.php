<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    /** Status user. ACTIVE */
    const ACTIVE = 1;
    /** Status user. INACTIVE */
    const INACTIVE = 0;
    /** Role user. USER */
    const USER = 0;
    /** Role user. MANAGER */
    const MANAGER = 1;
    /** Role user. ADMIN */
    const ADMIN = 9;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'core_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
