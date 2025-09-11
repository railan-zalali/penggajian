<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Akses Login - ') . $linmas->nama }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Back Button -->
                    <div class="mb-6">
                        <a href="{{ route('admin.linmas-login.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            ← Kembali
                        </a>
                    </div>

                    <!-- Linmas Info -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Informasi Perangkat Desa</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">NIK</p>
                                <p class="font-medium">{{ $linmas->nik }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Nama</p>
                                <p class="font-medium">{{ $linmas->nama }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Pekerjaan</p>
                                <p class="font-medium">{{ $linmas->pekerjaan }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Status Login</p>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $linmas->can_login ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $linmas->can_login ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('admin.linmas-login.update', $linmas) }}">
                        @csrf
                        @method('PUT')

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input id="email" 
                                   type="email" 
                                   name="email" 
                                   value="{{ old('email', $linmas->email) }}" 
                                   required 
                                   autocomplete="email"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Current Password Display -->
                        @if($linmas->password_plain)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
                            <div class="relative">
                                <input type="text" 
                                       id="current_password_display" 
                                       value="{{ $linmas->password_plain }}" 
                                       readonly 
                                       class="mt-1 block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-700 sm:text-sm">
                                <button type="button" 
                                        onclick="toggleCurrentPassword()" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg id="eye-icon-current" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @endif

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                            <input id="password" 
                                   type="password" 
                                   name="password" 
                                   autocomplete="new-password"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('password') border-red-500 @enderror">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ingin mengubah password. Password minimal 8 karakter.</p>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                            <input id="password_confirmation" 
                                   type="password" 
                                   name="password_confirmation" 
                                   autocomplete="new-password"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        <!-- Can Login Status -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="can_login" 
                                       value="1" 
                                       {{ old('can_login', $linmas->can_login) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Aktifkan akses login</span>
                            </label>
                            <p class="mt-1 text-sm text-gray-500">Jika dinonaktifkan, pengguna tidak akan bisa login meskipun memiliki email dan password.</p>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Update Akses Login
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleCurrentPassword() {
            const passwordField = document.getElementById('current_password_display');
            const eyeIcon = document.getElementById('eye-icon-current');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                `;
            } else {
                passwordField.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }
        
        // Set initial state to password (hidden)
        document.addEventListener('DOMContentLoaded', function() {
            const passwordField = document.getElementById('current_password_display');
            if (passwordField) {
                passwordField.type = 'password';
            }
        });
    </script>
</x-app-layout>