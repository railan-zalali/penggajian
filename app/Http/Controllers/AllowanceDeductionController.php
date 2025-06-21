<?php

namespace App\Http\Controllers;

use App\Models\AllowanceDeductionType;
use App\Models\LinmasAllowanceDeduction;
use App\Models\Linmas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AllowanceDeductionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allowances = AllowanceDeductionType::where('type', 'allowance')->get();
        $deductions = AllowanceDeductionType::where('type', 'deduction')->get();

        return view('settings.allowances-deductions.index', compact('allowances', 'deductions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('settings.allowances-deductions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:allowance_deduction_types',
            'type' => 'required|in:allowance,deduction',
            'calculation_type' => 'required|in:fixed,percentage',
            'default_value' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_taxable' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('settings.allowances-deductions.create')
                ->withErrors($validator)
                ->withInput();
        }

        AllowanceDeductionType::create([
            'name' => $request->name,
            'code' => $request->code,
            'type' => $request->type,
            'calculation_type' => $request->calculation_type,
            'default_value' => $request->default_value,
            'description' => $request->description,
            'is_taxable' => $request->is_taxable ?? false,
            'is_active' => true,
        ]);

        return redirect()->route('settings.allowances-deductions.index')
            ->with('success', 'Tunjangan/Potongan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AllowanceDeductionType $allowanceDeduction)
    {
        return view('settings.allowances-deductions.edit', compact('allowanceDeduction'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AllowanceDeductionType $allowanceDeduction)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'calculation_type' => 'required|in:fixed,percentage',
            'default_value' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_taxable' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('settings.allowances-deductions.edit', $allowanceDeduction->id)
                ->withErrors($validator)
                ->withInput();
        }

        $allowanceDeduction->update([
            'name' => $request->name,
            'calculation_type' => $request->calculation_type,
            'default_value' => $request->default_value,
            'description' => $request->description,
            'is_taxable' => $request->is_taxable ?? false,
            'is_active' => $request->is_active ?? false,
        ]);

        return redirect()->route('settings.allowances-deductions.index')
            ->with('success', 'Tunjangan/Potongan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AllowanceDeductionType $allowanceDeduction)
    {
        // Check if it's being used
        $isUsed = LinmasAllowanceDeduction::where('type_id', $allowanceDeduction->id)->exists();

        if ($isUsed) {
            return redirect()->route('settings.allowances-deductions.index')
                ->with('error', 'Tunjangan/Potongan tidak dapat dihapus karena sedang digunakan.');
        }

        $allowanceDeduction->delete();

        return redirect()->route('settings.allowances-deductions.index')
            ->with('success', 'Tunjangan/Potongan berhasil dihapus.');
    }

    /**
     * Show linmas assignments page
     */
    public function linmasAssignments()
    {
        $linmasMembers = Linmas::all();
        return view('settings.allowances-deductions.linmas-assignments', compact('linmasMembers'));
    }

    /**
     * Show form to assign allowances/deductions to a linmas
     */
    public function showAssignForm(Linmas $linmas)
    {
        $allowances = AllowanceDeductionType::where('type', 'allowance')->where('is_active', true)->get();
        $deductions = AllowanceDeductionType::where('type', 'deduction')->where('is_active', true)->get();

        $assignedAllowances = LinmasAllowanceDeduction::getLinmasAllowances($linmas->id);
        $assignedDeductions = LinmasAllowanceDeduction::getLinmasDeductions($linmas->id);

        return view('settings.allowances-deductions.assign', compact(
            'linmas',
            'allowances',
            'deductions',
            'assignedAllowances',
            'assignedDeductions'
        ));
    }

    /**
     * Save allowances/deductions assignments for a linmas
     */
    public function saveAssignments(Request $request, Linmas $linmas)
    {
        $allowanceTypes = AllowanceDeductionType::where('type', 'allowance')->where('is_active', true)->get();
        $deductionTypes = AllowanceDeductionType::where('type', 'deduction')->where('is_active', true)->get();

        // Process allowances
        foreach ($allowanceTypes as $type) {
            $value = $request->input('allowance_' . $type->id);
            if ($value !== null) {
                LinmasAllowanceDeduction::updateOrCreate(
                    ['linmas_id' => $linmas->id, 'type_id' => $type->id],
                    ['value' => $value, 'is_active' => true]
                );
            }
        }

        // Process deductions
        foreach ($deductionTypes as $type) {
            $value = $request->input('deduction_' . $type->id);
            if ($value !== null) {
                LinmasAllowanceDeduction::updateOrCreate(
                    ['linmas_id' => $linmas->id, 'type_id' => $type->id],
                    ['value' => $value, 'is_active' => true]
                );
            }
        }

        return redirect()->route('settings.allowances-deductions.linmas-assignments')
            ->with('success', 'Tunjangan/Potongan untuk ' . $linmas->nama . ' berhasil disimpan.');
    }
}
