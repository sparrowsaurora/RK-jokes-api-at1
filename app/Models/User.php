<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory>
     * @method bool hasRole(string|array $roles)
     * @method \Illuminate\Support\Collection getRoleNames()
     */
    use HasRoles;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'given_name',
        'family_name',
        'city',
        'state',
        // add all other columns you want mass assignable here
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function reactions()
    {
        return $this->hasMany(JokeReaction::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function clients()
    {
        return $this->hasMany(User::class, 'assigned_staff_id');
    }
}
