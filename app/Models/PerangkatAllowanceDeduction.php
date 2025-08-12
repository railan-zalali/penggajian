<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerangkatAllowanceDeduction extends Model
{
    use HasFactory;

    protected $table = 'tunjangan_potongan_perangkat';

    protected $fillable = [
        'linmas_id',
        'jenis_id',
        'nilai',
        'aktif'
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    /**
     * Get the perangkat that owns this allowance/deduction
     */
    public function perangkat()
    {
        return $this->belongsTo(Linmas::class);
    }

    /**
     * Get the type of this allowance/deduction
     */
    // public function type()
    // {
    //     return $this->belongsTo(JenisTunjanganPotongan::class, 'jenis_id');
    // }

    /**
     * Get all active allowances for a perangkat
     */
    public static function getPerangkatAllowances($perangkatId)
    {
        return self::where('perangkat_id', $perangkatId)
            ->where('aktif', true)
            ->whereHas('type', function ($query) {
                $query->where('tipe', 'tunjangan')->where('aktif', true);
            })
            ->with('type')
            ->get();
    }

    /**
     * Get all active deductions for a perangkat
     */
    public static function getPerangkatDeductions($perangkatId)
    {
        return self::where('perangkat_id', $perangkatId)
            ->where('aktif', true)
            ->whereHas('type', function ($query) {
                $query->where('tipe', 'potongan')->where('aktif', true);
            })
            ->with('type')
            ->get();
    }

    /**
     * Alias for backward compatibility
     */
    public static function getLinmasAllowances($linmasId)
    {
        return self::getPerangkatAllowances($linmasId);
    }

    /**
     * Alias for backward compatibility
     */
    public static function getLinmasDeductions($linmasId)
    {
        return self::getPerangkatDeductions($linmasId);
    }

    /**
     * Alias for backward compatibility
     */
    public function linmas()
    {
        return $this->perangkat();
    }
}
