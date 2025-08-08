<?php

namespace App\Imports;

use App\Models\Linmas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class LinmasImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnError,
    SkipsOnFailure,
    WithBatchInserts,
    WithChunkReading
{
    protected $errors = [];
    protected $rowCount = 0;
    protected $successCount = 0;

    public function prepareForValidation($row, $rowIndex)
    {
        Log::info('Baris ' . ($rowIndex + 2) . ' data mentah: ' . json_encode($row));

        foreach (['thn', 'bln', 'kontak'] as $field) {
            if (isset($row[$field])) {
                if (!is_string($row[$field])) {
                    $row[$field] = (string)$row[$field];
                    Log::info('Baris ' . ($rowIndex + 2) . ' konversi ' . $field . ' ke string: ' . $row[$field]);
                }
            } else {
                $row[$field] = null;
            }
        }

        return $row;
    }

    public function model(array $row)
    {
        try {
            if (!isset($row['nama']) || !isset($row['nik'])) {
                return null;
            }

            $this->rowCount++;

            if (!$this->validateNIK($row['nik'])) {
                return null;
            }

            // Validasi NIP hanya untuk format, tidak untuk keberadaan di database
            if (isset($row['nip']) && !empty($row['nip']) && !$this->validateNIP($row['nip'])) {
                return null;
            }

            // Validasi field yang diperlukan sesuai struktur tabel
            if (!$this->validateRequiredFields($row)) {
                return null;
            }

            $pendidikan = $this->validateAndGetPendidikan($row['pendidikan_terakhir'] ?? null);

            $linmasData = $this->prepareLinmasData($row, $pendidikan);

            if (config('app.debug')) {
                Log::info('Data Linmas yang akan disimpan: ' . json_encode($linmasData));
            }

            $this->successCount++;
            return new Linmas($linmasData);
        } catch (\Exception $e) {
            Log::error('Error saat import data linmas: ' . $e->getMessage());
            $this->errors[] = "Error pada baris: " . json_encode($row) . " - " . $e->getMessage();
            return null;
        }
    }

    private function validateNIK($nik)
    {
        if (!is_numeric($nik) || strlen((string)$nik) != 16) {
            $this->errors[] = "NIK '$nik' harus berupa 16 digit angka";
            return false;
        }

        if (Linmas::where('nik', $nik)->exists()) {
            $this->errors[] = "NIK '$nik' sudah terdaftar";
            return false;
        }

        return true;
    }

    private function validateNIP($nip)
    {
        // NIP tidak digunakan dalam tabel Linmas, jadi kita skip validasi ini
        if (empty($nip)) {
            return true;
        }

        // Hanya validasi format tanpa memeriksa keberadaan di database
        if (!is_numeric($nip) || strlen((string)$nip) != 14) {
            $this->errors[] = "NIP '$nip' harus berupa 14 digit angka";
            return false;
        }

        return true;
    }

    private function validateRequiredFields($row)
    {
        $requiredFields = [
            'tempat_tanggal_lahir' => 'Tempat tanggal lahir',
            'jabatan' => 'Jabatan'
        ];

        foreach ($requiredFields as $field => $label) {
            if (empty($row[$field])) {
                $this->errors[] = "$label tidak boleh kosong";
                return false;
            }
        }

        return true;
    }

    private function validateAndGetPendidikan($pendidikan)
    {
        $pendidikanValid = ['SD', 'SMP', 'SMA', 'SMK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'];
        $pendidikan = strtoupper($pendidikan ?? 'SMA');
        return in_array($pendidikan, $pendidikanValid) ? $pendidikan : 'SMA';
    }

    private function prepareLinmasData($row, $pendidikan)
    {
        // Ekstrak tempat dan tanggal lahir dari format "Tempat, Tanggal"
        $tempatTanggalLahir = trim($row['tempat_tanggal_lahir']);
        $parts = explode(',', $tempatTanggalLahir);
        $tempatLahir = trim($parts[0]);
        $tanggalLahir = isset($parts[1]) ? trim($parts[1]) : date('Y-m-d');

        // Konversi format tanggal jika perlu
        try {
            $tanggalLahir = \Carbon\Carbon::parse($tanggalLahir)->format('Y-m-d');
        } catch (\Exception $e) {
            $tanggalLahir = date('Y-m-d'); // Default ke hari ini jika parsing gagal
            Log::warning("Gagal parsing tanggal lahir: {$parts[1]} - {$e->getMessage()}");
        }

        return [
            'nik' => $row['nik'],
            'nama' => trim($row['nama']),
            'nip' => !empty($row['nip']) ? $row['nip'] : null,
            'tempat_lahir' => $tempatLahir,
            'tanggal_lahir' => $tanggalLahir,
            'alamat' => !empty($row['alamat']) ? trim($row['alamat']) : '-',
            'pendidikan' => $pendidikan,
            'pekerjaan' => !empty($row['pekerjaan']) ? trim($row['pekerjaan']) : trim($row['jabatan']),
            'posisi' => trim($row['jabatan']),
            'pangkat' => !empty($row['pangkat']) ? trim($row['pangkat']) : null,
            'jabatan' => trim($row['jabatan']),
            'masa_kerja' => !empty($row['masa_kerja']) ? trim($row['masa_kerja']) : null,
            'pendidikan_terakhir' => $pendidikan,
            'gol' => !empty($row['gol']) ? trim($row['gol']) : null,
            'tmt' => !empty($row['tmt']) ? trim($row['tmt']) : null,
            'thn' => !empty($row['thn']) ? trim($row['thn']) : null,
            'bln' => !empty($row['bln']) ? trim($row['bln']) : null,
            'kontak' => !empty($row['kontak']) ? trim($row['kontak']) : null,
            'can_login' => false,
        ];
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string',
            'nik' => 'required|numeric|digits:16|unique:linmas,nik',
            'tempat_tanggal_lahir' => 'required|string',
            'jabatan' => 'required|string',
            'pendidikan_terakhir' => 'nullable|string',
            'nip' => 'nullable|string',
            'pangkat' => 'nullable|string',
            'masa_kerja' => 'nullable|string',
            'gol' => 'nullable|string',
            'tmt' => 'nullable|string',
            'thn' => 'nullable|string',
            'bln' => 'nullable|string',
            'kontak' => 'required|string',
        ];
    }

    public function onError(Throwable $e)
    {
        Log::error('Error pada import: ' . $e->getMessage());
        $this->errors[] = $e->getMessage();
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
        }
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
