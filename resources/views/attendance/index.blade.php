<x-app-layout>
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <h1 class="text-4xl font-bold text-gray-800 mb-4 md:mb-0">Data Kehadiran</h1>
            <div class="flex space-x-2">
                <a href="{{ route('templates.attendance') }}"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 transition duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download Template
                </a>
                <a href="{{ route('month-closing.index') }}"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                            clip-rule="evenodd" />
                    </svg>
                    Tutup Bulan
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
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
                <table class="min-w-full bg-white" id="attendanceTable">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">No</th>
                            <th class="py-3 px-6 text-left">Nama</th>
                            <th class="py-3 px-6 text-left">NIK</th>
                            <th class="py-3 px-6 text-left">Waktu</th>
                            <th class="py-3 px-6 text-left">Status</th>
                            <th class="py-3 px-6 text-left">Status Baru</th>
                            <th class="py-3 px-6 text-left">Pengecualian</th>
                            <th class="py-3 px-6 text-left">Status Tutup</th>
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
                                <td class="py-3 px-6 text-left">
                                    <span
                                        class="px-2 py-1 rounded-full text-xs {{ $attendance->status == 'C/Masuk' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                        {{ $attendance->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-left">
                                    @if ($attendance->status_baru)
                                        <span class="px-2 py-1 rounded-full text-xs bg-blue-200 text-blue-800">
                                            {{ $attendance->status_baru }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-6 text-left">{{ $attendance->pengecualian }}</td>
                                <td class="py-3 px-6 text-left">
                                    @php
                                        $attendanceDate = \Carbon\Carbon::parse($attendance->waktu);
                                        $isClosed = \App\Models\MonthClosing::isMonthClosed($attendanceDate->year, $attendanceDate->month);
                                    @endphp
                                    @if ($isClosed)
                                        <span class="px-2 py-1 rounded-full text-xs bg-red-200 text-red-800">
                                            Ditutup
                                        </span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs bg-green-200 text-green-800">
                                            Terbuka
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-6 text-center">
                                    @if ($isClosed)
                                        <button type="button" disabled
                                            class="bg-gray-400 text-white px-3 py-1 rounded-lg shadow cursor-not-allowed"
                                            title="Data tidak dapat dihapus karena periode sudah ditutup">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    @else
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
                                    @endif
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

        $(document).ready(function() {
            $('#attendanceTable').DataTable({
                responsive: true,
                "paging": false,
                "info": false,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                }
            });
        });
    </script>
</x-app-layout>
