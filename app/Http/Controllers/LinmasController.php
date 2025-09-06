<?php

namespace App\Http\Controllers;

use App\Models\Linmas;
use App\Imports\LinmasImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LinmasController extends Controller
{
    public function index(Request $request)
    {
        $query = Linmas::query();

        // Pencarian
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Filter pendidikan
        if ($request->has('pendidikan') && $request->pendidikan != 'all') {
            $query->where('pendidikan', $request->pendidikan);
        }

        // Pagination yang lebih baik
        $linmas = $query->orderBy('nama')->paginate(15);

        $pendidikanOptions = Linmas::select('pendidikan')
            ->distinct()->pluck('pendidikan');

        return view('linmas.index', compact('linmas', 'pendidikanOptions'));
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
        try {
            return view('linmas.edit', compact('linmas'));
        } catch (\Exception $e) {
            Log::error('Error accessing linmas edit form: ' . $e->getMessage(), [
                'linmas_id' => $linmas->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('linmas.index')
                ->with('error', 'Gagal mengakses form edit: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Linmas $linmas)
    {
        try {
            // Validasi data
            $validatedData = $this->validateLinmas($request, $linmas->id);
            
            // Simpan data lama untuk log
            $oldData = $linmas->toArray();
            
            // Update data
            $linmas->update($validatedData);
            
            // Log perubahan data
            Log::info('Linmas updated successfully', [
                'id' => $linmas->id,
                'nik' => $linmas->nik,
                'old_data' => $oldData,
                'new_data' => $linmas->toArray()
            ]);
            
            return redirect()->route('linmas.index')
                ->with('success', 'Data Perangkat Desa berhasil diupdate!');
        } catch (\Exception $e) {
            Log::error('Linmas update failed: ' . $e->getMessage(), [
                'nik' => $request->nik,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Gagal mengupdate data Perangkat Desa: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Linmas $linmas)
    {
        try {
            // Periksa apakah linmas memiliki data kehadiran atau penggajian
            $attendanceCount = $linmas->attendances()->count();
            $payrollCount = $linmas->payrolls()->count();
            $userCount = $linmas->user()->count();
            
            $errorMessages = [];
            if ($attendanceCount > 0) {
                $errorMessages[] = "data kehadiran ({$attendanceCount} data)";
            }
            if ($payrollCount > 0) {
                $errorMessages[] = "data penggajian ({$payrollCount} data)";
            }
            if ($userCount > 0) {
                $errorMessages[] = "akun pengguna";
            }
            
            if (!empty($errorMessages)) {
                return redirect()->route('linmas.index')
                    ->with('error', 'Tidak dapat menghapus data Perangkat Desa karena masih memiliki ' . implode(', ', $errorMessages));
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
            'posisi' => 'nullable|string|max:255',
            'tanggal_bergabung' => 'nullable|date',
            'status' => 'nullable|string|in:aktif,tidak aktif',
            'gaji_pokok' => 'nullable|numeric',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        Log::info('Memulai import file: ' . $fileName);

        try {
            DB::beginTransaction();
            $import = new LinmasImport();

            // Gunakan queue untuk file besar
            Excel::import($import, $file);

            // Periksa jumlah data yang berhasil diimport
            $totalRows = $import->getRowCount();
            $errorCount = count($import->errors());
            $successCount = $import->getSuccessCount(); // Menggunakan metode baru yang lebih akurat
            $duplicateCount = $import->getDuplicateCount(); // Mendapatkan jumlah data duplikat
            
            Log::info('Jumlah data berhasil: ' . $successCount . 
                     ', Jumlah error: ' . $errorCount . 
                     ', Jumlah duplikat: ' . $duplicateCount . 
                     ', Total baris: ' . $totalRows);

            // Jika ada error, tampilkan pesan error
            if (count($import->errors()) > 0) {
                DB::rollBack();
                Log::warning('Import dibatalkan karena terdapat error: ' . json_encode($import->errors()));
                
                // Jika semua error adalah duplikasi, berikan pesan khusus
                if ($errorCount == $duplicateCount && $duplicateCount > 0) {
                    return redirect()->back()
                        ->withErrors($import->errors())
                        ->with('error', 'Semua data sudah ada dalam sistem. Tidak ada data baru yang diimport.');
                }
                
                return redirect()->back()
                    ->withErrors($import->errors())
                    ->with('error', 'Terdapat kesalahan pada file import. ' . 
                             ($duplicateCount > 0 ? "$duplicateCount data duplikat ditemukan." : ''));
            }

            if ($successCount == 0) {
                DB::rollBack();
                Log::warning('Import dibatalkan karena tidak ada data yang berhasil diproses');

                // Berikan pesan yang lebih informatif tentang masalah yang mungkin terjadi
                $errorMessage = 'Tidak ada data yang berhasil diimport. ';
                if ($totalRows == 0) {
                    $errorMessage .= 'File mungkin kosong atau tidak memiliki data yang valid.';
                } elseif ($errorCount > 0) {
                    if ($duplicateCount > 0) {
                        $errorMessage .= "$duplicateCount data sudah ada dalam sistem. ";
                    }
                    $errorMessage .= 'Periksa format data Anda, terutama tipe data untuk kolom thn, bln, dan kontak harus berupa teks.';
                } else {
                    $errorMessage .= 'Periksa format file dan struktur data Anda.';
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            DB::commit();
            $message = 'Data Perangkat Desa berhasil diimport (' . $successCount . ' data)';
            if ($duplicateCount > 0) {
                $message .= '. ' . $duplicateCount . ' data duplikat dilewati.';
            }
            
            Log::info('Import berhasil diselesaikan dengan ' . $successCount . ' data');
            return redirect()->route('linmas.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat import data linmas: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
