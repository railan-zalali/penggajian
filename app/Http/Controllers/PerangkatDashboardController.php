<?php

namespace App\Http\Controllers;

use App\Models\Attendances;
use App\Models\Payroll;
use App\Models\MonthClosing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PerangkatDashboardController extends Controller
{
    public function index()
    {
        $linmas = Auth::guard('perangkat')->user();

        // Ambil data kehadiran
        $attendances = Attendances::where('linmas_id', $linmas->id)
            ->orderBy('waktu', 'desc')
            ->paginate(10);

        // Ambil data gaji
        $payrolls = Payroll::where('linmas_id', $linmas->id)
            ->orderBy('payroll_date', 'desc')
            ->paginate(5);

        // Statistik kehadiran bulan ini
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $attendanceThisMonth = Attendances::where('linmas_id', $linmas->id)
            ->whereBetween('waktu', [$startOfMonth, $endOfMonth])
            ->count();

        return view('perangkat.dashboard', compact(
            'linmas',
            'attendances',
            'payrolls',
            'attendanceThisMonth'
        ));
    }

    public function profile()
    {
        $linmas = Auth::guard('perangkat')->user();
        $attendanceStats = $this->getAttendanceStats($linmas->id);
        $payrollStats = $this->getPayrollStats($linmas->id);

        return view('perangkat.profile', compact('linmas', 'attendanceStats', 'payrollStats'));
    }

    public function attendances()
    {
        $linmas = Auth::guard('perangkat')->user();
        $attendances = Attendances::where('linmas_id', $linmas->id)
            ->orderBy('waktu', 'desc')
            ->paginate(15);

        $attendanceStats = $this->getAttendanceStats($linmas->id, true);

        return view('perangkat.attendances', compact('attendances', 'linmas', 'attendanceStats'));
    }

    public function payrolls()
    {
        $linmas = Auth::guard('perangkat')->user();
        $payrolls = Payroll::where('linmas_id', $linmas->id)
            ->orderBy('payroll_date', 'desc')
            ->paginate(10);

        $payrollStats = $this->getPayrollStats($linmas->id);

        return view('perangkat.payrolls', compact('payrolls', 'linmas', 'payrollStats'));
    }

    public function payrollDetail(Payroll $payroll)
    {
        $linmas = Auth::guard('perangkat')->user();

        // This authorization check is specific and should remain.
        if ($payroll->linmas_id !== $linmas->id) {
            abort(403, 'Unauthorized action.');
        }

        $details = $payroll->details;

        return view('perangkat.payroll-detail', compact('payroll', 'details'));
    }

    public function monthClosing()
    {
        $linmas = Auth::guard('perangkat')->user();

        // Ambil semua month closing
        $monthClosings = MonthClosing::with('createdBy')
            ->orderBy('closing_date', 'desc')
            ->paginate(10);

        // Cek apakah bisa membuat tutup bulan baru
        $canCreateNew = false;
        $nextPeriod = null;

        // Ambil periode terakhir yang sudah ditutup
        $lastClosing = MonthClosing::orderBy('closing_date', 'desc')->first();
        
        if ($lastClosing) {
            $nextMonth = Carbon::parse($lastClosing->period_year . '-' . $lastClosing->period_month . '-01')
                ->addMonth();
        } else {
            // Jika belum ada tutup bulan, ambil dari payroll pertama
            $firstPayroll = Payroll::orderBy('payroll_date', 'asc')->first();
            if ($firstPayroll) {
                $nextMonth = Carbon::parse($firstPayroll->payroll_date);
            } else {
                $nextMonth = now()->subMonth();
            }
        }

        // Cek apakah ada payroll untuk periode tersebut yang belum ditutup
        $startOfNextMonth = $nextMonth->copy()->startOfMonth();
        $endOfNextMonth = $nextMonth->copy()->endOfMonth();
        $hasUnprocessedPayroll = Payroll::whereBetween('payroll_date', [$startOfNextMonth, $endOfNextMonth])
            ->whereNull('month_closing_id')
            ->whereIn('payment_status', ['paid', 'pending'])
            ->exists();

        if ($hasUnprocessedPayroll) {
            $canCreateNew = true;
            $nextPeriod = $nextMonth->format('F Y');
        }

        return view('perangkat.month-closing', compact(
            'monthClosings',
            'linmas',
            'canCreateNew',
            'nextPeriod'
        ));
    }

    /**
     * Get attendance statistics for a given linmas ID.
     *
     * @param int $linmasId
     * @param bool $includeToday
     * @return array
     */
    private function getAttendanceStats(int $linmasId, bool $includeToday = false): array
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();

        $stats = [
            'total' => Attendances::where('linmas_id', $linmasId)->count(),
            'thisMonth' => Attendances::where('linmas_id', $linmasId)
                ->whereBetween('waktu', [$startOfMonth, $endOfMonth])
                ->count(),
            'thisWeek' => Attendances::where('linmas_id', $linmasId)
                ->whereBetween('waktu', [$startOfWeek, $endOfWeek])
                ->count(),
        ];

        if ($includeToday) {
            $stats['today'] = Attendances::where('linmas_id', $linmasId)
                ->whereDate('waktu', $now->toDateString())
                ->count();
        }

        return $stats;
    }

    /**
     * Get payroll statistics for a given linmas ID.
     *
     * @param int $linmasId
     * @return array
     */
    private function getPayrollStats(int $linmasId): array
    {
        return [
            'totalReceived' => Payroll::where('linmas_id', $linmasId)
                ->where('payment_status', 'paid')
                ->sum('total_salary'),
            'lastPayroll' => Payroll::where('linmas_id', $linmasId)
                ->orderBy('payroll_date', 'desc')
                ->first(),
            'pending' => Payroll::where('linmas_id', $linmasId)
                ->where('payment_status', 'pending')
                ->count(),
            'totalPeriods' => Payroll::where('linmas_id', $linmasId)->count(),
        ];
    }
}
