<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class PayrollStatusExport implements WithMultipleSheets
{
    protected $summary;
    protected $detailData;

    public function __construct(array $summary, array $detailData)
    {
        $this->summary = $summary;
        $this->detailData = $detailData;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        
        // Ringkasan status
        $sheets[] = new class($this->summary) implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize {
            protected $summary;
            
            public function __construct($summary)
            {
                $this->summary = $summary;
            }
            
            public function collection()
            {
                $total = array_sum($this->summary);
                $rows = [];
                
                foreach ($this->summary as $status => $count) {
                    $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                    $rows[] = [
                        'Status' => ucfirst($status),
                        'Jumlah' => $count,
                        'Persentase' => $percentage . '%'
                    ];
                }
                
                // Tambahkan total
                $rows[] = [
                    'Status' => 'Total',
                    'Jumlah' => $total,
                    'Persentase' => '100%'
                ];
                
                return new Collection($rows);
            }
            
            public function headings(): array
            {
                return [
                    'Status',
                    'Jumlah',
                    'Persentase'
                ];
            }
            
            public function title(): string
            {
                return 'Ringkasan Status';
            }
            
            public function styles(Worksheet $sheet)
            {
                return [
                    1 => ['font' => ['bold' => true]],
                    count($this->summary) + 2 => ['font' => ['bold' => true]],
                ];
            }
        };
        
        // Detail per status
        $statusLabels = [
            'draft' => 'Draft',
            'verified' => 'Terverifikasi',
            'calculated' => 'Terhitung',
            'approved' => 'Disetujui',
            'processed' => 'Diproses',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak'
        ];
        
        foreach ($statusLabels as $status => $label) {
            if (count($this->detailData[$status]) > 0) {
                $sheets[] = new class($this->detailData[$status], $status, $label) implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize {
                    protected $data;
                    protected $status;
                    protected $label;
                    
                    public function __construct($data, $status, $label)
                    {
                        $this->data = $data;
                        $this->status = $status;
                        $this->label = $label;
                    }
                    
                    public function collection()
                    {
                        $rows = [];
                        
                        foreach ($this->data as $payroll) {
                            $row = [
                                'Tanggal' => $payroll->payroll_date->format('d/m/Y'),
                                'Perangkat Desa' => $payroll->linmas->name ?? 'Data tidak tersedia',
                                'NIK' => $payroll->linmas->nik ?? '-',
                                'Total Gaji' => $payroll->total_salary,
                            ];
                            
                            // Tambahkan kolom khusus berdasarkan status
                            if (in_array($this->status, ['verified', 'calculated'])) {
                                $row['Diverifikasi Oleh'] = $payroll->verifier->name ?? '-';
                                $row['Tanggal Verifikasi'] = $payroll->verified_at ? $payroll->verified_at->format('d/m/Y H:i') : '-';
                            }
                            
                            if (in_array($this->status, ['approved', 'processed'])) {
                                $row['Disetujui Oleh'] = $payroll->approver->name ?? '-';
                                $row['Tanggal Persetujuan'] = $payroll->approved_at ? $payroll->approved_at->format('d/m/Y H:i') : '-';
                            }
                            
                            if ($this->status === 'completed') {
                                $row['Metode Pembayaran'] = $payroll->payment_method ?? '-';
                                $row['Referensi'] = $payroll->payment_reference ?? '-';
                                $row['Tanggal Pembayaran'] = $payroll->payment_date ? $payroll->payment_date->format('d/m/Y H:i') : '-';
                            }
                            
                            if ($this->status === 'rejected') {
                                $row['Catatan'] = $payroll->status_notes ?? '-';
                            }
                            
                            $rows[] = $row;
                        }
                        
                        return new Collection($rows);
                    }
                    
                    public function headings(): array
                    {
                        $headings = [
                            'Tanggal',
                            'Perangkat Desa',
                            'NIK',
                            'Total Gaji',
                        ];
                        
                        // Tambahkan heading khusus berdasarkan status
                        if (in_array($this->status, ['verified', 'calculated'])) {
                            $headings[] = 'Diverifikasi Oleh';
                            $headings[] = 'Tanggal Verifikasi';
                        }
                        
                        if (in_array($this->status, ['approved', 'processed'])) {
                            $headings[] = 'Disetujui Oleh';
                            $headings[] = 'Tanggal Persetujuan';
                        }
                        
                        if ($this->status === 'completed') {
                            $headings[] = 'Metode Pembayaran';
                            $headings[] = 'Referensi';
                            $headings[] = 'Tanggal Pembayaran';
                        }
                        
                        if ($this->status === 'rejected') {
                            $headings[] = 'Catatan';
                        }
                        
                        return $headings;
                    }
                    
                    public function title(): string
                    {
                        return $this->label;
                    }
                    
                    public function styles(Worksheet $sheet)
                    {
                        return [
                            1 => ['font' => ['bold' => true]],
                        ];
                    }
                };
            }
        }
        
        return $sheets;
    }
}