@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.sidebar')

    <div id="main-content" class="flex-1 main-content">
        <header class="bg-white shadow-sm sticky top-0 z-20">
            <!-- ... header content ... -->
        </header>

        <div class="pt-2">
            <x-admin-breadcrumb :breadcrumbs="[
                ['name' => 'Laporan SR/WO', 'url' => route('admin.laporan.sr_wo')],
                ['name' => 'Edit WO', 'url' => null]
            ]" />
        </div>

        <main class="px-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold mb-4">Edit Work Order (WO)</h2>
                
                <form id="editWoForm" action="{{ route('admin.laporan.update-wo', $workOrder->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700">ID WO</label>
                        <input type="text" value="{{ $workOrder->id }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" disabled>
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="block text-gray-700">Deskripsi</label>
                        <textarea name="description" id="description" class="w-full px-3 py-2 border rounded-md" required>{{ $workOrder->description }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="kendala" class="block text-gray-700">Kendala</label>
                        <textarea name="kendala" id="kendala" class="w-full px-3 py-2 border rounded-md">{{ $workOrder->kendala ?? '' }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="tindak_lanjut" class="block text-gray-700">Tindak Lanjut</label>
                        <textarea name="tindak_lanjut" id="tindak_lanjut" class="w-full px-3 py-2 border rounded-md">{{ $workOrder->tindak_lanjut ?? '' }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="type" class="block text-gray-700">Type WO</label>
                        <select name="type" id="type" class="w-full px-3 py-2 border rounded-md" required>
                            @foreach(['CM', 'PM', 'PDM', 'PAM', 'OH', 'EJ', 'EM'] as $type)
                                <option value="{{ $type }}" {{ $workOrder->type == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="priority" class="block text-gray-700">Priority</label>
                        <select name="priority" id="priority" class="w-full px-3 py-2 border rounded-md" required>
                            @foreach(['emergency', 'normal', 'outage', 'urgent'] as $priority)
                                <option value="{{ $priority }}" {{ $workOrder->priority == $priority ? 'selected' : '' }}>
                                    {{ ucfirst($priority) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="schedule_start" class="block text-gray-700">Schedule Start</label>
                        <input type="date" name="schedule_start" id="schedule_start" 
                               value="{{ date('Y-m-d', strtotime($workOrder->schedule_start)) }}" 
                               class="w-full px-3 py-2 border rounded-md" required>
                    </div>

                    <div class="mb-4">
                        <label for="schedule_finish" class="block text-gray-700">Schedule Finish</label>
                        <input type="date" name="schedule_finish" id="schedule_finish" 
                               value="{{ date('Y-m-d', strtotime($workOrder->schedule_finish)) }}" 
                               class="w-full px-3 py-2 border rounded-md" required>
                    </div>

                    <div class="mb-4">
                        <label for="unit" class="block text-gray-700">Unit</label>
                        <select name="unit" id="unit" class="w-full px-3 py-2 border rounded-md" required>
                            @foreach($powerPlants as $powerPlant)
                                <option value="{{ $powerPlant->id }}" 
                                        {{ $workOrder->power_plant_id == $powerPlant->id ? 'selected' : '' }}>
                                    {{ $powerPlant->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('admin.laporan.sr_wo') }}" 
                           class="bg-gray-500 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </a>
                        <button type="submit" 
                                class="bg-blue-500 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="fas fa-save mr-2"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editWoForm');
    const submitButton = form.querySelector('button[type="submit"]');
    let isSubmitting = false;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        if (isSubmitting) return;
        
        try {
            isSubmitting = true;
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan...
            `;
            
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message || 'WO berhasil diupdate',
                    showConfirmButton: false,
                    timer: 1500
                });
                window.location.href = '{{ route("admin.laporan.sr_wo") }}';
            } else {
                throw new Error(data.message || 'Terjadi kesalahan saat update data');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: error.message || 'Terjadi kesalahan saat update WO'
            });
        } finally {
            isSubmitting = false;
            submitButton.disabled = false;
            submitButton.innerHTML = `<i class="fas fa-save mr-2"></i> Simpan`;
        }
    });

    // Fungsi untuk menyesuaikan tinggi textarea
    function autoResize() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    }
    
    // Terapkan autoResize ke semua textarea
    const textareas = ['description', 'kendala', 'tindak_lanjut'];
    textareas.forEach(id => {
        const textarea = document.getElementById(id);
        textarea.addEventListener('input', autoResize);
        // Trigger sekali saat halaman dimuat untuk menyesuaikan dengan konten awal
        autoResize.call(textarea);
    });
});
</script>
@endpush
@endsection 