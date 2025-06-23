<?php

namespace App\Imports;

use App\Models\Linmas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class LinmasImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // Skip baris header atau kosong
            if (!isset($row['nama']) || !isset($row['nik'])) {
                return null;
            }

            // Validasi NIK harus berupa angka 16 digit
            if (!is_numeric($row['nik']) || strlen((string)$row['nik']) != 16) {
                $this->errors[] = "NIK '{$row['nik']}' harus berupa 16 digit angka";
                return null;
            }

            // Cek NIK unik
            $existingNik = Linmas::where('nik', $row['nik'])->first();
            if ($existingNik) {
                $this->errors[] = "NIK '{$row['nik']}' sudah terdaftar untuk '{$existingNik->nama}'";
                return null;
            }

            // Parsing tanggal lahir
            try {
                $tanggalLahir = isset($row['tanggal_lahir']) ?
                    (is_numeric($row['tanggal_lahir']) ?
                        \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_lahir']) :
                        Carbon::parse($row['tanggal_lahir'])) :
                    null;
            } catch (\Exception $e) {
                $this->errors[] = "Format tanggal lahir tidak valid untuk NIK '{$row['nik']}'";
                return null;
            }

            return new Linmas([
                'nama' => $row['nama'],
                'nik' => $row['nik'],
                'tempat_lahir' => $row['tempat_lahir'] ?? '',
                'tanggal_lahir' => $tanggalLahir,
                'alamat' => $row['alamat'] ?? '',
                'pendidikan' => $row['pendidikan'] ?? '',
                'pekerjaan' => $row['pekerjaan'] ?? '',
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat import data linmas: ' . $e->getMessage());
            $this->errors[] = "Error pada baris: " . json_encode($row) . " - " . $e->getMessage();
            return null;
        }
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string',
            'nik' => 'required|string',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable',
            'alamat' => 'nullable|string',
            'pendidikan' => 'nullable|string',
            'pekerjaan' => 'nullable|string',
        ];
    }

    /**
     * @param \Throwable $e
     */
    public function onError(Throwable $e)
    {
        Log::error('Error pada import: ' . $e->getMessage());
        $this->errors[] = $e->getMessage();
    }

    /**
     * @param \Maatwebsite\Excel\Validators\Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
        }
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
