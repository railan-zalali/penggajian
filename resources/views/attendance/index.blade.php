<x-app-layout>
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold mb-6 text-gray-800">Data Kehadiran</h1>

        <div class="flex flex-col sm:flex-row justify-between mb-6 space-y-4 sm:space-y-0 sm:space-x-4">
            <form action="{{ route('attendances.import') }}" method="POST" enctype="multipart/form-data"
                class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-2">
                @csrf
                <div class="relative">
                    <input type="file" name="file" required
                        class="opacity-0 absolute inset-0 w-full h-full cursor-pointer" onchange="updateFileName(this)">
                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z"
                                clip-rule="evenodd" />
                        </svg>
                        <span id="file-name">Pilih file Excel</span>
                    </div>
                </div>
                <button type="submit"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 transition duration-300 ease-in-out transform hover:-translate-y-1 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    Upload Excel
                </button>
            </form>
        </div>

        <div class="mt-4">
            @if (session('success'))
                <div class="bg-green-500 text-white p-4 rounded-lg mb-6 shadow-md animate-fadeIn">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-500 text-white p-4 rounded-lg mb-6 shadow-md animate-fadeIn">
                    {{ session('error') }}
                </div>
            @endif

            <div class="overflow-x-auto bg-white rounded-lg shadow-md">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">No</th>
                            <th class="py-3 px-6 text-left">Nama</th>
                            <th class="py-3 px-6 text-left">NIK</th>
                            <th class="py-3 px-6 text-left">Waktu</th>
                            <th class="py-3 px-6 text-left">Status</th>
                            <th class="py-3 px-6 text-left">Status Baru</th>
                            <th class="py-3 px-6 text-left">Pengecualian</th>
                            <th class="py-3 px-6 text-left">Operasi</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        @foreach ($attendances as $index => $attendance)
                            <tr class="border-b border-gray-200 hover:bg-gray-100 transition duration-300 ease-in-out">
                                <td class="py-3 px-6 text-left whitespace-nowrap">{{ $index + 1 }}</td>
                                <td class="py-3 px-6 text-left">{{ $attendance->linmas->nama ?? 'N/A' }}</td>
                                <td class="py-3 px-6 text-left">{{ $attendance->linmas->nik ?? 'N/A' }}</td>
                                <td class="py-3 px-6 text-left">{{ $attendance->waktu }}</td>
                                <td class="py-3 px-6 text-left">{{ $attendance->status }}</td>
                                <td class="py-3 px-6 text-left">{{ $attendance->status_baru }}</td>
                                <td class="py-3 px-6 text-left">{{ $attendance->pengecualian }}</td>
                                <td class="py-3 px-6 text-left">{{ $attendance->operasi }}</td>
                                <td class="py-3 px-6 text-center">
                                    <form action="{{ route('attendances.destroy', $attendance->id) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-500 text-white px-3 py-1 rounded-lg shadow hover:bg-red-600 transition duration-300 ease-in-out transform hover:-translate-y-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>

    <script>
        function updateFileName(input) {
            const fileName = input.files[0]?.name || 'Pilih file Excel';
            document.getElementById('file-name').textContent = fileName;
        }
    </script>
</x-app-layout>
