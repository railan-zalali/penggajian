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

        .kop-surat {
            text-align: center;
            padding: 10px 20px;
            border-bottom: 2px solid #000;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .kop-logo {
            width: 80px;
            height: auto;
            margin-right: 15px;
        }

        .kop-text {
            text-align: center;
        }

        .kop-text h1, .kop-text h2, .kop-text h3 {
            margin: 0;
            line-height: 1.2;
        }

        .kop-text h1 {
            font-size: 16px;
            font-weight: bold;
        }

        .kop-text h2 {
            font-size: 14px;
            font-weight: bold;
        }

        .kop-text h3 {
            font-size: 12px;
            font-weight: bold;
        }

        .kop-text p {
            margin: 5px 0 0;
            font-size: 10px;
        }

        .kop-divider {
            border-bottom: 2px solid #000;
            margin-top: 5px;
        }

        .content {
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 12px;
            margin: 0;
        }

        .kop-surat {
            text-align: center;
            padding: 10px 20px;
            border-bottom: 2px solid #000;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .kop-logo {
            width: 80px;
            height: auto;
            margin-right: 15px;
        }

        .kop-text {
            text-align: center;
        }

        .kop-text h1, .kop-text h2, .kop-text h3 {
            margin: 0;
            line-height: 1.2;
        }

        .kop-text h1 {
            font-size: 16px;
            font-weight: bold;
        }

        .kop-text h2 {
            font-size: 14px;
            font-weight: bold;
        }

        .kop-text h3 {
            font-size: 12px;
            font-weight: bold;
        }

        .kop-text p {
            margin: 5px 0 0;
            font-size: 10px;
        }

        .kop-divider {
            border-bottom: 2px solid #000;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
            font-size: 9px;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: bold;
            text-align: center;
            padding: 8px 4px;
            border: 1px solid #ddd;
            font-size: 9px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        td {
            padding: 6px 4px;
            border: 1px solid #ddd;
            font-size: 9px;
            text-align: left;
            vertical-align: middle;
        }

        td.numeric {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        td.centered {
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .status {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 7px;
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
            margin-top: 25px;
            border: 1px solid #ddd;
            padding: 12px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .summary h2 {
            margin-top: 0;
            font-size: 12px;
            color: #343a40;
            border-bottom: 2px solid #343a40;
            padding-bottom: 4px;
            margin-bottom: 12px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 9px;
        }

        .summary-label {
            font-weight: bold;
            color: #495057;
        }

        .summary-value {
            font-weight: bold;
        }

        .summary-total {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            font-weight: bold;
            font-size: 10px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #6c757d;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }

        .signature-area {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            width: 100%;
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
            border-top: 1px solid #ddd;
            margin: 40px auto 8px auto;
            width: 80%;
        }

        .page-number {
            position: absolute;
            bottom: 15px;
            right: 15px;
            font-size: 8px;
            color: #6c757d;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            color: rgba(0, 0, 0, 0.03);
            z-index: -1;
        }
    </style>
</head>

<body>
    <div class="watermark">CISEWU</div>

    <div class="container">
        @include('components.header-kop-surat')
        <div class="header">
            <h1>LAPORAN PENGGAJIAN PERANGKAT DESA</h1>
            <p>Periode: {{ $monthName }} {{ $year }}</p>
        </div>

        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th width="4%">No.</th>
                        <th width="12%">NIK</th>
                        <th width="18%">Nama</th>
                        <th width="8%">Hari Kerja</th>
                        <th width="8%">Jam Lembur</th>
                        <th width="12%">Gaji Pokok</th>
                        <th width="12%">Tunjangan</th>
                        <th width="12%">Potongan</th>
                        <th width="14%">Total Gaji</th>
                        <th width="8%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalSalaryAll = 0; @endphp
                    @forelse($payrolls as $index => $payroll)
                        @php
                            $totalSalaryAll += $payroll->total_salary;
                            $totalAllowances = $payroll->details->where('type', 'allowance')->sum('amount');
                            $totalDeductions = $payroll->details->where('type', 'deduction')->sum('amount');
                        @endphp
                        <tr>
                            <td class="centered">{{ $index + 1 }}</td>
                            <td>{{ $payroll->linmas->nik ?? 'N/A' }}</td>
                            <td>{{ $payroll->linmas->nama ?? 'N/A' }}</td>
                            <td class="centered">{{ $payroll->total_days_present ?? 0 }}</td>
                            <td class="centered">{{ $payroll->overtime_hours ?? 0 }} jam</td>
                            <td class="numeric">Rp {{ number_format($payroll->base_salary ?? 0, 0, ',', '.') }}</td>
                            <td class="numeric">Rp {{ number_format($totalAllowances, 0, ',', '.') }}</td>
                            <td class="numeric">Rp {{ number_format($totalDeductions, 0, ',', '.') }}</td>
                            <td class="numeric">Rp {{ number_format($payroll->total_salary ?? 0, 0, ',', '.') }}</td>
                            <td class="centered">
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
                            <td colspan="10" style="text-align: center;">Tidak ada data penggajian untuk periode ini
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
                            <span class="summary-value">{{ $payrolls->sum('total_days_present') ?? 0 }} hari</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Total Jam Lembur:</span>
                            <span class="summary-value">{{ $payrolls->sum('overtime_hours') ?? 0 }} jam</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Total Tunjangan:</span>
                            <span class="summary-value">Rp
                                {{ number_format($payrolls->sum(function ($p) {return $p->details->where('type', 'allowance')->sum('amount');}),0,',','.') }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="summary-item">
                            <span class="summary-label">Total Potongan:</span>
                            <span class="summary-value">Rp
                                {{ number_format($payrolls->sum(function ($p) {return $p->details->where('type', 'deduction')->sum('amount');}),0,',','.') }}</span>
                        </div>
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
                <p>© {{ date('Y') }} Sistem Penggajian Perangkat Desa CISEWU</p>
            </div>

            <div class="page-number">Halaman 1 dari 1</div>
        </div>
    </div>
</body>

</html>
