<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MonthClosing extends Model
{
    use HasFactory;

    protected $fillable = [
        'period',
        'year',
        'month',
        'closing_date',
        'total_linmas',
        'total_payrolls',
        'total_amount',
        'closed_by',
        'notes',
        'status'
    ];

    protected $casts = [
        'period' => 'date',
        'closing_date' => 'date',
    ];

    /**
     * Get the user who closed the month
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /**
     * Get the payrolls for this closing
     */
    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'month_closing_id');
    }

    /**
     * Check if a month is closed
     */
    public static function isMonthClosed($year, $month)
    {
        return self::where('year', $year)
            ->where('month', $month)
            ->where('status', 'closed')
            ->exists();
    }

    /**
     * Get formatted period name
     */
    public function getFormattedPeriodAttribute()
    {
        return Carbon::createFromDate($this->year, $this->month, 1)->format('F Y');
    }
}
