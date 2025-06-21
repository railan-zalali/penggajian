<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key',
        'value',
        'description',
        'is_active'
    ];

    /**
     * Get rate value by key
     *
     * @param string $key
     * @return float|null
     */
    public static function getRate(string $key)
    {
        $rate = self::where('key', $key)->where('is_active', true)->first();
        return $rate ? $rate->value : null;
    }
}
