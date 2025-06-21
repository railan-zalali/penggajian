<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Sistem Penggajian Perangkat Desa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <header class="bg-blue-600 text-white">
        <nav class="container mx-auto px-6 py-3 flex justify-between items-center">
            <div class="text-2xl font-bold">
                Sistem Penggajian Perangkat Desa
            </div>
            <div>
                <a href="{{ route('login') }}"
                    class="bg-white text-blue-600 py-2 px-4 rounded-lg font-semibold hover:bg-blue-100 transition duration-300">Login</a>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-12">
        <section class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Selamat Datang di Sistem Penggajian Perangkat Desa</h1>
            <p class="text-xl text-gray-600">Mengelola penggajian dengan efisien dan transparan</p>
        </section>

        <section class="grid md:grid-cols-3 gap-8 mb-12">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-600 mb-4 mx-auto" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Penggajian Akurat</h2>
                <p class="text-gray-600">Perhitungan gaji yang tepat dan transparan untuk setiap anggota Perangkat Desa.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-600 mb-4 mx-auto" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Manajemen Absensi</h2>
                <p class="text-gray-600">Pencatatan kehadiran yang mudah dan efisien untuk memastikan akurasi
                    penggajian.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-600 mb-4 mx-auto" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Laporan Komprehensif</h2>
                <p class="text-gray-600">Generasi laporan yang detail untuk memudahkan pengambilan keputusan.</p>
            </div>
        </section>

        <section class="text-center">
            <a href="{{ route('login') }}"
                class="bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold text-lg hover:bg-blue-700 transition duration-300">Mulai
                Sekarang</a>
        </section>
    </main>

    <footer class="bg-gray-800 text-white py-6">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; {{ date('Y') }} Sistem Penggajian Perangkat Desa. Hak Cipta Dilindungi.</p>
        </div>
    </footer>
</body>

</html>