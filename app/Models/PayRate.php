<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayRate extends Model
{
    use HasFactory;
    protected $fillable = ['nik', 'hourly_rate', 'effective_date'];

    public function linmas()
    {
        return $this->belongsTo(Linmas::class, 'nik', 'nik');
    }
}
