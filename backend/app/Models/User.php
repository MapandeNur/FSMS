<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
<<<<<<< HEAD
    /** @use HasFactory<UserFactory> */
=======
    /** @use HasFactory<\Database\Factories\UserFactory> */
>>>>>>> a45ebbfdcd09c358913bc2cf7dd54bcbf2254134
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
<<<<<<< HEAD
    /**
 * The roles that belong to the user.
 */
public function roles()
{
    return $this->belongsToMany(\App\Models\Role::class, 'role_user')
                ->withPivot('start_date', 'end_date')
                ->withTimestamps();
=======

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
>>>>>>> a45ebbfdcd09c358913bc2cf7dd54bcbf2254134
}

    }
