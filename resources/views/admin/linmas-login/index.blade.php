<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Akses Login Perangkat Desa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Error Message -->
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Perangkat Desa dengan Akses Login -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Perangkat Desa dengan Akses Login</h3>
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                            {{ $linmasWithLogin->total() }} Orang
                        </span>
                    </div>

                    @if($linmasWithLogin->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Password</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($linmasWithLogin as $linmas)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $linmas->nik }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $linmas->nama }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $linmas->email }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div class="flex items-center space-x-2">
                                                    <input type="password" 
                                                           id="password-{{ $linmas->id }}" 
                                                           value="{{ $linmas->password_plain ? '••••••••' : 'Tidak ada password' }}" 
                                                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-32 p-1" 
                                                           readonly>
                                                    @if($linmas->password_plain)
                                                        <button type="button" 
                                                                onclick="togglePassword('{{ $linmas->id }}', '{{ $linmas->password_plain }}')"
                                                                class="text-blue-600 hover:text-blue-900 text-sm">
                                                            <svg id="eye-icon-{{ $linmas->id }}" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                        </button>
                                                        <button type="button" 
                                                                onclick="generatePassword({{ $linmas->id }})" 
                                                                class="text-green-600 hover:text-green-900 text-xs px-2 py-1 border border-green-300 rounded"
                                                                title="Generate Password Baru">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                            </svg>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Aktif
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('admin.linmas-login.edit', $linmas) }}" 
                                                       class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                    
                                                    <form action="{{ route('admin.linmas-login.toggle', $linmas) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                                class="text-yellow-600 hover:text-yellow-900"
                                                                onclick="return confirm('Yakin ingin menonaktifkan akses login?')">
                                                            Nonaktifkan
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('admin.linmas-login.destroy', $linmas) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="text-red-600 hover:text-red-900"
                                                                onclick="return confirm('Yakin ingin menghapus akses login? Data email dan password akan dihapus.')">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $linmasWithLogin->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">Belum ada perangkat desa yang memiliki akses login.</p>
                    @endif
                </div>
            </div>

            <!-- Perangkat Desa tanpa Akses Login -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Perangkat Desa tanpa Akses Login</h3>
                        <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">
                            {{ $linmasWithoutLogin->total() }} Orang
                        </span>
                    </div>

                    @if($linmasWithoutLogin->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($linmasWithoutLogin as $linmas)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $linmas->nik }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $linmas->nama }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $linmas->jabatan }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Tidak Aktif
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('admin.linmas-login.create', $linmas) }}" 
                                                   class="text-green-600 hover:text-green-900">Berikan Akses</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $linmasWithoutLogin->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">Semua perangkat desa sudah memiliki akses login.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(linmasId, actualPassword) {
            const passwordInput = document.getElementById('password-' + linmasId);
            const eyeIcon = document.getElementById('eye-icon-' + linmasId);
            
            if (passwordInput.type === 'password') {
                // Show password
                passwordInput.type = 'text';
                passwordInput.value = actualPassword;
                // Change icon to eye-slash
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                `;
            } else {
                // Hide password
                passwordInput.type = 'password';
                passwordInput.value = '••••••••';
                // Change icon back to eye
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        }
        
        function generatePassword(id) {
            if (!confirm('Generate password baru? Password lama akan diganti.')) {
                return;
            }
            
            fetch(`/admin/linmas-login/generate-password/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const passwordInput = document.getElementById('password-' + id);
                    passwordInput.value = data.password;
                    alert('Password baru berhasil di-generate: ' + data.password);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat generate password.');
            });
        }
    </script>
</x-app-layout>