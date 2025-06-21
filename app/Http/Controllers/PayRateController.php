<?php

namespace App\Http\Controllers;

use App\Models\linmas;
use App\Models\PayRate;
use Illuminate\Http\Request;

class PayRateController extends Controller
{
    public function index()
    {
        $payRates = PayRate::with('linmas')->latest('effective_date')->get();
        return view('pay-rates.index', compact('payRates'));
    }

    public function create()
    {
        $linmasMembers = linmas::all();
        return view('pay-rates.create', compact('linmasMembers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|exists:linmas,nik',
            'hourly_rate' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
        ]);

        PayRate::create($validated);

        return redirect()->route('pay-rates.index')->with('success', 'Pay rate added successfully');
    }
}
