<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinmasAllowanceDeduction extends Model
{
    use HasFactory;

    protected $table = 'linmas_allowances_deductions';

    protected $fillable = [
        'linmas_id',
        'type_id',
        'value',
        'is_active'
    ];

    /**
     * Get the linmas that owns this allowance/deduction
     */
    public function linmas()
    {
        return $this->belongsTo(Linmas::class);
    }

    /**
     * Get the type of this allowance/deduction
     */
    public function type()
    {
        return $this->belongsTo(AllowanceDeductionType::class, 'type_id');
    }

    /**
     * Get all active allowances for a linmas
     */
    public static function getLinmasAllowances($linmasId)
    {
        return self::where('linmas_id', $linmasId)
            ->where('is_active', true)
            ->whereHas('type', function ($query) {
                $query->where('type', 'allowance')->where('is_active', true);
            })
            ->with('type')
            ->get();
    }

    /**
     * Get all active deductions for a linmas
     */
    public static function getLinmasDeductions($linmasId)
    {
        return self::where('linmas_id', $linmasId)
            ->where('is_active', true)
            ->whereHas('type', function ($query) {
                $query->where('type', 'deduction')->where('is_active', true);
            })
            ->with('type')
            ->get();
    }
}
