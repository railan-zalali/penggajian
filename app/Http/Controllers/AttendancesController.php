<?php

namespace App\Http\Controllers;

use App\Imports\AttendancesImport;
use App\Models\Attendances;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendancesController extends Controller
{
    public function index()
    {
        $attendances = Attendances::with('linmas')->latest()->paginate(15);
        return view('attendance.index', compact('attendances'));
    }

    public function importAttendance(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // Baca file terlebih dahulu untuk validasi periode
            $import = new AttendancesImport();
            
            // Cek apakah ada data yang akan diimpor untuk periode yang sudah ditutup
            $tempFile = $request->file('file')->store('temp');
            $data = Excel::toCollection($import, storage_path('app/' . $tempFile))->first();
            
            // Simpan data kehadiran untuk setiap baris
            foreach ($data as $index => $row) {
                if ($index === 0 || $index === 1) continue; // Skip header row dan baris petunjuk
                
                if (isset($row[3]) && !empty($row[3])) {
                    try {
                        // Simpan data kehadiran
                        $attendance = new Attendances();
                        $attendance->nik = $row[1];
                        $attendance->nama = $row[2];
                        
                        // Validasi format tanggal dan jam (sekarang terpisah)
                        try {
                            $tanggal = $row[3];
                            $jam = $row[4];
                            $attendance->waktu = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $tanggal . ' ' . $jam)->format('Y-m-d H:i:s');
                        } catch (\Exception $e) {
                            throw new \Exception('Format tanggal atau jam tidak valid pada baris ' . ($index + 1) . '. Format yang benar: tanggal (YYYY-MM-DD) dan jam (HH:MM)');
                        }
                        
                        $attendance->status = $row[5];
                        
                        // Cari Linmas berdasarkan NIK
                        $linmas = \App\Models\Linmas::where('nik', $row[1])->first();
                        if ($linmas) {
                            $attendance->linmas_id = $linmas->id;
                        } else {
                            // Log jika Linmas tidak ditemukan
                            \Illuminate\Support\Facades\Log::warning('Linmas dengan NIK ' . $row[1] . ' tidak ditemukan saat import kehadiran');
                        }
                        
                        $attendance->save();
                    } catch (\Exception $e) {
                        throw $e;
                    }
                }
            }
            
            $hasClosedPeriod = false;
            $closedPeriods = [];
            
            foreach ($data as $index => $row) {
                if ($index === 0 || $index === 1) continue; // Skip header row dan baris petunjuk
                
                // Kolom waktu (indeks 3 untuk format: No, NIK, Nama, Waktu, Status)
                // Format data: [0]=No, [1]=NIK, [2]=Nama, [3]=Waktu, [4]=Status
                if (isset($row[3]) && !empty($row[3])) {
                    try {
                        // Validasi format tanggal dan waktu (sekarang terpisah)
                        try {
                            // Kolom tanggal (indeks 3) dan jam (indeks 4)
                            $tanggal = $row[3];
                            $jam = $row[4];
                            $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $tanggal . ' ' . $jam);
                        } catch (\Exception $e) {
                            throw new \Exception('Format tanggal atau jam tidak valid pada baris ' . ($index + 1) . '. Format yang benar: tanggal (YYYY-MM-DD) dan jam (HH:MM)');
                        }
                        
                        $year = $date->year;
                        $month = $date->month;
                        
                        // Validasi periode tutup bulan
                        if (\App\Models\MonthClosing::isMonthClosed($year, $month)) {
                            $hasClosedPeriod = true;
                            $period = $date->format('F Y');
                            if (!in_array($period, $closedPeriods)) {
                                $closedPeriods[] = $period;
                            }
                        }
                        
                        // Validasi NIK
                        if (empty($row[1])) {
                            throw new \Exception('NIK tidak boleh kosong pada baris ' . ($index + 1));
                        }
                        
                        // Validasi Nama
                        if (empty($row[2])) {
                            throw new \Exception('Nama tidak boleh kosong pada baris ' . ($index + 1));
                        }
                        
                        // Validasi Status (sekarang di indeks 5 karena kolom tanggal dan jam terpisah)
                        if (empty($row[5])) {
                            throw new \Exception('Status tidak boleh kosong pada baris ' . ($index + 1));
                        }
                        
                        // Validasi status harus salah satu dari: Masuk, Keluar, Hadir, Lembur, Lembur Masuk, Lembur Keluar, C/Masuk, C/Keluar
                        $validStatus = ['Masuk', 'Keluar', 'Hadir', 'Lembur', 'Lembur Masuk', 'Lembur Keluar', 'C/Masuk', 'C/Keluar'];
                        if (!in_array(trim($row[5]), $validStatus)) {
                            throw new \Exception('Status tidak valid pada baris ' . ($index + 1) . '. Status harus salah satu dari: ' . implode(', ', $validStatus));
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();
                        // Hapus file temporary
                        if (file_exists(storage_path('app/' . $tempFile))) {
                            unlink(storage_path('app/' . $tempFile));
                        }
                        return redirect()->back()->with('error', $e->getMessage());
                    }
                }
            }
            
            // Hapus file temporary
            if (file_exists(storage_path('app/' . $tempFile))) {
                unlink(storage_path('app/' . $tempFile));
            }
            
            if ($hasClosedPeriod) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Tidak dapat mengimpor data untuk periode yang sudah ditutup: ' . implode(', ', $closedPeriods));
            }
            
            // Lanjutkan dengan import jika tidak ada periode yang ditutup
            Excel::import($import, $request->file('file'));

            // Cek jika ada error pada import
            $errors = $import->getErrors();
            if (!empty($errors)) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . implode(', ', $errors));
            }

            DB::commit();
            return redirect()->route('attendances.index')->with('success', 'Data kehadiran berhasil diimpor');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Attendance import failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengimpor data kehadiran: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $attendance = Attendances::findOrFail($id);
            
            // Check if the month is closed
            $attendanceDate = \Carbon\Carbon::parse($attendance->waktu);
            $isClosed = \App\Models\MonthClosing::isMonthClosed($attendanceDate->year, $attendanceDate->month);
            
            if ($isClosed) {
                return redirect()->back()->with('error', 'Data kehadiran tidak dapat dihapus karena periode sudah ditutup.');
            }
            
            $attendance->delete();
            return redirect()->route('attendances.index')->with('success', 'Data kehadiran berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Attendance deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus data kehadiran: ' . $e->getMessage());
        }
    }
}
