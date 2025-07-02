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
    public function __construct()
    {
        $this->middleware('auth:perangkat');
    }

    public function index()
    {
        $linmas = Auth::guard('perangkat')->user();

        if (!$linmas || !$linmas->can_login) {
            Auth::guard('perangkat')->logout();
            return redirect()->route('perangkat.login')
                ->withErrors(['error' => 'Akses ditolak. Hubungi administrator.']);
        }

        // Ambil data kehadiran
        $attendances = Attendances::where('linmas_id', $linmas->id)
            ->orderBy('waktu', 'desc')
            ->paginate(10);

        // Ambil data gaji
        $payrolls = Payroll::where('linmas_id', $linmas->id)
            ->orderBy('payroll_date', 'desc')
            ->paginate(5);

        // Statistik kehadiran bulan ini
        $attendanceThisMonth = Attendances::where('linmas_id', $linmas->id)
            ->whereMonth('waktu', now()->month)
            ->whereYear('waktu', now()->year)
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

        if (!$linmas || !$linmas->can_login) {
            Auth::guard('perangkat')->logout();
            return redirect()->route('perangkat.login')
                ->withErrors(['error' => 'Akses ditolak. Hubungi administrator.']);
        }

        // Statistik kehadiran
        $attendanceStats = [
            'total' => Attendances::where('linmas_id', $linmas->id)->count(),
            'thisMonth' => Attendances::where('linmas_id', $linmas->id)
                ->whereMonth('waktu', now()->month)
                ->whereYear('waktu', now()->year)
                ->count(),
            'thisWeek' => Attendances::where('linmas_id', $linmas->id)
                ->whereBetween('waktu', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];

        // Statistik gaji
        $payrollStats = [
            'totalReceived' => Payroll::where('linmas_id', $linmas->id)
                ->where('payment_status', 'paid')
                ->sum('total_salary'),
            'lastPayroll' => Payroll::where('linmas_id', $linmas->id)
                ->orderBy('payroll_date', 'desc')
                ->first(),
            'pending' => Payroll::where('linmas_id', $linmas->id)
                ->where('payment_status', 'pending')
                ->count(),
            'totalPeriods' => Payroll::where('linmas_id', $linmas->id)->count(),
        ];

        return view('perangkat.profile', compact('linmas', 'attendanceStats', 'payrollStats'));
    }

    public function attendances()
    {
        $linmas = Auth::guard('perangkat')->user();

        if (!$linmas || !$linmas->can_login) {
            Auth::guard('perangkat')->logout();
            return redirect()->route('perangkat.login')
                ->withErrors(['error' => 'Akses ditolak. Hubungi administrator.']);
        }

        $attendances = Attendances::where('linmas_id', $linmas->id)
            ->orderBy('waktu', 'desc')
            ->paginate(15);

        // Statistik kehadiran
        $attendanceStats = [
            'total' => Attendances::where('linmas_id', $linmas->id)->count(),
            'thisMonth' => Attendances::where('linmas_id', $linmas->id)
                ->whereMonth('waktu', now()->month)
                ->whereYear('waktu', now()->year)
                ->count(),
            'thisWeek' => Attendances::where('linmas_id', $linmas->id)
                ->whereBetween('waktu', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'today' => Attendances::where('linmas_id', $linmas->id)
                ->whereDate('waktu', now()->toDateString())
                ->count(),
        ];

        return view('perangkat.attendances', compact('attendances', 'linmas', 'attendanceStats'));
    }

    public function payrolls()
    {
        $linmas = Auth::guard('perangkat')->user();

        if (!$linmas || !$linmas->can_login) {
            Auth::guard('perangkat')->logout();
            return redirect()->route('perangkat.login')
                ->withErrors(['error' => 'Akses ditolak. Hubungi administrator.']);
        }

        $payrolls = Payroll::where('linmas_id', $linmas->id)
            ->orderBy('payroll_date', 'desc')
            ->paginate(10);

        // Statistik gaji
        $payrollStats = [
            'totalReceived' => Payroll::where('linmas_id', $linmas->id)
                ->where('payment_status', 'paid')
                ->sum('total_salary'),
            'lastPayroll' => Payroll::where('linmas_id', $linmas->id)
                ->orderBy('payroll_date', 'desc')
                ->first(),
            'pending' => Payroll::where('linmas_id', $linmas->id)
                ->where('payment_status', 'pending')
                ->count(),
            'totalPeriods' => Payroll::where('linmas_id', $linmas->id)->count(),
        ];

        return view('perangkat.payrolls', compact('payrolls', 'linmas', 'payrollStats'));
    }

    public function payrollDetail(Payroll $payroll)
    {
        $linmas = Auth::guard('perangkat')->user();

        if ($payroll->linmas_id !== $linmas->id) {
            abort(403, 'Unauthorized action.');
        }

        $details = $payroll->details;

        return view('perangkat.payroll-detail', compact('payroll', 'details'));
    }

    public function monthClosing()
    {
        $linmas = Auth::guard('perangkat')->user();

        if (!$linmas || !$linmas->can_login) {
            Auth::guard('perangkat')->logout();
            return redirect()->route('perangkat.login')
                ->withErrors(['error' => 'Akses ditolak. Hubungi administrator.']);
        }

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
        $hasUnprocessedPayroll = Payroll::whereYear('payroll_date', $nextMonth->year)
            ->whereMonth('payroll_date', $nextMonth->month)
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
}
