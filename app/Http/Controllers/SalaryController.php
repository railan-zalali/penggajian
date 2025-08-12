<?php

namespace App\Http\Controllers;

use App\Models\attendances;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function index()
    {
        // Ambil semua data kehadiran, termasuk nama Linmas
        $attendances = attendances::with('linmas')->get();

        // Variabel untuk menyimpan total gaji setiap Linmas
        $salaryData = [];

        // Anggap ada tarif harian yang telah ditentukan
        $dailyRate = 75000;

        // Perhitungan gaji berdasarkan kehadiran
        foreach ($attendances as $attendance) {
            // Pastikan linmas ada
            if (!$attendance->linmas) {
                continue; // Lewati jika tidak ada data linmas
            }
            
            $linmasId = $attendance->linmas->id;

            if (!isset($salaryData[$linmasId])) {
                $salaryData[$linmasId] = [
                    'nama' => $attendance->linmas->nama,
                    'nik' => $attendance->linmas->nik,
                    'total_kehadiran' => 0,
                    'total_gaji' => 0,
                    'tanggal_hadir' => [], // Tambahkan array untuk melacak tanggal kehadiran
                ];
            }

            // Hitung kehadiran yang valid (status "Hadir", "Masuk", atau "Keluar")
            if (in_array($attendance->status, ['Hadir', 'Masuk', 'Keluar'])) {
                // Jika status Masuk atau Keluar, pastikan tidak menghitung dua kali untuk hari yang sama
                $attendanceDate = \Carbon\Carbon::parse($attendance->waktu)->format('Y-m-d');
                
                // Jika belum ada data untuk tanggal ini, tambahkan
                if (!isset($salaryData[$linmasId]['tanggal_hadir'][$attendanceDate])) {
                    $salaryData[$linmasId]['tanggal_hadir'][$attendanceDate] = true;
                    $salaryData[$linmasId]['total_kehadiran']++;
                    $salaryData[$linmasId]['total_gaji'] += $dailyRate;
                }
            }
        }

        // Kirimkan data ke view
        return view('salary.index', compact('salaryData'));
    }
}
