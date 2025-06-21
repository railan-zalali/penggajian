<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class linmas extends Model
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
}
