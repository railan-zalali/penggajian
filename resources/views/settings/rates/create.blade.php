<x-app-layout>
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Tambah Tarif Baru</h1>
            <a href="{{ route('settings.rates.index') }}"
                class="bg-gray-500 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-600 transition duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-800">Informasi Tarif</h2>
                <p class="text-sm text-gray-600 mt-1">Masukkan informasi tarif yang akan ditambahkan.</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 text-red-700 p-4 border-l-4 border-red-500">
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
                            <p class="text-sm font-medium">Ada beberapa kesalahan:</p>
                            <ul class="mt-1 text-sm list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('settings.rates.store') }}" method="POST" class="p-6">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Tarif</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required>
                    <p class="text-xs text-gray-500 mt-1">Contoh: Tarif Harian, Tarif Lembur, dll.</p>
                </div>

                <div class="mb-4">
                    <label for="key" class="block text-sm font-medium text-gray-700 mb-1">Kunci Tarif</label>
                    <input type="text" name="key" id="key" value="{{ old('key') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required>
                    <p class="text-xs text-gray-500 mt-1">Contoh: daily_rate, overtime_rate, dll. (tanpa spasi, gunakan underscore)</p>
                </div>

                <div class="mb-4">
                    <label for="value" class="block text-sm font-medium text-gray-700 mb-1">Nilai Tarif (Rp)</label>
                    <input type="number" name="value" id="value" value="{{ old('value') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required min="0">
                    <p class="text-xs text-gray-500 mt-1">Masukkan nilai dalam Rupiah tanpa titik atau koma.</p>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">{{ old('description') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Deskripsi singkat tentang kegunaan tarif ini (opsional).</p>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition duration-300">
                        Simpan Tarif
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>