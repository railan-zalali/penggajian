<?php

namespace App\Http\Controllers;

use App\Models\Linmas;
use App\Models\attendances;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Metrik Dasar
        $totalLinmas = Linmas::count();
        $attendanceThisMonth = attendances::whereMonth('waktu', Carbon::now()->month)
            ->whereYear('waktu', Carbon::now()->year)
            ->count();
        $totalSalary = Payroll::where('payment_status', 'paid')->sum('total_salary');

        // Hitung hari kerja dalam bulan ini (tidak termasuk Sabtu dan Minggu)
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $workingDays = CarbonPeriod::create($startOfMonth, $endOfMonth)
            ->filter(function ($date) {
                return !$date->isWeekend();
            })
            ->count();

        // Data Terbaru
        $recentLinmas = Linmas::latest()->take(5)->get();
        $recentPayrolls = Payroll::with('linmas')
            ->latest('payroll_date')
            ->take(5)
            ->get();

        // Data Kehadiran per bulan (6 bulan terakhir)
        $attendanceData = attendances::selectRaw('MONTH(waktu) as month, YEAR(waktu) as year, COUNT(*) as attendance, SUM(CASE WHEN status_baru = "Lembur Masuk" THEN 1 ELSE 0 END) as overtime')
            ->whereRaw('DATE(waktu) >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Statistik Status Pembayaran bulan ini
        $paymentStats = $this->getPaymentStats();

        return view('dashboard', compact(
            'totalLinmas',
            'attendanceThisMonth',
            'totalSalary',
            'workingDays',
            'recentLinmas',
            'recentPayrolls',
            'attendanceData',
            'paymentStats'
        ));
    }

    /**
     * Get payment status statistics
     */
    private function getPaymentStats()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Get counts for each payment status
        $paymentStatusCounts = Payroll::whereBetween('payroll_date', [$startOfMonth, $endOfMonth])
            ->select('payment_status', DB::raw('count(*) as total'))
            ->groupBy('payment_status')
            ->pluck('total', 'payment_status')
            ->toArray();

        // Ensure all statuses have a value
        $stats = [
            'paid' => $paymentStatusCounts['paid'] ?? 0,
            'pending' => $paymentStatusCounts['pending'] ?? 0,
            'cancelled' => $paymentStatusCounts['cancelled'] ?? 0,
        ];

        return $stats;
    }
}
