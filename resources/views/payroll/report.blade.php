<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penggajian</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        p {
            text-align: center;
            font-size: 18px;
            margin-bottom: 30px;
            color: #7f8c8d;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background-color: #3498db;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            font-size: 14px;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: #e8f4f8;
            transition: background-color 0.3s ease;
        }

        td:last-child {
            font-weight: bold;
            color: #2ecc71;
        }

        @media print {
            body {
                background-color: #ffffff;
            }

            .container {
                box-shadow: none;
            }

            table {
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Laporan Penggajian Perangkat Desa</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('F Y') }} -
            {{ \Carbon\Carbon::parse($endDate)->format('F Y') }}</p>

        <table>
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Hari Masuk</th>
                    <th>Lembur</th>
                    <th>Total Gaji</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payrollData as $payroll)
                    <tr>
                        <td>{{ $payroll['nik'] }}</td>
                        <td>{{ $payroll['nama'] }}</td>
                        <td>{{ $payroll['total_days_worked'] }}</td>
                        <td>{{ $payroll['total_overtime'] }}</td>
                        <td>Rp {{ number_format($payroll['total_wage'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
