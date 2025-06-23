<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'linmas_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function linmas()
    {
        return $this->belongsTo(Linmas::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPerangkatDesa()
    {
        return $this->role === 'perangkat_desa';
    }
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
}
