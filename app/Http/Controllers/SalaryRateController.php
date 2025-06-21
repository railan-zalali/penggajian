<?php

namespace App\Http\Controllers;

use App\Models\SalaryRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalaryRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rates = SalaryRate::all();
        return view('settings.rates.index', compact('rates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('settings.rates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:salary_rates',
            'value' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('settings.rates.create')
                ->withErrors($validator)
                ->withInput();
        }

        SalaryRate::create([
            'name' => $request->name,
            'key' => $request->key,
            'value' => $request->value,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('settings.rates.index')
            ->with('success', 'Tarif berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalaryRate $rate)
    {
        return view('settings.rates.edit', compact('rate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalaryRate $rate)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'value' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('settings.rates.edit', $rate->id)
                ->withErrors($validator)
                ->withInput();
        }

        $rate->update([
            'name' => $request->name,
            'value' => $request->value,
            'description' => $request->description,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('settings.rates.index')
            ->with('success', 'Tarif berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalaryRate $rate)
    {
        $rate->delete();

        return redirect()->route('settings.rates.index')
            ->with('success', 'Tarif berhasil dihapus.');
    }
}
