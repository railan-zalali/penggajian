<x-app-layout>
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Detail Tutup Bulan</h1>
            <a href="{{ route('month-closing.index') }}"
                class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 bg-indigo-600 text-white">
                <h2 class="text-xl font-semibold">Informasi Tutup Bulan: {{ $monthClosing->formatted_period }}</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Detail Periode</h3>
                        <table class="min-w-full">
                            <tr>
                                <td class="py-2 text-sm text-gray-500 font-medium">Periode:</td>
                                <td class="py-2 text-sm text-gray-900">{{ $monthClosing->formatted_period }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-sm text-gray-500 font-medium">Tanggal Ditutup:</td>
                                <td class="py-2 text-sm text-gray-900">
                                    {{ $monthClosing->closing_date->format('d F Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-sm text-gray-500 font-medium">Status:</td>
                                <td class="py-2">
                                    @if ($monthClosing->status == 'closed')
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Ditutup
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Dibuka Kembali
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 text-sm text-gray-500 font-medium">Ditutup Oleh:</td>
                                <td class="py-2 text-sm text-gray-900">{{ $monthClosing->user->name }}</td>
                            </tr>
                        </table>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Ringkasan</h3>
                        <table class="min-w-full">
                            <tr>
                                <td class="py-2 text-sm text-gray-500 font-medium">Total Perangkat Desa:</td>
                                <td class="py-2 text-sm text-gray-900">{{ $monthClosing->total_linmas }} orang</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-sm text-gray-500 font-medium">Total Penggajian:</td>
                                <td class="py-2 text-sm text-gray-900">{{ $monthClosing->total_payrolls }} transaksi
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 text-sm text-gray-500 font-medium">Total Gaji:</td>
                                <td class="py-2 text-sm font-medium text-gray-900">Rp
                                    {{ number_format($monthClosing->total_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-sm text-gray-500 font-medium">Catatan:</td>
                                <td class="py-2 text-sm text-gray-900">{{ $monthClosing->notes ?: '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-indigo-600 text-white flex justify-between items-center">
                <h2 class="text-xl font-semibold">Data Penggajian</h2>
                <a href="{{ route('month-closing.generate-report', $monthClosing->id) }}"
                    class="bg-white text-indigo-600 hover:bg-indigo-100 px-3 py-1 rounded-full text-sm font-medium">
                    Download Laporan
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                NIK</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Hari Kerja</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Gaji Pokok</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lembur</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Gaji</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($payrolls as $payroll)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $payroll->linmas->nik ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $payroll->linmas->nama ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $payroll->total_days_present }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">Rp
                                        {{ number_format($payroll->base_salary, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">Rp
                                        {{ number_format($payroll->overtime_payment, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">Rp
                                        {{ number_format($payroll->total_salary, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($payroll->payment_status == 'paid')
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Dibayar
                                        </span>
                                    @elseif ($payroll->payment_status == 'pending')
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Dibatalkan
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                    Tidak ada data penggajian
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50 text-right">
                <div
                    class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium">
                    Total Gaji: Rp {{ number_format($monthClosing->total_amount, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
