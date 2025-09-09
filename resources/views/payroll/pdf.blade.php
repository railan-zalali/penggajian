<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penggajian</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 10px;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 10px;
        }

        th,
        td {
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

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        @media print {
            body {
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN PENGGAJIAN PERANGKAT DESA</h1>
        <p>Periode: {{ $startDate }} - {{ $endDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Hari Kerja</th>
                <!-- Kolom lembur dihapus sesuai permintaan -->
                <th>Gaji Pokok</th>
                <th>Tunjangan</th>
                <th>Potongan</th>
                <th>Total Gaji</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
                $no = 1;
            @endphp
            @foreach ($payrollData as $data)
                @php
                    $totalGaji = $data['total_wage'] ?? 0;
                    $total += $totalGaji;
                @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $data['nik'] ?? '-' }}</td>
                    <td>{{ $data['nama'] ?? '-' }}</td>
                    <td>{{ $data['jabatan'] ?? '-' }}</td>
                    <td>{{ $data['total_days_worked'] ?? 0 }}</td>

                    <td class="amount-column">{{ number_format($data['base_salary'] ?? 0, 0, ',', '.') }}</td>
                    <td class="amount-column">{{ number_format($data['total_allowances'] ?? 0, 0, ',', '.') }}</td>
                    <td class="amount-column">{{ number_format($data['total_deductions'] ?? 0, 0, ',', '.') }}</td>
                    <td class="amount-column">{{ number_format($totalGaji, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="9" style="text-align: right;">Total</td>
                <td class="amount-column">{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d-m-Y H:i:s') }}</p>
    </div>
</body>

</html>
