<x-app-layout>
    <div class="bg-gray-100 min-h-screen">
        <div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </span>
                </div>
            @endif
            <h1 class="text-4xl font-extrabold text-gray-900 mb-8 text-center">Penggajian Perangkat Desa</h1>

            <!-- Payroll filter form -->
            <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
                <form action="{{ route('payroll.calculate') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md transition duration-150 ease-in-out">
                                Hitung Gaji
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Payroll result -->
            @if (isset($payrollData))
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="p-6 bg-indigo-600 text-white flex justify-between items-center">
                        <h2 class="text-2xl font-bold">Periode: {{ $startDate->format('F Y') }} -
                            {{ $endDate->format('F Y') }}</h2>
                        <form action="{{ route('payroll.exportPdf') }}" method="POST">
                            @csrf
                            <input type="hidden" name="payroll_data" value="{{ json_encode($payrollData) }}">
                            <input type="hidden" name="start_date" value="{{ $startDate }}">
                            <input type="hidden" name="end_date" value="{{ $endDate }}">
                            <button type="submit"
                                class="bg-white text-indigo-600 hover:bg-indigo-100 font-bold py-2 px-4 rounded-md transition duration-150 ease-in-out">
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                            Rp {{ number_format($payroll['total_wage'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <form action="{{ route('payroll.exportSlip', $payroll['nik']) }}"
                                                method="POST">
                                                @csrf
                                                <input type="hidden" name="payroll_data"
                                                    value="{{ json_encode($payrollData) }}">
                                                <input type="hidden" name="start_date" value="{{ $startDate }}">
                                                <input type="hidden" name="end_date" value="{{ $endDate }}">
                                                <button type="submit"
                                                    class="text-indigo-600 hover:text-indigo-900">Cetak Slip
                                                    Gaji</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Form untuk menyimpan penggajian -->
                    <div class="p-6">
                        <form action="{{ route('payroll.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="payroll_data" value="{{ json_encode($payrollData) }}">
                            <input type="hidden" name="start_date" value="{{ $startDate }}">
                            <input type="hidden" name="end_date" value="{{ $endDate }}">
                            <button type="submit"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md transition duration-150 ease-in-out">
                                Simpan Penggajian
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action="{{ route('payroll.store') }}"]');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const endDate = document.querySelector('input[name="end_date"]').value;

                try {
                    const response = await fetch(`/api/check-payroll?date=${endDate}`);
                    const data = await response.json();

                    if (data.exists) {
                        alert(
                            'Data penggajian untuk bulan ini sudah ada. Tidak dapat menyimpan duplikat.'
                        );
                    } else {
                        form.submit();
                    }
                } catch (error) {
                    console.error('Error:', error);
                    form.submit();
                }
            });
        });
    </script>
</x-app-layout>
