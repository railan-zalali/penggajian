<?php

namespace App\Imports;

use App\Models\Attendances;
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

class AttendancesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
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
            if (!isset($row['nama']) || !isset($row['waktu']) || !isset($row['status'])) {
                return null;
            }

            // Cari anggota linmas berdasarkan nama
            $linmas = Linmas::where('nama', $row['nama'])->first();
            if (!$linmas) {
                $this->errors[] = "Perangkat Desa dengan nama '{$row['nama']}' tidak ditemukan";
                return null;
            }

            // Parsing tanggal dengan validasi
            try {
                $waktu = Carbon::parse($row['waktu']);
            } catch (\Exception $e) {
                $this->errors[] = "Format tanggal '{$row['waktu']}' tidak valid. Gunakan format 'YYYY-MM-DD HH:MM:SS'";
                return null;
            }

            return new Attendances([
                'linmas_id' => $linmas->id,
                'waktu' => $waktu,
                'status' => $row['status'],
                'status_baru' => $row['status_baru'] ?? null,
                'pengecualian' => $row['pengecualian'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat import data kehadiran: ' . $e->getMessage());
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
            'waktu' => 'required',
            'status' => 'required|string',
            'status_baru' => 'nullable|string',
            'pengecualian' => 'nullable|string',
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
