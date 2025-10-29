<x-app-layout>
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Detail Penggajian</h1>
            <a href="{{ route('perangkat.payrolls') }}"
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
            <!-- Informasi Utama -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-indigo-600 text-white">
                        <h2 class="text-xl font-semibold">Informasi Penggajian</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Data Perangkat</h3>
                                <table class="min-w-full">
                                    <tr>
                                        <td class="py-2 text-sm font-medium text-gray-500">Nama:</td>
                                        <td class="py-2 text-sm text-gray-900">{{ $payroll->linmas->nama }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-sm font-medium text-gray-500">NIK:</td>
                                        <td class="py-2 text-sm text-gray-900">{{ $payroll->linmas->nik }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-sm font-medium text-gray-500">Periode:</td>
                                        <td class="py-2 text-sm text-gray-900">{{ $payroll->payroll_date->format('F Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-sm font-medium text-gray-500">Tanggal Dibuat:</td>
                                        <td class="py-2 text-sm text-gray-900">{{ $payroll->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Data Kehadiran</h3>
                                <table class="min-w-full">
                                    <tr>
                                        <td class="py-2 text-sm font-medium text-gray-500">Hari Kerja:</td>
                                        <td class="py-2 text-sm text-gray-900">{{ $payroll->working_days }} hari</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-sm font-medium text-gray-500">Kehadiran:</td>
                                        <td class="py-2 text-sm text-gray-900">{{ $payroll->attendance_days }} hari</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-sm font-medium text-gray-500">Persentase:</td>
                                        <td class="py-2 text-sm text-gray-900">
                                            {{ $payroll->working_days > 0 ? round(($payroll->attendance_days / $payroll->working_days) * 100, 1) : 0 }}%
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Perhitungan -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mt-6">
                    <div class="px-6 py-4 bg-green-600 text-white">
                        <h2 class="text-xl font-semibold">Detail Perhitungan</h2>
                    </div>
                    @if($details && $details->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Komponen
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tipe
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jumlah
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($details as $detail)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $detail->component_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($detail->component_type == 'allowance')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Tunjangan
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Potongan
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                                @if($detail->component_type == 'allowance')
                                                    <span class="text-green-600 font-medium">
                                                        +Rp {{ number_format($detail->amount, 0, ',', '.') }}
                                                    </span>
                                                @else
                                                    <span class="text-red-600 font-medium">
                                                        -Rp {{ number_format($detail->amount, 0, ',', '.') }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-6">
                            <div class="text-center py-8">
                                <div class="mx-auto h-12 w-12 text-gray-400">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada detail perhitungan</h3>
                                <p class="mt-1 text-sm text-gray-500">Gaji ini hanya terdiri dari gaji pokok tanpa tunjangan atau potongan tambahan.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Ringkasan -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-blue-600 text-white">
                        <h2 class="text-xl font-semibold">Ringkasan Gaji</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Gaji Pokok -->
                            <div class="flex justify-between items-center pb-2 border-b">
                                <span class="text-sm font-medium text-gray-500">Gaji Pokok:</span>
                                <span class="text-sm text-gray-900">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</span>
                            </div>
                            
                            <!-- Perhitungan Kehadiran -->
                            @if($payroll->working_days > 0 && $payroll->attendance_days != $payroll->working_days)
                                <div class="bg-yellow-50 p-3 rounded-lg">
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-gray-600">Gaji per hari:</span>
                                        <span class="text-gray-800">Rp {{ number_format($payroll->base_salary / $payroll->working_days, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs mt-1">
                                        <span class="text-gray-600">Potongan absen ({{ $payroll->working_days - $payroll->attendance_days }} hari):</span>
                                        <span class="text-red-600">-Rp {{ number_format(($payroll->base_salary / $payroll->working_days) * ($payroll->working_days - $payroll->attendance_days), 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm font-medium mt-2 pt-2 border-t border-yellow-200">
                                        <span class="text-gray-700">Gaji setelah potongan:</span>
                                        <span class="text-gray-900">Rp {{ number_format(($payroll->base_salary / $payroll->working_days) * $payroll->attendance_days, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Tunjangan -->
                            @if($details && $details->where('component_type', 'allowance')->count() > 0)
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <h5 class="text-sm font-medium text-green-800 mb-2">Tunjangan</h5>
                                    @foreach($details->where('component_type', 'allowance') as $allowance)
                                        <div class="flex justify-between items-center text-xs mb-1">
                                            <span class="text-green-700">{{ $allowance->component_name }}:</span>
                                            <span class="text-green-600">+Rp {{ number_format($allowance->amount, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                    <div class="flex justify-between items-center text-sm font-medium mt-2 pt-2 border-t border-green-200">
                                        <span class="text-green-800">Total Tunjangan:</span>
                                        <span class="text-green-600">+Rp {{ number_format($details->where('component_type', 'allowance')->sum('amount'), 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Potongan -->
                            @if($details && $details->where('component_type', 'deduction')->count() > 0)
                                <div class="bg-red-50 p-3 rounded-lg">
                                    <h5 class="text-sm font-medium text-red-800 mb-2">Potongan</h5>
                                    @foreach($details->where('component_type', 'deduction') as $deduction)
                                        <div class="flex justify-between items-center text-xs mb-1">
                                            <span class="text-red-700">{{ $deduction->component_name }}:</span>
                                            <span class="text-red-600">-Rp {{ number_format($deduction->amount, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                    <div class="flex justify-between items-center text-sm font-medium mt-2 pt-2 border-t border-red-200">
                                        <span class="text-red-800">Total Potongan:</span>
                                        <span class="text-red-600">-Rp {{ number_format($details->where('component_type', 'deduction')->sum('amount'), 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Perhitungan Akhir -->
                            <div class="bg-blue-50 p-3 rounded-lg">
                                @php
                                    $effectiveBaseSalary = $payroll->working_days > 0 ? ($payroll->base_salary / $payroll->working_days) * $payroll->attendance_days : $payroll->base_salary;
                                    $totalAllowances = $details ? $details->where('component_type', 'allowance')->sum('amount') : 0;
                                    $totalDeductions = $details ? $details->where('component_type', 'deduction')->sum('amount') : 0;
                                    $grossSalary = $effectiveBaseSalary + $totalAllowances;
                                    $netSalary = $grossSalary - $totalDeductions;
                                @endphp
                                
                                <div class="flex justify-between items-center text-sm mb-1">
                                    <span class="text-blue-700">Gaji Bruto:</span>
                                    <span class="text-blue-800">Rp {{ number_format($grossSalary, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm mb-2">
                                    <span class="text-blue-700">Total Potongan:</span>
                                    <span class="text-red-600">-Rp {{ number_format($totalDeductions, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center pt-2 border-t-2 border-blue-200">
                                    <span class="text-lg font-bold text-blue-900">Gaji Bersih:</span>
                                    <span class="text-lg font-bold text-blue-600">Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Status Pembayaran</h4>
                                @if($payroll->payment_status == 'paid')
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Dibayar
                                    </span>
                                    <div class="mt-3">
                                        @if($payroll->payment_confirmed)
                                            <div class="flex items-center text-sm text-green-700">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Pembayaran telah dikonfirmasi pada {{ optional($payroll->payment_confirmed_at)->format('d M Y H:i') }}.
                                            </div>
                                            @if($payroll->payment_confirmation_note)
                                                <p class="mt-1 text-xs text-gray-600">Catatan: {{ $payroll->payment_confirmation_note }}</p>
                                            @endif
                                        @else
                                            <form method="POST" action="{{ route('perangkat.payroll.confirm', $payroll) }}" class="space-y-2">
                                                @csrf
                                                <label class="block text-xs text-gray-700" for="note">Catatan (opsional)</label>
                                                <textarea name="note" id="note" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: sudah diterima tunai"></textarea>
                                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                    Konfirmasi Penerimaan
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @elseif($payroll->payment_status == 'pending')
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        Pending
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Dibatalkan
                                    </span>
                                @endif
                                
                                @if($payroll->payment_date)
                                    <p class="text-xs text-gray-500 mt-2">
                                        Dibayar pada: {{ $payroll->payment_date->format('d M Y H:i') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Tutup Bulan -->
                @if($payroll->monthClosing)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden mt-6">
                        <div class="px-6 py-4 bg-purple-600 text-white">
                            <h2 class="text-lg font-semibold">Tutup Bulan</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Periode:</span>
                                    <span class="text-sm text-gray-900">{{ $payroll->monthClosing->formatted_period }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Tanggal Tutup:</span>
                                    <span class="text-sm text-gray-900">{{ $payroll->monthClosing->closing_date->format('d M Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Status:</span>
                                    <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                                        {{ ucfirst($payroll->monthClosing->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Informasi Tambahan -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mt-6">
                    <div class="px-6 py-4 bg-gray-600 text-white">
                        <h2 class="text-lg font-semibold">Informasi Tambahan</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Riwayat Pembayaran -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Riwayat Pembayaran</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-600">Dibuat pada:</span>
                                        <span class="text-gray-900">{{ $payroll->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @if($payroll->payment_date)
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-600">Dibayar pada:</span>
                                            <span class="text-gray-900">{{ $payroll->payment_date->format('d/m/Y H:i') }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-600">Terakhir diupdate:</span>
                                        <span class="text-gray-900">{{ $payroll->updated_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Statistik Gaji -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Statistik Periode</h4>
                                <div class="space-y-2">
                                    @php
                                        $attendancePercentage = $payroll->working_days > 0 ? ($payroll->attendance_days / $payroll->working_days) * 100 : 0;
                                    @endphp
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-600">Tingkat Kehadiran:</span>
                                        <span class="text-gray-900 font-medium
                                            {{ $attendancePercentage >= 95 ? 'text-green-600' : ($attendancePercentage >= 80 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ number_format($attendancePercentage, 1) }}%
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-600">Efektivitas Gaji:</span>
                                        <span class="text-gray-900">{{ number_format(($payroll->total_salary / $payroll->base_salary) * 100, 1) }}%</span>
                                    </div>
                                    @if($details && $details->where('component_type', 'allowance')->count() > 0)
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-600">Bonus Tunjangan:</span>
                                            <span class="text-green-600">{{ number_format(($details->where('component_type', 'allowance')->sum('amount') / $payroll->base_salary) * 100, 1) }}%</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Catatan -->
                        @if($payroll->notes || $payroll->payment_status === 'cancelled')
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Catatan</h4>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    @if($payroll->notes)
                                        <p class="text-sm text-gray-700">{{ $payroll->notes }}</p>
                                    @elseif($payroll->payment_status === 'cancelled')
                                        <p class="text-sm text-red-600">Pembayaran gaji periode ini telah dibatalkan.</p>
                                    @else
                                        <p class="text-sm text-gray-500 italic">Tidak ada catatan khusus.</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>