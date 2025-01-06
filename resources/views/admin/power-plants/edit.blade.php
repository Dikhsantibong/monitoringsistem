@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    @include('components.sidebar')

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <div class="container mx-auto px-4 py-6">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Edit Unit Pembangkit</h2>
                    <a href="{{ route('admin.power-plants.index') }}" class="btn bg-gray-500 text-white hover:bg-gray-600 rounded-lg px-4 py-2">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>

                <form action="{{ route('admin.power-plants.update', $powerPlant->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Unit -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Unit</label>
                            <input type="text" name="name" id="name" 
                                   value="{{ old('name', $powerPlant->name) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Latitude -->
                        <div>
                            <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                            <input type="text" name="latitude" id="latitude" 
                                   value="{{ old('latitude', $powerPlant->latitude) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('latitude')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Longitude -->
                        <div>
                            <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                            <input type="text" name="longitude" id="longitude" 
                                   value="{{ old('longitude', $powerPlant->longitude) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('longitude')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="flex justify-end mt-6">
                        <button type="submit" class="btn bg-blue-500 text-white hover:bg-blue-600 rounded-lg px-6 py-2">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Validasi form sederhana
    document.querySelector('form').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value;
        const latitude = document.getElementById('latitude').value;
        const longitude = document.getElementById('longitude').value;

        if (!name || !latitude || !longitude) {
            e.preventDefault();
            alert('Semua field harus diisi');
        }
    });
</script>
@endpush
@endsection     