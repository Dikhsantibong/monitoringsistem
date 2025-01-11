@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.sidebar')

    <div id="main-content" class="flex-1 main-content">
        <header class="bg-white shadow-sm sticky top-0 z-10">
            <div class="flex justify-between items-center px-6 py-3">
                <h1 class="text-xl font-semibold text-gray-800">Tambah Unit Pembangkit</h1>
                @include('components.timer')
            </div>
        </header>

        <div class="p-6">
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6">
                    <form action="{{ route('admin.power-plants.store') }}" method="POST">
                        @csrf
                        
                        <!-- Nama Unit (Wajib) -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Unit <span class="text-red-500">*</span></label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Lokasi -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude <span class="text-red-500">*</span></label>
                                <input type="number" 
                                       step="any"
                                       name="latitude" 
                                       id="latitude" 
                                       value="0"
                                       required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude <span class="text-red-500">*</span></label>
                                <input type="number" 
                                       step="any"
                                       name="longitude" 
                                       id="longitude" 
                                       value="0"
                                       required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <!-- Tombol Submit dan Kembali -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.power-plants.index') }}" 
                               class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                                <i class="fas fa-arrow-left mr-2"></i>Kembali
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                <i class="fas fa-save mr-2"></i>Simpan Unit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 