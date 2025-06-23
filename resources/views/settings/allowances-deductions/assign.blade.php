<x-app-layout>
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Pengaturan Tunjangan & Potongan: {{ $linmas->nama }}</h1>
            <a href="{{ route('settings.allowances-deductions.linmas-assignments') }}"
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
                <h2 class="text-xl font-semibold">Informasi Perangkat Desa</h2>
            </div>
            <div class="p-6 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600">NIK</p>
                        <p class="text-lg font-medium">{{ $linmas->nik }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Nama</p>
                        <p class="text-lg font-medium">{{ $linmas->nama }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tempat, Tanggal Lahir</p>
                        <p class="text-lg font-medium">{{ $linmas->tempat_lahir }},
                            {{ $linmas->tanggal_lahir->format('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Alamat</p>
                        <p class="text-lg font-medium">{{ $linmas->alamat }}</p>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('settings.allowances-deductions.save-assignments', $linmas->id) }}" method="POST">
            @csrf

            <!-- Tunjangan -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="px-6 py-4 bg-green-600 text-white">
                    <h2 class="text-xl font-semibold">Tunjangan</h2>
                </div>
                <div class="p-6">
                    @if ($allowances->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-gray-600">Tidak ada tunjangan yang tersedia.</p>
                            <a href="{{ route('settings.allowances-deductions.create') }}"
                                class="mt-2 inline-block text-indigo-600 hover:text-indigo-800">
                                Tambah tunjangan baru
                            </a>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach ($allowances as $allowance)
                                @php
                                    $assignedValue = null;
                                    $isActive = false;

                                    // Cek apakah sudah di-assign
                                    $assigned = $assignedAllowances->firstWhere('type_id', $allowance->id);
                                    if ($assigned) {
                                        $assignedValue = $assigned->value;
                                        $isActive = $assigned->is_active;
                                    }
                                @endphp

                                <div
                                    class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition duration-150">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900">{{ $allowance->name }}</h3>
                                            <p class="text-sm text-gray-600">{{ $allowance->description }}</p>
                                            <div class="mt-1 flex items-center space-x-2">
                                                <span
                                                    class="px-2 py-1 text-xs rounded-full {{ $allowance->calculation_type == 'fixed' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                    {{ $allowance->calculation_type == 'fixed' ? 'Tetap' : 'Persentase' }}
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    Default:
                                                    @if ($allowance->calculation_type == 'fixed')
                                                        Rp {{ number_format($allowance->default_value, 0, ',', '.') }}
                                                    @else
                                                        {{ $allowance->default_value }}%
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <label class="inline-flex items-center mr-4">
                                                <input type="checkbox" name="allowance_active_{{ $allowance->id }}"
                                                    value="1"
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                    {{ $isActive ? 'checked' : '' }}>
                                                <span class="ml-2 text-sm text-gray-700">Aktif</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <label for="allowance_{{ $allowance->id }}"
                                            class="block text-sm font-medium text-gray-700">
                                            Nilai {{ $allowance->calculation_type == 'fixed' ? '(Rp)' : '(%)' }}
                                        </label>
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                            @if ($allowance->calculation_type == 'fixed')
                                                <span
                                                    class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                                    Rp
                                                </span>
                                            @endif
                                            <input type="number" name="allowance_{{ $allowance->id }}"
                                                id="allowance_{{ $allowance->id }}"
                                                value="{{ $assignedValue ?? $allowance->default_value }}"
                                                min="0"
                                                step="{{ $allowance->calculation_type == 'fixed' ? '1000' : '0.01' }}"
                                                class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none {{ $allowance->calculation_type == 'fixed' ? 'rounded-r-md' : 'rounded-md' }} focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300">
                                            @if ($allowance->calculation_type == 'percentage')
                                                <span
                                                    class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                                    %
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Potongan -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="px-6 py-4 bg-red-600 text-white">
                    <h2 class="text-xl font-semibold">Potongan</h2>
                </div>
                <div class="p-6">
                    @if ($deductions->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-gray-600">Tidak ada potongan yang tersedia.</p>
                            <a href="{{ route('settings.allowances-deductions.create') }}"
                                class="mt-2 inline-block text-indigo-600 hover:text-indigo-800">
                                Tambah potongan baru
                            </a>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach ($deductions as $deduction)
                                @php
                                    $assignedValue = null;
                                    $isActive = false;

                                    // Cek apakah sudah di-assign
                                    $assigned = $assignedDeductions->firstWhere('type_id', $deduction->id);
                                    if ($assigned) {
                                        $assignedValue = $assigned->value;
                                        $isActive = $assigned->is_active;
                                    }
                                @endphp

                                <div
                                    class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition duration-150">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900">{{ $deduction->name }}</h3>
                                            <p class="text-sm text-gray-600">{{ $deduction->description }}</p>
                                            <div class="mt-1 flex items-center space-x-2">
                                                <span
                                                    class="px-2 py-1 text-xs rounded-full {{ $deduction->calculation_type == 'fixed' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                    {{ $deduction->calculation_type == 'fixed' ? 'Tetap' : 'Persentase' }}
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    Default:
                                                    @if ($deduction->calculation_type == 'fixed')
                                                        Rp {{ number_format($deduction->default_value, 0, ',', '.') }}
                                                    @else
                                                        {{ $deduction->default_value }}%
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <label class="inline-flex items-center mr-4">
                                                <input type="checkbox" name="deduction_active_{{ $deduction->id }}"
                                                    value="1"
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                    {{ $isActive ? 'checked' : '' }}>
                                                <span class="ml-2 text-sm text-gray-700">Aktif</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <label for="deduction_{{ $deduction->id }}"
                                            class="block text-sm font-medium text-gray-700">
                                            Nilai {{ $deduction->calculation_type == 'fixed' ? '(Rp)' : '(%)' }}
                                        </label>
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                            @if ($deduction->calculation_type == 'fixed')
                                                <span
                                                    class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                                    Rp
                                                </span>
                                            @endif
                                            <input type="number" name="deduction_{{ $deduction->id }}"
                                                id="deduction_{{ $deduction->id }}"
                                                value="{{ $assignedValue ?? $deduction->default_value }}"
                                                min="0"
                                                step="{{ $deduction->calculation_type == 'fixed' ? '1000' : '0.01' }}"
                                                class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none {{ $deduction->calculation_type == 'fixed' ? 'rounded-r-md' : 'rounded-md' }} focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300">
                                            @if ($deduction->calculation_type == 'percentage')
                                                <span
                                                    class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                                    %
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-indigo-600 text-white px-6 py-3 rounded-lg shadow hover:bg-indigo-700 transition duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    Simpan Pengaturan
                </button>
            </div>
        </form>

        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Panduan Pengaturan</h2>
            <div class="text-gray-600">
                <ul class="list-disc list-inside space-y-2 mb-4 ml-4">
                    <li>Centang <strong>Aktif</strong> untuk mengaktifkan tunjangan/potongan untuk perangkat desa ini.
                    </li>
                    <li>Nilai pada kolom <strong>Nilai</strong> akan digunakan dalam perhitungan gaji:</li>
                    <ul class="list-disc list-inside ml-8 mt-2">
                        <li><strong>Tetap</strong>: Nilai dalam Rupiah yang akan ditambahkan/dikurangkan secara langsung
                            dari gaji.</li>
                        <li><strong>Persentase</strong>: Persentase dari gaji pokok yang akan ditambahkan/dikurangkan.
                        </li>
                    </ul>
                </ul>
                <p class="text-sm text-yellow-600 mt-4">
                    <strong>Catatan:</strong> Perubahan akan berlaku untuk penggajian yang dibuat setelah pengaturan ini
                    disimpan.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
