<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $payrollData['nama'] }}</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 12px;
            line-height: 1.5;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0;
            background-color: #fff;
        }

        .kop-surat {
            text-align: center;
            padding: 10px 20px;
            border-bottom: 2px solid #000;
            margin-bottom: 20px;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: flex-start;
        }
        
        .kop-logo {
            width: 80px;
            height: auto;
            margin-right: 15px;
            display: flex;
            align-items: center;
        }
        
        .kop-text {
            text-align: center;
            flex: 1;
        }
        
        .kop-text h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .kop-text p {
            margin: 2px 0;
            font-size: 12px;
        }
        
        .kop-divider {
            border: 1px solid #000;
            margin-bottom: 20px;
        }

        .header {
            background-color: #343a40;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            opacity: 0.8;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo {
            max-height: 80px;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(0, 0, 0, 0.05);
            z-index: -1;
        }

        .employee-info {
            padding: 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .employee-info h2 {
            margin: 0 0 15px;
            color: #495057;
            font-size: 18px;
            border-bottom: 2px solid #343a40;
            padding-bottom: 5px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .info-item {
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: 700;
            color: #6c757d;
            display: block;
            margin-bottom: 3px;
            font-size: 11px;
            text-transform: uppercase;
        }

        .info-value {
            font-size: 14px;
        }

        .salary-details {
            padding: 20px;
        }

        .salary-details h2 {
            margin: 0 0 15px;
            color: #495057;
            font-size: 18px;
            border-bottom: 2px solid #343a40;
            padding-bottom: 5px;
        }

        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .salary-table th {
            background-color: #e9ecef;
            padding: 10px;
            text-align: left;
            font-weight: 700;
            border-bottom: 2px solid #dee2e6;
        }

        .salary-table td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }

        .salary-table .amount {
            text-align: right;
        }

        .total-row td {
            font-weight: 700;
            border-top: 2px solid #343a40;
            border-bottom: none;
            background-color: #f8f9fa;
        }

        .footer {
            padding: 20px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }

        .signature-area {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            width: 100%;
            padding: 0 20px;
            align-items: flex-start; /* Pastikan semua elemen sejajar dari atas */
        }

        .signature-box {
            width: 45%;
            position: relative;
            display: inline-block;
            vertical-align: top; /* Pastikan sejajar dari atas */
            text-align: center;
        }

        .signature-line {
            border-top: 2px solid #333;
            margin: 60px auto 15px auto;
            width: 80%;
        }

        .signature-name {
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .signature-title {
            font-size: 12px;
            color: #666;
            font-style: italic;
        }

        .signature-date {
            font-size: 11px;
            color: #888;
            margin-top: 8px;
        }

        .qr-code {
            text-align: right;
            margin-top: 20px;
        }

        .qr-code img {
            width: 80px;
            height: 80px;
        }
    </style>
</head>

<body>
    <div class="watermark">SLIP GAJI</div>

    <div class="container">
        <div class="kop-surat">
            <div class="kop-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Desa Cisewu" width="80">
            </div>
            <div class="kop-text">
                <h2>PEMERINTAH KABUPATEN GARUT</h2>
                <h2>KECAMATAN CISEWU</h2>
                <h2>DESA CISEWU</h2>
                <p>Alamat: Jalan Wirabhakti Nomor 26 C Cisewu - Garut 44166</p>
                <p>Email: desacisewu1@gmail.com</p>
            </div>
        </div>
        <hr class="kop-divider">
        
        <div class="header">
            <div class="logo-container">
                <!-- Replace with your logo -->
                <h1>DESA CISEWU</h1>
            </div>
            <h1>SLIP GAJI PERANGKAT DESA</h1>
            <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} -
                {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
        </div>

        <div class="employee-info">
            <h2>Informasi Perangkat Desa</h2>
            <div class="info-grid">
                <div>
                    <div class="info-item">
                        <span class="info-label">Nama</span>
                        <span class="info-value">{{ $payrollData['nama'] }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">NIK</span>
                        <span class="info-value">{{ $payrollData['nik'] }}</span>
                    </div>
                </div>
                <div>
                    <div class="info-item">
                        <span class="info-label">Tanggal Gaji</span>
                        <span class="info-value">{{ \Carbon\Carbon::now()->format('d F Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Metode Pembayaran</span>
                        <span class="info-value">Transfer Bank</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="salary-details">
            <h2>Rincian Gaji</h2>
            <table class="salary-table">
                <thead>
                    <tr>
                        <th width="60%">Pendapatan</th>
                        <th width="40%" class="amount">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Gaji Pokok Bulanan</td>
                        <td class="amount">Rp {{ number_format($payrollData['base_salary'], 0, ',', '.') }}</td>
                    </tr>

                    @if (isset($payrollData['absence_deduction']) && $payrollData['absence_deduction'] > 0)
                        <tr>
                            <td>Potongan Ketidakhadiran
                                @if (isset($payrollData['expected_working_days']) && isset($payrollData['actual_days_worked']))
                                    ({{ $payrollData['expected_working_days'] - $payrollData['actual_days_worked'] }}
                                    hari)
                                @endif
                            </td>
                            <td class="amount text-red-600">
                                - Rp {{ number_format($payrollData['absence_deduction'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif

                    @foreach ($payrollData['allowances'] as $allowance)
                        <tr>
                            <td>{{ $allowance['name'] }}</td>
                            <td class="amount">Rp {{ number_format($allowance['amount'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach

                    <tr class="total-row">
                        <td>Total Pendapatan Kotor</td>
                        <td class="amount">Rp
                            {{ number_format($payrollData['base_salary'] + $payrollData['total_allowances'], 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="salary-table">
                <thead>
                    <tr>
                        <th width="60%">Potongan</th>
                        <th width="40%" class="amount">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Tampilkan potongan jika ada -->
                    @if (isset($payrollData['deductions']) && count($payrollData['deductions']) > 0)
                        @foreach ($payrollData['deductions'] as $deduction)
                            <tr>
                                <td>{{ $deduction['name'] }}</td>
                                <td class="amount">Rp
                                    {{ number_format(isset($deduction['amount']) ? $deduction['amount'] : $deduction['value'] ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>Pajak Penghasilan (PPh 21)</td>
                            <td class="amount">Rp 0</td>
                        </tr>
                        <tr>
                            <td>BPJS Kesehatan</td>
                            <td class="amount">Rp 0</td>
                        </tr>
                        <tr>
                            <td>BPJS Ketenagakerjaan</td>
                            <td class="amount">Rp 0</td>
                        </tr>
                    @endif

                    <tr class="total-row">
                        <td>Total Potongan</td>
                        <td class="amount">Rp {{ number_format($payrollData['total_deductions'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="salary-table">
                <tbody>
                    <tr class="total-row">
                        <td width="60%">Total Gaji Bersih</td>
                        <td width="40%" class="amount">Rp
                            {{ number_format($payrollData['total_wage'], 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="signature-area">
                <div class="signature-box" style="text-align: left;">
                    <div class="signature-line"></div>
                    <strong class="signature-name">Perangkat Desa</strong><br>
                    <span class="signature-title">Nama</span><br>
                    <span class="signature-date">{{ $payrollData['nama'] }}</span>
                </div>
                <div class="signature-box" style="text-align: right;">
                    <div class="signature-line"></div>
                    <strong class="signature-name">Pejabat Berwenang</strong><br>
                    <span class="signature-title">Jabatan</span><br>
                    <span class="signature-date">Kepala Desa</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Slip gaji ini diterbitkan secara elektronik dan sah tanpa tanda tangan.</p>
            <p>Dokumen ini bersifat rahasia dan hanya untuk kepentingan karyawan yang bersangkutan.</p>
            <p>© {{ date('Y') }} Sistem Penggajian Perangkat Desa CISEWU</p>
        </div>
    </div>
</body>

</html>
