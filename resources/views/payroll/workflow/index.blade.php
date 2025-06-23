<x-app-layout>
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Alur Proses Penggajian</h1>
            <a href="{{ route('payroll.workflow.report') }}"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition duration-300">
                Laporan Status
            </a>
        </div>

        <!-- Filter Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form action="{{ route('payroll.workflow.index') }}" method="GET" class="flex flex-wrap gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status"
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="all">Semua Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Terverifikasi
                        </option>
                        <option value="calculated" {{ request('status') == 'calculated' ? 'selected' : '' }}>Terhitung
                        </option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui
                        </option>
                        <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Diproses
                        </option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai
                        </option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak
                        </option>
                    </select>
                </div>

                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                    <select name="month" id="month"
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua Bulan</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $i, 1)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <select name="year" id="year"
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua Tahun</option>
                        @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                {{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Status Workflow Guide -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold mb-3">Panduan Status Alur Proses</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 bg-gray-400 rounded-full"></span>
                    <span class="text-sm">Draft: Data awal, belum diverifikasi</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                    <span class="text-sm">Terverifikasi: Data sudah diverifikasi</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 bg-indigo-500 rounded-full"></span>
                    <span class="text-sm">Terhitung: Perhitungan gaji selesai</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 bg-purple-500 rounded-full"></span>
                    <span class="text-sm">Disetujui: Disetujui untuk dibayar</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                    <span class="text-sm">Diproses: Proses pembayaran</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                    <span class="text-sm">Selesai: Selesai dibayar</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                    <span class="text-sm">Ditolak: Ditolak</span>
                </div>
            </div>
        </div>

        <!-- Payroll List -->
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Perangkat Desa
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total Gaji
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status Proses
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status Pembayaran
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($payrolls as $payroll)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $payroll->payroll_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $payroll->linmas->nama ?? 'N/A' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $payroll->linmas->nik ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if ($payroll->processing_status == 'draft') bg-gray-100 text-gray-800
                                    @elseif($payroll->processing_status == 'verified') bg-blue-100 text-blue-800
                                    @elseif($payroll->processing_status == 'calculated') bg-indigo-100 text-indigo-800
                                    @elseif($payroll->processing_status == 'approved') bg-purple-100 text-purple-800
                                    @elseif($payroll->processing_status == 'processed') bg-yellow-100 text-yellow-800
                                    @elseif($payroll->processing_status == 'completed') bg-green-100 text-green-800
                                    @elseif($payroll->processing_status == 'rejected') bg-red-100 text-red-800 @endif">
                                    {{ $payroll->status_text }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if ($payroll->payment_status == 'paid') bg-green-100 text-green-800
                                    @elseif($payroll->payment_status == 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($payroll->payment_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('payroll.workflow.show', $payroll->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Tidak ada data penggajian yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4">
                {{ $payrolls->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
