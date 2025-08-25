<x-perangkat-layout>
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Test Navigasi Perangkat</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h2 class="text-xl font-semibold text-blue-800 mb-3">Status Autentikasi</h2>
                    <div class="space-y-2 text-sm">
                        <div><strong>Auth Check:</strong> {{ Auth::check() ? '✅ Yes' : '❌ No' }}</div>
                        <div><strong>Perangkat Auth:</strong> {{ Auth::guard('perangkat')->check() ? '✅ Yes' : '❌ No' }}
                        </div>
                        <div><strong>Current User:</strong>
                            {{ Auth::guard('perangkat')->user() ? Auth::guard('perangkat')->user()->nama : 'None' }}
                        </div>
                        <div><strong>Current Route:</strong> {{ request()->route()->getName() ?? 'None' }}</div>
                    </div>
                </div>

                <div class="bg-green-50 p-4 rounded-lg">
                    <h2 class="text-xl font-semibold text-green-800 mb-3">Informasi User</h2>
                    @auth('perangkat')
                        @php $user = Auth::guard('perangkat')->user() @endphp
                        <div class="space-y-2 text-sm">
                            <div><strong>NIK:</strong> {{ $user->nik ?? 'N/A' }}</div>
                            <div><strong>Nama:</strong> {{ $user->nama ?? 'N/A' }}</div>
                            <div><strong>Posisi:</strong> {{ $user->posisi ?? 'N/A' }}</div>
                            <div><strong>Can Login:</strong> {{ $user->can_login ? '✅ Yes' : '❌ No' }}</div>
                            <div><strong>Email:</strong> {{ $user->email ?? 'N/A' }}</div>
                        </div>
                    @else
                        <div class="text-red-600">User tidak terautentikasi</div>
                    @endauth
                </div>
            </div>

            <div class="mt-8 bg-yellow-50 p-4 rounded-lg">
                <h2 class="text-xl font-semibold text-yellow-800 mb-3">Test Navigasi</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('perangkat.dashboard') }}"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-center">
                        Dashboard
                    </a>
                    <a href="{{ route('perangkat.attendances') }}"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-center">
                        Kehadiran
                    </a>
                    <a href="{{ route('perangkat.payrolls') }}"
                        class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 text-center">
                        Gaji
                    </a>
                    <a href="{{ route('perangkat.month-closing') }}"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-center">
                        Tutup Bulan
                    </a>
                    <a href="{{ route('perangkat.profile') }}"
                        class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 text-center">
                        Profil
                    </a>
                    <a href="{{ route('perangkat.login') }}"
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 text-center">
                        Login
                    </a>
                </div>
            </div>

            @if (config('app.debug'))
                <div class="mt-8 bg-red-50 p-4 rounded-lg">
                    <h2 class="text-xl font-semibold text-red-800 mb-3">Debug Information</h2>
                    <div class="text-sm">
                        <pre class="bg-white p-3 rounded overflow-x-auto">{{ print_r(session()->all(), true) }}</pre>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-perangkat-layout>
