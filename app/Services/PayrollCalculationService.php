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
    public function calculate(Carbon $startDate, Carbon $endDate)
    {
        $allLinmas = \App\Models\Linmas::where('status', 'aktif')->get();
        $attendances = Attendances::whereBetween('waktu', [$startDate, $endDate])->get()->groupBy('linmas_id');
        $generalRates = SalaryRate::where('is_active', true)->pluck('value', 'key');
        $overtimeRate = $generalRates->get('overtime_rate', 10000);

        // Calculate expected working days in the period (Mon-Sat)
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        $expectedWorkingDays = 0;
        foreach ($period as $date) {
            if (!$date->isSunday()) {
                $expectedWorkingDays++;
            }
        }

        $payrollData = $allLinmas->map(function ($linmas) use ($attendances, $overtimeRate, $expectedWorkingDays, $startDate) {
            $linmasId = $linmas->id;
            $linmasAttendances = $attendances->get($linmasId, collect());

            // --- Calculate Actual Days Worked ---
            $daysWorkedMap = [];
            $attendancesByDate = $linmasAttendances->groupBy(fn ($att) => Carbon::parse($att->waktu)->toDateString());
            foreach ($attendancesByDate as $date => $dailyAttendances) {
                $hasEntry = $dailyAttendances->where('status', 'C/Masuk')->isNotEmpty();
                $hasExit = $dailyAttendances->where('status', 'C/Keluar')->isNotEmpty();
                if ($hasEntry && $hasExit) {
                    $daysWorkedMap[$date] = true;
                }
            }
            $actualDaysWorked = count($daysWorkedMap);

            // --- Get Monthly Salary ---
            $position = $linmas->jabatan ?? 'Perangkat Lainnya';
            $positionRate = PositionSalaryRate::where('position', $position)->where('is_active', true)->first();
            if (!$positionRate) {
                $positionRate = PositionSalaryRate::where('position', 'Perangkat Lainnya')->where('is_active', true)->first();
            }
            $monthlySalary = $positionRate->monthly_rate ?? 0;

            // --- Calculate Prorated Absence Deduction ---
            $absenceDeduction = 0;
            if ($actualDaysWorked < $expectedWorkingDays) {
                $absentDays = $expectedWorkingDays - $actualDaysWorked;
                $dailyValue = $expectedWorkingDays > 0 ? ($monthlySalary / $expectedWorkingDays) : 0;
                $absenceDeduction = round($absentDays * $dailyValue);
            }

            $baseSalary = $monthlySalary - $absenceDeduction;

            // --- Calculate Overtime ---
            // (Assuming overtime logic remains the same for now, based on hours/days)
            // For simplicity, let's assume 1 overtime day = 1 overtime payment unit
            $overtimeDays = $linmasAttendances->filter(fn($att) => $att->status === 'Lembur Masuk')->count();
            $overtimePay = $overtimeDays * $overtimeRate;

            // --- Allowances and Deductions ---
            $allowances = LinmasAllowanceDeduction::getLinmasAllowances($linmasId);
            $totalAllowances = 0;
            $allowanceDetails = [];
            $salaryForPercentage = $baseSalary + $overtimePay;

            foreach ($allowances as $allowance) {
                if (!$allowance->type) continue;
                $amount = $allowance->type->calculation_type === 'fixed'
                    ? $allowance->value
                    : floor(($allowance->value / 100) * $salaryForPercentage);
                $totalAllowances += $amount;
                $allowanceDetails[] = ['name' => $allowance->type->name, 'amount' => $amount];
            }

            $deductions = LinmasAllowanceDeduction::getLinmasDeductions($linmasId);
            $totalDeductions = 0;
            $deductionDetails = [];
            $totalAvailableSalary = $salaryForPercentage + $totalAllowances;

            foreach ($deductions as $deduction) {
                if (!$deduction->type) continue;
                 $amount = $deduction->type->calculation_type === 'fixed'
                    ? $deduction->value
                    : floor(($deduction->value / 100) * $salaryForPercentage);
                $amount = min($amount, $totalAvailableSalary - $totalDeductions);
                if ($amount <= 0) continue;
                $totalDeductions += $amount;
                $deductionDetails[] = ['name' => $deduction->type->name, 'amount' => $amount];
            }

            $totalSalary = $baseSalary + $overtimePay + $totalAllowances - $totalDeductions;

            return [
                'nik' => $linmas->nik,
                'nama' => $linmas->nama,
                'linmas_id' => $linmasId,
                'monthly_salary' => $monthlySalary,
                'expected_working_days' => $expectedWorkingDays,
                'actual_days_worked' => $actualDaysWorked,
                'absence_deduction' => $absenceDeduction,
                'base_salary' => $baseSalary,
                'overtime_payment' => $overtimePay,
                'allowances' => $allowanceDetails,
                'total_allowances' => $totalAllowances,
                'deductions' => $deductionDetails,
                'total_deductions' => $totalDeductions,
                'total_wage' => max(0, $totalSalary),
            ];
        });

        return $payrollData;
    }
}
