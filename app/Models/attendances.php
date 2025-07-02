<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendances extends Model
{
    use HasFactory;
    protected $fillable = [
        'linmas_id',
        'waktu',
        'status',
        'status_baru',
        'pengecualian',
    ];

    // di model Attendance.php
    protected $casts = [
        'waktu' => 'datetime',
    ];

    public function linmas()
    {
        return $this->belongsTo(Linmas::class, 'linmas_id');
    }
}
