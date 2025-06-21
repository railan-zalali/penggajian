<x-app-layout>

    @if (session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <form action="{{ route('attendance.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Import Data Kehadiran</button>
    </form>
</x-app-layout>
