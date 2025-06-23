<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollWorkflowController extends Controller
{
    /**
     * Tampilkan daftar payroll dengan status
     */
    public function index(Request $request)
    {
        $query = Payroll::with(['linmas', 'verifier', 'approver']);

        // Filter berdasarkan status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('processing_status', $request->status);
        }

        // Filter berdasarkan periode
        if ($request->has('month') && $request->has('year')) {
            $query->whereMonth('payroll_date', $request->month)
                ->whereYear('payroll_date', $request->year);
        }

        $payrolls = $query->orderBy('payroll_date', 'desc')->paginate(15);

        return view('payroll.workflow.index', compact('payrolls'));
    }

    /**
     * Tampilkan detail payroll untuk verifikasi/approval
     */
    public function show(Payroll $payroll)
    {
        $payroll->load(['linmas', 'details', 'verifier', 'approver']);

        // Dapatkan daftar status yang valid untuk transisi
        $validStatusTransitions = [];
        $allStatuses = ['verified', 'calculated', 'approved', 'processed', 'completed', 'rejected', 'draft'];

        foreach ($allStatuses as $status) {
            if ($payroll->canTransitionTo($status)) {
                $validStatusTransitions[$status] = $this->getStatusLabel($status);
            }
        }

        return view('payroll.workflow.show', compact('payroll', 'validStatusTransitions'));
    }

    /**
     * Update status payroll
     */
    public function updateStatus(Request $request, Payroll $payroll)
    {
        $request->validate([
            'status' => 'required|string|in:draft,verified,calculated,approved,processed,completed,rejected',
            'notes' => 'nullable|string|max:500',
        ]);

        $targetStatus = $request->status;

        // Cek apakah transisi status valid
        if (!$payroll->canTransitionTo($targetStatus)) {
            return back()->with('error', "Tidak dapat mengubah status dari '{$payroll->status_text}' ke '{$this->getStatusLabel($targetStatus)}'");
        }

        // Update status
        $payroll->processing_status = $targetStatus;
        $payroll->status_notes = $request->notes;

        // Catat user yang melakukan verifikasi/approval
        if ($targetStatus === 'verified') {
            $payroll->verified_by = Auth::id();
            $payroll->verified_at = now();
        } elseif ($targetStatus === 'approved') {
            $payroll->approved_by = Auth::id();
            $payroll->approved_at = now();
        }

        // Otomatis update payment_status jika status adalah completed/rejected
        if ($targetStatus === 'completed') {
            $payroll->payment_status = 'paid';
            $payroll->payment_date = now();
        } elseif ($targetStatus === 'rejected') {
            $payroll->payment_status = 'cancelled';
        }

        $payroll->save();

        return redirect()->route('payroll.workflow.index')
            ->with('success', "Status penggajian berhasil diubah menjadi '{$this->getStatusLabel($targetStatus)}'");
    }

    /**
     * Get label status untuk UI
     */
    private function getStatusLabel($status): string
    {
        $labels = [
            'draft' => 'Draft',
            'verified' => 'Terverifikasi',
            'calculated' => 'Terhitung',
            'approved' => 'Disetujui',
            'processed' => 'Diproses',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak'
        ];

        return $labels[$status] ?? ucfirst($status);
    }

    /**
     * Generate laporan alur proses
     */
    public function report(Request $request)
    {
        $query = Payroll::with(['linmas', 'verifier', 'approver']);

        // Filter berdasarkan periode
        if ($request->has('month') && $request->has('year')) {
            $query->whereMonth('payroll_date', $request->month)
                ->whereYear('payroll_date', $request->year);
        }

        // Grouping berdasarkan status
        $summary = [
            'draft' => $query->clone()->where('processing_status', 'draft')->count(),
            'verified' => $query->clone()->where('processing_status', 'verified')->count(),
            'calculated' => $query->clone()->where('processing_status', 'calculated')->count(),
            'approved' => $query->clone()->where('processing_status', 'approved')->count(),
            'processed' => $query->clone()->where('processing_status', 'processed')->count(),
            'completed' => $query->clone()->where('processing_status', 'completed')->count(),
            'rejected' => $query->clone()->where('processing_status', 'rejected')->count(),
        ];

        // Data untuk grafik pie
        $chartData = [
            'labels' => array_map(function ($status) {
                return $this->getStatusLabel($status);
            }, array_keys($summary)),
            'data' => array_values($summary)
        ];

        return view('payroll.workflow.report', compact('summary', 'chartData'));
    }
}
