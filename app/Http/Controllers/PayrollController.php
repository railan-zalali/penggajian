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
use Illuminate\Validation\ValidationException;

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

        // Hitung jumlah hari kerja dalam periode
        $workingDays = 0;
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            // Hanya hitung hari Senin-Jumat sebagai hari kerja
            if ($currentDate->dayOfWeek !== Carbon::SATURDAY && $currentDate->dayOfWeek !== Carbon::SUNDAY) {
                $workingDays++;
            }
            $currentDate->addDay();
        }

        $payrollData = $this->payrollCalculationService->calculate($startDate, $endDate, $workingDays);

        return view('payroll.index', compact('payrollData', 'startDate', 'endDate', 'workingDays'));
    }

    public function exportPdf(Request $request)
    {
        try {
            // Coba ambil data dari request terlebih dahulu
            $payrollData = json_decode($request->payroll_data ?? '', true);
            $startMonth = $request->start_date;
            $endMonth = $request->end_date;

            // Jika tidak ada data dari request, coba ambil dari session
            if (empty($payrollData)) {
                if (!session()->has('payroll_data') || !session()->has('start_month') || !session()->has('end_month')) {
                    Log::error('Payroll data not found in request or session');
                    return redirect()->route('payroll.index')
                        ->withErrors(['error' => 'Data penggajian tidak ditemukan. Silakan lakukan perhitungan terlebih dahulu.']);
                }

                $payrollData = session('payroll_data');
                $startMonth = session('start_month');
                $endMonth = session('end_month');
            }

            // Validasi data penggajian
            if (empty($payrollData)) {
                Log::error('Payroll data is empty');
                return redirect()->route('payroll.index')
                    ->withErrors(['error' => 'Data penggajian kosong. Silakan lakukan perhitungan ulang.']);
            }

            // Format tanggal untuk tampilan
            try {
                $startDate = \Carbon\Carbon::parse($startMonth)->format('F Y');
                $endDate = \Carbon\Carbon::parse($endMonth)->format('F Y');
                
                // Extract month and year for PDF template
                $startCarbon = \Carbon\Carbon::parse($startMonth);
                $month = $startCarbon->format('F');
                $year = $startCarbon->format('Y');
            } catch (\Exception $e) {
                Log::warning('Error parsing date format: ' . $e->getMessage());
                $startDate = $startMonth;
                $endDate = $endMonth;
                $month = 'Unknown';
                $year = 'Unknown';
            }

            // Log untuk debugging
            Log::info('Exporting PDF for payroll data', [
                'start_month' => $startMonth,
                'end_month' => $endMonth,
                'data_count' => count($payrollData),
            ]);

            // Generate PDF
            $pdf = PDF::loadView('payroll.pdf', [
                'payrollData' => $payrollData,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'month' => $month,
                'year' => $year,
            ]);

            // Set paper size to A4 landscape
            $pdf->setPaper('a4', 'landscape');

            // Download PDF
            return $pdf->download('Laporan_Penggajian_' . str_replace('-', '_', $startMonth) . '_' . str_replace('-', '_', $endMonth) . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error exporting payroll PDF: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('payroll.index')
                ->withErrors(['error' => 'Terjadi kesalahan saat mengekspor PDF: ' . $e->getMessage()]);
        }
    }

    public function exportSlip(Request $request, $id = null)
    {
        try {
            // Coba ambil data dari request
            $payrollData = null;

            // Prioritas 1: Jika ID disediakan, cari data berdasarkan ID payroll
            if ($id && is_numeric($id)) {
                // Cari payroll berdasarkan ID
                $payroll = Payroll::with(['linmas', 'details'])->find($id);

                if (!$payroll) {
                    Log::warning("Payroll not found with ID: {$id}");
                    return redirect()->back()->withErrors(['msg' => 'Data penggajian dengan ID ' . $id . ' tidak ditemukan.']);
                }

                $linmas = $payroll->linmas;
                if (!$linmas) {
                    Log::warning("Linmas not found for payroll ID: {$id}");
                    return redirect()->back()->withErrors(['msg' => 'Data Linmas untuk penggajian ini tidak ditemukan.']);
                }

                $dailyRate = $payroll->total_days_present > 0 ? ($payroll->base_salary / $payroll->total_days_present) : 0;

                // Format data untuk slip gaji
                $payrollData = [
                    'nik' => $linmas->nik,
                    'nama' => $linmas->nama,
                    'total_days_worked' => $payroll->total_days_present,
                    'base_salary' => $payroll->base_salary,
                    'total_wage' => max(0, $payroll->total_salary), // Pastikan tidak negatif
                    'allowances' => [],
                    'deductions' => [],
                    'total_allowances' => 0,
                    'total_deductions' => 0,
                    'daily_rate' => $dailyRate,
                    'payment_status' => $payroll->payment_status,
                    'payment_date' => $payroll->payment_date ? $payroll->payment_date->format('d-m-Y') : null,
                    'payroll_date' => $payroll->payroll_date->format('F Y'),
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
                    }
                }

                // Set tanggal untuk tampilan
                $startDate = $payroll->payroll_date->format('Y-m-d');
                $endDate = $payroll->payroll_date->format('Y-m-d');

                Log::info("Successfully prepared payroll data for export slip", [
                    'payroll_id' => $id,
                    'nik' => $linmas->nik,
                    'nama' => $linmas->nama
                ]);
            }
            // Prioritas 2: Jika NIK disediakan, cari data berdasarkan NIK
            else if ($request->has('nik')) {
                $nik = $request->nik;

                // Coba ambil data dari form terlebih dahulu
                $payrollData = collect(json_decode($request->payroll_data, true) ?? [])->firstWhere('nik', $nik);

                // Log untuk debugging
                Log::info("Exporting slip for NIK: {$nik}", [
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
                            Log::warning("Linmas not found with NIK: {$nik}");
                            return redirect()->back()->withErrors(['msg' => 'Data Linmas dengan NIK ' . $nik . ' tidak ditemukan.']);
                        }

                        $payroll = Payroll::where('linmas_id', $linmas->id)
                            ->whereBetween('payroll_date', [$startDate, $endDate])
                            ->with(['details'])
                            ->first();

                        if ($payroll) {
                            // Get rates
                            $dailyRate = $payroll->total_days_present > 0 ? ($payroll->base_salary / $payroll->total_days_present) : 0;

                            // Format data untuk slip gaji
                            $payrollData = [
                                'nik' => $linmas->nik,
                                'nama' => $linmas->nama,
                                'total_days_worked' => $payroll->total_days_present,
                                'base_salary' => $payroll->base_salary,
                                'total_wage' => max(0, $payroll->total_salary), // Pastikan tidak negatif
                                'allowances' => [],
                                'deductions' => [],
                                'total_allowances' => 0,
                                'total_deductions' => 0,
                                'daily_rate' => $dailyRate,
                                'payment_status' => $payroll->payment_status,
                                'payment_date' => $payroll->payment_date ? $payroll->payment_date->format('d-m-Y') : null,
                                'payroll_date' => $payroll->payroll_date->format('F Y'),
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
                                }
                            }
                        } else {
                            Log::warning("Payroll not found for Linmas ID: {$linmas->id} in date range: {$startDate} to {$endDate}");
                            return redirect()->back()->withErrors(['msg' => 'Data penggajian tidak ditemukan untuk periode ini.']);
                        }
                    } catch (\Exception $e) {
                        Log::error("Error retrieving payroll data from database", [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        return redirect()->back()->withErrors(['msg' => 'Terjadi kesalahan saat mengambil data penggajian: ' . $e->getMessage()]);
                    }
                }
            }
            // Prioritas 3: Jika tidak ada ID atau NIK, coba ambil dari request atau session
            else {
                // Coba ambil dari request langsung
                $payrollData = json_decode($request->payroll_data, true);

                // Jika tidak ada di request, cek di session
                if (!$payrollData && session()->has('payroll_data')) {
                    $payrollData = session('payroll_data');
                    Log::info("Using payroll data from session");
                }
            }

            // Validasi data payroll
            if (!$payrollData) {
                Log::warning("No payroll data found for export slip");
                return redirect()->back()->withErrors(['msg' => 'Data slip gaji tidak ditemukan.']);
            }

            // Jika start_date dan end_date tidak diset, gunakan tanggal saat ini
            $startDate = $startDate ?? $request->start_date ?? date('Y-m-d');
            $endDate = $endDate ?? $request->end_date ?? date('Y-m-d');

            try {
                // Simpan data ke session untuk penggunaan berikutnya
                session(['payroll_data' => $payrollData]);

                $pdf = PDF::loadView('payroll.slip', compact('payrollData', 'startDate', 'endDate'));
                $pdf->setPaper('a4', 'portrait'); // Set ukuran kertas

                return $pdf->download('slip_gaji_' . $payrollData['nik'] . '_' . date('Ymd') . '.pdf');
            } catch (\Exception $e) {
                Log::error("Error generating PDF for slip", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->back()->withErrors(['msg' => 'Terjadi kesalahan saat membuat slip gaji: ' . $e->getMessage()]);
            }
        } catch (\Exception $e) {
            Log::error("Unexpected error in exportSlip", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['msg' => 'Terjadi kesalahan yang tidak terduga: ' . $e->getMessage()]);
        }
    }



    public function storePayroll(Request $request)
    {
        // Validasi data dari form
        $validator = Validator::make($request->all(), [
            'payroll_data' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        if ($validator->fails()) {
            Log::error('Payroll data validation failed during storePayroll', ['errors' => $validator->errors()]);
            return redirect()->route('payroll.index')
                ->withErrors(['error' => 'Data penggajian tidak lengkap. Silakan lakukan perhitungan terlebih dahulu.']);
        }

        // Ambil data dari request
        $payrollData = json_decode($request->payroll_data, true);
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Jika tidak ada data dari request, coba ambil dari session
        if (empty($payrollData) && (session()->has('payroll_data') && session()->has('start_month') && session()->has('end_month'))) {
            $payrollData = session('payroll_data');
            $startDate = session('start_month');
            $endDate = session('end_month');
        }

        // Jika masih tidak ada data
        if (empty($payrollData)) {
            Log::error('Payroll data not found in request or session during storePayroll');
            return redirect()->route('payroll.index')
                ->withErrors(['error' => 'Data penggajian tidak ditemukan. Silakan lakukan perhitungan terlebih dahulu.']);
        }

        // Variabel ini sudah diinisialisasi di atas dengan data dari request

        // Validasi data penggajian
        if (empty($payrollData)) {
            Log::error('Payroll data is empty during storePayroll');
            return redirect()->route('payroll.index')
                ->withErrors(['error' => 'Data penggajian kosong. Silakan lakukan perhitungan ulang.']);
        }

        // Tambahkan bulan dan tahun ke setiap data penggajian
        $endDateObj = \Carbon\Carbon::parse($endDate);
        $formattedPayrollData = [];

        foreach ($payrollData as $data) {
            // Pastikan data memiliki semua field yang diperlukan
            if (!isset($data['nik']) || !isset($data['nama']) || !isset($data['total_wage'])) {
                Log::warning('Incomplete payroll data found', ['data' => $data]);
                continue; // Lewati data yang tidak lengkap
            }

            $data['month'] = $endDateObj->month;
            $data['year'] = $endDateObj->year;
            $formattedPayrollData[] = $data;
        }

        // Periksa apakah masih ada data setelah validasi
        if (empty($formattedPayrollData)) {
            Log::error('No valid payroll data after validation');
            return redirect()->route('payroll.index')
                ->withErrors(['error' => 'Tidak ada data penggajian yang valid untuk disimpan.']);
        }

        // Cek apakah user memilih untuk paksa simpan
        $forceSave = $request->has('force_save');

        // Gunakan service untuk menyimpan data penggajian
        $result = $this->payrollStorageService->store($formattedPayrollData, $endDateObj, $forceSave);

        if (isset($result['success']) && $result['success']) {
            // Hapus data dari session setelah berhasil disimpan
            session()->forget(['payroll_data', 'start_month', 'end_month']);
            return redirect()->route('payroll.index')
                ->with('success', $result['message']);
        } elseif (isset($result['warning'])) {
            return redirect()->route('payroll.index')
                ->with('warning', $result['message']);
        } else {
            return redirect()->route('payroll.index')
                ->withErrors(['error' => $result['message'] ?? 'Terjadi kesalahan saat menyimpan data penggajian.']);
        }
    }


    public function changeStatus(Request $request, Payroll $payroll)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,verified,calculated,approved,processed,completed,rejected,cancelled',
                'notes' => 'nullable|string'
            ]);

            // Log untuk debugging
            Log::info("Attempting to change payroll status", [
                'payroll_id' => $payroll->id,
                'linmas_id' => $payroll->linmas_id,
                'current_status' => $payroll->processing_status,
                'target_status' => $request->status,
                'user_id' => auth()->id()
            ]);

            // Validasi transisi status yang valid
            $validTransitions = [
                'pending' => ['verified', 'cancelled'],
                'draft' => ['verified', 'rejected', 'cancelled'],
                'verified' => ['calculated', 'rejected', 'pending', 'cancelled'],
                'calculated' => ['approved', 'rejected', 'cancelled'],
                'approved' => ['processed', 'rejected', 'cancelled'],
                'processed' => ['completed', 'rejected', 'cancelled'],
                'completed' => ['cancelled'],
                'rejected' => ['draft', 'pending'],
                'cancelled' => ['pending', 'draft']
            ];

            // Periksa apakah status saat ini ada dalam array validTransitions
            if (!isset($validTransitions[$payroll->processing_status])) {
                Log::warning("Invalid current status", [
                    'payroll_id' => $payroll->id,
                    'current_status' => $payroll->processing_status
                ]);
                return back()->with('error', 'Status saat ini tidak valid.');
            }

            // Periksa apakah transisi status valid
            if (!in_array($request->status, $validTransitions[$payroll->processing_status])) {
                Log::warning("Invalid status transition", [
                    'payroll_id' => $payroll->id,
                    'current_status' => $payroll->processing_status,
                    'target_status' => $request->status
                ]);
                return back()->with('error', 'Perubahan status dari ' . $payroll->processing_status . ' ke ' . $request->status . ' tidak diperbolehkan.');
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
            } elseif ($request->status == 'cancelled') {
                $payroll->cancelled_by = auth()->id();
                $payroll->cancelled_at = now();
            }

            $payroll->save();

            Log::info("Payroll status changed successfully", [
                'payroll_id' => $payroll->id,
                'from_status' => $payroll->getOriginal('processing_status'),
                'to_status' => $request->status
            ]);

            return back()->with('success', 'Status berhasil diperbarui.');
        } catch (ValidationException $e) {
            Log::warning("Validation error in changeStatus", [
                'errors' => $e->errors()
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error("Error changing payroll status", [
                'payroll_id' => $payroll->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', "Terjadi kesalahan saat mengubah status: {$e->getMessage()}");
        }
    }
}
