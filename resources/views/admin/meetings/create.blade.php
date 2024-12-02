@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <!-- Sidebar -->
    

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->

        <!-- Content -->
        <main class="p-6">
            <div class="bg-white rounded-lg shadow p-6">
                <form action="{{ route('admin.meetings.store') }}" method="POST" class="mt-4">
                    @csrf
                    <div class="mb-4">
                        <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Judul Rapat</label>
                        <input type="text" id="title" name="title" class="mt-1 block w-full rounded-lg border-2 border-gray-300 px-4 py-2" required>
                    </div>
                    <div class="mb-4">
                        <label for="scheduled_at" class="block text-gray-700 text-sm font-bold mb-2">Tanggal dan Waktu</label>
                        <input type="datetime-local" id="scheduled_at" name="scheduled_at" class="mt-1 block w-full rounded-lg border-2 border-gray-300 px-4 py-2" required>
                    </div>
                    <div class="mb-4">
                        <label for="duration" class="block text-gray-700 text-sm font-bold mb-2">Durasi (menit)</label>
                        <select id="duration" name="duration" class="mt-1 block w-full rounded-lg border-2 border-gray-300 px-4 py-2" required>
                            <option value="30">30 menit</option>
                            <option value="60">60 menit</option>
                            <option value="90">90 menit</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Jadwalkan Rapat</button>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection
