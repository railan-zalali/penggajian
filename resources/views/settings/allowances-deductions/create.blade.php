<x-app-layout>
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Tambah Tunjangan/Potongan Baru</h1>
            <a href="{{ route('settings.allowances-deductions.index') }}"
                class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                Kembali
            </a>
        </div>

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
                        <p class="text-sm font-medium">Terdapat beberapa kesalahan:</p>
                        <ul class="mt-1 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <form action="{{ route('settings.allowances-deductions.store') }}" method="POST" class="p-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            required>
                        <p class="mt-1 text-xs text-gray-500">Contoh: Tunjangan Transportasi, Potongan BPJS, dll.</p>
                    </div>

                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Kode</label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            required>
                        <p class="mt-1 text-xs text-gray-500">Kode unik, gunakan huruf kapital tanpa spasi. Contoh:
                            TRANS, BPJS_KES</p>
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                        <select name="type" id="type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="allowance" {{ old('type') == 'allowance' ? 'selected' : '' }}>Tunjangan
                            </option>
                            <option value="deduction" {{ old('type') == 'deduction' ? 'selected' : '' }}>Potongan
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="calculation_type" class="block text-sm font-medium text-gray-700 mb-1">Jenis
                            Perhitungan</label>
                        <select name="calculation_type" id="calculation_type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            required>
                            <option value="">-- Pilih Jenis Perhitungan --</option>
                            <option value="fixed" {{ old('calculation_type') == 'fixed' ? 'selected' : '' }}>Tetap
                                (Nilai Rupiah)</option>
                            <option value="percentage" {{ old('calculation_type') == 'percentage' ? 'selected' : '' }}>
                                Persentase (%)</option>
                        </select>
                    </div>

                    <div>
                        <label for="default_value" class="block text-sm font-medium text-gray-700 mb-1">Nilai
                            Default</label>
                        <input type="number" name="default_value" id="default_value"
                            value="{{ old('default_value', '0') }}" min="0" step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            required>
                        <p class="mt-1 text-xs text-gray-500">Untuk jenis perhitungan tetap, masukkan nilai rupiah.
                            Untuk persentase, masukkan nilai persen (tanpa tanda %).</p>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_taxable" value="1"
                                {{ old('is_taxable') ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Kena Pajak</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">Centang jika nilai ini diperhitungkan dalam perhitungan
                            pajak penghasilan.</p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition duration-300">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calculationTypeSelect = document.getElementById('calculation_type');
            const defaultValueInput = document.getElementById('default_value');
            const defaultValueHelper = defaultValueInput.nextElementSibling;

            calculationTypeSelect.addEventListener('change', function() {
                if (this.value === 'percentage') {
                    defaultValueHelper.textContent = 'Masukkan nilai persentase (contoh: 5 untuk 5%).';
                } else {
                    defaultValueHelper.textContent = 'Masukkan nilai rupiah.';
                }
            });
        });
    </script>
</x-app-layout>
