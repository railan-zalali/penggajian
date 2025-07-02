<x-app-layout>
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Dashboard Perangkat Desa</h1>
            <p class="text-gray-600 mt-2">Selamat datang, {{ $linmas->nama }} ({{ $linmas->nik }})</p>
        </div>

        <!-- Statistik Ringkas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Kehadiran Bulan Ini -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Kehadiran Bulan Ini</h3>
                        <p class="text-2xl font-bold text-blue-600">{{ $attendanceThisMonth }}</p>
                    </div>
                </div>
            </div>

            <!-- Gaji Terakhir -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Gaji Terakhir</h3>
                        @if($payrolls->count() > 0)
                            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($payrolls->first()->total_salary, 0, ',', '.') }}</p>
                        @else
                            <p class="text-2xl font-bold text-gray-400">Belum ada</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Status Pembayaran -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Status Gaji Terakhir</h3>
                        @if($payrolls->count() > 0)
                            @php
                                $status = $payrolls->first()->payment_status;
                                $statusColor = $status == 'paid' ? 'text-green-600' : ($status == 'pending' ? 'text-yellow-600' : 'text-red-600');
                                $statusText = $status == 'paid' ? 'Dibayar' : ($status == 'pending' ? 'Pending' : 'Dibatalkan');
                            @endphp
                            <p class="text-2xl font-bold {{ $statusColor }}">{{ $statusText }}</p>
                        @else
                            <p class="text-2xl font-bold text-gray-400">-</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Navigasi -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <a href="{{ route('perangkat.attendances') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-300 group">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 group-hover:bg-blue-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">Riwayat Kehadiran</h3>
                        <p class="text-sm text-gray-500">Lihat kehadiran Anda</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('perangkat.payrolls') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-300 group">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 group-hover:bg-green-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800 group-hover:text-green-600">Riwayat Gaji</h3>
                        <p class="text-sm text-gray-500">Lihat riwayat gaji Anda</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('perangkat.month-closing') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-300 group">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 group-hover:bg-purple-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800 group-hover:text-purple-600">Tutup Bulan</h3>
                        <p class="text-sm text-gray-500">Lihat tutup bulan</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('perangkat.profile') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-300 group">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-gray-100 text-gray-600 group-hover:bg-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800 group-hover:text-gray-600">Profil</h3>
                        <p class="text-sm text-gray-500">Lihat profil Anda</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Kehadiran Terbaru -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-blue-600 text-white">
                    <h2 class="text-xl font-semibold">Kehadiran Terbaru</h2>
                </div>
                <div class="p-6">
                    @if($attendances->count() > 0)
                        <div class="space-y-3">
                            @foreach($attendances->take(5) as $attendance)
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $attendance->waktu->format('d M Y') }}</p>
                                        <p class="text-sm text-gray-500">{{ $attendance->waktu->format('H:i') }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        Hadir
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('perangkat.attendances') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Lihat semua kehadiran →</a>
                        </div>
                    @else
                        <p class="text-gray-500">Belum ada data kehadiran.</p>
                    @endif
                </div>
            </div>

            <!-- Gaji Terbaru -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-green-600 text-white">
                    <h2 class="text-xl font-semibold">Riwayat Gaji Terbaru</h2>
                </div>
                <div class="p-6">
                    @if($payrolls->count() > 0)
                        <div class="space-y-3">
                            @foreach($payrolls->take(3) as $payroll)
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $payroll->payroll_date->format('M Y') }}</p>
                                        <p class="text-sm text-gray-500">Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</p>
                                    </div>
                                    @php
                                        $status = $payroll->payment_status;
                                        $statusColor = $status == 'paid' ? 'bg-green-100 text-green-800' : ($status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                                        $statusText = $status == 'paid' ? 'Dibayar' : ($status == 'pending' ? 'Pending' : 'Dibatalkan');
                                    @endphp
                                    <span class="px-2 py-1 text-xs rounded-full {{ $statusColor }}">
                                        {{ $statusText }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('perangkat.payrolls') }}" class="text-green-600 hover:text-green-800 text-sm font-medium">Lihat semua gaji →</a>
                        </div>
                    @else
                        <p class="text-gray-500">Belum ada data gaji.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
