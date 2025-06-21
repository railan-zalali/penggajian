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
        ]);

        // Parse period into year and month
        list($year, $month) = explode('-', $request->period);

        // Check if this period is already closed
        if (MonthClosing::isMonthClosed($year, $month)) {
            return redirect()->route('month-closing.create')
                ->with('error', 'Periode ini sudah ditutup sebelumnya.');
        }

        // Begin transaction
        DB::beginTransaction();

        try {
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
                'notes' => $request->notes,
                'status' => 'closed'
            ]);

            // Update payrolls to link to this closing
            foreach ($payrolls as $payroll) {
                $payroll->month_closing_id = $closing->id;
                $payroll->save();
            }

            DB::commit();

            return redirect()->route('month-closing.index')
                ->with('success', 'Periode ' . $period->format('F Y') . ' berhasil ditutup.');
        } catch (\Exception $e) {
            DB::rollback();
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

        return view('month-closing.show', compact('monthClosing', 'payrolls'));
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

        DB::beginTransaction();

        try {
            // Update status
            $monthClosing->status = 'reopened';
            $monthClosing->save();

            // Unlink payrolls
            Payroll::where('month_closing_id', $monthClosing->id)
                ->update(['month_closing_id' => null]);

            DB::commit();

            return redirect()->route('month-closing.index')
                ->with('success', 'Periode ' . $monthClosing->formatted_period . ' berhasil dibuka kembali.');
        } catch (\Exception $e) {
            DB::rollback();
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
