<?php

namespace App\Services;

use App\Models\Attendances;
use App\Models\Linmas;
use App\Models\LinmasAllowanceDeduction;
use App\Models\PositionSalaryRate;
use App\Models\SalaryRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PayrollCalculationService
{
    public function calculate(Carbon $startDate, Carbon $endDate)
    {
        // Ambil hanya Linmas yang memiliki data kehadiran dalam periode yang dipilih
        $attendances = Attendances::whereBetween('waktu', [$startDate, $endDate])->get();
        $linmasIdsWithAttendance = $attendances->pluck('linmas_id')->unique()->filter()->toArray();

        // Filter Linmas yang aktif dan memiliki data kehadiran
        $allLinmas = Linmas::where('status', 'aktif')
            ->whereIn('id', $linmasIdsWithAttendance)
            ->get();

        $attendances = $attendances->groupBy('linmas_id');
        $generalRates = SalaryRate::where('is_active', true)->pluck('value', 'key');

        // Calculate expected working days in the period (Mon-Sat)
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        $expectedWorkingDays = 0;
        foreach ($period as $date) {
            if (!$date->isSunday()) {
                $expectedWorkingDays++;
            }
        }

        // Pastikan expected working days tidak nol untuk menghindari division by zero
        $expectedWorkingDays = max(1, $expectedWorkingDays);

        $payrollData = $allLinmas->map(function ($linmas) use ($attendances, $expectedWorkingDays, $startDate) {
            $linmasId = $linmas->id;
            $linmasAttendances = $attendances->get($linmasId, collect());

            // --- Calculate Actual Days Worked ---
            $daysWorkedMap = [];
            $attendancesByDate = $linmasAttendances->groupBy(fn($att) => Carbon::parse($att->waktu)->toDateString());
            foreach ($attendancesByDate as $date => $dailyAttendances) {
                // Periksa status kehadiran dengan lebih fleksibel
                $hasEntry = $dailyAttendances->whereIn('status', ['C/Masuk', 'Hadir', 'Masuk'])->isNotEmpty();
                $hasExit = $dailyAttendances->whereIn('status', ['C/Keluar', 'Keluar'])->isNotEmpty();

                // Jika ada status 'Hadir' tanpa pasangan masuk/keluar, tetap dihitung hadir
                $isPresent = $dailyAttendances->where('status', 'Hadir')->isNotEmpty();

                if (($hasEntry && $hasExit) || $isPresent) {
                    $daysWorkedMap[$date] = true;
                }
            }
            $actualDaysWorked = count($daysWorkedMap);

            // Pastikan actual days worked tidak melebihi expected working days
            $actualDaysWorked = min($actualDaysWorked, $expectedWorkingDays);

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

            // Field lembur sudah dihapus dari sistem

            // --- Allowances and Deductions ---
            $allowances = LinmasAllowanceDeduction::getLinmasAllowances($linmasId);
            $totalAllowances = 0;
            $allowanceDetails = [];
            $salaryForPercentage = max(0, $baseSalary); // Pastikan tidak negatif

            foreach ($allowances as $allowance) {
                if (!$allowance->type) continue;

                // Pastikan nilai dan tipe perhitungan valid
                $value = is_numeric($allowance->value) ? $allowance->value : 0;
                $calculationType = $allowance->type->calculation_type ?? 'fixed';

                // Hitung jumlah tunjangan
                if ($calculationType === 'percentage' && $salaryForPercentage > 0) {
                    $amount = floor(($value / 100) * $salaryForPercentage);
                } else {
                    $amount = $value; // Fixed amount
                }

                // Pastikan jumlah tidak negatif
                $amount = max(0, $amount);

                if ($amount > 0) {
                    $totalAllowances += $amount;
                    $allowanceDetails[] = [
                        'name' => $allowance->type->name ?? 'Tunjangan',
                        'amount' => $amount,
                        'code' => $allowance->type->code ?? null
                    ];
                }
            }

            $deductions = LinmasAllowanceDeduction::getLinmasDeductions($linmasId);
            $totalDeductions = 0;
            $deductionDetails = [];
            $totalAvailableSalary = max(0, $salaryForPercentage + $totalAllowances);

            foreach ($deductions as $deduction) {
                if (!$deduction->type) continue;

                // Pastikan nilai dan tipe perhitungan valid
                $value = is_numeric($deduction->value) ? $deduction->value : 0;
                $calculationType = $deduction->type->calculation_type ?? 'fixed';

                // Hitung jumlah potongan
                if ($calculationType === 'percentage' && $salaryForPercentage > 0) {
                    $amount = floor(($value / 100) * $salaryForPercentage);
                } else {
                    $amount = $value; // Fixed amount
                }

                // Pastikan tidak memotong lebih dari yang tersedia
                $amount = min($amount, $totalAvailableSalary - $totalDeductions);
                $amount = max(0, $amount); // Pastikan tidak negatif

                if ($amount > 0) {
                    $totalDeductions += $amount;
                    $deductionDetails[] = [
                        'name' => $deduction->type->name ?? 'Potongan',
                        'amount' => $amount,
                        'code' => $deduction->type->code ?? null
                    ];
                }
            }

            // Pastikan total gaji tidak negatif
            $totalSalary = max(0, $baseSalary + $totalAllowances - $totalDeductions);

            return [
                'nik' => $linmas->nik,
                'nama' => $linmas->nama,
                'linmas_id' => $linmasId,
                'monthly_salary' => $monthlySalary,
                'expected_working_days' => $expectedWorkingDays,
                'actual_days_worked' => $actualDaysWorked,
                'absence_deduction' => $absenceDeduction,
                'base_salary' => $baseSalary,
                // Field overtime_payment sudah dihapus
                'allowances' => $allowanceDetails,
                'total_allowances' => $totalAllowances,
                'deductions' => $deductionDetails,
                'total_deductions' => $totalDeductions,
                'total_wage' => $totalSalary,
                'working_days' => $expectedWorkingDays, // Tambahkan untuk kompatibilitas dengan view
                'attendance_days' => $actualDaysWorked, // Tambahkan untuk kompatibilitas dengan view
                'total_days_worked' => $actualDaysWorked, // Tambahkan untuk kompatibilitas dengan PayrollController
            ];
        });

        return $payrollData;
    }
}
