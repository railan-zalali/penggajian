<x-app-layout>
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold mb-6 text-gray-800">Pay Rates</h1>

        <a href="{{ route('pay-rates.create') }}"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
            Add New Pay Rate
        </a>

        <div class="overflow-x-auto bg-white rounded-lg shadow-md mt-4">
            <table class="min-w-full bg-white">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">NIK</th>
                        <th class="py-3 px-6 text-left">Name</th>
                        <th class="py-3 px-6 text-left">Hourly Rate</th>
                        <th class="py-3 px-6 text-left">Effective Date</th>
                        <th class="py-3 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @foreach ($payRates as $payRate)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">{{ $payRate->nik }}</td>
                            <td class="py-3 px-6 text-left">{{ $payRate->linmas->nama }}</td>
                            <td class="py-3 px-6 text-left">{{ $payRate->hourly_rate }}</td>
                            <td class="py-3 px-6 text-left">{{ $payRate->effective_date }}</td>
                            <td class="py-3 px-6 text-center">
                                <a href="{{ route('pay-rates.edit', $payRate->id) }}"
                                    class="text-blue-600 hover:text-blue-900 mr-2">Edit</a>
                                <form action="{{ route('pay-rates.destroy', $payRate->id) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Are you sure you want to delete this pay rate?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
