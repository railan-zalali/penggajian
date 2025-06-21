<?php

namespace App\Http\Controllers;

use App\Models\Linmas;
use App\Models\Attendance;
use App\Models\attendances;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    public function index()
    {
        $totalLinmas = Linmas::count();
        $attendanceThisMonth = attendances::whereMonth('waktu', Carbon::now()->month)->count();
        $totalSalary = Payroll::sum('total_salary');

        // Hitung hari kerja dalam bulan ini (tidak termasuk Sabtu dan Minggu)
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $workingDays = CarbonPeriod::create($startOfMonth, $endOfMonth)
            ->filter(function ($date) {
                return !$date->isWeekend();
            })
            ->count();

        $recentLinmas = Linmas::latest()->take(5)->get();
        $recentPayrolls = Payroll::latest()->take(3)->get();

        $attendanceData = attendances::selectRaw('MONTH(waktu) as month, COUNT(*) as attendance, SUM(CASE WHEN status_baru = "Lembur Masuk" THEN 1 ELSE 0 END) as overtime')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('dashboard', compact(
            'totalLinmas',
            'attendanceThisMonth',
            'totalSalary',
            'workingDays',
            'recentLinmas',
            'recentPayrolls',
            'attendanceData'
        ));
    }
}
