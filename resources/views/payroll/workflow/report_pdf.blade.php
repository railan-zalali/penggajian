<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Status Alur Proses Penggajian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 14px;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .summary-card {
            width: 23%;
            float: left;
            margin-right: 2%;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .summary-card h2 {
            font-size: 14px;
            margin-top: 0;
            margin-bottom: 5px;
        }
        .summary-card p {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-draft { background-color: #f3f4f6; color: #1f2937; }
        .status-verified { background-color: #dbeafe; color: #1e40af; }
        .status-calculated { background-color: #e0e7ff; color: #4338ca; }
        .status-approved { background-color: #ede9fe; color: #5b21b6; }
        .status-processed { background-color: #fef3c7; color: #92400e; }
        .status-completed { background-color: #d1fae5; color: #065f46; }
        .status-rejected { background-color: #fee2e2; color: #b91c1c; }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Status Alur Proses Penggajian</h1>
        @if($month && $year)
            <p>Periode: {{ \Carbon\Carbon::create(null, $month, 1)->format('F') }} {{ $year }}</p>
        @elseif($year)
            <p>Periode: Tahun {{ $year }}</p>
        @else
            <p>Periode: Semua</p>
        @endif
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}</p>
    </div>

    <div class="clearfix">
        <div class="summary-card">
            <h2>Total Penggajian</h2>
            <p>{{ array_sum($summary) }}</p>
        </div>
        <div class="summary-card">
            <h2>Menunggu Verifikasi</h2>
            <p>{{ $summary['draft'] }}</p>
        </div>
        <div class="summary-card">
            <h2>Dalam Proses</h2>
            <p>{{ $summary['verified'] + $summary['calculated'] + $summary['approved'] + $summary['processed'] }}</p>
        </div>
        <div class="summary-card">
            <h2>Selesai</h2>
            <p>{{ $summary['completed'] }}</p>
        </div>
    </div>

    <h2>Detail Status</h2>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th style="text-align: right;">Jumlah</th>
                <th style="text-align: right;">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = array_sum($summary);
            @endphp
            @foreach ($summary as $status => $count)
                <tr>
                    <td>
                        <span class="status-badge status-{{ $status }}">
                            {{ ucfirst($status) }}
                        </span>
                    </td>
                    <td style="text-align: right;">{{ $count }}</td>
                    <td style="text-align: right;">{{ $total > 0 ? number_format(($count / $total) * 100, 1) : 0 }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(count($detailData['draft']) > 0)
    <div class="page-break"></div>
    <h2>Detail Penggajian - Status Draft</h2>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Perangkat Desa</th>
                <th>Total Gaji</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detailData['draft'] as $payroll)
            <tr>
                <td>{{ $payroll->payroll_date->format('d/m/Y') }}</td>
                <td>{{ $payroll->linmas->name ?? 'Data tidak tersedia' }}</td>
                <td>Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(count($detailData['verified']) > 0)
    <div class="page-break"></div>
    <h2>Detail Penggajian - Status Terverifikasi</h2>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Perangkat Desa</th>
                <th>Total Gaji</th>
                <th>Diverifikasi Oleh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detailData['verified'] as $payroll)
            <tr>
                <td>{{ $payroll->payroll_date->format('d/m/Y') }}</td>
                <td>{{ $payroll->linmas->name ?? 'Data tidak tersedia' }}</td>
                <td>Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</td>
                <td>{{ $payroll->verifier->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(count($detailData['calculated']) > 0)
    <div class="page-break"></div>
    <h2>Detail Penggajian - Status Terhitung</h2>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Perangkat Desa</th>
                <th>Total Gaji</th>
                <th>Diverifikasi Oleh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detailData['calculated'] as $payroll)
            <tr>
                <td>{{ $payroll->payroll_date->format('d/m/Y') }}</td>
                <td>{{ $payroll->linmas->name ?? 'Data tidak tersedia' }}</td>
                <td>Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</td>
                <td>{{ $payroll->verifier->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(count($detailData['approved']) > 0)
    <div class="page-break"></div>
    <h2>Detail Penggajian - Status Disetujui</h2>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Perangkat Desa</th>
                <th>Total Gaji</th>
                <th>Disetujui Oleh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detailData['approved'] as $payroll)
            <tr>
                <td>{{ $payroll->payroll_date->format('d/m/Y') }}</td>
                <td>{{ $payroll->linmas->name ?? 'Data tidak tersedia' }}</td>
                <td>Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</td>
                <td>{{ $payroll->approver->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(count($detailData['processed']) > 0)
    <div class="page-break"></div>
    <h2>Detail Penggajian - Status Diproses</h2>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Perangkat Desa</th>
                <th>Total Gaji</th>
                <th>Disetujui Oleh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detailData['processed'] as $payroll)
            <tr>
                <td>{{ $payroll->payroll_date->format('d/m/Y') }}</td>
                <td>{{ $payroll->linmas->name ?? 'Data tidak tersedia' }}</td>
                <td>Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</td>
                <td>{{ $payroll->approver->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(count($detailData['completed']) > 0)
    <div class="page-break"></div>
    <h2>Detail Penggajian - Status Selesai</h2>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Perangkat Desa</th>
                <th>Total Gaji</th>
                <th>Metode Pembayaran</th>
                <th>Referensi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detailData['completed'] as $payroll)
            <tr>
                <td>{{ $payroll->payroll_date->format('d/m/Y') }}</td>
                <td>{{ $payroll->linmas->name ?? 'Data tidak tersedia' }}</td>
                <td>Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</td>
                <td>{{ $payroll->payment_method ?? '-' }}</td>
                <td>{{ $payroll->payment_reference ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(count($detailData['rejected']) > 0)
    <div class="page-break"></div>
    <h2>Detail Penggajian - Status Ditolak</h2>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Perangkat Desa</th>
                <th>Total Gaji</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detailData['rejected'] as $payroll)
            <tr>
                <td>{{ $payroll->payroll_date->format('d/m/Y') }}</td>
                <td>{{ $payroll->linmas->name ?? 'Data tidak tersedia' }}</td>
                <td>Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</td>
                <td>{{ $payroll->status_notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div style="margin-top: 40px; display: flex; justify-content: space-between;">
        <div style="width: 45%; text-align: left;">
            <div style="border-top: 1px solid #ddd; margin-top: 50px; margin-bottom: 10px; width: 80%;"></div>
            <strong>Dibuat oleh,</strong><br>
            Admin Penggajian
        </div>
        <div style="width: 45%; text-align: right;">
            <div style="border-top: 1px solid #ddd; margin-top: 50px; margin-bottom: 10px; width: 80%; margin-left: auto;"></div>
            <strong>Disetujui oleh,</strong><br>
            Kepala Desa
        </div>
    </div>

    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
        <p>Laporan ini dibuat secara otomatis dari Sistem Penggajian Perangkat Desa.</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}</p>
    </div>
</body>
</html>