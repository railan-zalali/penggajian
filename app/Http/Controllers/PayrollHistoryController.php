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
        if ($request->filled('month') && $request->filled('year')) {
            $startOfMonth = Carbon::createFromDate($request->year, $request->month, 1)->startOfMonth();
            $endOfMonth = $startOfMonth->copy()->endOfMonth();
            $query->whereBetween('payroll_date', [$startOfMonth, $endOfMonth]);
        }

        // Filter by payment status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('payment_status', $request->status);
        }

        // Filter by linmas NIK
        if ($request->filled('linmas_nik')) {
            $linmas = Linmas::where('nik', $request->linmas_nik)->first();
            // If a linmas is found, filter by their ID. If not found, return no results.
            $query->where('linmas_id', $linmas ? $linmas->id : -1);
        }

        $payrolls = $query->latest('payroll_date')->paginate(10);

        // Get unique months and years for the filter
        $dates = Payroll::selectRaw('DISTINCT MONTH(payroll_date) as month, YEAR(payroll_date) as year')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('payroll.history', compact('payrolls', 'dates'));
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
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $payrolls = Payroll::with(['linmas', 'details'])
            ->whereBetween('payroll_date', [$startOfMonth, $endOfMonth])
            ->get();

        $monthName = $startOfMonth->format('F');

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
