<x-app-layout>
    <div class="container mx-auto mt-8">
        <h1 class="text-3xl font-bold mb-6 text-center">Hasil Perhitungan Gaji Perangkat Desa</h1>

        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <div class="min-w-full py-2">
                    <div class="shadow-md sm:rounded-lg">
                        <table class="min-w-full border-collapse block md:table">
                            <thead class="block md:table-header-group">
                                <tr class="border border-gray-300 block md:table-row">
                                    <th class="p-3 font-bold text-gray-700 text-left block md:table-cell">Nama Perangkat Desa
                                    </th>
                                    <th class="p-3 font-bold text-gray-700 text-left block md:table-cell">Total
                                        Kehadiran</th>
                                    <th class="p-3 font-bold text-gray-700 text-left block md:table-cell">Total Gaji
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="block md:table-row-group">
                                @foreach ($salaryData as $data)
                                    <tr class="border border-gray-300 block md:table-row">
                                        <td class="p-3 block md:table-cell">{{ $data['nama'] }}</td>
                                        <td class="p-3 block md:table-cell">{{ $data['total_kehadiran'] }}</td>
                                        <td class="p-3 block md:table-cell">Rp
                                            {{ number_format($data['total_gaji'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
