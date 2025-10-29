<?php

namespace App\Http\Controllers;

use App\Models\Attendances;
use App\Models\Payroll;
use App\Models\MonthClosing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    public function attendances(Request $request)
    {
        $linmas = Auth::guard('perangkat')->user();
        
        $query = Attendances::where('linmas_id', $linmas->id);
        
        // Filter berdasarkan tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('waktu', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('waktu', '<=', $request->end_date);
        }
        
        // Filter berdasarkan status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        $attendances = $query->orderBy('waktu', 'desc')->paginate(15);
        
        // Append query parameters to pagination links
        $attendances->appends($request->query());

        $attendanceStats = $this->getAttendanceStats($linmas->id, true);

        return view('perangkat.attendances', compact('attendances', 'linmas', 'attendanceStats'));
    }

    public function payrolls(Request $request)
    {
        $linmas = Auth::guard('perangkat')->user();
        
        $query = Payroll::where('linmas_id', $linmas->id);
        
        // Filter berdasarkan tahun
        if ($request->filled('year') && $request->year !== 'all') {
            $query->whereYear('payroll_date', $request->year);
        }
        
        // Filter berdasarkan bulan
        if ($request->filled('month') && $request->month !== 'all') {
            $query->whereMonth('payroll_date', $request->month);
        }
        
        // Filter berdasarkan status pembayaran
        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }
        
        $payrolls = $query->orderBy('payroll_date', 'desc')->paginate(10);
        
        // Append query parameters to pagination links
        $payrolls->appends($request->query());

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

    /**
     * Konfirmasi penerimaan pembayaran oleh perangkat desa
     */
    public function confirmPayment(Request $request, Payroll $payroll)
    {
        $linmas = Auth::guard('perangkat')->user();

        // Pastikan payroll milik perangkat yang login
        if ($payroll->linmas_id !== $linmas->id) {
            abort(403, 'Unauthorized action.');
        }

        // Hanya bisa konfirmasi jika status pembayaran sudah paid
        if ($payroll->payment_status !== 'paid') {
            return redirect()->route('perangkat.payroll-detail', $payroll)
                ->with('error', 'Konfirmasi hanya dapat dilakukan untuk pembayaran yang sudah Dibayar.');
        }

        // Jika sudah dikonfirmasi, hindari duplikasi
        if ($payroll->payment_confirmed) {
            return redirect()->route('perangkat.payroll-detail', $payroll)
                ->with('info', 'Pembayaran sudah dikonfirmasi sebelumnya.');
        }

        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $payroll->payment_confirmed = true;
        $payroll->payment_confirmed_at = now();
        $payroll->payment_confirmation_note = $request->note;
        $payroll->save();

        return redirect()->route('perangkat.payroll-detail', $payroll)
            ->with('success', 'Terima kasih, konfirmasi penerimaan pembayaran berhasil dikirim.');
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

    /**
     * Update the authenticated perangkat's profile.
     */
    public function updateProfile(Request $request)
    {
        $linmas = Auth::guard('perangkat')->user();
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:linmas,email,' . $linmas->id,
            'kontak' => 'nullable|string|max:20',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'pendidikan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:500',
        ]);

        $linmas->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'kontak' => $request->kontak,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'pendidikan' => $request->pendidikan,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('perangkat.profile')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update the authenticated perangkat's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password:perangkat',
            'password' => 'required|confirmed|min:8',
        ]);

        $linmas = Auth::guard('perangkat')->user();
        $linmas->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('perangkat.profile')
            ->with('success', 'Password berhasil diubah!');
    }
}
