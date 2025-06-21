<x-app-layout>
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold mb-6 text-gray-800">Detail Penggajian</h1>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold mb-4">{{ $payroll->linmas->nama }} ({{ $payroll->linmas->nik }})</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="font-medium text-gray-600">Total Hari Kerja:</p>
                    <p>{{ $payroll->total_days_present }}</p>
                </div>

                <div>
                    <p class="font-medium text-gray-600">Gaji Pokok:</p>
                    <p>Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</p>
                </div>

                <div>
                    <p class="font-medium text-gray-600">Lembur:</p>
                    <p>Rp {{ number_format($payroll->overtime_payment, 0, ',', '.') }}</p>
                </div>

                <div>
                    <p class="font-medium text-gray-600">Total Gaji:</p>
                    <p>Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</p>
                </div>

                <div>
                    <p class="font-medium text-gray-600">Tanggal Gaji:</p>
                    <p>{{ $payroll->payroll_date->format('d M Y') }}</p>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('payrolls.index') }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition duration-300 ease-in-out">
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
