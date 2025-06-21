<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Gaji Bulanan - {{ $monthName }} {{ $year }}</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 10px;
            line-height: 1.5;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 0;
            background-color: #fff;
        }

        .header {
            background-color: #343a40;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 12px;
            opacity: 0.8;
        }

        .content {
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 10px;
        }

        td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 9px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .status {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-cancelled {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .summary {
            margin-top: 30px;
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .summary h2 {
            margin-top: 0;
            font-size: 14px;
            color: #343a40;
            border-bottom: 2px solid #343a40;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 11px;
        }

        .summary-label {
            font-weight: bold;
            color: #495057;
        }

        .summary-value {
            font-weight: bold;
        }

        .summary-total {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-weight: bold;
            font-size: 12px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9px;
            color: #6c757d;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .signature-area {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 200px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #ddd;
            margin-top: 50px;
            margin-bottom: 10px;
        }

        .page-number {
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 9px;
            color: #6c757d;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(0, 0, 0, 0.03);
            z-index: -1;
        }
    </style>
</head>

<body>
    <div class="watermark">KIARACONDONG</div>

    <div class="container">
        <div class="header">
            <h1>LAPORAN PENGGAJIAN PERANGKAT DESA</h1>
            <p>Periode: {{ $monthName }} {{ $year }}</p>
        </div>

        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th width="5%">No.</th>
                        <th width="15%">NIK</th>
                        <th width="20%">Nama</th>
                        <th width="10%">Hari Kerja</th>
                        <th width="10%">Lembur</th>
                        <th width="15%">Gaji Pokok</th>
                        <th width="10%">Lembur</th>
                        <th width="15%">Total Gaji</th>
                        <th width="10%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalSalaryAll = 0; @endphp
                    @forelse($payrolls as $index => $payroll)
                        @php $totalSalaryAll += $payroll->total_salary; @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $payroll->linmas->nik ?? 'N/A' }}</td>
                            <td>{{ $payroll->linmas->nama ?? 'N/A' }}</td>
                            <td>{{ $payroll->total_days_present }}</td>
                            <td>{{ number_format($payroll->overtime_payment / 10000) }} jam</td>
                            <td>Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($payroll->overtime_payment, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</td>
                            <td>
                                @if ($payroll->payment_status == 'paid')
                                    <span class="status status-paid">Dibayar</span>
                                @elseif($payroll->payment_status == 'pending')
                                    <span class="status status-pending">Pending</span>
                                @else
                                    <span class="status status-cancelled">Dibatalkan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center;">Tidak ada data penggajian untuk periode ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="summary">
                <h2>Ringkasan Penggajian</h2>
                <div class="summary-grid">
                    <div>
                        <div class="summary-item">
                            <span class="summary-label">Total Perangkat Desa:</span>
                            <span class="summary-value">{{ $payrolls->count() }} orang</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Total Hari Kerja:</span>
                            <span class="summary-value">{{ $payrolls->sum('total_days_present') }} hari</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Total Jam Lembur:</span>
                            <span class="summary-value">{{ number_format($payrolls->sum('overtime_payment') / 10000) }}
                                jam</span>
                        </div>
                    </div>
                    <div>
                        <div class="summary-item">
                            <span class="summary-label">Total Gaji Dibayarkan:</span>
                            <span class="summary-value">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Total Gaji Pending:</span>
                            <span class="summary-value">Rp {{ number_format($totalPending, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Total Gaji Dibatalkan:</span>
                            <span class="summary-value">Rp {{ number_format($totalCancelled, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <div class="summary-item summary-total">
                    <span class="summary-label">Total Anggaran Gaji:</span>
                    <span class="summary-value">Rp {{ number_format($totalSalaryAll, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="signature-area">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <strong>Dibuat oleh,</strong><br>
                    Admin Penggajian
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <strong>Disetujui oleh,</strong><br>
                    Kepala Desa
                </div>
            </div>

            <div class="footer">
                <p>Laporan ini dibuat secara otomatis pada {{ date('d F Y H:i:s') }}</p>
                <p>© {{ date('Y') }} Sistem Penggajian Perangkat Desa Kecamatan Kiaracondong</p>
            </div>

            <div class="page-number">Halaman 1 dari 1</div>
        </div>
    </div>
</body>

</html>
