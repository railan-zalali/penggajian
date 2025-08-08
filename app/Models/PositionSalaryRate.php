<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PositionSalaryRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'position',
        'monthly_rate',
        'daily_rate',
        'working_days',
        'description',
        'is_active'
    ];

    /**
     * Get the daily rate for a specific position
     *
     * @param string $position
     * @return float|null
     */
    public static function getDailyRate(string $position)
    {
        $rate = self::where('position', $position)
            ->where('is_active', true)
            ->first();

        return $rate ? $rate->daily_rate : null;
    }

    /**
     * Get the monthly rate for a specific position
     *
     * @param string $position
     * @return float|null
     */
    public static function getMonthlyRate(string $position)
    {
        $rate = self::where('position', $position)
            ->where('is_active', true)
            ->first();

        return $rate ? $rate->monthly_rate : null;
    }

    /**
     * Calculate daily rate from monthly rate
     *
     * @param float $monthlyRate
     * @param int $workingDays
     * @return float
     */
    public static function calculateDailyRate(float $monthlyRate, int $workingDays = 22)
    {
        return $monthlyRate / $workingDays;
    }

    /**
     * Get all active position rates
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveRates()
    {
        return self::where('is_active', true)->get();
    }
}