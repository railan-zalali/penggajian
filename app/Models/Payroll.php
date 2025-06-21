<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'linmas_id',
        'total_days_present',
        'base_salary',
        'overtime_payment',
        'total_salary',
        'payroll_date',
        'payment_status',
        'payment_method',
        'payment_reference',
        'payment_date',
        'notes'
    ];

    protected $casts = [
        'payroll_date' => 'date',
        'payment_date' => 'datetime',
    ];

    public function linmas()
    {
        return $this->belongsTo(Linmas::class);
    }
}
