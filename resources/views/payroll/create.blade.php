<x-app-layout>
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold mb-6 text-gray-800">Tambah Penggajian</h1>

        <form action="{{ route('payrolls.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Pilih Linmas -->
                <div>
                    <label for="linmas_id" class="block text-sm font-medium text-gray-700">Pilih Linmas</label>
                    <select id="linmas_id" name="linmas_id" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">-- Pilih Perangkat Desa --</option>
                        @foreach ($linmasMembers as $linmas)
                            <option value="{{ $linmas->id }}">{{ $linmas->nama }} ({{ $linmas->nik }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Total Hari Kerja -->
                <div>
                    <label for="total_days_present" class="block text-sm font-medium text-gray-700">Total Hari
                        Kerja</label>
                    <input type="number" id="total_days_present" name="total_days_present" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <!-- Gaji Pokok -->
                <div>
                    <label for="base_salary" class="block text-sm font-medium text-gray-700">Gaji Pokok</label>
                    <input type="number" id="base_salary" name="base_salary" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <!-- Lembur -->
                <div>
                    <label for="overtime_payment" class="block text-sm font-medium text-gray-700">Lembur</label>
                    <input type="number" id="overtime_payment" name="overtime_payment"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>

            <div class="mt-6">
                <button type="submit"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 transition duration-300 ease-in-out">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
