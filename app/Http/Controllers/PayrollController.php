<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\attendances;
use App\Models\Linmas;
use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index()
    {
        return view('payroll.index');
    }

    public function calculatePayroll(Request $request)
    {
        $request->validate([
            'start_month' => 'required|date_format:Y-m',
            'end_month' => 'required|date_format:Y-m|after_or_equal:start_month',
        ]);

        $startDate = Carbon::parse($request->start_month)->startOfMonth();
        $endDate = Carbon::parse($request->end_month)->endOfMonth();

        $attendances = attendances::whereBetween('waktu', [$startDate, $endDate])
            ->with('linmas')
            ->get();

        $payrollData = $attendances->groupBy('linmas.nik')->map(function ($attendanceGroup) {
            $totalDaysWorked = $attendanceGroup->where('status', 'C/Masuk')->count();
            $totalOvertime = $attendanceGroup->where('status_baru', 'Lembur Masuk')->count();
            $dailyWage = 75114;
            $overtimeWage = 10000;

            return [
                'nik' => $attendanceGroup->first()->linmas->nik,
                'nama' => $attendanceGroup->first()->linmas->nama,
                'total_days_worked' => $totalDaysWorked,
                'total_overtime' => $totalOvertime,
                'total_wage' => ($totalDaysWorked * $dailyWage) + ($totalOvertime * $overtimeWage),
            ];
        })->values();

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
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if (!$payrollData) {
            return redirect()->back()->withErrors(['msg' => 'Data tidak ditemukan untuk NIK ini.']);
        }

        $pdf = PDF::loadView('payroll.slip', compact('payrollData', 'startDate', 'endDate'));

        return $pdf->download('slip_gaji_' . $payrollData['nik'] . '.pdf');
    }

    public function storePayroll(Request $request)
    {
        $payrollData = json_decode($request->payroll_data, true);
        $endDate = Carbon::parse($request->end_date);

        $existingPayrolls = Payroll::whereMonth('payroll_date', $endDate->month)
            ->whereYear('payroll_date', $endDate->year)
            ->get();

        if ($existingPayrolls->isNotEmpty()) {
            return redirect()->route('payroll.index')
                ->withErrors(['msg' => 'Data penggajian untuk bulan ini sudah ada. Tidak dapat menyimpan duplikat.']);
        }

        foreach ($payrollData as $data) {
            $linmas = Linmas::where('nik', $data['nik'])->first();

            if ($linmas) {
                Payroll::create([
                    'linmas_id' => $linmas->id,
                    'total_days_present' => $data['total_days_worked'],
                    'base_salary' => $data['total_days_worked'] * 75114,
                    'overtime_payment' => $data['total_overtime'] * 10000,
                    'total_salary' => $data['total_wage'],
                    'payroll_date' => $endDate,
                ]);
            } else {
                return redirect()->route('payroll.index')
                    ->withErrors(['msg' => 'NIK ' . $data['nik'] . ' tidak ditemukan.']);
            }
        }

        return redirect()->route('payroll.index')
            ->with('success', 'Data penggajian berhasil disimpan.');
    }
}
