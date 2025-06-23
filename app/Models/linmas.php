<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Linmas extends Model
{
    use HasFactory;
    protected $fillable = [
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'pendidikan',
        'pekerjaan'
    ];
    public function attendances()
    {
        return $this->hasMany(Attendances::class, 'linmas_id');
    }

    // Tambahkan relasi ke Payroll
    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
}
