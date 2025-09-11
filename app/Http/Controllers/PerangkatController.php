<?php

namespace App\Http\Controllers;

use App\Models\Linmas;
use App\Models\PositionSalaryRate;
use App\Imports\LinmasImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class PerangkatController extends Controller
{
    /**
     * Display a listing of the perangkat desa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Linmas::query();

        // Pencarian
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('posisi', 'like', "%{$search}%");
            });
        }

        // Filter pendidikan
        if ($request->has('pendidikan') && $request->pendidikan != 'all') {
            $query->where('pendidikan', $request->pendidikan);
        }

        // Filter posisi
        if ($request->has('posisi') && $request->posisi != 'all') {
            $query->where('posisi', $request->posisi);
        }

        // Filter status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Pagination
        $perangkat = $query->orderBy('nama')->paginate(15);

        // Ambil opsi untuk filter
        $pendidikanOptions = Linmas::select('pendidikan')
            ->distinct()->pluck('pendidikan');
        
        $posisiOptions = Linmas::select('posisi')
            ->distinct()->pluck('posisi');

        // Ambil data position salary rates untuk referensi
        $positionRates = PositionSalaryRate::getActiveRates();

        return view('perangkat.index', compact(
            'perangkat', 
            'pendidikanOptions', 
            'posisiOptions',
            'positionRates'
        ));
    }

    /**
     * Show the form for creating a new perangkat desa.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Ambil data position salary rates untuk dropdown posisi
        $positionRates = PositionSalaryRate::getActiveRates();
        $workingDays = 22; // Default working days
        
        return view('perangkat.create', compact('positionRates', 'workingDays'));
    }

    /**
     * Store a newly created perangkat desa in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $this->validatePerangkat($request);

        try {
            DB::beginTransaction();
            
            // Jika posisi dipilih dari position_salary_rates, ambil gaji_pokok dari monthly_rate
            if ($request->filled('posisi_id')) {
                $positionRate = PositionSalaryRate::findOrFail($request->posisi_id);
                $validatedData['posisi'] = $positionRate->position;
                $validatedData['gaji_pokok'] = $positionRate->monthly_rate;
                $validatedData['working_days'] = $positionRate->working_days;
            }
            
            // Set password jika can_login dicentang
            if ($request->has('can_login')) {
                $validatedData['can_login'] = true;
                $validatedData['email'] = $request->email;
                $validatedData['password'] = Hash::make($request->password);
                $validatedData['password_plain'] = $request->password;
            }
            
            Linmas::create($validatedData);
            
            DB::commit();
            return redirect()->route('perangkat.index')
                ->with('success', 'Data Perangkat Desa berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Perangkat creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menambahkan data Perangkat Desa: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified perangkat desa.
     *
     * @param  \App\Models\Linmas  $perangkat
     * @return \Illuminate\Http\Response
     */
    public function edit(Linmas $perangkat)
    {
        try {
            // Ambil data position salary rates untuk dropdown posisi
            $positionRates = PositionSalaryRate::getActiveRates();
            
            // Cari posisi_id berdasarkan posisi yang ada
            $currentPositionRate = $positionRates->where('position', $perangkat->posisi)->first();
            $posisi_id = $currentPositionRate ? $currentPositionRate->id : null;
            
            return view('perangkat.edit', compact('perangkat', 'positionRates', 'posisi_id'));
        } catch (\Exception $e) {
            Log::error('Error accessing perangkat edit form: ' . $e->getMessage(), [
                'perangkat_id' => $perangkat->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('perangkat.index')
                ->with('error', 'Gagal mengakses form edit: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified perangkat desa in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Linmas  $perangkat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Linmas $perangkat)
    {
        try {
            // Validasi data
            $validatedData = $this->validatePerangkat($request, $perangkat->id);
            
            // Simpan data lama untuk log
            $oldData = $perangkat->toArray();
            
            DB::beginTransaction();
            
            // Jika posisi dipilih dari position_salary_rates, ambil gaji_pokok dari monthly_rate
            if ($request->filled('posisi_id')) {
                $positionRate = PositionSalaryRate::findOrFail($request->posisi_id);
                $validatedData['posisi'] = $positionRate->position;
                $validatedData['gaji_pokok'] = $positionRate->monthly_rate;
                $validatedData['working_days'] = $positionRate->working_days;
            }
            
            // Update data login jika diperlukan
            if ($request->has('can_login')) {
                $validatedData['can_login'] = true;
                $validatedData['email'] = $request->email;
                
                // Update password hanya jika diisi
                if ($request->filled('password')) {
                    $validatedData['password'] = Hash::make($request->password);
                    $validatedData['password_plain'] = $request->password;
                }
            } else {
                $validatedData['can_login'] = false;
                $validatedData['email'] = null;
                $validatedData['password'] = null;
                $validatedData['password_plain'] = null;
            }
            
            // Update data
            $perangkat->update($validatedData);
            
            DB::commit();
            
            // Log perubahan data
            Log::info('Perangkat updated successfully', [
                'id' => $perangkat->id,
                'nik' => $perangkat->nik,
                'old_data' => $oldData,
                'new_data' => $perangkat->toArray()
            ]);
            
            return redirect()->route('perangkat.index')
                ->with('success', 'Data Perangkat Desa berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Perangkat update failed: ' . $e->getMessage(), [
                'nik' => $request->nik,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Gagal mengupdate data Perangkat Desa: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified perangkat desa from storage.
     *
     * @param  \App\Models\Linmas  $perangkat
     * @return \Illuminate\Http\Response
     */
    public function destroy(Linmas $perangkat)
    {
        try {
            // Periksa apakah perangkat memiliki data kehadiran atau penggajian
            $attendanceCount = $perangkat->attendances()->count();
            $payrollCount = $perangkat->payrolls()->count();
            $userCount = $perangkat->user()->count();
            
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
                return redirect()->route('perangkat.index')
                    ->with('error', 'Tidak dapat menghapus data Perangkat Desa karena masih memiliki ' . implode(', ', $errorMessages));
            }

            $perangkat->delete();
            return redirect()->route('perangkat.index')
                ->with('success', 'Data Perangkat Desa berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Perangkat deletion failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menghapus data Perangkat Desa: ' . $e->getMessage());
        }
    }

    /**
     * Import perangkat desa from Excel file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
            $successCount = $import->getSuccessCount(); 
            $duplicateCount = $import->getDuplicateCount(); 
            
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
            return redirect()->route('perangkat.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat import data perangkat: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Validate perangkat desa data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int|null  $id
     * @return array
     */
    private function validatePerangkat(Request $request, $id = null)
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'nik' => 'required|digits:16|unique:linmas,nik' . ($id ? ",$id" : ''),
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string|max:255',
            'pendidikan' => 'required|string|max:255',
            'pekerjaan' => 'required|string|max:255',
            'posisi' => 'nullable|string|max:255',
            'posisi_id' => 'nullable|exists:position_salary_rates,id',
            'tanggal_bergabung' => 'nullable|date',
            'status' => 'nullable|string|in:aktif,tidak aktif',
            'gaji_pokok' => 'nullable|numeric',
            'working_days' => 'nullable|integer|min:1|max:31',
        ];

        // Tambahkan validasi untuk login jika diperlukan
        if ($request->has('can_login')) {
            $rules['email'] = 'required|email|max:255|unique:linmas,email' . ($id ? ",$id" : '');
            
            // Password hanya required saat membuat baru atau jika diisi saat update
            if (!$id || $request->filled('password')) {
                $rules['password'] = 'required|min:8|confirmed';
            }
        }

        return $request->validate($rules);
    }
}