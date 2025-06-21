<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllowanceDeductionType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'calculation_type',
        'default_value',
        'description',
        'is_taxable',
        'is_active'
    ];

    /**
     * Get all allowances
     */
    public static function getAllowances()
    {
        return self::where('type', 'allowance')->where('is_active', true)->get();
    }

    /**
     * Get all deductions
     */
    public static function getDeductions()
    {
        return self::where('type', 'deduction')->where('is_active', true)->get();
    }

    /**
     * Get linmas assignments
     */
    public function linmasAssignments()
    {
        return $this->hasMany(LinmasAllowanceDeduction::class, 'type_id');
    }

    /**
     * Get payroll details
     */
    public function payrollDetails()
    {
        return $this->hasMany(PayrollDetail::class, 'type_id');
    }
}
