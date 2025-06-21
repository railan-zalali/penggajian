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
            $linmasId = $attendance->linmas->id;

            if (!isset($salaryData[$linmasId])) {
                $salaryData[$linmasId] = [
                    'nama' => $attendance->linmas->nama,
                    'total_kehadiran' => 0,
                    'total_gaji' => 0,
                ];
            }

            // Hitung hanya kehadiran yang valid (misalnya, status "Hadir")
            if ($attendance->status == 'Hadir') {
                $salaryData[$linmasId]['total_kehadiran']++;
                $salaryData[$linmasId]['total_gaji'] += $dailyRate;
            }
        }

        // Kirimkan data ke view
        return view('salary.index', compact('salaryData'));
    }
}
