<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Tutup Bulan - {{ $monthClosing->formatted_period }}</title>
    <style>
        @page {
            margin: 2cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 12px;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0 0 5px;
            text-transform: uppercase;
        }

        .header p {
            font-size: 14px;
            margin: 0;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .info-item {
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .status {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
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
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #f8f9fa;
        }

        .summary-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .summary-label {
            font-weight: bold;
        }

        .summary-total {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .signature-area {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #ddd;
            margin: 50px auto 10px;
            width: 80%;
        }

        .page-number {
            text-align: right;
            font-size: 10px;
            color: #666;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Tutup Bulan</h1>
        <p>Periode: {{ $monthClosing->formatted_period }}</p>
    </div>

    <div class="info-section">
        <div class="info-title">Informasi Tutup Bulan</div>
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <span class="info-label">Periode:</span>
                    <span>{{ $monthClosing->formatted_period }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal Tutup:</span>
                    <span>{{ $monthClosing->closing_date->format('d F Y H:i') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span>{{ $monthClosing->status == 'closed' ? 'Ditutup' : 'Dibuka Kembali' }}</span>
                </div>
            </div>
            <div>
                <div class="info-item">
                    <span class="info-label">Total Perangkat Desa:</span>
                    <span>{{ $monthClosing->total_linmas }} orang</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Total Penggajian:</span>
                    <span>{{ $monthClosing->total_payrolls }} transaksi</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Ditutup Oleh:</span>
                    <span>{{ $monthClosing->user->name }}</span>
                </div>
            </div>
        </div>
        @if ($monthClosing->notes)
            <div class="info-item" style="margin-top: 10px;">
                <span class="info-label">Catatan:</span>
                <span>{{ $monthClosing->notes }}</span>
            </div>
        @endif
    </div>

    <div class="info-title">Data Penggajian</div>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Hari Kerja</th>
                <th>Gaji Pokok</th>
                <th>Lembur</th>
                <th>Total Gaji</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payrolls as $index => $payroll)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $payroll->linmas->nik ?? 'N/A' }}</td>
                    <td>{{ $payroll->linmas->nama ?? 'N/A' }}</td>
                    <td>{{ $payroll->total_days_present }} hari</td>
                    <td>Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($payroll->overtime_payment, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</td>
                    <td>
                        @if ($payroll->payment_status == 'paid')
                            <span class="status status-paid">Dibayar</span>
                        @elseif ($payroll->payment_status == 'pending')
                            <span class="status status-pending">Pending</span>
                        @else
                            <span class="status status-cancelled">Dibatalkan</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data penggajian</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-title">Ringkasan Keuangan</div>

        <div class="summary-item">
            <span class="summary-label">Total Gaji Dibayarkan:</span>
            <span>Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
        </div>

        <div class="summary-item">
            <span class="summary-label">Total Gaji Pending:</span>
            <span>Rp {{ number_format($totalPending, 0, ',', '.') }}</span>
        </div>

        <div class="summary-item">
            <span class="summary-label">Total Gaji Dibatalkan:</span>
            <span>Rp {{ number_format($totalCancelled, 0, ',', '.') }}</span>
        </div>

        <div class="summary-item summary-total">
            <span class="summary-label">Total Keseluruhan:</span>
            <span>Rp {{ number_format($monthClosing->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="signature-area">
        <div class="signature-box">
            <div class="signature-line"></div>
            <strong>Dibuat oleh,</strong><br>
            {{ $monthClosing->user->name }}<br>
            Admin Penggajian
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <strong>Disetujui oleh,</strong><br>
            _______________________<br>
            Kepala Desa
        </div>
    </div>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis dari Sistem Penggajian Perangkat Desa.</p>
        <p>Dicetak pada: {{ now()->format('d F Y H:i:s') }}</p>
    </div>

    <div class="page-number">Halaman 1 dari 1</div>
</body>

</html>
