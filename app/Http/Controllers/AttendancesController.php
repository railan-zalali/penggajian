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
            
            $hasClosedPeriod = false;
            $closedPeriods = [];
            
            foreach ($data as $index => $row) {
                if ($index === 0) continue; // Skip header row
                
                if (isset($row[3]) && !empty($row[3])) { // Kolom waktu
                    try {
                        $date = \Carbon\Carbon::parse($row[3]);
                        $year = $date->year;
                        $month = $date->month;
                        
                        if (\App\Models\MonthClosing::isMonthClosed($year, $month)) {
                            $hasClosedPeriod = true;
                            $period = $date->format('F Y');
                            if (!in_array($period, $closedPeriods)) {
                                $closedPeriods[] = $period;
                            }
                        }
                    } catch (\Exception $e) {
                        // Skip invalid date format
                        continue;
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
