<x-app-layout>
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-3xl font-bold mb-6 text-gray-800">Tambah Data Perangkat Desa</h1>

            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                    <p class="font-bold">Oops! Ada beberapa kesalahan:</p>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('linmas.store') }}" method="POST"
                class="bg-white shadow-md rounded-lg px-8 pt-6 pb-8 mb-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="nama">Nama</label>
                        <input name="nama"
                            class="shadow appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300 transition duration-300"
                            id="nama" type="text" placeholder="Masukkan nama" value="{{ old('nama') }}"
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="nik">NIK</label>
                        <input name="nik"
                            class="shadow appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300 transition duration-300"
                            id="nik" type="number" placeholder="Masukkan NIK" value="{{ old('nik') }}"
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="tempat_lahir">Tempat
                            Lahir</label>
                        <input name="tempat_lahir"
                            class="shadow appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300 transition duration-300"
                            id="tempat_lahir" type="text" placeholder="Masukkan tempat lahir"
                            value="{{ old('tempat_lahir') }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="tanggal_lahir">Tanggal
                            Lahir</label>
                        <input name="tanggal_lahir"
                            class="shadow appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300 transition duration-300"
                            id="tanggal_lahir" type="date" value="{{ old('tanggal_lahir') }}" required>
                    </div>

                    <div class="mb-4 md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="alamat">Alamat</label>
                        <textarea name="alamat"
                            class="shadow appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300 transition duration-300"
                            id="alamat" placeholder="Masukkan alamat" rows="3" required>{{ old('alamat') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="pendidikan">Pendidikan</label>
                        <select name="pendidikan"
                            class="shadow appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300 transition duration-300"
                            id="pendidikan" required>
                            <option value="">Pilih Pendidikan</option>
                            <option value="SD" {{ old('pendidikan') == 'SD' ? 'selected' : '' }}>SD</option>
                            <option value="SMP" {{ old('pendidikan') == 'SMP' ? 'selected' : '' }}>SMP</option>
                            <option value="SMA" {{ old('pendidikan') == 'SMA' ? 'selected' : '' }}>SMA</option>
                            <option value="D3" {{ old('pendidikan') == 'D3' ? 'selected' : '' }}>D3</option>
                            <option value="S1" {{ old('pendidikan') == 'S1' ? 'selected' : '' }}>S1</option>
                            <option value="S2" {{ old('pendidikan') == 'S2' ? 'selected' : '' }}>S2</option>
                            <option value="S3" {{ old('pendidikan') == 'S3' ? 'selected' : '' }}>S3</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="pekerjaan">Pekerjaan</label>
                        <input name="pekerjaan"
                            class="shadow appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300 transition duration-300"
                            id="pekerjaan" type="text" placeholder="Masukkan pekerjaan"
                            value="{{ old('pekerjaan') }}" required>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-110">
                        Simpan Data
                    </button>
                    <a href="{{ route('linmas.index') }}"
                        class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800 transition duration-300">
                        Kembali ke Daftar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
