<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ringkasan Tahunan - {{ $perangkat->nama }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            padding: 20px;
            border-bottom: 2px solid #333;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 5px 0 0;
            font-size: 16px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 12px;
        }
        .info-section {
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .amount-column {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #f2f2f2;
        }
        .summary-section {
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .summary-table {
            width: 50%;
            margin-left: auto;
            margin-right: auto;
        }
        .signature-area {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            width: 100%;
            align-items: flex-start; /* Pastikan semua elemen sejajar dari atas */
        }
        .signature-box {
            width: 45%;
            display: inline-block;
            vertical-align: top; /* Pastikan sejajar dari atas */
        }
        .signature-box:first-child {
            text-align: center;
        }
        .signature-box:last-child {
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
        @media print {
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        @include('components.header-kop-surat')
        
        <div class="header">
            <h1>Ringkasan Tahunan Penggajian</h1>
            <p>Tahun: {{ $tahun }}</p>
        </div>
        
        <div class="info-section">
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
                <div class="info-label">Tanggal Cetak:</div>
                <div class="info-value">{{ now()->format('d F Y') }}</div>
            </div>
        </div>
        
        <h3>Rincian Bulanan</h3>
        <table>
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Gaji Pokok</th>
                    <th>Tunjangan</th>
                    <th>Potongan</th>
                    <th>Total Gaji</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data_bulanan as $bulan => $data)
                <tr>
                    <td>{{ $bulan }}</td>
                    <td class="amount-column">Rp {{ number_format($data['gaji_pokok'], 0, ',', '.') }}</td>
                    <td class="amount-column">Rp {{ number_format($data['total_tunjangan'], 0, ',', '.') }}</td>
                    <td class="amount-column">Rp {{ number_format($data['total_potongan'], 0, ',', '.') }}</td>
                    <td class="amount-column">Rp {{ number_format($data['total_gaji'], 0, ',', '.') }}</td>
                    <td>{{ $data['ada_data'] ? 'Dibayarkan' : 'Tidak Ada Data' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="summary-section">
            <h3>Ringkasan Tahunan</h3>
            <table class="summary-table">
                <tr>
                    <td>Total Gaji Pokok</td>
                    <td class="amount-column">Rp {{ number_format($ringkasan['total_gaji_pokok'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total Tunjangan</td>
                    <td class="amount-column">Rp {{ number_format($ringkasan['total_tunjangan'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total Potongan</td>
                    <td class="amount-column">Rp {{ number_format($ringkasan['total_potongan'], 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total Gaji Tahunan</td>
                    <td class="amount-column">Rp {{ number_format($ringkasan['total_gaji'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Jumlah Bulan Terbayar</td>
                    <td class="amount-column">{{ $ringkasan['bulan_terbayar'] }} bulan</td>
                </tr>
                <tr>
                    <td>Rata-rata Gaji Bulanan</td>
                    <td class="amount-column">Rp {{ number_format($ringkasan['rata_rata_gaji'], 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
        
        <div class="signature-area">
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