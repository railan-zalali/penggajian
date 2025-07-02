<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Linmas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Payroll::with('linmas');

        // Filter by month and year
        if ($request->has('month') && $request->has('year')) {
            $query->whereMonth('payroll_date', $request->month)
                ->whereYear('payroll_date', $request->year);
        }

        // Filter by payment status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('payment_status', $request->status);
        }

        // Filter by linmas
        if ($request->has('linmas_id') && $request->linmas_id != 'all') {
            $query->where('linmas_id', $request->linmas_id);
        }

        $payrolls = $query->latest('payroll_date')->paginate(10);
        $linmasOptions = Linmas::orderBy('nama')->get();

        // Get unique months and years for the filter
        $dates = Payroll::selectRaw('DISTINCT MONTH(payroll_date) as month, YEAR(payroll_date) as year')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('payroll.history', compact('payrolls', 'linmasOptions', 'dates'));
    }

    public function updateStatus(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid,cancelled',
            'payment_method' => 'nullable|string|max:255',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validated['payment_status'] === 'paid' && $payroll->payment_status !== 'paid') {
            $validated['payment_date'] = now();
        }

        $payroll->update($validated);

        return redirect()->route('payroll.history.index')
            ->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    public function monthlyReport(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $month = $request->month;
        $year = $request->year;

        $payrolls = Payroll::with('linmas')
            ->whereMonth('payroll_date', $month)
            ->whereYear('payroll_date', $year)
            ->get();

        $monthName = Carbon::createFromDate($year, $month, 1)->format('F');

        $totalPaid = $payrolls->where('payment_status', 'paid')->sum('total_salary');
        $totalPending = $payrolls->where('payment_status', 'pending')->sum('total_salary');
        $totalCancelled = $payrolls->where('payment_status', 'cancelled')->sum('total_salary');

        $pdf = PDF::loadView('payroll.monthly-report', compact(
            'payrolls',
            'month',
            'year',
            'monthName',
            'totalPaid',
            'totalPending',
            'totalCancelled'
        ));

        return $pdf->download("laporan_gaji_{$monthName}_{$year}.pdf");
    }
}
