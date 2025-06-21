<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\Response;

class TemplateController extends Controller
{
    public function downloadLinmasTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['nama', 'nik', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'pendidikan', 'pekerjaan'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->getFont()->setBold(true);
            $column++;
        }

        // Set example data
        $sheet->setCellValue('A2', 'John Doe');
        $sheet->setCellValue('B2', '1234567890123456');
        $sheet->setCellValue('C2', 'Jakarta');
        $sheet->setCellValue('D2', '1990-01-01'); // Format YYYY-MM-DD
        $sheet->setCellValue('E2', 'Jl. Contoh No. 123');
        $sheet->setCellValue('F2', 'S1');
        $sheet->setCellValue('G2', 'Wiraswasta');

        // Auto size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Save to temporary file
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_perangkat_desa.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        // Return response
        return response()->download($temp_file, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function downloadAttendanceTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['No.', 'NIK', 'Nama', 'Waktu', 'Status', 'Status Baru', 'Pengecualian'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->getFont()->setBold(true);
            $column++;
        }

        // Set example data
        $sheet->setCellValue('A2', '1');
        $sheet->setCellValue('B2', '1234567890123456');
        $sheet->setCellValue('C2', 'John Doe');
        $sheet->setCellValue('D2', '2023-06-01 07:30:00'); // Format YYYY-MM-DD HH:MM:SS
        $sheet->setCellValue('E2', 'C/Masuk');
        $sheet->setCellValue('F2', '');
        $sheet->setCellValue('G2', '');

        $sheet->setCellValue('A3', '2');
        $sheet->setCellValue('B3', '1234567890123456');
        $sheet->setCellValue('C3', 'John Doe');
        $sheet->setCellValue('D3', '2023-06-01 16:30:00');
        $sheet->setCellValue('E3', 'C/Keluar');
        $sheet->setCellValue('F3', '');
        $sheet->setCellValue('G3', '');

        $sheet->setCellValue('A4', '3');
        $sheet->setCellValue('B4', '1234567890123456');
        $sheet->setCellValue('C4', 'John Doe');
        $sheet->setCellValue('D4', '2023-06-02 07:30:00');
        $sheet->setCellValue('E4', 'C/Masuk');
        $sheet->setCellValue('F4', 'Lembur Masuk');
        $sheet->setCellValue('G4', '');

        // Auto size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Save to temporary file
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_kehadiran.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        // Return response
        return response()->download($temp_file, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
