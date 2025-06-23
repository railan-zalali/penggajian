<?php

namespace App\Http\Controllers;

use App\Models\Attendances;
use App\Models\Linmas;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\SalaryRate;
use App\Models\AllowanceDeductionType;
use App\Models\LinmasAllowanceDeduction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

        // Mendapatkan rate gaji dari database
        $dailyRate = SalaryRate::where('key', 'daily_rate')->where('is_active', true)->first()->value ?? 75114;
        $overtimeRate = SalaryRate::where('key', 'overtime_rate')->where('is_active', true)->first()->value ?? 10000;

        // Mengelompokkan kehadiran berdasarkan NIK
        $payrollData = $attendances->groupBy('linmas.nik')->map(function ($attendanceGroup) use ($dailyRate, $overtimeRate) {
            // Jika tidak ada data linmas, lewati
            if (!$attendanceGroup->first()->linmas) {
                return null;
            }

            $linmas = $attendanceGroup->first()->linmas;
            $linmasId = $linmas->id;

            // Hitung hari kerja dan lembur
            $totalDaysWorked = $attendanceGroup->where('status', 'C/Masuk')->count();
            $totalOvertime = $attendanceGroup->where('status_baru', 'Lembur Masuk')->count();

            // Hitung gaji pokok dan lembur
            $baseSalary = $totalDaysWorked * $dailyRate;
            $overtimePay = $totalOvertime * $overtimeRate;

            // Ambil data tunjangan dan potongan
            $allowances = LinmasAllowanceDeduction::getLinmasAllowances($linmasId);
            $deductions = LinmasAllowanceDeduction::getLinmasDeductions($linmasId);

            // Hitung total tunjangan
            $totalAllowances = 0;
            $allowanceDetails = [];
            foreach ($allowances as $allowance) {
                $allowanceType = $allowance->type;
                $amount = 0;

                if ($allowanceType->calculation_type == 'fixed') {
                    $amount = $allowance->value;
                } else { // percentage
                    $amount = ($allowance->value / 100) * $baseSalary;
                }

                $totalAllowances += $amount;
                $allowanceDetails[] = [
                    'name' => $allowanceType->name,
                    'code' => $allowanceType->code,
                    'amount' => $amount
                ];
            }

            // Hitung total potongan
            $totalDeductions = 0;
            $deductionDetails = [];
            foreach ($deductions as $deduction) {
                $deductionType = $deduction->type;
                $amount = 0;

                if ($deductionType->calculation_type == 'fixed') {
                    $amount = $deduction->value;
                } else { // percentage
                    $amount = ($deduction->value / 100) * $baseSalary;
                }

                $totalDeductions += $amount;
                $deductionDetails[] = [
                    'name' => $deductionType->name,
                    'code' => $deductionType->code,
                    'amount' => $amount
                ];
            }

            // Hitung total gaji
            $totalSalary = $baseSalary + $overtimePay + $totalAllowances - $totalDeductions;

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
        $payrollData = json_decode($request->payroll_data, true);
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $pdf = Pdf::loadView('payroll.report', compact('payrollData', 'startDate', 'endDate'));

        return $pdf->download('laporan_penggajian.pdf');
    }

    public function exportSlip(Request $request, $nik)
    {
        $payrollData = collect(json_decode($request->payroll_data, true))->firstWhere('nik', $nik);

        // Jika data tidak dari form, coba ambil dari database
        if (!$payrollData && $request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfMonth();
            $endDate = Carbon::parse($request->end_date)->endOfMonth();

            $linmas = Linmas::where('nik', $nik)->first();
            if ($linmas) {
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
                        'total_wage' => $payroll->total_salary,
                        'allowances' => [],
                        'deductions' => [],
                    ];

                    // Tambahkan detail tunjangan dan potongan
                    foreach ($payroll->details as $detail) {
                        if ($detail->type == 'allowance') {
                            $payrollData['allowances'][] = [
                                'name' => $detail->name,
                                'amount' => $detail->amount
                            ];
                        } elseif ($detail->type == 'deduction') {
                            $payrollData['deductions'][] = [
                                'name' => $detail->name,
                                'amount' => $detail->amount
                            ];
                        } elseif ($detail->type == 'overtime') {
                            $payrollData['total_overtime'] = $detail->amount / 10000; // Asumsi rate lembur 10.000
                        }
                    }
                }
            }
        }

        if (!$payrollData) {
            return redirect()->back()->withErrors(['msg' => 'Data tidak ditemukan untuk NIK ini.']);
        }

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $pdf = PDF::loadView('payroll.slip', compact('payrollData', 'startDate', 'endDate'));

        return $pdf->download('slip_gaji_' . $payrollData['nik'] . '.pdf');
    }

    public function storePayroll(Request $request)
    {
        $payrollData = json_decode($request->payroll_data, true);
        $endDate = Carbon::parse($request->end_date);

        // Validasi server-side untuk mencegah duplikasi
        $existingPayrolls = Payroll::whereMonth('payroll_date', $endDate->month)
            ->whereYear('payroll_date', $endDate->year)
            ->get();

        if ($existingPayrolls->isNotEmpty() && !$request->has('force_save')) {
            return redirect()->route('payroll.index')
                ->withErrors(['msg' => 'Data penggajian untuk bulan ini sudah ada. Tidak dapat menyimpan duplikat. Gunakan force_save=1 untuk memaksa menyimpan.']);
        }

        DB::beginTransaction();

        try {
            foreach ($payrollData as $data) {
                $linmas = Linmas::where('nik', $data['nik'])->first();

                if ($linmas) {
                    // Buat payroll baru
                    $payroll = Payroll::create([
                        'linmas_id' => $linmas->id,
                        'total_days_present' => $data['total_days_worked'],
                        'base_salary' => $data['base_salary'],
                        'overtime_payment' => $data['overtime_payment'],
                        'total_salary' => $data['total_wage'],
                        'payroll_date' => $endDate,
                        'payment_status' => 'pending'
                    ]);

                    // Simpan detail payroll (base salary)
                    PayrollDetail::create([
                        'payroll_id' => $payroll->id,
                        'type_id' => null,
                        'name' => 'Gaji Pokok',
                        'type' => 'base',
                        'amount' => $data['base_salary']
                    ]);

                    // Simpan detail payroll (overtime)
                    if ($data['overtime_payment'] > 0) {
                        PayrollDetail::create([
                            'payroll_id' => $payroll->id,
                            'type_id' => null,
                            'name' => 'Lembur',
                            'type' => 'overtime',
                            'amount' => $data['overtime_payment']
                        ]);
                    }

                    // Simpan detail tunjangan
                    if (isset($data['allowances'])) {
                        foreach ($data['allowances'] as $allowance) {
                            $type = AllowanceDeductionType::where('code', $allowance['code'])->first();
                            PayrollDetail::create([
                                'payroll_id' => $payroll->id,
                                'type_id' => $type ? $type->id : null,
                                'name' => $allowance['name'],
                                'type' => 'allowance',
                                'amount' => $allowance['amount']
                            ]);
                        }
                    }

                    // Simpan detail potongan
                    if (isset($data['deductions'])) {
                        foreach ($data['deductions'] as $deduction) {
                            $type = AllowanceDeductionType::where('code', $deduction['code'])->first();
                            PayrollDetail::create([
                                'payroll_id' => $payroll->id,
                                'type_id' => $type ? $type->id : null,
                                'name' => $deduction['name'],
                                'type' => 'deduction',
                                'amount' => $deduction['amount']
                            ]);
                        }
                    }
                } else {
                    throw new \Exception('NIK ' . $data['nik'] . ' tidak ditemukan.');
                }
            }

            DB::commit();
            return redirect()->route('payroll.index')
                ->with('success', 'Data penggajian berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('payroll.index')
                ->withErrors(['msg' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
