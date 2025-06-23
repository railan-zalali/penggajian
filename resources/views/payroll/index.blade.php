<x-app-layout>
    <div class="bg-gray-100 min-h-screen">
        <div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow"
                    role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">Terjadi kesalahan:</p>
                            <ul class="mt-1 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
                <div class="p-6 bg-indigo-600 text-white">
                    <h1 class="text-3xl font-bold">Penggajian Perangkat Desa</h1>
                    <p class="mt-2">Hitung dan kelola gaji perangkat desa dengan mudah</p>
                </div>

                <!-- Payroll filter form -->
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold mb-4">Filter Periode Penggajian</h2>
                    <form action="{{ route('payroll.calculate') }}" method="POST"
                        class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @csrf
                        <div>
                            <label for="start_month" class="block text-sm font-medium text-gray-700 mb-1">Bulan
                                Awal</label>
                            <input type="month" name="start_month" id="start_month"
                                class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500"
                                required>
                        </div>
                        <div>
                            <label for="end_month" class="block text-sm font-medium text-gray-700 mb-1">Bulan
                                Akhir</label>
                            <input type="month" name="end_month" id="end_month"
                                class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500"
                                required>
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                        clip-rule="evenodd" />
                                </svg>
                                Hitung Gaji
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Links to related pages -->
                <div class="p-6 bg-gray-50">
                    <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
                        <a href="{{ route('payroll.history.index') }}"
                            class="inline-flex items-center text-indigo-600 hover:text-indigo-800 transition duration-150 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                    clip-rule="evenodd" />
                            </svg>
                            Lihat Riwayat Penggajian
                        </a>
                    </div>
                </div>
            </div>

            <!-- Payroll result -->
            @if (isset($payrollData))
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div
                        class="p-6 bg-indigo-600 text-white flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                        <h2 class="text-2xl font-bold">Periode: {{ $startDate->format('F Y') }} -
                            {{ $endDate->format('F Y') }}</h2>
                        <form action="{{ route('payroll.exportPdf') }}" method="POST">
                            @csrf
                            <input type="hidden" name="payroll_data" value="{{ json_encode($payrollData) }}">
                            <input type="hidden" name="start_date" value="{{ $startDate }}">
                            <input type="hidden" name="end_date" value="{{ $endDate }}">
                            <button type="submit"
                                class="bg-white text-indigo-600 hover:bg-indigo-100 font-bold py-2 px-4 rounded-md transition duration-150 ease-in-out flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                                        clip-rule="evenodd" />
                                </svg>
                                Cetak Laporan PDF
                            </button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        NIK</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Hari Masuk</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Lembur</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tunjangan</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Potongan</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Gaji</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($payrollData as $payroll)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $payroll['nik'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $payroll['nama'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $payroll['total_days_worked'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $payroll['total_overtime'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @php
                                                $totalAllowances = 0;
                                                if (isset($payroll['total_allowances'])) {
                                                    $totalAllowances = $payroll['total_allowances'];
                                                } elseif (isset($payroll['allowances'])) {
                                                    foreach ($payroll['allowances'] as $allowance) {
                                                        $totalAllowances += $allowance['value'];
                                                    }
                                                }
                                            @endphp
                                            @if ($totalAllowances > 0)
                                                <span class="text-green-600">Rp
                                                    {{ number_format($totalAllowances, 0, ',', '.') }}</span>
                                                <button type="button"
                                                    onclick="showAllowanceDetails('{{ $payroll['nik'] }}')"
                                                    class="ml-1 text-blue-500 hover:text-blue-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @php
                                                $totalDeductions = 0;
                                                if (isset($payroll['total_deductions'])) {
                                                    $totalDeductions = $payroll['total_deductions'];
                                                } elseif (isset($payroll['deductions'])) {
                                                    foreach ($payroll['deductions'] as $deduction) {
                                                        $totalDeductions += $deduction['value'];
                                                    }
                                                }
                                            @endphp
                                            @if ($totalDeductions > 0)
                                                <span class="text-red-600">Rp
                                                    {{ number_format($totalDeductions, 0, ',', '.') }}</span>
                                                <button type="button"
                                                    onclick="showDeductionDetails('{{ $payroll['nik'] }}')"
                                                    class="ml-1 text-blue-500 hover:text-blue-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                            Rp {{ number_format($payroll['total_wage'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <form action="{{ route('payroll.exportSlip', $payroll['nik']) }}"
                                                method="POST" class="inline-block">
                                                @csrf
                                                <input type="hidden" name="payroll_data"
                                                    value="{{ json_encode($payroll) }}">
                                                <input type="hidden" name="start_date" value="{{ $startDate }}">
                                                <input type="hidden" name="end_date" value="{{ $endDate }}">
                                                <button type="submit"
                                                    class="text-indigo-600 hover:text-indigo-900 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Cetak Slip Gaji
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Form untuk menyimpan penggajian -->
                    <div class="p-6 bg-gray-50">
                        <form action="{{ route('payroll.store') }}" method="POST" id="storePayrollForm">
                            @csrf
                            <input type="hidden" name="payroll_data" value="{{ json_encode($payrollData) }}">
                            <input type="hidden" name="start_date" value="{{ $startDate }}">
                            <input type="hidden" name="end_date" value="{{ $endDate }}">
                            <button type="submit"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-md transition duration-150 ease-in-out flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Simpan Data Penggajian
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                    <div class="flex flex-col items-center justify-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-indigo-400 mb-6" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Belum Ada Data Penggajian</h2>
                        <p class="text-gray-600 mb-6">Pilih periode penggajian di atas untuk mulai menghitung gaji
                            perangkat desa</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal for allowance details -->
    <div id="allowanceModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Rincian Tunjangan
                            </h3>
                            <div class="mt-4">
                                <div id="allowanceDetails" class="divide-y divide-gray-200">
                                    <!-- Content will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeModal('allowanceModal')"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for deduction details -->
    <div id="deductionModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Rincian Potongan
                            </h3>
                            <div class="mt-4">
                                <div id="deductionDetails" class="divide-y divide-gray-200">
                                    <!-- Content will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeModal('deductionModal')"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('storePayrollForm');
            if (form) {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const endDate = document.querySelector('input[name="end_date"]').value;

                    try {
                        const response = await fetch(`/api/check-payroll?date=${endDate}`);
                        const data = await response.json();

                        if (data.exists) {
                            if (confirm(
                                    'Data penggajian untuk bulan ini sudah ada. Apakah Anda ingin tetap menyimpan?'
                                )) {
                                form.appendChild(document.createElement('input')).setAttribute('name',
                                    'force_save');
                                form.submit();
                            }
                        } else {
                            form.submit();
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        form.submit();
                    }
                });
            }
        });

        // Store payroll data in a global variable for modals
        const payrollData = @json($payrollData ?? []);

        function showAllowanceDetails(nik) {
            const payroll = payrollData.find(p => p.nik === nik);
            const allowanceDetails = document.getElementById('allowanceDetails');
            allowanceDetails.innerHTML = '';

            if (payroll && payroll.allowances && payroll.allowances.length > 0) {
                payroll.allowances.forEach(allowance => {
                    const item = document.createElement('div');
                    item.className = 'py-3';
                    item.innerHTML = `
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-900">${allowance.name}</p>
                                <p class="text-xs text-gray-500">${allowance.code}</p>
                            </div>
                            <p class="text-sm font-medium text-green-600">Rp ${new Intl.NumberFormat('id-ID').format(allowance.value)}</p>
                        </div>
                    `;
                    allowanceDetails.appendChild(item);
                });
            } else {
                allowanceDetails.innerHTML =
                    '<p class="py-3 text-sm text-gray-500 text-center">Tidak ada data tunjangan</p>';
            }

            document.getElementById('allowanceModal').classList.remove('hidden');
        }

        function showDeductionDetails(nik) {
            const payroll = payrollData.find(p => p.nik === nik);
            const deductionDetails = document.getElementById('deductionDetails');
            deductionDetails.innerHTML = '';

            if (payroll && payroll.deductions && payroll.deductions.length > 0) {
                payroll.deductions.forEach(deduction => {
                    const item = document.createElement('div');
                    item.className = 'py-3';
                    item.innerHTML = `
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-900">${deduction.name}</p>
                                <p class="text-xs text-gray-500">${deduction.code}</p>
                            </div>
                            <p class="text-sm font-medium text-red-600">Rp ${new Intl.NumberFormat('id-ID').format(deduction.value)}</p>
                        </div>
                    `;
                    deductionDetails.appendChild(item);
                });
            } else {
                deductionDetails.innerHTML =
                    '<p class="py-3 text-sm text-gray-500 text-center">Tidak ada data potongan</p>';
            }

            document.getElementById('deductionModal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
    </script>
</x-app-layout>
