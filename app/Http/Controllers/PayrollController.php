<?php

namespace App\Http\Controllers;

use App\Models\Attendances;
use App\Models\Linmas;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\SalaryRate;
use App\Models\AllowanceDeductionType;
use App\Models\LinmasAllowanceDeduction;
use App\Models\PositionSalaryRate;
use App\Services\PayrollCalculationService;
use App\Services\PayrollStorageService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PayrollController extends Controller
{
    protected $payrollCalculationService;
    protected $payrollStorageService;

    public function __construct(
        PayrollCalculationService $payrollCalculationService,
        PayrollStorageService $payrollStorageService
    ) {
        $this->payrollCalculationService = $payrollCalculationService;
        $this->payrollStorageService = $payrollStorageService;
    }

    public function index()
    {
        return view('payroll.index');
    }

    public function calculatePayroll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_month' => 'required|date_format:Y-m',
            'end_month' => 'required|date_format:Y-m|after_or_equal:start_month',
        ]);

        if ($validator->fails()) {
            return redirect()->route('payroll.index')
                ->withErrors($validator)
                ->withInput();
        }

        $startDate = Carbon::parse($request->start_month)->startOfMonth();
        $endDate = Carbon::parse($request->end_month)->endOfMonth();

        $payrollData = $this->payrollCalculationService->calculate($startDate, $endDate);

        return view('payroll.index', compact('payrollData', 'startDate', 'endDate'));
    }

    public function exportPdf(Request $request)
    {
        try {
            $payrollData = json_decode($request->payroll_data, true);

            // Validasi data penggajian
            if (empty($payrollData) || !is_array($payrollData)) {
                \Illuminate\Support\Facades\Log::warning("Invalid payroll data in exportPdf", [
                    'payroll_data' => $request->payroll_data
                ]);
                return redirect()->back()->withErrors(['msg' => 'Data penggajian kosong atau tidak valid.']);
            }

            // Validasi tanggal
            try {
                $startDate = $request->start_date;
                $endDate = $request->end_date;

                // Pastikan tanggal valid
                if (!$startDate || !$endDate) {
                    throw new \Exception('Tanggal tidak boleh kosong');
                }

                // Validasi format tanggal
                Carbon::parse($startDate);
                Carbon::parse($endDate);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Invalid date format in exportPdf", [
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'error' => $e->getMessage()
                ]);
                return redirect()->back()->withErrors(['msg' => 'Format tanggal tidak valid.']);
            }

            // Log untuk debugging
            \Illuminate\Support\Facades\Log::info("Generating PDF report", [
                'record_count' => count($payrollData),
                'date_range' => "{$startDate} - {$endDate}"
            ]);

            try {
                $pdf = Pdf::loadView('payroll.report', compact('payrollData', 'startDate', 'endDate'));
                return $pdf->download('laporan_penggajian.pdf');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error generating PDF report", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->back()->withErrors(['msg' => 'Terjadi kesalahan saat membuat laporan PDF: ' . $e->getMessage()]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Unexpected error in exportPdf", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['msg' => 'Terjadi kesalahan yang tidak terduga: ' . $e->getMessage()]);
        }
    }

    public function exportSlip(Request $request, $nik)
    {
        try {
            $payrollData = collect(json_decode($request->payroll_data, true) ?? [])->firstWhere('nik', $nik);

            // Log untuk debugging
            \Illuminate\Support\Facades\Log::info("Exporting slip for NIK: {$nik}", [
                'has_payroll_data' => !empty($payrollData),
                'has_date_range' => $request->has('start_date') && $request->has('end_date')
            ]);

            // Jika data tidak dari form, coba ambil dari database
            if (!$payrollData && $request->has('start_date') && $request->has('end_date')) {
                try {
                    $startDate = Carbon::parse($request->start_date)->startOfMonth();
                    $endDate = Carbon::parse($request->end_date)->endOfMonth();

                    $linmas = Linmas::where('nik', $nik)->first();
                    if (!$linmas) {
                        \Illuminate\Support\Facades\Log::warning("Linmas not found with NIK: {$nik}");
                        return redirect()->back()->withErrors(['msg' => 'Data Linmas dengan NIK ' . $nik . ' tidak ditemukan.']);
                    }

                    $payroll = Payroll::where('linmas_id', $linmas->id)
                        ->whereBetween('payroll_date', [$startDate, $endDate])
                        ->with(['details'])
                        ->first();

                    if ($payroll) {
                        // Get rates
                        $overtimeRate = SalaryRate::where('key', 'overtime_rate')->where('is_active', true)->first()->value ?? 10000;
                        $dailyRate = $payroll->total_days_present > 0 ? ($payroll->base_salary / $payroll->total_days_present) : 0;

                        // Format data untuk slip gaji
                        $payrollData = [
                            'nik' => $linmas->nik,
                            'nama' => $linmas->nama,
                            'total_days_worked' => $payroll->total_days_present,
                            'total_overtime' => 0, // Ambil dari detail jika ada
                            'base_salary' => $payroll->base_salary,
                            'overtime_payment' => $payroll->overtime_payment,
                            'total_wage' => max(0, $payroll->total_salary), // Pastikan tidak negatif
                            'allowances' => [],
                            'deductions' => [],
                            'total_allowances' => 0,
                            'total_deductions' => 0,
                            'daily_rate' => $dailyRate,
                            'overtime_rate' => $overtimeRate,
                        ];

                        // Tambahkan detail tunjangan dan potongan
                        foreach ($payroll->details as $detail) {
                            if ($detail->type == 'allowance') {
                                $amount = $detail->amount ?? 0; // Pastikan amount tidak null
                                $payrollData['allowances'][] = [
                                    'name' => $detail->name,
                                    'amount' => $amount
                                ];
                                $payrollData['total_allowances'] += $amount;
                            } elseif ($detail->type == 'deduction') {
                                $amount = $detail->amount ?? 0; // Pastikan amount tidak null
                                $payrollData['deductions'][] = [
                                    'name' => $detail->name,
                                    'amount' => $amount
                                ];
                                $payrollData['total_deductions'] += $amount;
                            } elseif ($detail->type == 'overtime') {
                                $payrollData['total_overtime'] = $overtimeRate > 0 ? $detail->amount / $overtimeRate : 0;
                            }
                        }
                    } else {
                        \Illuminate\Support\Facades\Log::warning("Payroll not found for Linmas ID: {$linmas->id} in date range: {$startDate} to {$endDate}");
                        return redirect()->back()->withErrors(['msg' => 'Data penggajian tidak ditemukan untuk periode ini.']);
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Error retrieving payroll data from database", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return redirect()->back()->withErrors(['msg' => 'Terjadi kesalahan saat mengambil data penggajian: ' . $e->getMessage()]);
                }
            }

            if (!$payrollData) {
                \Illuminate\Support\Facades\Log::warning("No payroll data found for NIK: {$nik}");
                return redirect()->back()->withErrors(['msg' => 'Data tidak ditemukan untuk NIK ini.']);
            }

            $startDate = $request->start_date;
            $endDate = $request->end_date;

            try {
                $pdf = PDF::loadView('payroll.slip', compact('payrollData', 'startDate', 'endDate'));
                return $pdf->download('slip_gaji_' . $payrollData['nik'] . '.pdf');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error generating PDF for NIK: {$nik}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->back()->withErrors(['msg' => 'Terjadi kesalahan saat membuat slip gaji: ' . $e->getMessage()]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Unexpected error in exportSlip", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['msg' => 'Terjadi kesalahan yang tidak terduga: ' . $e->getMessage()]);
        }
    }



    public function storePayroll(Request $request)
    {
        $payrollData = json_decode($request->payroll_data, true) ?? [];
        $endDate = Carbon::parse($request->end_date);
        $forceSave = $request->has('force_save');

        $result = $this->payrollStorageService->store($payrollData, $endDate, $forceSave);

        if (!$result['success']) {
            return redirect()->route('payroll.index')->withErrors(['msg' => $result['message']]);
        }

        if (isset($result['warning'])) {
            return redirect()->route('payroll.index')->with('warning', $result['message']);
        }

        return redirect()->route('payroll.index')->with('success', $result['message']);
    }


    public function changeStatus(Request $request, Payroll $payroll)
    {
        try {
            $request->validate([
                'status' => 'required|in:verified,calculated,approved,processed,completed,rejected',
                'notes' => 'nullable|string'
            ]);

            // Log untuk debugging
            \Illuminate\Support\Facades\Log::info("Attempting to change payroll status", [
                'payroll_id' => $payroll->id,
                'linmas_id' => $payroll->linmas_id,
                'current_status' => $payroll->processing_status,
                'target_status' => $request->status,
                'user_id' => auth()->id()
            ]);

            // Validasi transisi status yang valid
            $validTransitions = [
                'draft' => ['verified', 'rejected'],
                'verified' => ['calculated', 'rejected'],
                'calculated' => ['approved', 'rejected'],
                'approved' => ['processed', 'rejected'],
                'processed' => ['completed', 'rejected'],
                'completed' => [], // End state
                'rejected' => ['draft'] // Can be restarted
            ];

            // Periksa apakah status saat ini ada dalam array validTransitions
            if (!isset($validTransitions[$payroll->processing_status])) {
                \Illuminate\Support\Facades\Log::warning("Invalid current status", [
                    'payroll_id' => $payroll->id,
                    'current_status' => $payroll->processing_status
                ]);
                return back()->with('error', 'Status saat ini tidak valid.');
            }

            // Periksa apakah transisi status valid
            if (!in_array($request->status, $validTransitions[$payroll->processing_status])) {
                \Illuminate\Support\Facades\Log::warning("Invalid status transition", [
                    'payroll_id' => $payroll->id,
                    'current_status' => $payroll->processing_status,
                    'target_status' => $request->status
                ]);
                return back()->with('error', 'Transisi status tidak valid.');
            }

            // Update status
            $payroll->processing_status = $request->status;
            $payroll->status_notes = $request->notes;

            // Set verification/approval data
            if ($request->status == 'verified') {
                $payroll->verified_by = auth()->id();
                $payroll->verified_at = now();
            } elseif ($request->status == 'approved') {
                $payroll->approved_by = auth()->id();
                $payroll->approved_at = now();
            }

            $payroll->save();

            \Illuminate\Support\Facades\Log::info("Payroll status changed successfully", [
                'payroll_id' => $payroll->id,
                'from_status' => $payroll->getOriginal('processing_status'),
                'to_status' => $request->status
            ]);

            return back()->with('success', 'Status berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::warning("Validation error in changeStatus", [
                'errors' => $e->errors()
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error changing payroll status", [
                'payroll_id' => $payroll->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', "Terjadi kesalahan saat mengubah status: {$e->getMessage()}");
        }
    }
}
