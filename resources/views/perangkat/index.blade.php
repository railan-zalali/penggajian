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
                                    -
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Kehadiran Terbaru -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4">Kehadiran Terbaru</h2>
                        @if ($attendances->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="py-2 px-4 border-b text-left">Tanggal</th>
                                            <th class="py-2 px-4 border-b text-left">Waktu</th>
                                            <th class="py-2 px-4 border-b text-left">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($attendances as $attendance)
                                            <tr>
                                                <td class="py-2 px-4 border-b">
                                                    {{ \Carbon\Carbon::parse($attendance->waktu)->format('d M Y') }}
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    {{ \Carbon\Carbon::parse($attendance->waktu)->format('H:i') }}</td>
                                                <td class="py-2 px-4 border-b">
                                                    <span
                                                        class="px-2 py-1 rounded-full text-xs
                                                        @if ($attendance->status == 'C/Masuk') bg-green-100 text-green-800 
                                                        @elseif($attendance->status == 'C/Keluar') 
                                                            bg-red-100 text-red-800 @endif">
                                                        {{ $attendance->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('perangkat.attendances') }}"
                                    class="text-blue-600 hover:text-blue-800">Lihat semua kehadiran →</a>
                            </div>
                        @else
                            <p class="text-gray-500">Belum ada data kehadiran.</p>
                        @endif
                    </div>

                    <!-- Penggajian Terbaru -->
                    <div>
                        <h2 class="text-xl font-semibold mb-4">Penggajian Terbaru</h2>
                        @if ($payrolls->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="py-2 px-4 border-b text-left">Periode</th>
                                            <th class="py-2 px-4 border-b text-left">Total Gaji</th>
                                            <th class="py-2 px-4 border-b text-left">Status</th>
                                            <th class="py-2 px-4 border-b text-left">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($payrolls as $payroll)
                                            <tr>
                                                <td class="py-2 px-4 border-b">
                                                    {{ \Carbon\Carbon::parse($payroll->payroll_date)->format('M Y') }}
                                                </td>
                                                <td class="py-2 px-4 border-b">Rp
                                                    {{ number_format($payroll->total_salary, 0, ',', '.') }}</td>
                                                <td class="py-2 px-4 border-b">
                                                    <span
                                                        class="px-2 py-1 rounded-full text-xs
                                                        @if ($payroll->payment_status == 'paid') bg-green-100 text-green-800
                                                        @elseif($payroll->payment_status == 'pending') 
                                                            bg-yellow-100 text-yellow-800
                                                        @else 
                                                            bg-red-100 text-red-800 @endif">
                                                        {{ ucfirst($payroll->payment_status) }}
                                                    </span>
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    <a href="{{ route('perangkat.payroll-detail', $payroll->id) }}"
                                                        class="text-blue-600 hover:text-blue-800">Detail</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('perangkat.payrolls') }}"
                                    class="text-blue-600 hover:text-blue-800">Lihat semua penggajian →</a>
                            </div>
                        @else
                            <p class="text-gray-500">Belum ada data penggajian.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
