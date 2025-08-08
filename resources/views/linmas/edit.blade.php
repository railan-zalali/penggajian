<x-app-layout>
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-4">Edit Data Perangkat Desa</h1>

        @if ($errors->any())
            <div class="bg-red-500 text-white p-4 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('linmas.update', $linmas->id) }}" method="POST"
            class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nik">NIK</label>
                    <input name="nik"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="nik" type="text" value="{{ $linmas->nik }}" readonly>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nama">Nama</label>
                    <input name="nama"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="nama" type="text" value="{{ $linmas->nama }}" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="tempat_lahir">Tempat Lahir</label>
                    <input name="tempat_lahir"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="tempat_lahir" type="text" value="{{ $linmas->tempat_lahir }}" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="tanggal_lahir">Tanggal Lahir</label>
                    <input name="tanggal_lahir"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="tanggal_lahir" type="date" value="{{ $linmas->tanggal_lahir }}" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="alamat">Alamat</label>
                    <input name="alamat"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="alamat" type="text" value="{{ $linmas->alamat }}" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="pendidikan">Pendidikan</label>
                    <select name="pendidikan"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="pendidikan" required>
                        <option value="">Pilih Pendidikan</option>
                        <option value="SD" {{ $linmas->pendidikan == 'SD' ? 'selected' : '' }}>SD</option>
                        <option value="SMP" {{ $linmas->pendidikan == 'SMP' ? 'selected' : '' }}>SMP</option>
                        <option value="SMA" {{ $linmas->pendidikan == 'SMA' ? 'selected' : '' }}>SMA</option>
                        <option value="SMK" {{ $linmas->pendidikan == 'SMK' ? 'selected' : '' }}>SMK</option>
                        <option value="D1" {{ $linmas->pendidikan == 'D1' ? 'selected' : '' }}>D1</option>
                        <option value="D2" {{ $linmas->pendidikan == 'D2' ? 'selected' : '' }}>D2</option>
                        <option value="D3" {{ $linmas->pendidikan == 'D3' ? 'selected' : '' }}>D3</option>
                        <option value="D4" {{ $linmas->pendidikan == 'D4' ? 'selected' : '' }}>D4</option>
                        <option value="S1" {{ $linmas->pendidikan == 'S1' ? 'selected' : '' }}>S1</option>
                        <option value="S2" {{ $linmas->pendidikan == 'S2' ? 'selected' : '' }}>S2</option>
                        <option value="S3" {{ $linmas->pendidikan == 'S3' ? 'selected' : '' }}>S3</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="pekerjaan">Pekerjaan</label>
                    <input name="pekerjaan"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="pekerjaan" type="text" value="{{ $linmas->pekerjaan }}" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="posisi">Posisi</label>
                    <input name="posisi"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="posisi" type="text" value="{{ $linmas->posisi ?? 'Perangkat Desa' }}">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="tanggal_bergabung">Tanggal Bergabung</label>
                    <input name="tanggal_bergabung"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="tanggal_bergabung" type="date" value="{{ $linmas->tanggal_bergabung ?? date('Y-m-d') }}">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="status">Status</label>
                    <select name="status"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="status">
                        <option value="aktif" {{ $linmas->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $linmas->status == 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="gaji_pokok">Gaji Pokok</label>
                    <input name="gaji_pokok"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="gaji_pokok" type="number" value="{{ $linmas->gaji_pokok ?? 0 }}">
                </div>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
        </form>
    </div>
</x-app-layout>
