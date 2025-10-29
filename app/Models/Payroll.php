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
        'approved_at',
        'payment_confirmed',
        'payment_confirmed_at',
        'payment_confirmation_note'
    ];

    protected $casts = [
        'payroll_date' => 'date',
        'payment_date' => 'datetime',
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'payment_confirmed_at' => 'datetime',
        'payment_confirmed' => 'boolean',
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
     * 
     * @param string $targetStatus The target status to transition to
     * @return bool Whether the transition is valid
     */
    public function canTransitionTo(string $targetStatus): bool
    {
        // If current status is null or empty, assume it's a new record
        if (empty($this->processing_status)) {
            return $targetStatus === 'draft';
        }
        
        $validTransitions = [
            'draft' => ['verified', 'rejected'],
            'verified' => ['calculated', 'rejected'],
            'calculated' => ['approved', 'rejected'],
            'approved' => ['processed', 'rejected'],
            'processed' => ['completed', 'rejected'],
            'completed' => [], // End state
            'rejected' => ['draft'] // Can be restarted
        ];
        
        // Check if current status exists in valid transitions
        if (!array_key_exists($this->processing_status, $validTransitions)) {
            return false;
        }
        
        return in_array($targetStatus, $validTransitions[$this->processing_status]);
    }
    
    /**
     * Validate if the status change is consistent with business rules
     * 
     * @param string $targetStatus The target status to transition to
     * @param int|null $userId The ID of the user making the change
     * @return array [bool $isValid, string $errorMessage]
     */
    public function validateStatusConsistency(string $targetStatus, ?int $userId = null): array
    {
        // Check if the transition is valid based on workflow rules
        if (!$this->canTransitionTo($targetStatus)) {
            return [false, "Status tidak dapat diubah dari '{$this->processing_status}' ke '{$targetStatus}'"];
        }
        
        // Specific validation rules for each status transition
        switch ($targetStatus) {
            case 'verified':
                if (!$userId) {
                    return [false, 'Verifikasi membutuhkan ID pengguna yang valid'];
                }
                break;
                
            case 'approved':
                if (!$userId) {
                    return [false, 'Persetujuan membutuhkan ID pengguna yang valid'];
                }
                
                // Ensure it has been verified first
                if (empty($this->verified_by) || empty($this->verified_at)) {
                    return [false, 'Penggajian harus diverifikasi terlebih dahulu sebelum disetujui'];
                }
                
                // Verifier and approver should be different people
                if ($userId == $this->verified_by) {
                    return [false, 'Verifikator dan pemberi persetujuan harus orang yang berbeda'];
                }
                break;
                
            case 'processed':
                // Ensure it has been approved
                if (empty($this->approved_by) || empty($this->approved_at)) {
                    return [false, 'Penggajian harus disetujui terlebih dahulu sebelum diproses'];
                }
                break;
                
            case 'completed':
                // Ensure payment details are provided
                if (empty($this->payment_method) || empty($this->payment_reference)) {
                    return [false, 'Detail pembayaran harus dilengkapi sebelum menyelesaikan proses'];
                }
                break;
        }
        
        return [true, ''];
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
