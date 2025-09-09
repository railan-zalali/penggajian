<x-app-layout>
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Profil Saya</h1>
            <div class="flex gap-3">
                <button onclick="openEditModal()"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Edit Profil
                </button>
                <a href="{{ route('perangkat.dashboard') }}"
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <title>Close</title>
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                    </svg>
                </span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <title>Close</title>
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                    </svg>
                </span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informasi Profil -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-indigo-600 text-white">
                        <h2 class="text-xl font-semibold">Informasi Pribadi</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Data Diri</h3>
                                <table class="min-w-full">
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500 w-1/3">Nama Lengkap:</td>
                                        <td class="py-3 text-sm text-gray-900">{{ $linmas->nama }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500">NIK:</td>
                                        <td class="py-3 text-sm text-gray-900">{{ $linmas->nik }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500">Tempat Lahir:</td>
                                        <td class="py-3 text-sm text-gray-900">{{ $linmas->tempat_lahir ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500">Tanggal Lahir:</td>
                                        <td class="py-3 text-sm text-gray-900">
                                            {{ $linmas->tanggal_lahir ? \Carbon\Carbon::parse($linmas->tanggal_lahir)->format('d M Y') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500">Pendidikan:</td>
                                        <td class="py-3 text-sm text-gray-900">{{ $linmas->pendidikan ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500">Jabatan:</td>
                                        <td class="py-3 text-sm text-gray-900">{{ $linmas->jabatan ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500">Status:</td>
                                        <td class="py-3 text-sm text-gray-900">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                @if($linmas->status == 'aktif') bg-green-100 text-green-800
                                                @elseif($linmas->status == 'non-aktif') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($linmas->status ?? 'Tidak diketahui') }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Kontak & Alamat</h3>
                                <table class="min-w-full">
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500 w-1/3">Kontak:</td>
                                        <td class="py-3 text-sm text-gray-900">{{ $linmas->kontak ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500">Email:</td>
                                        <td class="py-3 text-sm text-gray-900">{{ $linmas->email ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500">Tanggal Bergabung:</td>
                                        <td class="py-3 text-sm text-gray-900">
                                            {{ $linmas->tanggal_bergabung ? \Carbon\Carbon::parse($linmas->tanggal_bergabung)->format('d M Y') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500 align-top">Alamat:</td>
                                        <td class="py-3 text-sm text-gray-900">
                                            {{ $linmas->alamat ?? '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500">RT/RW:</td>
                                        <td class="py-3 text-sm text-gray-900">
                                            @if ($linmas->rt || $linmas->rw)
                                                {{ $linmas->rt ?? '-' }}/{{ $linmas->rw ?? '-' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500">Kelurahan:</td>
                                        <td class="py-3 text-sm text-gray-900">{{ $linmas->kelurahan ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500">Kecamatan:</td>
                                        <td class="py-3 text-sm text-gray-900">{{ $linmas->kecamatan ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Pekerjaan -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mt-6">
                    <div class="px-6 py-4 bg-green-600 text-white">
                        <h2 class="text-xl font-semibold">Informasi Kepegawaian</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <table class="min-w-full">
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500 w-1/2">Posisi:</td>
                                        <td class="py-3 text-sm text-gray-900">
                                            {{ $linmas->posisi ?? 'Perangkat Desa' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500">Status:</td>
                                        <td class="py-3 text-sm">
                                            @if ($linmas->status == 'aktif')
                                                <span
                                                    class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                                    Tidak Aktif
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500">Tanggal Bergabung:</td>
                                        <td class="py-3 text-sm text-gray-900">
                                            {{ $linmas->tanggal_bergabung ? \Carbon\Carbon::parse($linmas->tanggal_bergabung)->format('d M Y') : '-' }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div>
                                <table class="min-w-full">
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500 w-1/2">Gaji Pokok:</td>
                                        <td class="py-3 text-sm text-gray-900">
                                            Rp {{ number_format($linmas->gaji_pokok ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500">Masa Kerja:</td>
                                        <td class="py-3 text-sm text-gray-900">
                                            @if ($linmas->tanggal_bergabung)
                                                {{ \Carbon\Carbon::parse($linmas->tanggal_bergabung)->diffForHumans(null, true) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik & Akun -->
            <div class="lg:col-span-1">
                <!-- Statistik Kehadiran -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-blue-600 text-white">
                        <h2 class="text-lg font-semibold">Statistik Kehadiran</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600">{{ $attendanceStats['total'] }}</div>
                                <div class="text-sm text-gray-500">Total Kehadiran</div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-center">
                                <div>
                                    <div class="text-xl font-semibold text-green-600">
                                        {{ $attendanceStats['thisMonth'] }}</div>
                                    <div class="text-xs text-gray-500">Bulan Ini</div>
                                </div>
                                <div>
                                    <div class="text-xl font-semibold text-orange-600">
                                        {{ $attendanceStats['thisWeek'] }}</div>
                                    <div class="text-xs text-gray-500">Minggu Ini</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistik Gaji -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mt-6">
                    <div class="px-6 py-4 bg-purple-600 text-white">
                        <h2 class="text-lg font-semibold">Statistik Gaji</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="text-center">
                                <div class="text-lg font-bold text-purple-600">
                                    Rp {{ number_format($payrollStats['totalReceived'], 0, ',', '.') }}
                                </div>
                                <div class="text-sm text-gray-500">Total Diterima</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-semibold text-green-600">
                                    @if ($payrollStats['lastPayroll'])
                                        Rp {{ number_format($payrollStats['lastPayroll']->total_salary, 0, ',', '.') }}
                                    @else
                                        Rp 0
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    @if ($payrollStats['lastPayroll'])
                                        Gaji {{ $payrollStats['lastPayroll']->payroll_date->format('M Y') }}
                                    @else
                                        Belum ada gaji
                                    @endif
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-center">
                                <div>
                                    <div class="text-sm font-semibold text-yellow-600">{{ $payrollStats['pending'] }}
                                    </div>
                                    <div class="text-xs text-gray-500">Pending</div>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-blue-600">
                                        {{ $payrollStats['totalPeriods'] }}</div>
                                    <div class="text-xs text-gray-500">Total Periode</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Akun -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mt-6">
                    <div class="px-6 py-4 bg-gray-600 text-white">
                        <h2 class="text-lg font-semibold">Informasi Akun</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Username:</span>
                                <span class="text-sm text-gray-900">{{ $linmas->nama }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Email:</span>
                                <span class="text-sm text-gray-900">{{ $linmas->email ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Role:</span>
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    Perangkat Desa
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Bergabung:</span>
                                <span class="text-sm text-gray-900">{{ $linmas->created_at->format('d M Y') }}</span>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <button onclick="openPasswordModal()"
                                class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition duration-300 text-sm font-medium">
                                Ubah Password
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Profil -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Edit Profil</h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form id="editProfileForm" method="POST" action="{{ route('perangkat.profile.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ $linmas->nama }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ $linmas->email }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kontak</label>
                            <input type="text" name="kontak" value="{{ $linmas->kontak }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" value="{{ $linmas->tempat_lahir }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" value="{{ $linmas->tanggal_lahir ? \Carbon\Carbon::parse($linmas->tanggal_lahir)->format('Y-m-d') : '' }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan</label>
                            <input type="text" name="pendidikan" value="{{ $linmas->pendidikan }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                            <textarea name="alamat" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $linmas->alamat }}</textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-300">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition duration-300">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ubah Password -->
    <div id="passwordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Ubah Password</h3>
                    <button onclick="closePasswordModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form id="changePasswordForm" method="POST" action="{{ route('perangkat.password.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
                            <input type="password" name="current_password" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                            <input type="password" name="password" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closePasswordModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-300">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition duration-300">
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal() {
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function openPasswordModal() {
            document.getElementById('passwordModal').classList.remove('hidden');
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const editModal = document.getElementById('editModal');
            const passwordModal = document.getElementById('passwordModal');
            if (event.target == editModal) {
                closeEditModal();
            }
            if (event.target == passwordModal) {
                closePasswordModal();
            }
        }
    </script>
</x-perangkat-layout>
