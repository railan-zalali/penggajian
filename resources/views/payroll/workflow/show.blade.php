<x-app-layout>
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-800">Detail Penggajian</h1>
            <a href="{{ route('payroll.workflow.index') }}"
                class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                Kembali
            </a>
        </div>

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informasi Utama -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-indigo-600 text-white">
                        <h2 class="text-xl font-semibold">Informasi Penggajian</h2>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Perangkat Desa</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">
                                    {{ $payroll->linmas->nama ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">NIK</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">
                                    {{ $payroll->linmas->nik ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Periode</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">
                                    {{ $payroll->payroll_date->format('F Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total Hari Kerja</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $payroll->total_days_present }}
                                    hari</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gaji Pokok</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">Rp
                                    {{ number_format($payroll->base_salary, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Lembur</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">Rp
                                    {{ number_format($payroll->overtime_payment, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total Gaji</dt>
                                <dd class="mt-1 text-xl font-bold text-indigo-600">Rp
                                    {{ number_format($payroll->total_salary, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status Pembayaran</dt>
                                <dd class="mt-1">
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if ($payroll->payment_status == 'paid') bg-green-100 text-green-800
                                        @elseif($payroll->payment_status == 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($payroll->payment_status) }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Detail Komponen Gaji -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mt-6">
                    <div class="px-6 py-4 bg-indigo-600 text-white">
                        <h2 class="text-xl font-semibold">Komponen Gaji</h2>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Komponen
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jenis
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jumlah
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($payroll->details as $detail)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $detail->name }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if ($detail->type == 'base') bg-blue-100 text-blue-800
                                                    @elseif($detail->type == 'allowance') bg-green-100 text-green-800
                                                    @elseif($detail->type == 'deduction') bg-red-100 text-red-800
                                                    @elseif($detail->type == 'overtime') bg-yellow-100 text-yellow-800 @endif">
                                                    {{ ucfirst($detail->type) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <div
                                                    class="text-sm font-medium 
                                                    @if ($detail->type == 'deduction') text-red-600 @else text-gray-900 @endif">
                                                    {{ $detail->type == 'deduction' ? '-' : '' }}
                                                    Rp {{ number_format($detail->amount, 0, ',', '.') }}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3"
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                Tidak ada detail komponen gaji.
                                            </td>
                                        </tr>
                                    @endforelse
                                    <tr class="bg-gray-50">
                                        <td colspan="2" class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm font-bold text-gray-900">Total</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm font-bold text-gray-900">
                                                Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status & Workflow -->
            <div>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-indigo-600 text-white">
                        <h2 class="text-xl font-semibold">Status Proses</h2>
                    </div>
                    <div class="p-6">
                        <div class="mb-6">
                            <span
                                class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                @if ($payroll->processing_status == 'draft') bg-gray-100 text-gray-800
                                @elseif($payroll->processing_status == 'verified') bg-blue-100 text-blue-800
                                @elseif($payroll->processing_status == 'calculated') bg-indigo-100 text-indigo-800
                                @elseif($payroll->processing_status == 'approved') bg-purple-100 text-purple-800
                                @elseif($payroll->processing_status == 'processed') bg-yellow-100 text-yellow-800
                                @elseif($payroll->processing_status == 'completed') bg-green-100 text-green-800
                                @elseif($payroll->processing_status == 'rejected') bg-red-100 text-red-800 @endif">
                                {{ $payroll->status_text }}
                            </span>

                            @if ($payroll->status_notes)
                                <div class="mt-3 text-sm text-gray-600 bg-gray-50 p-3 rounded">
                                    <span class="font-semibold">Catatan:</span> {{ $payroll->status_notes }}
                                </div>
                            @endif
                        </div>

                        <!-- Riwayat Status -->
                        <div class="mb-6">
                            <h3 class="text-md font-semibold text-gray-700 mb-3">Riwayat Status</h3>
                            <div class="space-y-4">
                                @if ($payroll->verified_by)
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-500 text-white">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-sm font-medium text-gray-900">Verifikasi</h4>
                                            <div class="mt-1 flex text-xs text-gray-500">
                                                <p>Oleh: {{ $payroll->verifier->name ?? 'N/A' }}</p>
                                                <span class="mx-1">•</span>
                                                <p>{{ $payroll->verified_at->format('d M Y H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($payroll->approved_by)
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="flex items-center justify-center h-8 w-8 rounded-full bg-purple-500 text-white">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-sm font-medium text-gray-900">Persetujuan</h4>
                                            <div class="mt-1 flex text-xs text-gray-500">
                                                <p>Oleh: {{ $payroll->approver->name ?? 'N/A' }}</p>
                                                <span class="mx-1">•</span>
                                                <p>{{ $payroll->approved_at->format('d M Y H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($payroll->payment_date && $payroll->payment_status == 'paid')
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="flex items-center justify-center h-8 w-8 rounded-full bg-green-500 text-white">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-sm font-medium text-gray-900">Pembayaran</h4>
                                            <div class="mt-1 flex text-xs text-gray-500">
                                                <p>{{ $payroll->payment_date->format('d M Y H:i') }}</p>
                                                @if ($payroll->payment_method)
                                                    <span class="mx-1">•</span>
                                                    <p>{{ $payroll->payment_method }}</p>
                                                @endif
                                                @if ($payroll->payment_reference)
                                                    <span class="mx-1">•</span>
                                                    <p>Ref: {{ $payroll->payment_reference }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Update Status Form -->
                        @if (count($validStatusTransitions) > 0)
                            <form action="{{ route('payroll.workflow.update-status', $payroll->id) }}" method="POST"
                                class="mt-6">
                                @csrf
                                @method('PUT')

                                <div class="mb-4">
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Update
                                        Status</label>
                                    <select name="status" id="status"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required>
                                        <option value="">-- Pilih Status --</option>
                                        @foreach ($validStatusTransitions as $status => $label)
                                            <option value="{{ $status }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="notes"
                                        class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                                    <textarea name="notes" id="notes" rows="3"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>

                                <button type="submit"
                                    class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition duration-300">
                                    Update Status
                                </button>
                            </form>
                        @else
                            <div class="mt-6 bg-gray-50 p-4 rounded text-sm text-gray-600">
                                Status penggajian ini tidak dapat diubah lagi. Status sudah final.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
