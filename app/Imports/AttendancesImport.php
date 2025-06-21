<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\attendances;
use App\Models\Linmas;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;

class AttendancesImport implements ToModel
{
    public function model(array $row)
    {
        // Cari anggota linmas berdasarkan nama, lalu buat data kehadiran
        // $linmas = Linmas::where('nama', $row[2])->first();

        // return new attendances([
        //     'linmas_id' => $linmas->id,
        //     'waktu' => Carbon::parse($row[3]),
        //     'status' => $row[4], // C/Masuk atau C/Keluar
        //     'status_baru' => $row[5], // Lembur Masuk, Lembur Keluar
        //     'pengecualian' => $row[6],
        // ]);
        // Skip header row jika ada
        if ($row[0] === 'No.' || $row[0] === null) {
            return null;
        }

        $linmas = Linmas::where('nama', $row[2])->first();

        if ($linmas) {
            return new attendances([
                'linmas_id' => $linmas->id,
                'waktu' => Carbon::parse($row[3]),
                'status' => $row[4],
                'status_baru' => $row[5],
                'pengecualian' => $row[6],
            ]);
        }

        // Jika anggota linmas tidak ditemukan, Anda bisa memutuskan untuk menambahkannya atau mengabaikan
        return null;
    }
}
