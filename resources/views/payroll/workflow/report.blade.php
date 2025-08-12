<x-app-layout>
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Laporan Status Alur Proses Penggajian</h1>
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

        <!-- Filter Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form action="{{ route('payroll.workflow.report') }}" method="GET" class="flex flex-wrap gap-4">
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
                
                <!-- Export Buttons -->
                <div class="flex items-end ml-auto gap-2">
                    <button type="submit" name="export" value="pdf"
                        class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition duration-300 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd" />
                        </svg>
                        Export PDF
                    </button>
                    <button type="submit" name="export" value="excel"
                        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition duration-300 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd" />
                        </svg>
                        Export Excel
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Period Info -->
        @if(isset($monthName) && isset($yearValue))
            <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded" role="alert">
                <p class="font-medium">Periode: {{ $monthName }} {{ $yearValue }}</p>
            </div>
        @elseif(isset($yearValue))
            <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded" role="alert">
                <p class="font-medium">Periode: Tahun {{ $yearValue }}</p>
            </div>
        @endif

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-2">Total Penggajian</h2>
                <p class="text-3xl font-bold text-indigo-600">{{ array_sum($summary) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-2">Menunggu Verifikasi</h2>
                <p class="text-3xl font-bold text-yellow-600">{{ $summary['draft'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-2">Dalam Proses</h2>
                <p class="text-3xl font-bold text-blue-600">{{ $summary['verified'] + $summary['calculated'] + $summary['approved'] + $summary['processed'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-2">Selesai</h2>
                <p class="text-3xl font-bold text-green-600">{{ $summary['completed'] }}</p>
            </div>
        </div>

        <!-- Status Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Pie Chart -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-indigo-600 text-white">
                    <h2 class="text-xl font-semibold">Distribusi Status</h2>
                </div>
                <div class="p-6">
                    <canvas id="statusChart" width="400" height="300"></canvas>
                </div>
            </div>

            <!-- Status Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-indigo-600 text-white">
                    <h2 class="text-xl font-semibold">Detail Status</h2>
                </div>
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jumlah
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Persentase
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $total = array_sum($summary);
                            @endphp
                            @foreach ($summary as $status => $count)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if ($status == 'draft') bg-gray-100 text-gray-800
                                            @elseif($status == 'verified') bg-blue-100 text-blue-800
                                            @elseif($status == 'calculated') bg-indigo-100 text-indigo-800
                                            @elseif($status == 'approved') bg-purple-100 text-purple-800
                                            @elseif($status == 'processed') bg-yellow-100 text-yellow-800
                                            @elseif($status == 'completed') bg-green-100 text-green-800
                                            @elseif($status == 'rejected') bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        {{ $count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        {{ $total > 0 ? number_format(($count / $total) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Detail Data Tabs -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="px-6 py-4 bg-indigo-600 text-white">
                <h2 class="text-xl font-semibold">Detail Data Penggajian</h2>
            </div>
            
            <!-- Tabs -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex" aria-label="Tabs">
                    @php
                        $statusLabels = [
                            'draft' => 'Draft',
                            'verified' => 'Terverifikasi',
                            'calculated' => 'Terhitung',
                            'approved' => 'Disetujui',
                            'processed' => 'Diproses',
                            'completed' => 'Selesai',
                            'rejected' => 'Ditolak'
                        ];
                    @endphp
                    
                    @foreach($statusLabels as $statusKey => $statusLabel)
                        <button type="button" onclick="showTab('{{ $statusKey }}')" 
                            class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm"
                            id="tab-{{ $statusKey }}"
                            aria-selected="false"
                            aria-controls="tab-panel-{{ $statusKey }}">
                            {{ $statusLabel }} ({{ $summary[$statusKey] }})
                        </button>
                    @endforeach
                </nav>
            </div>
            
            <!-- Tab Panels -->
            <div class="p-6">
                @foreach($statusLabels as $statusKey => $statusLabel)
                    <div id="tab-panel-{{ $statusKey }}" class="tab-panel hidden" role="tabpanel" aria-labelledby="tab-{{ $statusKey }}">
                        @if(isset($detailData[$statusKey]) && count($detailData[$statusKey]) > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perangkat Desa</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Gaji</th>
                                            
                                            @if(in_array($statusKey, ['verified', 'calculated']))
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diverifikasi Oleh</th>
                                            @endif
                                            
                                            @if(in_array($statusKey, ['approved', 'processed']))
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disetujui Oleh</th>
                                            @endif
                                            
                                            @if($statusKey === 'completed')
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode Pembayaran</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referensi</th>
                                            @endif
                                            
                                            @if($statusKey === 'rejected')
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                                            @endif
                                            
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($detailData[$statusKey] as $payroll)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payroll->payroll_date->format('d/m/Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $payroll->linmas->name ?? 'Data tidak tersedia' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payroll->linmas->nik ?? '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</td>
                                                
                                                @if(in_array($statusKey, ['verified', 'calculated']))
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payroll->verifier->name ?? '-' }}</td>
                                                @endif
                                                
                                                @if(in_array($statusKey, ['approved', 'processed']))
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payroll->approver->name ?? '-' }}</td>
                                                @endif
                                                
                                                @if($statusKey === 'completed')
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payroll->payment_method ?? '-' }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payroll->payment_reference ?? '-' }}</td>
                                                @endif
                                                
                                                @if($statusKey === 'rejected')
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payroll->status_notes ?? '-' }}</td>
                                                @endif
                                                
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <a href="{{ route('payroll.workflow.show', $payroll->id) }}" class="text-indigo-600 hover:text-indigo-900">Lihat Detail</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-gray-500">
                                Tidak ada data penggajian dengan status {{ $statusLabel }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize chart
                const ctx = document.getElementById('statusChart').getContext('2d');
                const statusChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: {!! json_encode($chartData['labels']) !!},
                        datasets: [{
                            data: {!! json_encode($chartData['data']) !!},
                            backgroundColor: [
                                '#9CA3AF', // draft - gray
                                '#3B82F6', // verified - blue
                                '#6366F1', // calculated - indigo
                                '#8B5CF6', // approved - purple
                                '#F59E0B', // processed - yellow
                                '#10B981', // completed - green
                                '#EF4444', // rejected - red
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'right',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
                
                // Show first tab with data by default
                const firstTabWithData = findFirstTabWithData();
                if (firstTabWithData) {
                    showTab(firstTabWithData);
                } else {
                    // If no data, show first tab
                    showTab('draft');
                }
            });
            
            function findFirstTabWithData() {
                const statusOrder = ['draft', 'verified', 'calculated', 'approved', 'processed', 'completed', 'rejected'];
                const summary = {!! json_encode($summary) !!};
                
                for (const status of statusOrder) {
                    if (summary[status] > 0) {
                        return status;
                    }
                }
                
                return null;
            }
            
            function showTab(tabId) {
                // Hide all tab panels
                document.querySelectorAll('.tab-panel').forEach(panel => {
                    panel.classList.add('hidden');
                });
                
                // Deactivate all tab buttons
                document.querySelectorAll('.tab-button').forEach(button => {
                    button.classList.remove('border-indigo-500', 'text-indigo-600');
                    button.classList.add('border-transparent', 'text-gray-500');
                    button.setAttribute('aria-selected', 'false');
                });
                
                // Show selected tab panel
                const panel = document.getElementById(`tab-panel-${tabId}`);
                if (panel) {
                    panel.classList.remove('hidden');
                }
                
                // Activate selected tab button
                const button = document.getElementById(`tab-${tabId}`);
                if (button) {
                    button.classList.remove('border-transparent', 'text-gray-500');
                    button.classList.add('border-indigo-500', 'text-indigo-600');
                    button.setAttribute('aria-selected', 'true');
                }
            }
        </script>
    @endpush
</x-app-layout>