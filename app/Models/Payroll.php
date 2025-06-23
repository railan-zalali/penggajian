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
        'notes',
        'processing_status',
        'status_notes',
        'verified_by',
        'approved_by',
        'verified_at',
        'approved_at'
    ];

    protected $casts = [
        'payroll_date' => 'date',
        'payment_date' => 'datetime',
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function linmas()
    {
        return $this->belongsTo(Linmas::class);
    }

    public function details()
    {
        return $this->hasMany(PayrollDetail::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function monthClosing()
    {
        return $this->belongsTo(MonthClosing::class);
    }

    /**
     * Check if the current status can transition to the target status
     */
    public function canTransitionTo(string $targetStatus): bool
    {
        $validTransitions = [
            'draft' => ['verified', 'rejected'],
            'verified' => ['calculated', 'rejected'],
            'calculated' => ['approved', 'rejected'],
            'approved' => ['processed', 'rejected'],
            'processed' => ['completed', 'rejected'],
            'completed' => [], // End state
            'rejected' => ['draft'] // Can be restarted
        ];

        return in_array($targetStatus, $validTransitions[$this->processing_status] ?? []);
    }

    /**
     * Get the status text for display
     */
    public function getStatusTextAttribute(): string
    {
        $statusTexts = [
            'draft' => 'Draft',
            'verified' => 'Terverifikasi',
            'calculated' => 'Terhitung',
            'approved' => 'Disetujui',
            'processed' => 'Diproses',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak'
        ];

        return $statusTexts[$this->processing_status] ?? $this->processing_status;
    }
}
