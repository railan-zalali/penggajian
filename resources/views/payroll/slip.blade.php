<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .header,
        .footer {
            text-align: center;
        }

        .header {
            margin-bottom: 20px;
        }

        .footer {
            margin-top: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Slip Gaji Perangkat Desa</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('F Y') }} -
            {{ \Carbon\Carbon::parse($endDate)->format('F Y') }}</p>
    </div>

    <table>
        <tr>
            <th>NIK</th>
            <td>{{ $payrollData['nik'] }}</td>
        </tr>
        <tr>
            <th>Nama</th>
            <td>{{ $payrollData['nama'] }}</td>
        </tr>
        <tr>
            <th>Hari Masuk</th>
            <td>{{ $payrollData['total_days_worked'] }}</td>
        </tr>
        <tr>
            <th>Lembur</th>
            <td>{{ $payrollData['total_overtime'] }}</td>
        </tr>
        <tr>
            <th>Total Gaji</th>
            <td>Rp {{ number_format($payrollData['total_wage'], 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>Terima kasih atas kerja keras Anda!</p>
    </div>
</body>

</html>
