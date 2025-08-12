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
        try {
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

            // Log untuk debugging
            \Illuminate\Support\Facades\Log::info("Fetching payroll workflow list", [
                'status_filter' => $request->status ?? 'all',
                'month_filter' => $request->month ?? 'all',
                'year_filter' => $request->year ?? 'all'
            ]);

            $payrolls = $query->orderBy('payroll_date', 'desc')->paginate(15);

            return view('payroll.workflow.index', compact('payrolls'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error in payroll workflow index", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return view('payroll.workflow.index', ['payrolls' => collect()])->with('error', 'Terjadi kesalahan saat memuat data: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail payroll untuk verifikasi/approval
     */
    public function show(Payroll $payroll)
    {
        try {
            // Log untuk debugging
            \Illuminate\Support\Facades\Log::info("Viewing payroll detail", [
                'payroll_id' => $payroll->id,
                'linmas_id' => $payroll->linmas_id,
                'current_status' => $payroll->processing_status,
                'user_id' => \Illuminate\Support\Facades\Auth::id()
            ]);

            $payroll->load(['linmas', 'details', 'verifier', 'approver']);

            // Periksa apakah data linmas ada
            if (!$payroll->linmas) {
                \Illuminate\Support\Facades\Log::warning("Linmas not found for payroll ID: {$payroll->id}");
                return redirect()->route('payroll.workflow.index')
                    ->withErrors(['msg' => 'Data anggota Linmas tidak ditemukan untuk penggajian ini.']);
            }

            // Dapatkan daftar status yang valid untuk transisi
            $validStatusTransitions = [];
            $allStatuses = ['verified', 'calculated', 'approved', 'processed', 'completed', 'rejected', 'draft'];

            foreach ($allStatuses as $status) {
                if ($payroll->canTransitionTo($status)) {
                    $validStatusTransitions[$status] = $this->getStatusLabel($status);
                }
            }

            return view('payroll.workflow.show', compact('payroll', 'validStatusTransitions'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error viewing payroll detail", [
                'payroll_id' => $payroll->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('payroll.workflow.index')
                ->withErrors(['msg' => 'Terjadi kesalahan saat memuat detail penggajian: ' . $e->getMessage()]);
        }
    }

    /**
     * Update status payroll
     */
    public function updateStatus(Request $request, Payroll $payroll)
    {
        try {
            $request->validate([
                'status' => 'required|string|in:draft,verified,calculated,approved,processed,completed,rejected',
                'notes' => 'nullable|string|max:500',
            ]);

            $targetStatus = $request->status;
            $currentStatus = $payroll->processing_status;

            // Log untuk debugging
            \Illuminate\Support\Facades\Log::info("Attempting to change payroll status", [
                'payroll_id' => $payroll->id,
                'linmas_id' => $payroll->linmas_id,
                'current_status' => $currentStatus,
                'target_status' => $targetStatus,
                'user_id' => Auth::id()
            ]);

            // Validasi peran pengguna berdasarkan status target
            $userRoles = Auth::user()->roles->pluck('name')->toArray();
            $requiredRoles = [
                'verified' => ['admin', 'verifier'],
                'approved' => ['admin', 'approver'],
                'processed' => ['admin', 'finance'],
                'completed' => ['admin', 'finance'],
            ];
            
            // Cek apakah pengguna memiliki peran yang diperlukan untuk status ini
            if (isset($requiredRoles[$targetStatus]) && !empty($requiredRoles[$targetStatus])) {
                $hasRequiredRole = false;
                foreach ($userRoles as $role) {
                    if (in_array($role, $requiredRoles[$targetStatus])) {
                        $hasRequiredRole = true;
                        break;
                    }
                }
                
                if (!$hasRequiredRole) {
                    \Illuminate\Support\Facades\Log::warning("Unauthorized status change attempt", [
                        'payroll_id' => $payroll->id,
                        'user_id' => Auth::id(),
                        'user_roles' => $userRoles,
                        'required_roles' => $requiredRoles[$targetStatus],
                        'target_status' => $targetStatus
                    ]);
                    return back()->with('error', "Anda tidak memiliki hak akses untuk mengubah status menjadi '{$this->getStatusLabel($targetStatus)}'");
                }
            }
            
            // Validasi konsistensi status menggunakan metode baru
            [$isValid, $errorMessage] = $payroll->validateStatusConsistency($targetStatus, Auth::id());
            if (!$isValid) {
                \Illuminate\Support\Facades\Log::warning("Invalid status transition", [
                    'payroll_id' => $payroll->id,
                    'current_status' => $currentStatus,
                    'target_status' => $targetStatus,
                    'error' => $errorMessage
                ]);
                return back()->with('error', $errorMessage);
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

            \Illuminate\Support\Facades\Log::info("Payroll status changed successfully", [
                'payroll_id' => $payroll->id,
                'from_status' => $currentStatus,
                'to_status' => $targetStatus
            ]);

            return redirect()->route('payroll.workflow.index')
                ->with('success', "Status penggajian berhasil diubah menjadi '{$this->getStatusLabel($targetStatus)}'");
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::warning("Validation error in updateStatus", [
                'errors' => $e->errors()
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error updating payroll status", [
                'payroll_id' => $payroll->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', "Terjadi kesalahan saat mengubah status: {$e->getMessage()}");
        }
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
     * 
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function report(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'month' => 'nullable|integer|min:1|max:12',
                'year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
                'export' => 'nullable|in:pdf,excel'
            ]);
            
            $query = Payroll::with(['linmas', 'verifier', 'approver']);

            // Filter berdasarkan periode
            if ($request->filled('month')) {
                $query->whereMonth('payroll_date', $request->month);
            }
            
            if ($request->filled('year')) {
                $query->whereYear('payroll_date', $request->year);
            }

            // Log untuk debugging
            \Illuminate\Support\Facades\Log::info("Generating workflow report", [
                'month_filter' => $request->month ?? 'all',
                'year_filter' => $request->year ?? 'all',
                'export_format' => $request->export ?? 'none',
                'user_id' => \Illuminate\Support\Facades\Auth::id()
            ]);

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
            
            // Tambahkan data detail untuk tabel
            $payrolls = $query->orderBy('payroll_date', 'desc')->get();
            $detailData = [
                'draft' => $payrolls->where('processing_status', 'draft'),
                'verified' => $payrolls->where('processing_status', 'verified'),
                'calculated' => $payrolls->where('processing_status', 'calculated'),
                'approved' => $payrolls->where('processing_status', 'approved'),
                'processed' => $payrolls->where('processing_status', 'processed'),
                'completed' => $payrolls->where('processing_status', 'completed'),
                'rejected' => $payrolls->where('processing_status', 'rejected'),
            ];
            
            // Jika request adalah untuk export
            if ($request->filled('export')) {
                if ($request->export === 'pdf') {
                    return $this->exportReportToPdf($summary, $chartData, $detailData, $request->month, $request->year);
                } elseif ($request->export === 'excel') {
                    return $this->exportReportToExcel($summary, $detailData, $request->month, $request->year);
                }
            }

            // Tampilkan view dengan data
            $monthName = $request->filled('month') ? \Carbon\Carbon::create(null, $request->month, 1)->format('F') : null;
            $yearValue = $request->year;
            
            return view('payroll.workflow.report', compact('summary', 'chartData', 'detailData', 'monthName', 'yearValue'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::warning("Validation error in workflow report", [
                'errors' => $e->errors()
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error generating workflow report", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Buat data kosong untuk ditampilkan
            $summary = [
                'draft' => 0,
                'verified' => 0,
                'calculated' => 0,
                'approved' => 0,
                'processed' => 0,
                'completed' => 0,
                'rejected' => 0,
            ];

            $chartData = [
                'labels' => array_map(function ($status) {
                    return $this->getStatusLabel($status);
                }, array_keys($summary)),
                'data' => array_values($summary)
            ];
            
            $detailData = [
                'draft' => collect(),
                'verified' => collect(),
                'calculated' => collect(),
                'approved' => collect(),
                'processed' => collect(),
                'completed' => collect(),
                'rejected' => collect(),
            ];

            return view('payroll.workflow.report', compact('summary', 'chartData', 'detailData'))
                ->with('error', 'Terjadi kesalahan saat membuat laporan: ' . $e->getMessage());
        }
    }
    
    /**
     * Export laporan ke PDF
     * 
     * @param array $summary Data ringkasan status
     * @param array $chartData Data untuk chart
     * @param array $detailData Data detail per status
     * @param int|null $month Bulan filter
     * @param int|null $year Tahun filter
     * @return \Illuminate\Http\Response
     */
    private function exportReportToPdf(array $summary, array $chartData, array $detailData, ?int $month = null, ?int $year = null)
    {
        // Gunakan package PDF yang tersedia (misalnya dompdf)
        $pdf = \PDF::loadView('payroll.workflow.report_pdf', compact('summary', 'chartData', 'detailData', 'month', 'year'));
        
        $filename = 'laporan_status_penggajian';
        if ($month && $year) {
            $filename .= "_{$year}_" . str_pad($month, 2, '0', STR_PAD_LEFT);
        } elseif ($year) {
            $filename .= "_{$year}";
        }
        $filename .= '.pdf';
        
        return $pdf->download($filename);
    }
    
    /**
     * Export laporan ke Excel
     * 
     * @param array $summary Data ringkasan status
     * @param array $detailData Data detail per status
     * @param int|null $month Bulan filter
     * @param int|null $year Tahun filter
     * @return \Illuminate\Http\Response
     */
    private function exportReportToExcel(array $summary, array $detailData, ?int $month = null, ?int $year = null)
    {
        // Gunakan package Excel yang tersedia (misalnya maatwebsite/excel)
        $export = new \App\Exports\PayrollStatusExport($summary, $detailData);
        
        $filename = 'laporan_status_penggajian';
        if ($month && $year) {
            $filename .= "_{$year}_" . str_pad($month, 2, '0', STR_PAD_LEFT);
        } elseif ($year) {
            $filename .= "_{$year}";
        }
        $filename .= '.xlsx';
        
        return \Excel::download($export, $filename);
    }
}
