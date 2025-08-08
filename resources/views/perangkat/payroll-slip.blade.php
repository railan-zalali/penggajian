<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $payroll->linmas->nama }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
        }
        .slip-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
        }
        .slip-header {
            text-align: center;
            padding: 20px;
            border-bottom: 2px solid #333;
        }
        .slip-header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .slip-header h2 {
            margin: 5px 0 0;
            font-size: 16px;
        }
        .slip-header p {
            margin: 5px 0 0;
            font-size: 12px;
        }
        .slip-body {
            padding: 20px;
        }
        .employee-info {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        .slip-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .slip-table th, .slip-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .slip-table th {
            background-color: #f2f2f2;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .slip-footer {
            display: flex;
            justify-content: space-between;
            padding: 20px;
            border-top: 1px solid #ddd;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .print-button {
            text-align: center;
            margin-top: 20px;
        }
        .print-button button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .print-button button:hover {
            background-color: #45a049;
        }
        @media print {
            .print-button {
                display: none;
            }
            body {
                padding: 0;
            }
            .slip-container {
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="slip-container">
        <div class="slip-header">
            <h1>Slip Gaji Perangkat Desa</h1>
            <h2>Desa {{ config('app.village_name', 'Desa') }}</h2>
            <p>Periode: {{ $payroll->payroll_date->format('F Y') }}</p>
        </div>
        
        <div class="slip-body">
            <!-- Informasi Perangkat -->
            <div class="employee-info">
                <div class="info-row">
                    <div class="info-label">Nama:</div>
                    <div class="info-value">{{ $payroll->linmas->nama }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">NIK:</div>
                    <div class="info-value">{{ $payroll->linmas->nik }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Jabatan:</div>
                    <div class="info-value">{{ $payroll->linmas->jabatan->nama }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Slip:</div>
                    <div class="info-value">{{ now()->format('d F Y') }}</div>
                </div>
            </div>
            
            <!-- Informasi Kehadiran -->
            <h3>Informasi Kehadiran</h3>
            <table class="slip-table">
                <tr>
                    <th>Hari Kerja</th>
                    <th>Kehadiran</th>
                    <th>Persentase</th>
                </tr>
                <tr>
                    <td>{{ $payroll->working_days }} hari</td>
                    <td>{{ $payroll->attendance_days }} hari</td>
                    <td>{{ $payroll->working_days > 0 ? round(($payroll->attendance_days / $payroll->working_days) * 100, 1) : 0 }}%</td>
                </tr>
            </table>
            
            <!-- Rincian Pendapatan -->
            <h3>Rincian Pendapatan</h3>
            <table class="slip-table">
                <tr>
                    <th>Komponen</th>
                    <th>Jumlah</th>
                </tr>
                <tr>
                    <td>Gaji Pokok</td>
                    <td>Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</td>
                </tr>
                
                @if($details && $details->where('component_type', 'allowance')->count() > 0)
                    @foreach($details->where('component_type', 'allowance') as $allowance)
                        <tr>
                            <td>{{ $allowance->component_name }}</td>
                            <td>Rp {{ number_format($allowance->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endif
                
                <tr class="total-row">
                    <td>Total Pendapatan</td>
                    <td>Rp {{ number_format($payroll->base_salary + ($details ? $details->where('component_type', 'allowance')->sum('amount') : 0), 0, ',', '.') }}</td>
                </tr>
            </table>
            
            <!-- Rincian Potongan -->
            <h3>Rincian Potongan</h3>
            <table class="slip-table">
                <tr>
                    <th>Komponen</th>
                    <th>Jumlah</th>
                </tr>
                
                @if($details && $details->where('component_type', 'deduction')->count() > 0)
                    @foreach($details->where('component_type', 'deduction') as $deduction)
                        <tr>
                            <td>{{ $deduction->component_name }}</td>
                            <td>Rp {{ number_format($deduction->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="2" style="text-align: center;">Tidak ada potongan</td>
                    </tr>
                @endif
                
                <tr class="total-row">
                    <td>Total Potongan</td>
                    <td>Rp {{ number_format($details ? $details->where('component_type', 'deduction')->sum('amount') : 0, 0, ',', '.') }}</td>
                </tr>
            </table>
            
            <!-- Total Gaji Bersih -->
            <h3>Total Gaji Bersih</h3>
            <table class="slip-table">
                <tr class="total-row">
                    <td>Total Gaji Bersih</td>
                    <td>Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
        
        <div class="slip-footer">
            <div class="signature-box">
                <p>Diterima oleh,</p>
                <div class="signature-line">{{ $payroll->linmas->nama }}</div>
            </div>
            
            <div class="signature-box">
                <p>{{ config('app.village_name', 'Desa') }}, {{ now()->format('d F Y') }}</p>
                <p>Kepala Desa</p>
                <div class="signature-line">{{ config('app.village_head', 'Kepala Desa') }}</div>
            </div>
        </div>
    </div>
    
    <div class="print-button">
        <button onclick="window.print()">Cetak Slip Gaji</button>
    </div>
</body>
</html>