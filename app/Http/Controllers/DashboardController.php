<?php

namespace App\Http\Controllers;

use App\Models\Attendances;
use App\Models\Linmas;
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
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $totalLinmas = Linmas::count();
        $attendanceThisMonth = Attendances::whereBetween('waktu', [$startOfMonth, $endOfMonth])->count();
        $totalSalary = Payroll::where('payment_status', 'paid')->sum('total_salary');

        // Hitung hari kerja dalam bulan ini (tidak termasuk Sabtu dan Minggu)
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
        $sixMonthsAgo = $now->copy()->subMonths(5)->startOfMonth();
        $attendanceData = attendances::selectRaw('MONTH(waktu) as month, YEAR(waktu) as year, COUNT(*) as attendance, SUM(CASE WHEN status = "C/Masuk" THEN 1 ELSE 0 END) as overtime')
            ->where('waktu', '>=', $sixMonthsAgo)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // $attendanceData = [];

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
