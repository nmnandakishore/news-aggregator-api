<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


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

    public static array $createRules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|max:255|email|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ];

    public static array $loginRules = [
        'email' => 'required|string|email',
        'password' => 'required|string',
    ];

    public static array $emailRules = [
        'email' => 'required|string|email|max:255'
    ];

    public static array $passwordResetRules = [
        'email' => 'required|string|email|max:255',
        'password' => 'required|string|min:8|confirmed',
        'token' => 'required|string'
    ];
}
