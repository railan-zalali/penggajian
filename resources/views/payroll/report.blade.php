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

        .amount-column {
            text-align: right;
            font-weight: bold;
        }

        .total-row {
            background-color: #eaf2f8;
            font-weight: bold;
        }

        .total-row td {
            border-top: 2px solid #3498db;
        }

        .section-title {
            margin-top: 40px;
            margin-bottom: 15px;
            font-size: 20px;
            color: #2c3e50;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 5px;
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
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} -
            {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>

        <table>
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Hari Kerja</th>
                    <th>Gaji Pokok</th>
                    <th>Lembur</th>
                    <th>Tunjangan</th>
                    <th>Potongan</th>
                    <th>Total Gaji</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalBaseSalary = 0;
                    $totalOvertime = 0;
                    $totalAllowances = 0;
                    $totalDeductions = 0;
                    $totalSalary = 0;
                @endphp

                @foreach ($payrollData as $data)
                    @php
                        $totalBaseSalary += $data['base_salary'];
                        $totalOvertime += $data['overtime_payment'];

                        $allowanceAmount = 0;
                        if (isset($data['total_allowances'])) {
                            $allowanceAmount = $data['total_allowances'];
                        } elseif (isset($data['allowances'])) {
                            foreach ($data['allowances'] as $allowance) {
                                $allowanceAmount += $allowance['value'];
                            }
                        }
                        $totalAllowances += $allowanceAmount;

                        $deductionAmount = 0;
                        if (isset($data['total_deductions'])) {
                            $deductionAmount = $data['total_deductions'];
                        } elseif (isset($data['deductions'])) {
                            foreach ($data['deductions'] as $deduction) {
                                $deductionAmount += $deduction['value'];
                            }
                        }
                        $totalDeductions += $deductionAmount;

                        $totalSalary += $data['total_wage'];
                    @endphp

                    <tr>
                        <td>{{ $data['nik'] }}</td>
                        <td>{{ $data['nama'] }}</td>
                        <td>{{ $data['total_days_worked'] }}</td>
                        <td class="amount-column">Rp {{ number_format($data['base_salary'], 0, ',', '.') }}</td>
                        <td class="amount-column">Rp {{ number_format($data['overtime_payment'], 0, ',', '.') }}</td>
                        <td class="amount-column">Rp {{ number_format($allowanceAmount, 0, ',', '.') }}</td>
                        <td class="amount-column">Rp {{ number_format($deductionAmount, 0, ',', '.') }}</td>
                        <td class="amount-column">Rp {{ number_format($data['total_wage'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach

                <!-- Total Row -->
                <tr class="total-row">
                    <td colspan="3"><strong>TOTAL</strong></td>
                    <td class="amount-column">Rp {{ number_format($totalBaseSalary, 0, ',', '.') }}</td>
                    <td class="amount-column">Rp {{ number_format($totalOvertime, 0, ',', '.') }}</td>
                    <td class="amount-column">Rp {{ number_format($totalAllowances, 0, ',', '.') }}</td>
                    <td class="amount-column">Rp {{ number_format($totalDeductions, 0, ',', '.') }}</td>
                    <td class="amount-column">Rp {{ number_format($totalSalary, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Detailed breakdown for each employee -->
        <h2 class="section-title">Rincian Tunjangan & Potongan</h2>

        @foreach ($payrollData as $data)
            <h3 style="margin-top: 30px; color: #3498db;">{{ $data['nama'] }} ({{ $data['nik'] }})</h3>

            <!-- Allowances Table -->
            @if (isset($data['allowances']) && count($data['allowances']) > 0)
                <h4 style="margin-bottom: 10px; color: #27ae60;">Tunjangan</h4>
                <table style="margin-bottom: 20px;">
                    <thead>
                        <tr>
                            <th>Nama Tunjangan</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['allowances'] as $allowance)
                            <tr>
                                <td>{{ $allowance['name'] }}</td>
                                <td class="amount-column">Rp {{ number_format($allowance['value'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td><strong>Total Tunjangan</strong></td>
                            @php
                                $totalAllowance = 0;
                                foreach ($data['allowances'] as $allowance) {
                                    $totalAllowance += $allowance['value'];
                                }
                            @endphp
                            <td class="amount-column">Rp {{ number_format($totalAllowance, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            @else
                <p style="color: #7f8c8d; font-style: italic; margin-bottom: 20px;">Tidak ada tunjangan</p>
            @endif

            <!-- Deductions Table -->
            @if (isset($data['deductions']) && count($data['deductions']) > 0)
                <h4 style="margin-bottom: 10px; color: #e74c3c;">Potongan</h4>
                <table style="margin-bottom: 30px;">
                    <thead>
                        <tr>
                            <th>Nama Potongan</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['deductions'] as $deduction)
                            <tr>
                                <td>{{ $deduction['name'] }}</td>
                                <td class="amount-column">Rp {{ number_format($deduction['value'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td><strong>Total Potongan</strong></td>
                            @php
                                $totalDeduction = 0;
                                foreach ($data['deductions'] as $deduction) {
                                    $totalDeduction += $deduction['value'];
                                }
                            @endphp
                            <td class="amount-column">Rp {{ number_format($totalDeduction, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            @else
                <p style="color: #7f8c8d; font-style: italic; margin-bottom: 30px;">Tidak ada potongan</p>
            @endif

            <hr style="border: 1px dashed #e0e0e0; margin-bottom: 20px;">
        @endforeach
    </div>
</body>

</html>
