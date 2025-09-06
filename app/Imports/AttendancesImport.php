<?php
// app/Imports/AttendancesImport.php

namespace App\Imports;

use App\Models\Attendances;
use App\Models\Linmas;
use App\Models\MonthClosing;
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
use Illuminate\Support\Facades\DB;
use Throwable;

class AttendancesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    /**
     * @var array
     */
    protected $errors = [];
    
    /**
     * @var array
     */
    protected $processedDates = [];
    
    /**
     * @var int
     */
    protected $successCount = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // Skip baris header atau kosong
            if (!isset($row['nama']) || !isset($row['tanggal']) || !isset($row['jam']) || !isset($row['status'])) {
                return null;
            }
            
            // Validasi status harus salah satu dari nilai yang valid
            $validStatus = ['C/Masuk', 'C/Keluar', 'Lembur Masuk', 'Lembur Keluar'];
            if (!in_array($row['status'], $validStatus)) {
                $this->errors[] = "Status '{$row['status']}' tidak valid. Gunakan salah satu dari: " . implode(', ', $validStatus);
                return null;
            }

            // Coba cari berdasarkan NIK terlebih dahulu (lebih akurat)
            $linmas = null;
            if (isset($row['nik']) && !empty($row['nik'])) {
                $linmas = Linmas::where('nik', $row['nik'])->first();
            }
            
            // Jika tidak ditemukan dengan NIK, cari berdasarkan nama (case insensitive)
            if (!$linmas) {
                $linmas = Linmas::where(DB::raw('LOWER(nama)'), strtolower(trim($row['nama'])))->first();
                
                if (!$linmas) {
                    $errorMsg = "Perangkat Desa tidak ditemukan";
                    if (isset($row['nik']) && !empty($row['nik'])) {
                        $errorMsg .= " dengan NIK '{$row['nik']}'";
                    }
                    if (isset($row['nama']) && !empty($row['nama'])) {
                        $errorMsg .= (isset($row['nik']) && !empty($row['nik'])) ? " dan nama '{$row['nama']}'" : " dengan nama '{$row['nama']}'";
                    }
                    $this->errors[] = $errorMsg;
                    return null;
                }
            }
            
            // Gabungkan tanggal dan jam menjadi satu string datetime
            $tanggalJam = $row['tanggal'] . ' ' . $row['jam'];
            
            // Format tanggal yang didukung
            $formats = [
                'Y-m-d H:i',     // Format utama: 2023-01-01 08:00
                'Y-m-d H:i:s',   // Format dengan detik
                'd-m-Y H:i',     // Format alternatif
                'Y/m/d H:i',
                'd/m/Y H:i'
            ];
            $waktu = null;

            // Parsing tanggal dengan validasi
            try {
                foreach ($formats as $format) {
                    try {
                        $parsed = Carbon::createFromFormat($format, $tanggalJam);
                        $waktu = $parsed;
                        break;
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                
                if (!$waktu) {
                    throw new \Exception("Format tanggal dan jam tidak valid");
                }
            } catch (\Exception $e) {
                $this->errors[] = "Format tanggal '{$row['tanggal']}' atau jam '{$row['jam']}' tidak valid. Gunakan format tanggal 'YYYY-MM-DD' dan jam 'HH:MM'";
                return null;
            }
            
            // Cek apakah bulan sudah ditutup
            if (MonthClosing::isMonthClosed($waktu->year, $waktu->month)) {
                $this->errors[] = "Periode {$waktu->format('F Y')} sudah ditutup, tidak bisa menambah data kehadiran";
                return null;
            }

            // Buat kunci unik untuk mencegah duplikasi dalam satu batch impor
            $dateKey = $linmas->id . '_' . $waktu->toDateString() . '_' . $row['status'];
            if (isset($this->processedDates[$dateKey])) {
                $this->errors[] = "Duplikasi data kehadiran untuk {$linmas->nama} pada {$waktu->format('d-m-Y')} dengan status {$row['status']} dalam file impor";
                return null;
            }
            $this->processedDates[$dateKey] = true;
            
            // =====================================================================
            // VALIDASI PENCEGAHAN DUPLIKASI DATA KEHADIRAN
            // =====================================================================

            // 1. Cek duplikasi data yang persis sama (linmas_id, waktu, status)
            // Gunakan exists untuk performa lebih baik
            $exactDuplicate = Attendances::where('linmas_id', $linmas->id)
                ->where('waktu', $waktu)
                ->where('status', $row['status'])
                ->exists();

            if ($exactDuplicate) {
                $this->errors[] = "Data kehadiran untuk {$linmas->nama} pada {$waktu->format('d-m-Y H:i')} dengan status {$row['status']} sudah ada";
                return null;
            }

            // 2. Cek duplikasi status pada hari yang sama
            // Hanya cek duplikasi untuk status yang sama pada hari yang sama
            $sameDayStatusDuplicate = Attendances::where('linmas_id', $linmas->id)
                ->whereDate('waktu', $waktu->toDateString())
                ->where('status', $row['status'])
                ->exists();

            if ($sameDayStatusDuplicate) {
                $this->errors[] = "Status {$row['status']} untuk {$linmas->nama} pada tanggal {$waktu->format('d-m-Y')} sudah ada";
                return null;
            }

            // 3. Validasi urutan masuk/keluar
            if ($row['status'] == 'C/Masuk') {
                // Cek apakah sudah ada status keluar pada waktu yang lebih awal di hari yang sama
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

            $this->successCount++;
            return new Attendances([
                'linmas_id' => $linmas->id,
                'waktu' => $waktu,
                'status' => $row['status'],
                // status_baru dan pengecualian dihapus sesuai kebutuhan
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
            'tanggal' => 'required',
            'jam' => 'required',
            'status' => 'required|in:C/Masuk,C/Keluar,Lembur Masuk,Lembur Keluar',
            'nik' => 'nullable|string',
            // status_baru column removed as per requirements
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
    
    /**
     * @return int
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }
    
    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 100; // Proses 100 baris sekaligus untuk optimasi
    }
    
    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 500; // Baca 500 baris sekaligus untuk optimasi memori
    }
}
