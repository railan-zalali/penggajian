<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Main Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg overflow-hidden shadow-md border-b-4 border-blue-500">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="rounded-md bg-blue-100 p-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="ml-5">
                                <h3 class="text-sm text-gray-600 uppercase font-semibold">Total Perangkat Desa</h3>
                                <p class="text-3xl font-bold text-gray-800">{{ $totalLinmas }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg overflow-hidden shadow-md border-b-4 border-green-500">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="rounded-md bg-green-100 p-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="ml-5">
                                <h3 class="text-sm text-gray-600 uppercase font-semibold">Kehadiran Bulan Ini</h3>
                                <p class="text-3xl font-bold text-gray-800">{{ $attendanceThisMonth }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg overflow-hidden shadow-md border-b-4 border-purple-500">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="rounded-md bg-purple-100 p-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5">
                                <h3 class="text-sm text-gray-600 uppercase font-semibold">Total Gaji Dibayarkan</h3>
                                <p class="text-3xl font-bold text-gray-800">Rp
                                    {{ number_format($totalSalary, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg overflow-hidden shadow-md border-b-4 border-amber-500">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="rounded-md bg-amber-100 p-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-5">
                                <h3 class="text-sm text-gray-600 uppercase font-semibold">Hari Kerja Bulan Ini</h3>
                                <p class="text-3xl font-bold text-gray-800">{{ $workingDays }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Chart Kehadiran -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">Statistik Kehadiran</h2>
                        <div class="text-sm text-gray-500">6 bulan terakhir</div>
                    </div>
                    <div style="height: 300px; position: relative;">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>

                <!-- Chart Status Pembayaran -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">Status Pembayaran</h2>
                        <div class="text-sm text-gray-500">Bulan Ini</div>
                    </div>
                    <div style="height: 300px; position: relative;">
                        <canvas id="paymentStatusChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Perangkat Desa Terbaru -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">Perangkat Desa Terbaru</h2>
                        <a href="{{ route('linmas.index') }}"
                            class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Lihat Semua</a>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach ($recentLinmas as $linmas)
                            <div class="py-3 flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span
                                            class="text-indigo-800 font-semibold">{{ substr($linmas->nama, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <div class="font-medium text-gray-900">{{ $linmas->nama }}</div>
                                    <div class="text-sm text-gray-500">NIK: {{ $linmas->nik }}</div>
                                </div>
                                <div class="ml-2 flex-shrink-0">
                                    <a href="{{ route('linmas.edit', $linmas->id) }}"
                                        class="text-indigo-600 hover:text-indigo-900 text-sm">Detail</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Penggajian Terakhir -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">Penggajian Terakhir</h2>
                        <a href="{{ route('payroll.history') }}"
                            class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Lihat Semua</a>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach ($recentPayrolls as $payroll)
                            <div class="py-3 flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <div class="font-medium text-gray-900">{{ $payroll->linmas->nama ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $payroll->payroll_date->format('F Y') }} ·
                                        @if ($payroll->payment_status == 'paid')
                                            <span class="text-green-600">Dibayar</span>
                                        @elseif($payroll->payment_status == 'pending')
                                            <span class="text-amber-600">Pending</span>
                                        @else
                                            <span class="text-red-600">Dibatalkan</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-2 flex-shrink-0 font-medium text-gray-900">
                                    Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Attendance Chart
                var attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
                var attendanceChart = new Chart(attendanceCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode(
                            $attendanceData->pluck('month')->map(function ($month) {
                                return \Carbon\Carbon::create(null, $month, 1)->format('M');
                            }),
                        ) !!},
                        datasets: [{
                            label: 'Kehadiran',
                            data: {!! json_encode($attendanceData->pluck('attendance')) !!},
                            backgroundColor: 'rgba(79, 70, 229, 0.6)',
                            borderColor: 'rgba(79, 70, 229, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Lembur',
                            data: {!! json_encode($attendanceData->pluck('overtime')) !!},
                            backgroundColor: 'rgba(245, 158, 11, 0.6)',
                            borderColor: 'rgba(245, 158, 11, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        layout: {
                            padding: {
                                left: 10,
                                right: 10,
                                top: 0,
                                bottom: 10
                            }
                        }
                    }
                });

                // Payment Status Chart
                var paymentStatusCtx = document.getElementById('paymentStatusChart').getContext('2d');
                var paymentStatusChart = new Chart(paymentStatusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Dibayar', 'Pending', 'Dibatalkan'],
                        datasets: [{
                            data: [{{ $paymentStats['paid'] }}, {{ $paymentStats['pending'] }},
                                {{ $paymentStats['cancelled'] }}
                            ],
                            backgroundColor: [
                                'rgba(16, 185, 129, 0.8)', // green
                                'rgba(245, 158, 11, 0.8)', // amber
                                'rgba(239, 68, 68, 0.8)' // red
                            ],
                            borderColor: [
                                'rgb(16, 185, 129)',
                                'rgb(245, 158, 11)',
                                'rgb(239, 68, 68)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        },
                        cutout: '70%'
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
