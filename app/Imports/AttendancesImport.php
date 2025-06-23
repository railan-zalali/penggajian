<?php
// app/Imports/AttendancesImport.php

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

            // =====================================================================
            // VALIDASI PENCEGAHAN DUPLIKASI DATA KEHADIRAN
            // =====================================================================

            // 1. Cek duplikasi data yang persis sama (linmas_id, waktu, status)
            $exactDuplicate = Attendances::where('linmas_id', $linmas->id)
                ->where('waktu', $waktu)
                ->where('status', $row['status'])
                ->first();

            if ($exactDuplicate) {
                $this->errors[] = "Data kehadiran untuk {$linmas->nama} pada {$waktu->format('d-m-Y H:i:s')} dengan status {$row['status']} sudah ada";
                return null;
            }

            // 2. Cek duplikasi status pada hari yang sama
            $sameDayStatusDuplicate = Attendances::where('linmas_id', $linmas->id)
                ->whereDate('waktu', $waktu->toDateString())
                ->where('status', $row['status'])
                ->first();

            if ($sameDayStatusDuplicate) {
                $this->errors[] = "Status {$row['status']} untuk {$linmas->nama} pada tanggal {$waktu->toDateString()} sudah ada";
                return null;
            }

            // 3. Validasi urutan masuk/keluar
            if ($row['status'] == 'C/Masuk') {
                // Cek apakah sudah ada status keluar pada waktu yang lebih awal
                $existingExit = Attendances::where('linmas_id', $linmas->id)
                    ->whereDate('waktu', $waktu->toDateString())
                    ->where('status', 'C/Keluar')
                    ->where('waktu', '<', $waktu)
                    ->first();

                if ($existingExit) {
                    $this->errors[] = "Status Masuk untuk {$linmas->nama} tidak dapat dicatat setelah status Keluar pada tanggal yang sama";
                    return null;
                }
            } elseif ($row['status'] == 'C/Keluar') {
                // Cek apakah sudah ada status masuk pada waktu yang lebih akhir
                $existingEntry = Attendances::where('linmas_id', $linmas->id)
                    ->whereDate('waktu', $waktu->toDateString())
                    ->where('status', 'C/Masuk')
                    ->where('waktu', '>', $waktu)
                    ->first();

                if ($existingEntry) {
                    $this->errors[] = "Status Keluar untuk {$linmas->nama} tidak dapat dicatat sebelum status Masuk pada tanggal yang sama";
                    return null;
                }
            }

            // 4. Validasi interval waktu yang wajar
            // Jika ada kehadiran pada hari yang sama dengan status berbeda, periksa intervalnya
            if ($row['status'] == 'C/Keluar') {
                $entryTime = Attendances::where('linmas_id', $linmas->id)
                    ->whereDate('waktu', $waktu->toDateString())
                    ->where('status', 'C/Masuk')
                    ->first();

                if ($entryTime) {
                    $entryCarbon = Carbon::parse($entryTime->waktu);
                    $diffInMinutes = $waktu->diffInMinutes($entryCarbon);

                    // Jika interval < 5 menit atau > 24 jam, mungkin ada kesalahan
                    if ($diffInMinutes < 5) {
                        $this->errors[] = "Interval waktu antara Masuk dan Keluar untuk {$linmas->nama} terlalu pendek ({$diffInMinutes} menit)";
                        return null;
                    } elseif ($diffInMinutes > 1440) { // 24 jam
                        $this->errors[] = "Interval waktu antara Masuk dan Keluar untuk {$linmas->nama} terlalu panjang ({$diffInMinutes} menit)";
                        // Tetap lanjutkan, hanya peringatan
                    }
                }
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
