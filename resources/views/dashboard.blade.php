<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="container mx-auto px-4 py-8">


        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-2">Total Perangkat Desa</h2>
                <p class="text-3xl font-bold">{{ $totalLinmas }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-2">Kehadiran Bulan Ini</h2>
                <p class="text-3xl font-bold">{{ $attendanceThisMonth }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-2">Total Gaji Dibayarkan</h2>
                <p class="text-3xl font-bold">Rp {{ number_format($totalSalary, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-2">Hari Kerja Bulan Ini</h2>
                <p class="text-3xl font-bold">{{ $workingDays }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Statistik Kehadiran</h2>
            <canvas id="attendanceChart" width="400" height="200"></canvas>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Perangkat Desa Terbaru</h2>
                <ul class="space-y-2">
                    @foreach ($recentLinmas as $linmas)
                        <li>{{ $linmas->nama }} - NIK: {{ $linmas->nik }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Penggajian Terakhir</h2>
                <ul class="space-y-2">
                    @foreach ($recentPayrolls as $payroll)
                        <li>Periode: {{ $payroll->period }} - Total: Rp
                            {{ number_format($payroll->total, 0, ',', '.') }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <script>
        var ctx = document.getElementById('attendanceChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($attendanceData->pluck('month')) !!},
                datasets: [{
                    label: 'Kehadiran',
                    data: {!! json_encode($attendanceData->pluck('attendance')) !!},
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }, {
                    label: 'Lembur',
                    data: {!! json_encode($attendanceData->pluck('overtime')) !!},
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</x-app-layout>
