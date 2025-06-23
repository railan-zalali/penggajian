<?php

namespace App\Http\Controllers;

use App\Models\MonthClosing;
use App\Models\Payroll;
use App\Models\Linmas;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MonthClosingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $closings = MonthClosing::with('user')->orderBy('year', 'desc')->orderBy('month', 'desc')->paginate(10);
        return view('month-closing.index', compact('closings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get available periods (months that have payrolls but are not closed yet)
        $availablePeriods = Payroll::selectRaw('DISTINCT YEAR(payroll_date) as year, MONTH(payroll_date) as month')
            ->whereNull('month_closing_id')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($item) {
                $date = Carbon::createFromDate($item->year, $item->month, 1);
                return [
                    'year' => $item->year,
                    'month' => $item->month,
                    'formatted' => $date->format('F Y'),
                    'value' => $date->format('Y-m')
                ];
            });

        return view('month-closing.create', compact('availablePeriods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'period' => 'required|string',
            'notes' => 'nullable|string',
            'ignore_pending' => 'nullable|boolean',
        ]);

        // Parse period into year and month
        list($year, $month) = explode('-', $request->period);

        // Cek apakah bulan sudah ditutup
        if (MonthClosing::isMonthClosed($year, $month)) {
            return redirect()->back()->with('error', 'Periode ini sudah ditutup sebelumnya.');
        }

        // Create mutex lock SEBELUM transaksi
        $lockFile = storage_path("app/month_closing_{$year}_{$month}.lock");

        // Cek apakah proses sudah berjalan
        if (file_exists($lockFile)) {
            $lockTime = file_get_contents($lockFile);
            $lockAge = time() - strtotime($lockTime);

            // Jika lock berusia > 30 menit, anggap proses sebelumnya gagal
            if ($lockAge < 1800) {
                return redirect()->back()->with('error', 'Proses tutup bulan sedang berjalan. Mohon tunggu beberapa saat.');
            }
        }

        // Buat lock baru
        file_put_contents($lockFile, Carbon::now()->toDateTimeString());


        try {
            // Begin transaction
            DB::beginTransaction();

            // Get all payrolls for this period
            $period = Carbon::createFromDate($year, $month, 1);
            $payrolls = Payroll::whereYear('payroll_date', $year)
                ->whereMonth('payroll_date', $month)
                ->whereNull('month_closing_id')
                ->get();

            if ($payrolls->isEmpty()) {
                return redirect()->route('month-closing.create')
                    ->with('error', 'Tidak ada data penggajian untuk periode ini.');
            }

            // Check if there are any pending payments
            $pendingPayrolls = $payrolls->where('payment_status', 'pending');
            if ($pendingPayrolls->isNotEmpty() && !$request->has('ignore_pending')) {
                $pendingCount = $pendingPayrolls->count();
                $totalCount = $payrolls->count();

                return redirect()->route('month-closing.create')
                    ->with('error', "Masih terdapat $pendingCount dari $totalCount gaji dengan status pending. Mohon selesaikan pembayaran terlebih dahulu atau centang 'Abaikan status pending' untuk melanjutkan.")
                    ->withInput();
            }

            // Check if there are any cancelled payments and summarize them
            $cancelledPayrolls = $payrolls->where('payment_status', 'cancelled');
            $cancelledAmount = $cancelledPayrolls->sum('total_salary');
            $cancelledCount = $cancelledPayrolls->count();

            // Create month closing record
            $closing = MonthClosing::create([
                'period' => $period->format('Y-m-d'),
                'year' => $year,
                'month' => $month,
                'closing_date' => Carbon::now(),
                'total_linmas' => Linmas::count(),
                'total_payrolls' => $payrolls->count(),
                'total_amount' => $payrolls->sum('total_salary'),
                'closed_by' => Auth::id(),
                'notes' => $request->notes . ($cancelledCount > 0 ?
                    " (Terdapat $cancelledCount pembayaran dibatalkan senilai Rp " . number_format($cancelledAmount, 0, ',', '.') . ")" :
                    ""),
                'status' => 'closed'
            ]);

            // Update payrolls to link to this closing

            foreach ($payrolls as $payroll) {
                $payroll->month_closing_id = $closing->id;
                $payroll->save();
            }

            DB::commit();
            return redirect()->route('month-closing.index')->with('success', 'Periode berhasil ditutup.');
        } catch (\Exception $e) {
            DB::rollback();
            @unlink($lockFile);
            Log::error('Month closing failed: ' . $e->getMessage());
            return redirect()->route('month-closing.create')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MonthClosing $monthClosing)
    {
        $payrolls = Payroll::with('linmas')
            ->where('month_closing_id', $monthClosing->id)
            ->get();

        // Hitung summary berdasarkan status
        $summary = [
            'paid' => $payrolls->where('payment_status', 'paid')->sum('total_salary'),
            'pending' => $payrolls->where('payment_status', 'pending')->sum('total_salary'),
            'cancelled' => $payrolls->where('payment_status', 'cancelled')->sum('total_salary'),
        ];

        return view('month-closing.show', compact('monthClosing', 'payrolls', 'summary'));
    }

    /**
     * Reopen a closed month
     */
    public function reopen(MonthClosing $monthClosing)
    {
        // Only allow reopening if status is 'closed'
        if ($monthClosing->status !== 'closed') {
            return redirect()->route('month-closing.index')
                ->with('error', 'Periode ini tidak dapat dibuka kembali.');
        }

        // Check if it's the latest closed month (tidak boleh membuka bulan yang bukan terakhir)
        $latestClosing = MonthClosing::where('status', 'closed')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->first();

        if ($latestClosing->id !== $monthClosing->id) {
            return redirect()->route('month-closing.index')
                ->with('error', 'Hanya periode terakhir yang ditutup yang dapat dibuka kembali.');
        }

        DB::beginTransaction();

        try {
            // Update status
            $monthClosing->status = 'reopened';
            $monthClosing->save();

            // Unlink payrolls
            Payroll::where('month_closing_id', $monthClosing->id)
                ->update(['month_closing_id' => null]);

            DB::commit();

            // Remove lock file if exists
            $lockFile = storage_path("app/month_closing_{$monthClosing->year}_{$monthClosing->month}.lock");
            if (file_exists($lockFile)) {
                unlink($lockFile);
            }

            return redirect()->route('month-closing.index')
                ->with('success', 'Periode ' . $monthClosing->formatted_period . ' berhasil dibuka kembali.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Month reopening failed: ' . $e->getMessage());
            return redirect()->route('month-closing.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Generate monthly report for a closed month
     */
    public function generateReport(MonthClosing $monthClosing)
    {
        $payrolls = Payroll::with('linmas')
            ->where('month_closing_id', $monthClosing->id)
            ->get();

        $totalPaid = $payrolls->where('payment_status', 'paid')->sum('total_salary');
        $totalPending = $payrolls->where('payment_status', 'pending')->sum('total_salary');
        $totalCancelled = $payrolls->where('payment_status', 'cancelled')->sum('total_salary');

        $pdf = Pdf::loadView('month-closing.report', [
            'monthClosing' => $monthClosing,
            'payrolls' => $payrolls,
            'totalPaid' => $totalPaid,
            'totalPending' => $totalPending,
            'totalCancelled' => $totalCancelled
        ]);

        return $pdf->download('laporan_tutup_bulan_' . $monthClosing->year . '_' . $monthClosing->month . '.pdf');
    }
}
