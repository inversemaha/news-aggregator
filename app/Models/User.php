<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /**
     * @OA\Schema(
     *     schema="User",
     *     description="User model",
     *     @OA\Property(property="id", type="integer", description="User ID"),
     *     @OA\Property(property="name", type="string", description="User's full name"),
     *     @OA\Property(property="email", type="string", format="email", description="User's email address"),
     *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the user was created"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp when the user was last updated")
     * )
     */

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
}
