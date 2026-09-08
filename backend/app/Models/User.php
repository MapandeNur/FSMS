<?php

namespace App\Models;

<<<<<<< HEAD
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifable;

>>>>>>> 5d48079fd2491b555b2bd37ceedda945168a6850
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

<<<<<<< HEAD
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
=======
>>>>>>> 5d48079fd2491b555b2bd37ceedda945168a6850
    protected $hidden = [
        'password',
        'remember_token',
    ];

<<<<<<< HEAD
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
=======
>>>>>>> 5d48079fd2491b555b2bd37ceedda945168a6850
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
