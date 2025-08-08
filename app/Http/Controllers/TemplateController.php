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

        // Set headers sesuai dengan format file import yang diinginkan
        $headers = ['nik', 'nip', 'nama', 'tempat_tanggal_lahir', 'pangkat', 'jabatan', 'masa_kerja', 'pendidikan_terakhir', 'kontak'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->getFont()->setBold(true);
            $column++;
        }

        // Set example data
        $sheet->setCellValue('A2', '1234567890123456');
        $sheet->setCellValue('B2', '12345678901234'); // NIP (opsional)
        $sheet->setCellValue('C2', 'John Doe');
        $sheet->setCellValue('D2', 'Jakarta, 1985-01-01'); // Format: Tempat, YYYY-MM-DD
        $sheet->setCellValue('E2', 'Penata Muda');
        $sheet->setCellValue('F2', 'Perangkat Desa');
        $sheet->setCellValue('G2', '5 tahun');
        $sheet->setCellValue('H2', 'S1');
        $sheet->setCellValue('I2', '081234567890');

        // Auto size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Tambahkan instruksi penggunaan template
        $sheet->insertNewRowBefore(1, 2);
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'PETUNJUK: Format tempat_tanggal_lahir harus "Tempat, YYYY-MM-DD" (contoh: Jakarta, 1985-01-01). Kolom NIP bersifat opsional.');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(12);
        $sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A1')->getFill()->getStartColor()->setARGB('FFFFFF00'); // Yellow background

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

        // Set headers sesuai dengan format yang diinginkan
        $headers = ['No.', 'NIK', 'Nama', 'Waktu', 'Status'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->getFont()->setBold(true);
            $column++;
        }

        // Set example data
        $sheet->setCellValue('A2', '1');
        $sheet->setCellValue('B2', '3205350412700002');
        $sheet->setCellValue('C2', 'THOMAS,S.IP');
        $sheet->setCellValue('D2', '2025-08-01 07:30:00'); // Format YYYY-MM-DD HH:MM:SS
        $sheet->setCellValue('E2', 'C/Masuk');

        $sheet->setCellValue('A3', '2');
        $sheet->setCellValue('B3', '3205350412700002');
        $sheet->setCellValue('C3', 'THOMAS,S.IP');
        $sheet->setCellValue('D3', '2025-08-01 16:30:00');
        $sheet->setCellValue('E3', 'C/Keluar');

        $sheet->setCellValue('A4', '3');
        $sheet->setCellValue('B4', '3205350507730007');
        $sheet->setCellValue('C4', 'BEBEN SOPANDI');
        $sheet->setCellValue('D4', '2025-08-01 07:30:00');
        $sheet->setCellValue('E4', 'C/Masuk');

        $sheet->setCellValue('A5', '4');
        $sheet->setCellValue('B5', '3205350507730007');
        $sheet->setCellValue('C5', 'BEBEN SOPANDI');
        $sheet->setCellValue('D5', '2025-08-01 16:30:00');
        $sheet->setCellValue('E5', 'C/Keluar');

        $sheet->setCellValue('A6', '5');
        $sheet->setCellValue('B6', '3205353001910001');
        $sheet->setCellValue('C6', 'TATAN TASWARA');
        $sheet->setCellValue('D6', '2025-08-01 07:30:00');
        $sheet->setCellValue('E6', 'C/Masuk');

        $sheet->setCellValue('A7', '6');
        $sheet->setCellValue('B7', '3205353001910001');
        $sheet->setCellValue('C7', 'TATAN TASWARA');
        $sheet->setCellValue('D7', '2025-08-01 16:30:00');
        $sheet->setCellValue('E7', 'C/Keluar');

        // Auto size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Tambahkan instruksi penggunaan template
        $sheet->insertNewRowBefore(1, 2);
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', 'PETUNJUK PENGGUNAAN TEMPLATE KEHADIRAN:\n1. Format waktu harus YYYY-MM-DD HH:MM:SS (contoh: 2025-08-01 07:30:00)\n2. Status yang valid: C/Masuk, C/Keluar, Lembur Masuk, Lembur Keluar\n3. Pastikan NIK dan Nama sesuai dengan data yang terdaftar di sistem');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(11);
        $sheet->getStyle('A1')->getAlignment()->setWrapText(true);
        $sheet->getRowDimension(1)->setRowHeight(60); // Tinggi baris untuk petunjuk
        $sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A1')->getFill()->getStartColor()->setARGB('FFFFFF00'); // Yellow background

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
