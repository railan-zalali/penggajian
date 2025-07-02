<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Linmas extends Authenticatable
{
    use HasFactory, Notifiable;
    
    protected $fillable = [
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'pendidikan',
        'pekerjaan',
        'tanggal_bergabung',
        'status',
        'posisi',
        'gaji_pokok',
        'can_login',
        'password',
        'email',
        'email_verified_at'
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'tanggal_lahir' => 'date',
        'tanggal_bergabung' => 'date',
        'gaji_pokok' => 'decimal:2',
        'can_login' => 'boolean',
    ];
    
    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'nik';
    }
    
    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->getAttribute('nik');
    }
    
    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->password;
    }
    
    /**
     * Get the name attribute for display.
     */
    public function getNameAttribute()
    {
        return $this->nama;
    }
    
    /**
     * Check if user has perangkat_desa role.
     */
    public function hasRole($role)
    {
        return $role === 'perangkat_desa' && $this->can_login;
    }
    
    /**
     * Check if user is perangkat desa.
     */
    public function isPerangkatDesa()
    {
        return $this->can_login;
    }
    
    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return false; // Linmas tidak bisa jadi admin
    }
    public function attendances()
    {
        return $this->hasMany(Attendances::class, 'linmas_id');
    }

    // Tambahkan relasi ke Payroll
    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
    public function user()
    {
        return $this->hasOne(User::class);
    }
}
