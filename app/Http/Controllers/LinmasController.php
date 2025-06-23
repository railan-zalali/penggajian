<?php

namespace App\Http\Controllers;

use App\Models\Linmas;
use App\Http\Requests\StorelinmasRequest;
use App\Http\Requests\UpdatelinmasRequest;
use App\Imports\LinmasImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        try {
            Linmas::create($validatedData);
            return redirect()->route('linmas.index')->with('success', 'Data Perangkat Desa berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Linmas creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan data Perangkat Desa: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Linmas $linmas)
    {
        return view('linmas.edit', compact('linmas'));
    }

    public function update(Request $request, Linmas $linmas)
    {
        $validatedData = $this->validateLinmas($request, $linmas->id);

        try {
            $linmas->update($validatedData);
            return redirect()->route('linmas.index')->with('success', 'Data Perangkat Desa berhasil diupdate!');
        } catch (\Exception $e) {
            Log::error('Linmas update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengupdate data Perangkat Desa: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Linmas $linmas)
    {
        try {
            // Periksa apakah linmas memiliki data kehadiran atau penggajian
            if ($linmas->attendances()->count() > 0 || $linmas->payrolls()->count() > 0) {
                return redirect()->route('linmas.index')->with('error', 'Tidak dapat menghapus data Perangkat Desa karena masih memiliki data kehadiran atau penggajian');
            }

            $linmas->delete();
            return redirect()->route('linmas.index')->with('success', 'Data Perangkat Desa berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Linmas deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus data Perangkat Desa: ' . $e->getMessage());
        }
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
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        DB::beginTransaction();

        try {
            $import = new LinmasImport();
            Excel::import($import, $request->file('file'));

            // Cek jika ada error pada import
            $errors = $import->getErrors();
            if (!empty($errors)) {
                DB::rollBack();
                return redirect()->route('linmas.index')->with('error', 'Terjadi kesalahan saat import: ' . implode(', ', $errors));
            }

            DB::commit();
            return redirect()->route('linmas.index')->with('success', 'Data Perangkat Desa berhasil diimpor!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Linmas import failed: ' . $e->getMessage());
            return redirect()->route('linmas.index')->with('error', 'Gagal mengimpor data Perangkat Desa: ' . $e->getMessage());
        }
    }
}
