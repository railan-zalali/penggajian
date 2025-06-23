<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class attendances extends Model
{
    use HasFactory;
    protected $fillable = [
        'linmas_id',
        'waktu',
        'status',
        'status_baru',
        'pengecualian',
    ];

    public function linmas()
    {
        return $this->belongsTo(Linmas::class, 'linmas_id');
    }
}
