<?php

namespace App\Http\Controllers;

use App\Imports\AttendancesImport;
use App\Models\Attendance;
use App\Models\attendances;
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
            $import = new AttendancesImport();
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
            $attendance->delete();
            return redirect()->route('attendances.index')->with('success', 'Data kehadiran berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Attendance deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus data kehadiran: ' . $e->getMessage());
        }
    }
}
