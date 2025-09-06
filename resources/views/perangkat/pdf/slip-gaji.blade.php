<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $perangkat->nama }}</title>
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
        }
        .signature-box:first-child {
            text-align: left;
        }
        .signature-box:last-child {
            text-align: right;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        @media print {
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
            <p>Periode: {{ $penggajian->payroll_date->format('F Y') }}</p>
        </div>
        
        <div class="slip-body">
            <!-- Informasi Perangkat -->
            <div class="employee-info">
                <div class="info-row">
                    <div class="info-label">Nama:</div>
                    <div class="info-value">{{ $perangkat->nama }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">NIK:</div>
                    <div class="info-value">{{ $perangkat->nik }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Jabatan:</div>
                    <div class="info-value">{{ $perangkat->jabatan->nama ?? 'Tidak ada jabatan' }}</div>
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
                    <td>{{ $penggajian->working_days ?? 0 }} hari</td>
                    <td>{{ $penggajian->attendance_days ?? 0 }} hari</td>
                    <td>{{ $penggajian->working_days > 0 ? round(($penggajian->attendance_days / $penggajian->working_days) * 100, 1) : 0 }}%</td>
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
                    <td>Rp {{ number_format($penggajian->base_salary ?? 0, 0, ',', '.') }}</td>
                </tr>
                
                @if($tunjangan && count($tunjangan) > 0)
                    @foreach($tunjangan as $item)
                        <tr>
                            <td>{{ $item->component_name }}</td>
                            <td>Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endif
                
                <tr class="total-row">
                    <td>Total Pendapatan</td>
                    <td>Rp {{ number_format(($penggajian->base_salary ?? 0) + ($total_tunjangan ?? 0), 0, ',', '.') }}</td>
                </tr>
            </table>
            
            <!-- Rincian Potongan -->
            <h3>Rincian Potongan</h3>
            <table class="slip-table">
                <tr>
                    <th>Komponen</th>
                    <th>Jumlah</th>
                </tr>
                
                @if($potongan && count($potongan) > 0)
                    @foreach($potongan as $item)
                        <tr>
                            <td>{{ $item->component_name }}</td>
                            <td>Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="2" style="text-align: center;">Tidak ada potongan</td>
                    </tr>
                @endif
                
                <tr class="total-row">
                    <td>Total Potongan</td>
                    <td>Rp {{ number_format($total_potongan ?? 0, 0, ',', '.') }}</td>
                </tr>
            </table>
            
            <!-- Total Gaji Bersih -->
            <h3>Total Gaji Bersih</h3>
            <table class="slip-table">
                <tr class="total-row">
                    <td>Total Gaji Bersih</td>
                    <td>Rp {{ number_format($penggajian->total_salary ?? 0, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
        
        <div class="slip-footer">
            <div class="signature-box">
                <p>Diterima oleh,</p>
                <div class="signature-line">{{ $perangkat->nama }}</div>
            </div>
            
            <div class="signature-box">
                <p>{{ config('app.village_name', 'Desa') }}, {{ now()->format('d F Y') }}</p>
                <p>Kepala Desa</p>
                <div class="signature-line">{{ config('app.village_head', 'Kepala Desa') }}</div>
            </div>
        </div>
    </div>
</body>
</html>