<x-app-layout>
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Edit Perangkat Desa</h1>
            <a href="{{ route('perangkat.index') }}"
                class="bg-gray-500 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-600 transition duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('perangkat.update', $perangkat->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- NIK -->
                    <div>
                        <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nik" id="nik" value="{{ old('nik', $perangkat->nik) }}"
                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('nik') border-red-500 @enderror bg-gray-100"
                            placeholder="Nomor Induk Kependudukan" readonly>
                        @error('nik')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama -->
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama', $perangkat->nama) }}"
                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('nama') border-red-500 @enderror"
                            placeholder="Nama Lengkap" required>
                        @error('nama')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tempat Lahir -->
                    <div>
                        <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ old('tempat_lahir', $perangkat->tempat_lahir) }}"
                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('tempat_lahir') border-red-500 @enderror"
                            placeholder="Tempat Lahir" required>
                        @error('tempat_lahir')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', $perangkat->tanggal_lahir->format('Y-m-d')) }}"
                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('tanggal_lahir') border-red-500 @enderror"
                            required>
                        @error('tanggal_lahir')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Alamat -->
                    <div class="md:col-span-2">
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat <span
                                class="text-red-500">*</span></label>
                        <textarea name="alamat" id="alamat" rows="3"
                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('alamat') border-red-500 @enderror"
                            placeholder="Alamat Lengkap" required>{{ old('alamat', $perangkat->alamat) }}</textarea>
                        @error('alamat')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pendidikan -->
                    <div>
                        <label for="pendidikan" class="block text-sm font-medium text-gray-700 mb-1">Pendidikan <span
                                class="text-red-500">*</span></label>
                        <select name="pendidikan" id="pendidikan"
                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('pendidikan') border-red-500 @enderror"
                            required>
                            <option value="">Pilih Pendidikan</option>
                            @foreach ($pendidikanOptions as $pendidikan)
                                <option value="{{ $pendidikan }}" {{ old('pendidikan', $perangkat->pendidikan) == $pendidikan ? 'selected' : '' }}>
                                    {{ $pendidikan }}
                                </option>
                            @endforeach
                        </select>
                        @error('pendidikan')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pekerjaan -->
                    <div>
                        <label for="pekerjaan" class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="pekerjaan" id="pekerjaan" value="{{ old('pekerjaan', $perangkat->pekerjaan) }}"
                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('pekerjaan') border-red-500 @enderror"
                            placeholder="Pekerjaan" required>
                        @error('pekerjaan')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Posisi -->
                    <div>
                        <label for="posisi" class="block text-sm font-medium text-gray-700 mb-1">Posisi <span
                                class="text-red-500">*</span></label>
                        <select name="posisi" id="posisi"
                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('posisi') border-red-500 @enderror"
                            required>
                            <option value="">Pilih Posisi</option>
                            @foreach ($posisiOptions as $posisi)
                                <option value="{{ $posisi }}" {{ old('posisi', $perangkat->posisi) == $posisi ? 'selected' : '' }}>
                                    {{ $posisi }}
                                </option>
                            @endforeach
                        </select>
                        @error('posisi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Bergabung -->
                    <div>
                        <label for="tanggal_bergabung" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bergabung
                            <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_bergabung" id="tanggal_bergabung"
                            value="{{ old('tanggal_bergabung', $perangkat->tanggal_bergabung->format('Y-m-d')) }}"
                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('tanggal_bergabung') border-red-500 @enderror"
                            required>
                        @error('tanggal_bergabung')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span
                                class="text-red-500">*</span></label>
                        <select name="status" id="status"
                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('status') border-red-500 @enderror"
                            required>
                            <option value="aktif" {{ old('status', $perangkat->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak aktif" {{ old('status', $perangkat->status) == 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>