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

        <!-- Ringkasan Pembayaran -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 bg-gray-800 text-white">
                <h2 class="text-xl font-semibold">Ringkasan Pembayaran</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-lg font-medium text-green-800">Dibayar</h3>
                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                {{ $payrolls->where('payment_status', 'paid')->count() }} transaksi
                            </span>
                        </div>
                        <p class="text-2xl font-bold text-green-700">
                            Rp {{ number_format($summary['paid'] ?? 0, 0, ',', '.') }}
                        </p>
                        <p class="text-sm text-green-600 mt-2">
                            {{ round((($summary['paid'] ?? 0) / $monthClosing->total_amount) * 100, 1) }}% dari total
                        </p>
                    </div>

                    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-100">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-lg font-medium text-yellow-800">Pending</h3>
                            <span
                                class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                {{ $payrolls->where('payment_status', 'pending')->count() }} transaksi
                            </span>
                        </div>
                        <p class="text-2xl font-bold text-yellow-700">
                            Rp {{ number_format($summary['pending'] ?? 0, 0, ',', '.') }}
                        </p>
                        <p class="text-sm text-yellow-600 mt-2">
                            {{ round((($summary['pending'] ?? 0) / $monthClosing->total_amount) * 100, 1) }}% dari total
                        </p>
                    </div>

                    <div class="bg-red-50 rounded-lg p-4 border border-red-100">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-lg font-medium text-red-800">Dibatalkan</h3>
                            <span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                {{ $payrolls->where('payment_status', 'cancelled')->count() }} transaksi
                            </span>
                        </div>
                        <p class="text-2xl font-bold text-red-700">
                            Rp {{ number_format($summary['cancelled'] ?? 0, 0, ',', '.') }}
                        </p>
                        <p class="text-sm text-red-600 mt-2">
                            {{ round((($summary['cancelled'] ?? 0) / $monthClosing->total_amount) * 100, 1) }}% dari
                            total
                        </p>
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
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button onclick="openPaymentModal({{ $payroll->id }})"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        Update Status
                                    </button>

                                    <a href="{{ route('payroll.exportSlip', $payroll->linmas->nik ?? '') }}"
                                        onclick="event.preventDefault(); document.getElementById('export-slip-form-{{ $payroll->id }}').submit();"
                                        class="text-green-600 hover:text-green-900">
                                        Slip Gaji
                                    </a>

                                    <form id="export-slip-form-{{ $payroll->id }}"
                                        action="{{ route('payroll.exportSlip', $payroll->linmas->nik ?? '') }}"
                                        method="POST" class="hidden">
                                        @csrf
                                        <input type="hidden" name="start_date"
                                            value="{{ $monthClosing->period->startOfMonth() }}">
                                        <input type="hidden" name="end_date"
                                            value="{{ $monthClosing->period->endOfMonth() }}">
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
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
