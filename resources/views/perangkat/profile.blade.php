<x-app-layout>
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Profil Saya</h1>
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
                                            {{ $linmas->tanggal_lahir ? $linmas->tanggal_lahir->format('d M Y') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500">Jenis Kelamin:</td>
                                        <td class="py-3 text-sm text-gray-900">
                                            {{ $linmas->jenis_kelamin == 'L' ? 'Laki-laki' : ($linmas->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500">Agama:</td>
                                        <td class="py-3 text-sm text-gray-900">{{ $linmas->agama ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Kontak & Alamat</h3>
                                <table class="min-w-full">
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500 w-1/3">No. Telepon:</td>
                                        <td class="py-3 text-sm text-gray-900">{{ $linmas->no_telepon ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500">Email:</td>
                                        <td class="py-3 text-sm text-gray-900">{{ $linmas->email ?? '-' }}</td>
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
                                            @if($linmas->rt || $linmas->rw)
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
                                        <td class="py-3 text-sm text-gray-900">{{ $linmas->posisi ?? 'Perangkat Desa' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-500">Status:</td>
                                        <td class="py-3 text-sm">
                                            @if($linmas->status == 'aktif')
                                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
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
                                            {{ $linmas->tanggal_bergabung ? $linmas->tanggal_bergabung->format('d M Y') : '-' }}
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
                                            @if($linmas->tanggal_bergabung)
                                                {{ $linmas->tanggal_bergabung->diffForHumans(null, true) }}
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
                                    <div class="text-xl font-semibold text-green-600">{{ $attendanceStats['thisMonth'] }}</div>
                                    <div class="text-xs text-gray-500">Bulan Ini</div>
                                </div>
                                <div>
                                    <div class="text-xl font-semibold text-orange-600">{{ $attendanceStats['thisWeek'] }}</div>
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
                                    @if($payrollStats['lastPayroll'])
                                        Rp {{ number_format($payrollStats['lastPayroll']->total_salary, 0, ',', '.') }}
                                    @else
                                        Rp 0
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    @if($payrollStats['lastPayroll'])
                                        Gaji {{ $payrollStats['lastPayroll']->payroll_date->format('M Y') }}
                                    @else
                                        Belum ada gaji
                                    @endif
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-center">
                                <div>
                                    <div class="text-sm font-semibold text-yellow-600">{{ $payrollStats['pending'] }}</div>
                                    <div class="text-xs text-gray-500">Pending</div>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-blue-600">{{ $payrollStats['totalPeriods'] }}</div>
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
                            <button class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition duration-300 text-sm font-medium">
                                Ubah Password
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>