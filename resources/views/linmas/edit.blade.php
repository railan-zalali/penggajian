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

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nama">Nama</label>
                <input name="nama"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="nama" type="text" value="{{ $linmas->nama }}">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nik">NIK</label>
                <input name="nik"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="nik" type="text" value="{{ $linmas->nik }}" readonly>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="tempat_lahir">Tempat Lahir</label>
                <input name="tempat_lahir"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="tempat_lahir" type="text" value="{{ $linmas->tempat_lahir }}">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="tanggal_lahir">Tanggal Lahir</label>
                <input name="tanggal_lahir"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="tanggal_lahir" type="date" value="{{ $linmas->tanggal_lahir }}">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="alamat">Alamat</label>
                <input name="alamat"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="alamat" type="text" value="{{ $linmas->alamat }}">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="pendidikan">Pendidikan</label>
                <input name="pendidikan"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="pendidikan" type="text" value="{{ $linmas->pendidikan }}">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="pekerjaan">Pekerjaan</label>
                <input name="pekerjaan"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="pekerjaan" type="text" value="{{ $linmas->pekerjaan }}">
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
        </form>
    </div>
</x-app-layout>
