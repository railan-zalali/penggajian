<?php

namespace App\Services;

use App\Models\Attendances;
use App\Models\LinmasAllowanceDeduction;
use App\Models\PositionSalaryRate;
use App\Models\SalaryRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PayrollCalculationService
{
    /**
     * Calculate payroll for a given date range.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return \Illuminate\Support\Collection
     */
    public function calculate(Carbon $startDate, Carbon $endDate)
    {
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
                Log::warning("Skipping attendance group without linmas data");
                return null;
            }

            $linmas = $attendanceGroup->first()->linmas;
            $linmasId = $linmas->id;

            // Hitung hari kerja dan lembur
            $daysWorkedMap = [];
            $overtimeDaysMap = [];
            $attendancesByDate = $attendanceGroup->groupBy(fn ($attendance) => date('Y-m-d', strtotime($attendance->waktu)));

            foreach ($attendancesByDate as $date => $dailyAttendances) {
                $hasEntry = $dailyAttendances->where('status', 'C/Masuk')->isNotEmpty();
                $hasExit = $dailyAttendances->where('status', 'C/Keluar')->isNotEmpty();

                if ($hasEntry && $hasExit && !isset($daysWorkedMap[$date])) {
                    $daysWorkedMap[$date] = true;
                }

                $hasOvertimeEntry = $dailyAttendances->where('status', 'Lembur Masuk')->isNotEmpty();
                $hasOvertimeExit = $dailyAttendances->where('status', 'Lembur Keluar')->isNotEmpty();

                if ($hasOvertimeEntry && $hasOvertimeExit && !isset($overtimeDaysMap[$date])) {
                    $overtimeDaysMap[$date] = true;
                }
            }

            $totalDaysWorked = count($daysWorkedMap);
            $totalOvertime = count($overtimeDaysMap);

            // Dapatkan tarif harian berdasarkan jabatan
            $position = $linmas->jabatan ?? 'Perangkat Lainnya';
            $positionRate = PositionSalaryRate::where('position', $position)->where('is_active', true)->first();
            if (!$positionRate) {
                $positionRate = PositionSalaryRate::where('position', 'Perangkat Lainnya')->where('is_active', true)->first();
            }
            $dailyRate = $positionRate->daily_rate ?? 100000;

            // Validasi tarif lembur
            if (!$overtimeRate || $overtimeRate <= 0) {
                $overtimeRate = 10000;
            }

            // Hitung gaji
            $baseSalary = $totalDaysWorked * $dailyRate;
            $overtimePay = $totalOvertime * $overtimeRate;

            // Ambil dan hitung tunjangan
            $allowances = LinmasAllowanceDeduction::getLinmasAllowances($linmasId);
            $totalAllowances = 0;
            $allowanceDetails = [];
            $totalSalaryBeforeDeductions = $baseSalary + $overtimePay;

            foreach ($allowances as $allowance) {
                if (!$allowance->type) continue;
                $amount = 0;
                if ($allowance->type->calculation_type === 'fixed') {
                    $amount = $allowance->value;
                } elseif ($allowance->type->calculation_type === 'percentage') {
                    $amount = floor(($allowance->value / 100) * $totalSalaryBeforeDeductions);
                }
                $totalAllowances += $amount;
                $allowanceDetails[] = [
                    'name' => $allowance->type->name,
                    'code' => $allowance->type->code,
                    'amount' => $amount,
                    'type' => $allowance->type->calculation_type,
                    'percentage' => $allowance->type->calculation_type === 'percentage' ? $allowance->value . '%' : null
                ];
            }

            // Ambil dan hitung potongan
            $deductions = LinmasAllowanceDeduction::getLinmasDeductions($linmasId);
            $totalDeductions = 0;
            $deductionDetails = [];
            $totalAvailableSalary = $totalSalaryBeforeDeductions + $totalAllowances;

            foreach ($deductions as $deduction) {
                if (!$deduction->type) continue;
                $amount = 0;
                if ($deduction->type->calculation_type === 'fixed') {
                    $amount = $deduction->value;
                } elseif ($deduction->type->calculation_type === 'percentage') {
                    $amount = floor(($deduction->value / 100) * $totalSalaryBeforeDeductions);
                }
                $amount = min($amount, $totalAvailableSalary - $totalDeductions);
                if ($amount <= 0) continue;

                $totalDeductions += $amount;
                $deductionDetails[] = [
                    'name' => $deduction->type->name,
                    'code' => $deduction->type->code,
                    'amount' => $amount,
                    'type' => $deduction->type->calculation_type,
                    'percentage' => $deduction->type->calculation_type === 'percentage' ? $deduction->value . '%' : null
                ];
            }

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
                'total_wage' => max(0, $totalSalary),
                'daily_rate' => $dailyRate,
                'overtime_rate' => $overtimeRate,
            ];
        })->filter()->values();

        return $payrollData;
    }
}
