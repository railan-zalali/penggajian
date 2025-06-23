<?php

namespace App\Http\Controllers;

use App\Models\Attendances;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerangkatDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $linmas = $user->linmas;

        if (!$linmas) {
            return view('perangkat.dashboard', [
                'error' => 'Akun Anda tidak terhubung dengan data perangkat desa. Hubungi administrator.'
            ]);
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
        $user = Auth::user();
        $linmas = $user->linmas;

        return view('perangkat.profile', compact('user', 'linmas'));
    }

    public function attendances()
    {
        $user = Auth::user();
        $linmas = $user->linmas;

        $attendances = Attendances::where('linmas_id', $linmas->id)
            ->orderBy('waktu', 'desc')
            ->paginate(15);

        return view('perangkat.attendances', compact('attendances', 'linmas'));
    }

    public function payrolls()
    {
        $user = Auth::user();
        $linmas = $user->linmas;

        $payrolls = Payroll::where('linmas_id', $linmas->id)
            ->orderBy('payroll_date', 'desc')
            ->paginate(10);

        return view('perangkat.payrolls', compact('payrolls', 'linmas'));
    }

    public function payrollDetail(Payroll $payroll)
    {
        $user = Auth::user();

        if ($payroll->linmas_id !== $user->linmas->id) {
            abort(403, 'Unauthorized action.');
        }

        $details = $payroll->details;

        return view('perangkat.payroll-detail', compact('payroll', 'details'));
    }
}
