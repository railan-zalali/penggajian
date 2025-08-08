<?php

namespace App\Http\Controllers;

use App\Models\PositionSalaryRate;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PositionSalaryRateController extends Controller
{
    /**
     * Display a listing of the position salary rates.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $positionRates = PositionSalaryRate::all();
        return view('position-rates.index', compact('positionRates'));
    }

    /**
     * Show the form for creating a new position salary rate.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $workingDays = CarbonPeriod::create($startOfMonth, $endOfMonth)
            ->filter(function ($date) {
                return !$date->isWeekend();
            })
            ->count();
        return view('position-rates.create', compact('workingDays'));
    }

    /**
     * Store a newly created position salary rate in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'position' => 'required|string|max:255|unique:position_salary_rates',
            'monthly_rate' => 'required|numeric|min:0',
            'working_days' => 'required|integer|min:1|max:31',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('settings.position-rates.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Calculate daily rate
        $monthlyRate = $request->monthly_rate;
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $workingDays = CarbonPeriod::create($startOfMonth, $endOfMonth)
            ->filter(function ($date) {
                return !$date->isWeekend();
            })
            ->count();
        $dailyRate = PositionSalaryRate::calculateDailyRate($monthlyRate, $workingDays);

        PositionSalaryRate::create([
            'position' => $request->position,
            'monthly_rate' => $monthlyRate,
            'daily_rate' => $dailyRate,
            'working_days' => $workingDays,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('settings.position-rates.index')
            ->with('success', 'Tarif gaji jabatan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified position salary rate.
     *
     * @param  \App\Models\PositionSalaryRate  $positionRate
     * @return \Illuminate\Http\Response
     */
    public function edit(PositionSalaryRate $positionRate)
    {
        return view('position-rates.edit', compact('positionRate'));
    }

    /**
     * Update the specified position salary rate in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PositionSalaryRate  $positionRate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PositionSalaryRate $positionRate)
    {
        $validator = Validator::make($request->all(), [
            'position' => 'required|string|max:255|unique:position_salary_rates,position,' . $positionRate->id,
            'monthly_rate' => 'required|numeric|min:0',
            'working_days' => 'required|integer|min:1|max:31',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('position-rates.edit', $positionRate->id)
                ->withErrors($validator)
                ->withInput();
        }

        // Calculate daily rate
        $monthlyRate = $request->monthly_rate;
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $workingDays = CarbonPeriod::create($startOfMonth, $endOfMonth)
            ->filter(function ($date) {
                return !$date->isWeekend();
            })
            ->count();
        $dailyRate = PositionSalaryRate::calculateDailyRate($monthlyRate, $workingDays);

        $positionRate->update([
            'position' => $request->position,
            'monthly_rate' => $monthlyRate,
            'daily_rate' => $dailyRate,
            'working_days' => $workingDays,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('position-rates.index')
            ->with('success', 'Tarif gaji jabatan berhasil diperbarui.');
    }

    /**
     * Remove the specified position salary rate from storage.
     *
     * @param  \App\Models\PositionSalaryRate  $positionRate
     * @return \Illuminate\Http\Response
     */
    public function destroy(PositionSalaryRate $positionRate)
    {
        $positionRate->delete();

        return redirect()->route('settings.position-rates.index')
            ->with('success', 'Tarif gaji jabatan berhasil dihapus.');
    }

    /**
     * Recalculate daily rates for all positions.
     *
     * @return \Illuminate\Http\Response
     */
    public function recalculateRates()
    {
        $positionRates = PositionSalaryRate::all();

        foreach ($positionRates as $rate) {
            $dailyRate = PositionSalaryRate::calculateDailyRate($rate->monthly_rate, $rate->working_days);
            $rate->update(['daily_rate' => $dailyRate]);
        }

        return redirect()->route('settings.position-rates.index')
            ->with('success', 'Semua tarif harian berhasil dihitung ulang.');
    }
}
