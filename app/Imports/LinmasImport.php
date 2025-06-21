<?php

namespace App\Imports;

use App\Models\Linmas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LinmasImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // dd($row);
        return new Linmas([
            'nama' => $row['nama'],
            'nik' => $row['nik'],
            'tempat_lahir' => $row['tempat_lahir'],
            'tanggal_lahir' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_lahir']),
            'alamat' => $row['alamat'],
            'pendidikan' => $row['pendidikan'],
            'pekerjaan' => $row['pekerjaan'],
        ]);
    }
}
