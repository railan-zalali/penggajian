<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_id',
        'type_id',
        'name',
        'type',
        'amount'
    ];

    /**
     * Get the payroll that owns this detail
     */
    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    /**
     * Get the type of this detail
     */
    public function allowanceDeductionType()
    {
        return $this->belongsTo(AllowanceDeductionType::class, 'type_id');
    }

    /**
     * Get all allowances for a payroll
     */
    public static function getAllowances($payrollId)
    {
        return self::where('payroll_id', $payrollId)
            ->where('type', 'allowance')
            ->get();
    }

    /**
     * Get all deductions for a payroll
     */
    public static function getDeductions($payrollId)
    {
        return self::where('payroll_id', $payrollId)
            ->where('type', 'deduction')
            ->get();
    }
}
