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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PayrollController extends Controller
{
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

        // Mengambil data kehadiran dalam rentang waktu
        $attendances = Attendances::whereBetween('waktu', [$startDate, $endDate])
            ->with('linmas')
            ->get();

        // Mendapatkan rate lembur dari database
        $overtimeRate = SalaryRate::where('key', 'overtime_rate')->where('is_active', true)->first()->value ?? 10000;

        // Mengelompokkan kehadiran berdasarkan NIK
        $payrollData = $attendances->groupBy('linmas.nik')->map(function ($attendanceGroup) use ($overtimeRate) {
            // Jika tidak ada data linmas, lewati
            if (!$attendanceGroup->first() || !$attendanceGroup->first()->linmas) {
                \Illuminate\Support\Facades\Log::warning("Skipping attendance group without linmas data");
                return null;
            }

            $linmas = $attendanceGroup->first()->linmas;
            $linmasId = $linmas->id;

            // Hitung hari kerja dan lembur dengan metode yang lebih efisien dan robust
            // Menggunakan array asosiatif untuk tracking hari kerja dan lembur
            $daysWorkedMap = [];
            $overtimeDaysMap = [];

            // Kelompokkan kehadiran berdasarkan tanggal untuk pemrosesan lebih cepat
            $attendancesByDate = $attendanceGroup->groupBy(function ($attendance) {
                return date('Y-m-d', strtotime($attendance->waktu));
            });

            // Log untuk debugging
            \Illuminate\Support\Facades\Log::info("Processing attendance for NIK: {$linmas->nik}", [
                'dates_count' => count($attendancesByDate),
                'total_records' => $attendanceGroup->count()
            ]);

            // Proses setiap tanggal untuk mencari pasangan masuk-keluar
            foreach ($attendancesByDate as $date => $dailyAttendances) {
                // Cek kehadiran reguler (C/Masuk dan C/Keluar)
                $hasEntry = $dailyAttendances->where('status', 'C/Masuk')->count() > 0;
                $hasExit = $dailyAttendances->where('status', 'C/Keluar')->count() > 0;

                // Cek juga status alternatif yang mungkin digunakan
                if (!$hasEntry) {
                    $hasEntry = $dailyAttendances->where('status', 'Masuk')->count() > 0 ||
                        $dailyAttendances->where('status_baru', 'C/Masuk')->count() > 0 ||
                        $dailyAttendances->where('status_baru', 'Masuk')->count() > 0;
                }

                if (!$hasExit) {
                    $hasExit = $dailyAttendances->where('status', 'Keluar')->count() > 0 ||
                        $dailyAttendances->where('status_baru', 'C/Keluar')->count() > 0 ||
                        $dailyAttendances->where('status_baru', 'Keluar')->count() > 0;
                }

                // Hanya hitung hari dengan pasangan masuk dan keluar
                if ($hasEntry && $hasExit && !isset($daysWorkedMap[$date])) {
                    $daysWorkedMap[$date] = true;
                    \Illuminate\Support\Facades\Log::debug("Counted work day for {$linmas->nik} on {$date}");
                }

                // Cek lembur (Lembur Masuk dan Lembur Keluar)
                $hasOvertimeEntry = $dailyAttendances->where('status', 'Lembur Masuk')->count() > 0;
                $hasOvertimeExit = $dailyAttendances->where('status', 'Lembur Keluar')->count() > 0;

                // Jika tidak ada status lembur di status utama, cek di status_baru
                if (!$hasOvertimeEntry) {
                    $hasOvertimeEntry = $dailyAttendances->where('status_baru', 'Lembur Masuk')->count() > 0;
                }

                if (!$hasOvertimeExit) {
                    $hasOvertimeExit = $dailyAttendances->where('status_baru', 'Lembur Keluar')->count() > 0;
                }

                // Hanya hitung hari dengan pasangan lembur masuk dan keluar
                if ($hasOvertimeEntry && $hasOvertimeExit && !isset($overtimeDaysMap[$date])) {
                    $overtimeDaysMap[$date] = true;
                    \Illuminate\Support\Facades\Log::debug("Counted overtime for {$linmas->nik} on {$date}");
                }
            }

            $totalDaysWorked = count($daysWorkedMap);
            $totalOvertime = count($overtimeDaysMap);

            // Mendapatkan jabatan linmas
            $position = $linmas->jabatan ?? 'Perangkat Lainnya';

            // Mendapatkan tarif harian berdasarkan jabatan
            $positionRate = PositionSalaryRate::where('position', $position)
                ->where('is_active', true)
                ->first();

            // Log untuk debugging
            \Illuminate\Support\Facades\Log::info("Position rate for {$position}", [
                'position' => $position,
                'found_rate' => $positionRate ? $positionRate->daily_rate : 'not found'
            ]);

            // Jika jabatan tidak ditemukan, gunakan tarif default untuk Perangkat Lainnya
            if (!$positionRate) {
                $positionRate = PositionSalaryRate::where('position', 'Perangkat Lainnya')
                    ->where('is_active', true)
                    ->first();

                // Log jika menggunakan tarif default
                if ($positionRate) {
                    \Illuminate\Support\Facades\Log::info("Using default rate for Perangkat Lainnya", [
                        'daily_rate' => $positionRate->daily_rate
                    ]);
                }
            }

            // Mendapatkan tarif harian
            $dailyRate = $positionRate ? $positionRate->daily_rate : 100000; // Default 100.000 jika tidak ada tarif

            // Mendapatkan tarif lembur yang valid
            if (!$overtimeRate || $overtimeRate <= 0) {
                $overtimeRate = 10000; // Default 10.000 jika tidak ada tarif lembur yang valid
                \Illuminate\Support\Facades\Log::warning("Using default overtime rate: 10000");
            }

            // Hitung gaji pokok dan lembur
            $baseSalary = $totalDaysWorked * $dailyRate;
            $overtimePay = $totalOvertime * $overtimeRate;

            // Log hasil perhitungan dasar
            \Illuminate\Support\Facades\Log::info("Base calculation for {$linmas->nik}", [
                'total_days_worked' => $totalDaysWorked,
                'daily_rate' => $dailyRate,
                'base_salary' => $baseSalary,
                'total_overtime' => $totalOvertime,
                'overtime_rate' => $overtimeRate,
                'overtime_pay' => $overtimePay
            ]);

            // Ambil data tunjangan dan potongan
            $allowances = LinmasAllowanceDeduction::getLinmasAllowances($linmasId);
            $deductions = LinmasAllowanceDeduction::getLinmasDeductions($linmasId);

            // Hitung total tunjangan dengan metode yang lebih efisien
            $totalAllowances = 0;
            $allowanceDetails = [];
            $totalSalaryBeforeDeductions = $baseSalary + $overtimePay; // Gaji dasar untuk perhitungan persentase

            // Proses tunjangan tetap terlebih dahulu
            foreach ($allowances->where('type.calculation_type', 'fixed') as $allowance) {
                if (!$allowance->type) {
                    \Illuminate\Support\Facades\Log::warning("Allowance type not found for allowance ID: {$allowance->id}");
                    continue;
                }

                $allowanceType = $allowance->type;
                $amount = max(0, $allowance->value); // Pastikan nilai tunjangan tidak negatif

                $totalAllowances += $amount;
                $allowanceDetails[] = [
                    'name' => $allowanceType->name,
                    'code' => $allowanceType->code,
                    'amount' => $amount,
                    'type' => 'fixed'
                ];
            }

            // Kemudian proses tunjangan persentase
            foreach ($allowances->where('type.calculation_type', 'percentage') as $allowance) {
                if (!$allowance->type) {
                    \Illuminate\Support\Facades\Log::warning("Allowance type not found for allowance ID: {$allowance->id}");
                    continue;
                }

                $allowanceType = $allowance->type;
                $amount = floor(($allowance->value / 100) * $totalSalaryBeforeDeductions); // Pembulatan ke bawah
                $amount = max(0, $amount); // Pastikan nilai tunjangan tidak negatif

                $totalAllowances += $amount;
                $allowanceDetails[] = [
                    'name' => $allowanceType->name,
                    'code' => $allowanceType->code,
                    'amount' => $amount,
                    'type' => 'percentage',
                    'percentage' => $allowance->value . '%'
                ];
            }

            // Hitung total potongan dengan metode yang lebih efisien
            $totalDeductions = 0;
            $deductionDetails = [];
            $totalAvailableSalary = $totalSalaryBeforeDeductions + $totalAllowances; // Total yang tersedia untuk dipotong

            // Proses potongan tetap terlebih dahulu
            foreach ($deductions->where('type.calculation_type', 'fixed') as $deduction) {
                if (!$deduction->type) {
                    \Illuminate\Support\Facades\Log::warning("Deduction type not found for deduction ID: {$deduction->id}");
                    continue;
                }

                $deductionType = $deduction->type;
                $amount = max(0, $deduction->value); // Pastikan nilai potongan tidak negatif

                // Pastikan potongan tidak melebihi sisa gaji yang tersedia
                $remainingSalary = $totalAvailableSalary - $totalDeductions;
                $amount = min($amount, $remainingSalary);

                if ($amount > 0) { // Hanya tambahkan jika nilainya positif
                    $totalDeductions += $amount;
                    $deductionDetails[] = [
                        'name' => $deductionType->name,
                        'code' => $deductionType->code,
                        'amount' => $amount,
                        'type' => 'fixed'
                    ];
                }
            }

            // Kemudian proses potongan persentase
            foreach ($deductions->where('type.calculation_type', 'percentage') as $deduction) {
                if (!$deduction->type) {
                    \Illuminate\Support\Facades\Log::warning("Deduction type not found for deduction ID: {$deduction->id}");
                    continue;
                }

                $deductionType = $deduction->type;
                $amount = floor(($deduction->value / 100) * $totalSalaryBeforeDeductions); // Pembulatan ke bawah
                $amount = max(0, $amount); // Pastikan nilai potongan tidak negatif

                // Pastikan potongan tidak melebihi sisa gaji yang tersedia
                $remainingSalary = $totalAvailableSalary - $totalDeductions;
                $amount = min($amount, $remainingSalary);

                if ($amount > 0) { // Hanya tambahkan jika nilainya positif
                    $totalDeductions += $amount;
                    $deductionDetails[] = [
                        'name' => $deductionType->name,
                        'code' => $deductionType->code,
                        'amount' => $amount,
                        'type' => 'percentage',
                        'percentage' => $deduction->value . '%'
                    ];
                }
            }

            // Hitung total gaji
            $totalSalary = $baseSalary + $overtimePay + $totalAllowances - $totalDeductions;

            // Pastikan total gaji tidak negatif
            $totalSalary = max(0, $totalSalary);

            // Log hasil akhir perhitungan
            \Illuminate\Support\Facades\Log::info("Final calculation for {$linmas->nik}", [
                'base_salary' => $baseSalary,
                'overtime_pay' => $overtimePay,
                'total_allowances' => $totalAllowances,
                'total_deductions' => $totalDeductions,
                'total_salary' => $totalSalary
            ]);

            return [
                'nik' => $linmas->nik,
                'nama' => $linmas->nama,
                'linmas_id' => $linmasId,
                'total_days_worked' => $totalDaysWorked,
                'total_overtime' => $totalOvertime,
                'base_salary' => $baseSalary,
                'overtime_payment' => $overtimePay,
                'allowances' => $allowanceDetails,
                'total_allowances' => $totalAllowances,
                'deductions' => $deductionDetails,
                'total_deductions' => $totalDeductions,
                'total_wage' => $totalSalary
            ];
        })->filter()->values();

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
                                // Pastikan rate lembur valid
                                $overtimeRate = SalaryRate::where('key', 'overtime_rate')->where('is_active', true)->first()->value ?? 10000;
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
        $payrollData = json_decode($request->payroll_data, true);
        $endDate = Carbon::parse($request->end_date);
        $successCount = 0; // Inisialisasi counter untuk data yang berhasil disimpan
        $errorMessages = []; // Inisialisasi array untuk menyimpan pesan error

        // Validasi data JSON
        if (empty($payrollData)) {
            return redirect()->route('payroll.index')
                ->withErrors(['msg' => 'Data penggajian tidak valid atau kosong.']);
        }

        // Validasi server-side untuk mencegah duplikasi
        $existingPayrolls = Payroll::whereMonth('payroll_date', $endDate->month)
            ->whereYear('payroll_date', $endDate->year)
            ->get();

        // Log untuk debugging
        \Illuminate\Support\Facades\Log::info("Checking existing payrolls for {$endDate->format('Y-m')}", [
            'existing_count' => $existingPayrolls->count(),
            'force_save' => $request->has('force_save')
        ]);

        if ($existingPayrolls->isNotEmpty() && !$request->has('force_save')) {
            return redirect()->route('payroll.index')
                ->withErrors(['msg' => 'Data penggajian untuk bulan ini sudah ada. Tidak dapat menyimpan duplikat. Gunakan force_save=1 untuk memaksa menyimpan.']);
        }

        DB::beginTransaction();

        try {
            $successCount = 0;
            $errorMessages = [];

            foreach ($payrollData as $data) {
                try {
                    // Validasi data yang diperlukan
                    if (empty($data['nik'])) {
                        $errorMessages[] = 'Ditemukan data tanpa NIK';
                        continue;
                    }

                    $linmas = Linmas::where('nik', $data['nik'])->first();

                    if (!$linmas) {
                        $errorMessages[] = 'NIK ' . $data['nik'] . ' tidak ditemukan';
                        continue;
                    }

                    // Pastikan nilai tidak negatif
                    $totalWage = max(0, $data['total_wage'] ?? 0);
                    $baseSalary = max(0, $data['base_salary'] ?? 0);
                    $overtimePayment = max(0, $data['overtime_payment'] ?? 0);
                    $totalDaysWorked = max(0, $data['total_days_worked'] ?? 0);

                    // Log untuk debugging
                    \Illuminate\Support\Facades\Log::info("Storing payroll for {$linmas->nik}", [
                        'name' => $linmas->nama,
                        'total_wage' => $totalWage,
                        'base_salary' => $baseSalary,
                        'overtime' => $overtimePayment,
                        'days_worked' => $totalDaysWorked
                    ]);

                    // Buat payroll baru dengan status draft
                    $payroll = Payroll::create([
                        'linmas_id' => $linmas->id,
                        'total_days_present' => $totalDaysWorked,
                        'base_salary' => $baseSalary,
                        'overtime_payment' => $overtimePayment,
                        'total_salary' => $totalWage,
                        'payroll_date' => $endDate,
                        'payment_status' => 'pending',
                        'processing_status' => 'draft', // Set initial workflow status
                        'status_notes' => 'Data awal, menunggu verifikasi'
                    ]);

                    // Simpan detail payroll (base salary)
                    PayrollDetail::create([
                        'payroll_id' => $payroll->id,
                        'type_id' => null,
                        'name' => 'Gaji Pokok',
                        'type' => 'base',
                        'amount' => $baseSalary
                    ]);

                    // Simpan detail payroll (overtime)
                    if ($overtimePayment > 0) {
                        PayrollDetail::create([
                            'payroll_id' => $payroll->id,
                            'type_id' => null,
                            'name' => 'Lembur',
                            'type' => 'overtime',
                            'amount' => $overtimePayment
                        ]);
                    }

                    // Simpan detail tunjangan
                    if (isset($data['allowances']) && is_array($data['allowances'])) {
                        foreach ($data['allowances'] as $allowance) {
                            if (!isset($allowance['code']) || !isset($allowance['name'])) {
                                continue; // Skip jika data tidak lengkap
                            }
                            $type = AllowanceDeductionType::where('code', $allowance['code'])->first();
                            $amount = $allowance['amount'] ?? 0; // Pastikan amount tidak null
                            PayrollDetail::create([
                                'payroll_id' => $payroll->id,
                                'type_id' => $type ? $type->id : null,
                                'name' => $allowance['name'],
                                'type' => 'allowance',
                                'amount' => $amount
                            ]);
                        }
                    }

                    // Simpan detail potongan
                    if (isset($data['deductions']) && is_array($data['deductions'])) {
                        foreach ($data['deductions'] as $deduction) {
                            if (!isset($deduction['code']) || !isset($deduction['name'])) {
                                continue; // Skip jika data tidak lengkap
                            }
                            $type = AllowanceDeductionType::where('code', $deduction['code'])->first();
                            $amount = $deduction['amount'] ?? 0; // Pastikan amount tidak null
                            PayrollDetail::create([
                                'payroll_id' => $payroll->id,
                                'type_id' => $type ? $type->id : null,
                                'name' => $deduction['name'],
                                'type' => 'deduction',
                                'amount' => $amount
                            ]);
                        }
                    }

                    $successCount++;
                } catch (\Exception $innerException) {
                    // Log error untuk debugging
                    Log::error("Error processing payroll for NIK: " . (isset($data['nik']) ?? 'unknown' ?? $data['nik']), [
                        'error' => $innerException->getMessage(),
                        'trace' => $innerException->getTraceAsString()
                    ]);

                    $errorMessages[] = "Error pada NIK " . (isset($data['nik']) ? $data['nik'] : 'unknown') . ": " . $innerException->getMessage();
                }
            }

            // Jika tidak ada data yang berhasil disimpan, rollback transaksi
            if ($successCount == 0) {
                DB::rollback();
                return redirect()->route('payroll.index')
                    ->withErrors(['msg' => 'Tidak ada data yang berhasil disimpan. ' . implode(', ', $errorMessages)]);
            }

            DB::commit();

            // Jika ada error tapi sebagian data berhasil disimpan
            if (!empty($errorMessages)) {
                return redirect()->route('payroll.index')
                    ->with('warning', "Berhasil menyimpan {$successCount} data penggajian, tetapi terdapat beberapa error: " . implode(', ', $errorMessages));
            }

            return redirect()->route('payroll.index')
                ->with('success', "Berhasil menyimpan {$successCount} data penggajian.");
        } catch (\Exception $e) {
            DB::rollback();
            \Illuminate\Support\Facades\Log::error("Fatal error in storePayroll", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('payroll.index')
                ->withErrors(['msg' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
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
