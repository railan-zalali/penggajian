<?php

namespace App\Services;

use App\Models\Linmas;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\AllowanceDeductionType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayrollStorageService
{
    /**
     * Store payroll data to the database.
     *
     * @param array $payrollData
     * @param Carbon $endDate
     * @param bool $forceSave
     * @return array
     */
    public function store(array $payrollData, Carbon $endDate, bool $forceSave = false): array
    {
        if (empty($payrollData)) {
            return ['success' => false, 'message' => 'Data penggajian tidak valid atau kosong.'];
        }

        // Server-side validation to prevent duplicates
        if (!$forceSave) {
            $existingPayrolls = Payroll::whereMonth('payroll_date', $endDate->month)
                ->whereYear('payroll_date', $endDate->year)
                ->exists();

            if ($existingPayrolls) {
                return ['success' => false, 'message' => 'Data penggajian untuk bulan ini sudah ada. Tidak dapat menyimpan duplikat.'];
            }
        }

        $successCount = 0;
        $errorMessages = [];

        DB::beginTransaction();
        try {
            foreach ($payrollData as $data) {
                try {
                    $linmas = Linmas::where('nik', $data['nik'])->first();
                    if (!$linmas) {
                        $errorMessages[] = 'NIK ' . $data['nik'] . ' tidak ditemukan.';
                        continue;
                    }

                    $payroll = Payroll::create([
                        'linmas_id' => $linmas->id,
                        'total_days_present' => $data['total_days_worked'] ?? 0,
                        'base_salary' => $data['base_salary'] ?? 0,
                        'overtime_payment' => $data['overtime_payment'] ?? 0,
                        'total_salary' => $data['total_wage'] ?? 0,
                        'payroll_date' => $endDate,
                        'payment_status' => 'pending',
                        'processing_status' => 'draft',
                        'status_notes' => 'Data awal, menunggu verifikasi',
                    ]);

                    $this->storePayrollDetails($payroll, $data);

                    $successCount++;
                } catch (\Exception $innerException) {
                    Log::error("Error processing payroll for NIK: " . ($data['nik'] ?? 'unknown'), [
                        'error' => $innerException->getMessage(),
                    ]);
                    $errorMessages[] = "Error pada NIK " . ($data['nik'] ?? 'unknown') . ": " . $innerException->getMessage();
                }
            }

            if ($successCount == 0) {
                DB::rollBack();
                $finalMessage = 'Tidak ada data yang berhasil disimpan. ' . implode(', ', $errorMessages);
                return ['success' => false, 'message' => $finalMessage];
            }

            DB::commit();

            $finalMessage = "Berhasil menyimpan {$successCount} data penggajian.";
            if (!empty($errorMessages)) {
                $finalMessage .= " Terdapat beberapa error: " . implode(', ', $errorMessages);
                return ['success' => true, 'warning' => true, 'message' => $finalMessage];
            }

            return ['success' => true, 'message' => $finalMessage];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Fatal error in PayrollStorageService", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return ['success' => false, 'message' => 'Terjadi kesalahan fatal saat menyimpan data: ' . $e->getMessage()];
        }
    }

    /**
     * Store the details for a given payroll.
     *
     * @param Payroll $payroll
     * @param array $data
     */
    private function storePayrollDetails(Payroll $payroll, array $data): void
    {
        // Base salary
        PayrollDetail::create([
            'payroll_id' => $payroll->id, 'name' => 'Gaji Pokok', 'type' => 'base', 'amount' => $data['base_salary'] ?? 0
        ]);

        // Overtime
        if (($data['overtime_payment'] ?? 0) > 0) {
            PayrollDetail::create([
                'payroll_id' => $payroll->id, 'name' => 'Lembur', 'type' => 'overtime', 'amount' => $data['overtime_payment']
            ]);
        }

        // Allowances
        foreach ($data['allowances'] ?? [] as $allowance) {
            $type = AllowanceDeductionType::where('code', $allowance['code'])->first();
            PayrollDetail::create([
                'payroll_id' => $payroll->id,
                'type_id' => $type ? $type->id : null,
                'name' => $allowance['name'],
                'type' => 'allowance',
                'amount' => $allowance['amount'] ?? 0
            ]);
        }

        // Deductions
        foreach ($data['deductions'] ?? [] as $deduction) {
            $type = AllowanceDeductionType::where('code', $deduction['code'])->first();
            PayrollDetail::create([
                'payroll_id' => $payroll->id,
                'type_id' => $type ? $type->id : null,
                'name' => $deduction['name'],
                'type' => 'deduction',
                'amount' => $deduction['amount'] ?? 0
            ]);
        }
    }
}
