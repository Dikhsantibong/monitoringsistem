@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.pemeliharaan-sidebar')
    <div class="flex-1 main-content">
        <header class="bg-white shadow-sm sticky top-0">
            <div class="flex justify-between items-center px-6 py-3">
                <h1 class="text-xl font-semibold text-gray-800">Detail WO Material (WMATL)</h1>
            </div>
        </header>
        <main class="px-6 pt-6">
            <div class="bg-white rounded-lg shadow p-6 w-full">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">ID WO (WONUM)</label>
                            <input type="text" value="{{ $workOrder->id }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Type WO</label>
                            <input type="text" value="{{ $workOrder->type }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Priority</label>
                            <input type="text" value="{{ $workOrder->priority }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Labor (Lead)</label>
                            <input type="text" value="{{ $workOrder->labor }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Unit (Location)</label>
                            <input type="text" value="{{ $workOrder->location }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Status</label>
                            <input type="text" value="{{ $workOrder->status }}" class="w-full px-3 py-2 border rounded-md bg-blue-50 text-blue-700 font-bold" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Deskripsi</label>
                            <textarea class="w-full px-3 py-2 border rounded-md bg-gray-100 h-24" readonly>{{ $workOrder->description }}</textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">Schedule Start</label>
                                <input type="text" value="{{ $workOrder->schedule_start ?? '-' }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">Schedule Finish</label>
                                <input type="text" value="{{ $workOrder->schedule_finish ?? '-' }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-start mt-6">
                    <a href="{{ route('pemeliharaan.wo-wmatl.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
