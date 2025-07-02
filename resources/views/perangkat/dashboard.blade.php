<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h1 class="text-2xl font-bold mb-6">Dashboard Perangkat Desa</h1>

                @if (isset($error))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p>{{ $error }}</p>
                    </div>
                @else
                    <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6">
                        <div class="flex">
                            <div class="py-1"><svg class="fill-current h-6 w-6 text-blue-500 mr-4"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path
                                        d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z" />
                                </svg></div>
                            <div>
                                <p class="font-bold">Selamat datang, {{ $linmas->nama }}</p>
                                <p class="text-sm">NIK: {{ $linmas->nik }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Statistik Ringkas -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                            <h3 class="text-lg font-semibold text-gray-700">Kehadiran Bulan Ini</h3>
                            <p class="text-3xl font-bold text-green-600">{{ $attendanceThisMonth }}</p>
                        </div>

                        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                            <h3 class="text-lg font-semibold text-gray-700">Gaji Terakhir</h3>
                            <p class="text-3xl font-bold text-blue-600">
                                @if ($payrolls->count() > 0)
                                    Rp {{ number_format($payrolls->first()->total_salary, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>

                        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                            <h3 class="text-lg font-semibold text-gray-700">Status Pembayaran</h3>
                            <p class="text-xl font-semibold">
                                @if ($payrolls->count() > 0)
                                    @if ($payrolls->first()->payment_status == 'paid')
                                        <span class="text-green-600">Dibayar</span>
                                    @elseif($payrolls->first()->payment_status == 'pending')
                                        <span class="text-yellow-600">Pending</span>
                                    @else
                                        <span class="text-red-600">Dibatalkan</span>
                                    @endif
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Menu Navigasi -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                        <a href="{{ route('perangkat.attendances') }}"
                            class="bg-gradient-to-r from-green-400 to-green-600 text-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <div>
                                    <h3 class="font-bold text-lg">Kehadiran</h3>
                                    <p class="text-sm opacity-90">Lihat riwayat kehadiran</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('perangkat.payrolls') }}"
                            class="bg-gradient-to-r from-blue-400 to-blue-600 text-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h3 class="font-bold text-lg">Riwayat Gaji</h3>
                                    <p class="text-sm opacity-90">Lihat riwayat penggajian</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('perangkat.month-closing') }}"
                            class="bg-gradient-to-r from-purple-400 to-purple-600 text-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <h3 class="font-bold text-lg">Tutup Bulan</h3>
                                    <p class="text-sm opacity-90">Lihat riwayat tutup bulan</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('perangkat.profile') }}"
                            class="bg-gradient-to-r from-orange-400 to-orange-600 text-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <div>
                                    <h3 class="font-bold text-lg">Profil</h3>
                                    <p class="text-sm opacity-90">Lihat dan edit profil</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Kehadiran Terbaru -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Kehadiran Terbaru</h3>
                            @if ($attendances->count() > 0)
                                <div class="space-y-3">
                                    @foreach ($attendances->take(5) as $attendance)
                                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                            <div>
                                                <p class="font-medium">{{ $attendance->waktu->format('d M Y') }}</p>
                                                <p class="text-sm text-gray-600">
                                                    {{ $attendance->waktu->format('H:i') }}</p>
                                            </div>
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                Hadir
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('perangkat.attendances') }}"
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">Lihat Semua →</a>
                                </div>
                            @else
                                <p class="text-gray-500">Belum ada data kehadiran.</p>
                            @endif
                        </div>

                        <!-- Gaji Terbaru -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Riwayat Gaji Terbaru</h3>
                            @if ($payrolls->count() > 0)
                                <div class="space-y-3">
                                    @foreach ($payrolls->take(3) as $payroll)
                                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                            <div>
                                                <p class="font-medium">{{ $payroll->payroll_date->format('M Y') }}</p>
                                                <p class="text-sm text-gray-600">Rp
                                                    {{ number_format($payroll->total_salary, 0, ',', '.') }}</p>
                                            </div>
                                            <span
                                                class="px-2 py-1 text-xs rounded-full
                                                @if ($payroll->payment_status == 'paid') bg-green-100 text-green-800
                                                @elseif($payroll->payment_status == 'pending') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800 @endif">
                                                @if ($payroll->payment_status == 'paid')
                                                    Dibayar
                                                @elseif($payroll->payment_status == 'pending')
                                                    Pending
                                                @else
                                                    Dibatalkan
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('perangkat.payrolls') }}"
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">Lihat Semua →</a>
                                </div>
                            @else
                                <p class="text-gray-500">Belum ada data gaji.</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
