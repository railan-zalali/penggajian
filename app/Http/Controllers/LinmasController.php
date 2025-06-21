<?php

namespace App\Http\Controllers;

use App\Models\linmas;
use App\Http\Requests\StorelinmasRequest;
use App\Http\Requests\UpdatelinmasRequest;
use App\Imports\LinmasImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LinmasController extends Controller
{
    public function index()
    {
        $linmas = Linmas::all();
        return view('linmas.index', compact('linmas'));
    }

    public function create()
    {
        return view('linmas.create');
    }

    public function store(Request $request)
    {
        $validatedData = $this->validateLinmas($request);
        Linmas::create($validatedData);
        return redirect()->route('linmas.index')->with('success', 'Data Linmas berhasil ditambahkan!');
    }

    public function edit(Linmas $linmas)
    {
        return view('linmas.edit', compact('linmas'));
    }

    public function update(Request $request, Linmas $linmas)
    {
        $validatedData = $this->validateLinmas($request, $linmas->id);
        $linmas->update($validatedData);
        return redirect()->route('linmas.index')->with('success', 'Data Linmas berhasil diupdate!');
    }

    public function destroy(Linmas $linmas)
    {
        // Pastikan linmas yang dihapus adalah yang dikirimkan melalui form
        $linmas->delete();
        return redirect()->route('linmas.index')->with('success', 'Data Linmas berhasil dihapus.');
    }


    private function validateLinmas(Request $request, $id = null)
    {
        return $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|digits:16|unique:linmas,nik' . ($id ? ",$id" : ''),
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string|max:255',
            'pendidikan' => 'required|string|max:255',
            'pekerjaan' => 'required|string|max:255',
        ]);
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        Excel::import(new LinmasImport, $request->file('file'));

        return redirect()->route('linmas.index')->with('success', 'Data Linmas berhasil diimpor!');
    }
}
