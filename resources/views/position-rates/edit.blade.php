<x-app-layout>
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">{{ __('Edit Tarif Gaji Jabatan') }}</h1>
            <a href="{{ route('settings.position-rates.index') }}"
                class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                {{ __('Kembali') }}
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
            <form method="POST" action="{{ route('settings.position-rates.update', $positionRate->id) }}"
                class="p-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Jabatan') }}
                            <span class="text-red-500">*</span></label>
                        <input type="text" id="position" name="position"
                            value="{{ old('position', $positionRate->position) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('position') border-red-500 @enderror"
                            required>
                        @error('position')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="monthly_rate"
                            class="block text-sm font-medium text-gray-700 mb-1">{{ __('Gaji Pokok Bulanan (Rp)') }} <span
                                class="text-red-500">*</span></label>
                        <input type="number" id="monthly_rate" name="monthly_rate"
                            value="{{ old('monthly_rate', $positionRate->monthly_rate) }}" min="0" step="1000"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('monthly_rate') border-red-500 @enderror"
                            required>
                        @error('monthly_rate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="working_days"
                            class="block text-sm font-medium text-gray-700 mb-1">{{ __('Jumlah Hari Kerja') }} <span
                                class="text-red-500">*</span></label>
                        <input type="number" id="working_days" name="working_days"
                            value="{{ old('working_days', $positionRate->working_days) }}" min="1" max="31" step="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('working_days') border-red-500 @enderror"
                            required>
                        @error('working_days')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Jumlah hari kerja dalam sebulan untuk menghitung tarif harian.</p>
                    </div>

                    <div>
                        <label for="is_active"
                            class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                        <div class="flex items-center mt-2">
                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $positionRate->is_active) ? 'checked' : '' }}
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-700">{{ __('Aktif') }}</label>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description"
                            class="block text-sm font-medium text-gray-700 mb-1">{{ __('Keterangan') }}</label>
                        <textarea id="description" name="description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror">{{ old('description', $positionRate->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition duration-300">
                        {{ __('Simpan Perubahan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
