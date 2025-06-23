<x-app-layout>
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <h1 class="text-4xl font-bold text-gray-800 mb-4 md:mb-0">Riwayat Penggajian</h1>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Filter</h2>
            <form action="{{ route('payroll.history') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="month_year" class="block text-sm font-medium text-gray-700 mb-1">Bulan & Tahun</label>
                    <select name="month_year" id="month_year"
                        class="w-full border border-gray-300 rounded-md shadow-sm p-2">
                        <option value="">Semua</option>
                        @foreach ($dates as $date)
                            @php
                                $monthYear = $date->year . '-' . str_pad($date->month, 2, '0', STR_PAD_LEFT);
                                $monthName = \Carbon\Carbon::createFromDate($date->year, $date->month, 1)->format(
                                    'F Y',
                                );
                                $isSelected = request('month') == $date->month && request('year') == $date->year;
                            @endphp
                            <option value="{{ $date->month }},{{ $date->year }}" {{ $isSelected ? 'selected' : '' }}>
                                {{ $monthName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran</label>
                    <select name="status" id="status"
                        class="w-full border border-gray-300 rounded-md shadow-sm p-2">
                        <option value="all">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Dibayar</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan
                        </option>
                    </select>
                </div>

                <div>
                    <label for="linmas_id" class="block text-sm font-medium text-gray-700 mb-1">Perangkat Desa</label>
                    <select name="linmas_id" id="linmas_id"
                        class="w-full border border-gray-300 rounded-md shadow-sm p-2">
                        <option value="all">Semua Perangkat Desa</option>
                        @foreach ($linmasOptions as $linmas)
                            <option value="{{ $linmas->id }}"
                                {{ request('linmas_id') == $linmas->id ? 'selected' : '' }}>
                                {{ $linmas->nama }} ({{ $linmas->nik }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold">Laporan Bulanan</h2>
                <p class="text-gray-600 mt-1">Download laporan gaji bulanan dalam format PDF</p>

                <form action="{{ route('payroll.monthly-report') }}" method="POST"
                    class="mt-4 flex items-end space-x-4">
                    @csrf
                    <div>
                        <label for="report_month" class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                        <select name="month" id="report_month" class="border border-gray-300 rounded-md shadow-sm p-2"
                            required>
                            @foreach (range(1, 12) as $month)
                                <option value="{{ $month }}" {{ date('n') == $month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromDate(null, $month, 1)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="report_year" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                        <select name="year" id="report_year" class="border border-gray-300 rounded-md shadow-sm p-2"
                            required>
                            @foreach (range(date('Y'), date('Y') - 5, -1) as $year)
                                <option value="{{ $year }}" {{ date('Y') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition duration-300 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Laporan
                    </button>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-500 text-white p-4 rounded-lg mb-6 shadow-md animate-fadeIn">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto bg-white rounded-lg shadow-md">
            <table class="min-w-full bg-white">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Tanggal</th>
                        <th class="py-3 px-6 text-left">Nama</th>
                        <th class="py-3 px-6 text-left">NIK</th>
                        <th class="py-3 px-6 text-right">Total Gaji</th>
                        <th class="py-3 px-6 text-center">Status</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($payrolls as $payroll)
                        <tr class="border-b border-gray-200 hover:bg-gray-100 transition duration-300 ease-in-out">
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                {{ $payroll->payroll_date->format('d M Y') }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $payroll->linmas->nama ?? 'N/A' }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $payroll->linmas->nik ?? 'N/A' }}
                            </td>
                            <td class="py-3 px-6 text-right font-medium">
                                Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-6 text-center">
                                @if ($payroll->payment_status == 'paid')
                                    <span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-xs">
                                        Dibayar
                                        @if ($payroll->payment_date)
                                            <span
                                                class="block text-xs mt-1">{{ $payroll->payment_date->format('d/m/Y H:i') }}</span>
                                        @endif
                                    </span>
                                @elseif($payroll->payment_status == 'pending')
                                    <span
                                        class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full text-xs">Pending</span>
                                @else
                                    <span
                                        class="px-2 py-1 bg-red-200 text-red-800 rounded-full text-xs">Dibatalkan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <!-- Indikator status -->
                                    @switch($payroll->processing_status)
                                        @case('draft')
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Draft
                                            </span>
                                        @break

                                        @case('verified')
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Terverifikasi
                                            </span>
                                        @break

                                        @case('calculated')
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Dihitung
                                            </span>
                                        @break

                                        @case('processed')
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Diproses
                                            </span>
                                        @break

                                        @case('completed')
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                Selesai
                                            </span>
                                        @break

                                        @default
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Tidak Diketahui
                                            </span>
                                        @break

                                        @case('rejected')
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Ditolak
                                            </span>
                                        @break
                                    @endswitch
                                </div>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center space-x-2">
                                    <button type="button" onclick="openPaymentModal({{ $payroll->id }})"
                                        class="bg-blue-500 text-white px-3 py-1 rounded-lg shadow hover:bg-blue-600 transition duration-300 ease-in-out transform hover:-translate-y-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <a href="{{ route('payroll.exportSlip', $payroll->linmas->nik) }}"
                                        onclick="event.preventDefault(); document.getElementById('export-slip-form-{{ $payroll->id }}').submit();"
                                        class="bg-green-500 text-white px-3 py-1 rounded-lg shadow hover:bg-green-600 transition duration-300 ease-in-out transform hover:-translate-y-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </a>

                                    <form id="export-slip-form-{{ $payroll->id }}"
                                        action="{{ route('payroll.exportSlip', $payroll->linmas->nik) }}"
                                        method="POST" class="hidden">
                                        @csrf
                                        <input type="hidden" name="start_date"
                                            value="{{ $payroll->payroll_date->startOfMonth() }}">
                                        <input type="hidden" name="end_date"
                                            value="{{ $payroll->payroll_date->endOfMonth() }}">
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr class="border-b border-gray-200">
                                <td class="py-3 px-6 text-center" colspan="6">
                                    <div class="flex flex-col items-center justify-center py-8">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-4"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <p class="text-gray-600 text-lg">Tidak ada data penggajian ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $payrolls->withQueryString()->links() }}
            </div>
        </div>

        <!-- Payment Status Modal -->
        <div id="paymentModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form id="paymentStatusForm" action="" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Update Status Pembayaran
                                    </h3>
                                    <div class="mt-4 space-y-4">
                                        <div>
                                            <label for="payment_status"
                                                class="block text-sm font-medium text-gray-700">Status</label>
                                            <select id="payment_status" name="payment_status"
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                                                <option value="pending">Pending</option>
                                                <option value="paid">Dibayar</option>
                                                <option value="cancelled">Dibatalkan</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="payment_method"
                                                class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                                            <input type="text" id="payment_method" name="payment_method"
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                                                placeholder="Transfer Bank, Tunai, dll">
                                        </div>
                                        <div>
                                            <label for="payment_reference"
                                                class="block text-sm font-medium text-gray-700">Referensi
                                                Pembayaran</label>
                                            <input type="text" id="payment_reference" name="payment_reference"
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                                                placeholder="No. Referensi, Kwitansi, dll">
                                        </div>
                                        <div>
                                            <label for="notes"
                                                class="block text-sm font-medium text-gray-700">Catatan</label>
                                            <textarea id="notes" name="notes" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                                                rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Simpan
                            </button>
                            <button type="button" onclick="closePaymentModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Handle month_year select
                const monthYearSelect = document.getElementById('month_year');
                monthYearSelect.addEventListener('change', function() {
                    const value = this.value;
                    if (value) {
                        const [month, year] = value.split(',');
                        const form = this.closest('form');
                        const monthInput = document.createElement('input');
                        monthInput.type = 'hidden';
                        monthInput.name = 'month';
                        monthInput.value = month;

                        const yearInput = document.createElement('input');
                        yearInput.type = 'hidden';
                        yearInput.name = 'year';
                        yearInput.value = year;

                        form.appendChild(monthInput);
                        form.appendChild(yearInput);
                    }
                });
            });

            function openPaymentModal(payrollId) {
                const modal = document.getElementById('paymentModal');
                const form = document.getElementById('paymentStatusForm');
                form.action = `/payroll/history/${payrollId}/status`;
                modal.classList.remove('hidden');
            }

            function closePaymentModal() {
                const modal = document.getElementById('paymentModal');
                modal.classList.add('hidden');
            }
        </script>
    </x-app-layout>
