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
            <div class="bg-white rounded-lg shadow p-6 sm:p-3">
                <div class="pt-2">
                    <h2 class="text-2xl font-bold mb-4">Edit Work Order (WO)</h2>
                    <form id="editWoForm" action="{{ route('admin.laporan.update-wo', $workOrder->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- Grid container untuk 2 kolom -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kolom Kiri -->
                            <div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 font-medium mb-2">ID WO</label>
                                    <input type="text" value="{{ $workOrder->id }}" 
                                        class="w-full px-3 py-2 border rounded-md bg-gray-100" disabled>
                                </div>

                                <div class="mb-4">
                                    <label for="type" class="block text-gray-700 font-medium mb-2">Type WO</label>
                                    <select name="type" id="type" 
                                        class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                                        @foreach(['CM', 'PM', 'PDM', 'PAM', 'OH', 'EJ', 'EM'] as $type)
                                            <option value="{{ $type }}" {{ $workOrder->type == $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="priority" class="block text-gray-700 font-medium mb-2">Priority</label>
                                    <select name="priority" id="priority" 
                                        class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                                        @foreach(['emergency', 'normal', 'outage', 'urgent'] as $priority)
                                            <option value="{{ $priority }}" {{ $workOrder->priority == $priority ? 'selected' : '' }}>
                                                {{ ucfirst($priority) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="unit" class="block text-gray-700 font-medium mb-2">Unit</label>
                                    <select name="unit" id="unit" 
                                        class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                                        @foreach($powerPlants as $powerPlant)
                                            <option value="{{ $powerPlant->id }}" 
                                                    {{ $workOrder->power_plant_id == $powerPlant->id ? 'selected' : '' }}>
                                                {{ $powerPlant->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="mb-4">
                                        <label for="schedule_start" class="block text-gray-700 font-medium mb-2">Schedule Start</label>
                                        <input type="date" name="schedule_start" id="schedule_start" 
                                            value="{{ date('Y-m-d', strtotime($workOrder->schedule_start)) }}" 
                                            class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                                    </div>

                                    <div class="mb-4">
                                        <label for="schedule_finish" class="block text-gray-700 font-medium mb-2">Schedule Finish</label>
                                        <input type="date" name="schedule_finish" id="schedule_finish" 
                                            value="{{ date('Y-m-d', strtotime($workOrder->schedule_finish)) }}" 
                                            class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div>
                                <div class="mb-4">
                                    <label for="description" class="block text-gray-700 font-medium mb-2">Deskripsi</label>
                                    <textarea name="description" id="description" 
                                        class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500 h-24" required>{{ $workOrder->description }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="kendala" class="block text-gray-700 font-medium mb-2">Kendala</label>
                                    <textarea name="kendala" id="kendala" 
                                        class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500 h-24">{{ $workOrder->kendala }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="tindak_lanjut" class="block text-gray-700 font-medium mb-2">Tindak Lanjut</label>
                                    <textarea name="tindak_lanjut" id="tindak_lanjut" 
                                        class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500 h-24">{{ $workOrder->tindak_lanjut }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="document" class="block text-gray-700 font-medium mb-2">Upload Job Card</label>
                                    <div class="flex flex-col space-y-4">
                                        <!-- Custom File Upload -->
                                        <div class="relative">
                                            <input type="file" name="document" id="document" class="hidden"
                                                accept=".pdf,.doc,.docx,.xls,.xlsx">
                                            <label for="document" 
                                                class="flex items-center justify-center w-full p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all cursor-pointer group">
                                                <div class="flex flex-col items-center">
                                                    <i class="fas fa-cloud-upload-alt text-3xl mb-2 text-gray-400 group-hover:text-blue-500"></i>
                                                    <span class="text-gray-600 group-hover:text-blue-500">Klik atau seret file ke sini</span>
                                                    <span class="text-sm text-gray-500 mt-1">Format: PDF, DOC, DOCX, XLS, XLSX (Maks. 5MB)</span>
                                                </div>
                                            </label>
                                            <!-- File Preview -->
                                            <div id="filePreview" class="hidden mt-3 p-3 bg-gray-50 rounded-lg">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-file-alt text-blue-500 mr-2"></i>
                                                        <span id="fileName" class="text-sm text-gray-600"></span>
                                                    </div>
                                                    <button type="button" id="removeFile" class="text-red-500 hover:text-red-700">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Existing Document -->
                                        @if($workOrder->document_path)
                                        <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                                            <div class="flex-1 flex items-center">
                                                <i class="fas fa-file-alt text-blue-500 mr-2"></i>
                                                <span class="text-sm text-gray-600">Dokumen saat ini</span>
                                            </div>
                                            <a href="{{ $workOrder->document_url }}" 
                                               target="_blank"
                                               class="ml-4 inline-flex items-center px-3 py-1.5 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition-colors">
                                                <i class="fas fa-download mr-2"></i>
                                                Lihat Dokumen
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Submit dan Kembali -->
                        <div class="flex justify-end space-x-4 mt-6">
                            <a href="{{ route('admin.laporan.sr_wo') }}" 
                                class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                                <i class="fas fa-arrow-left mr-2"></i> Kembali
                            </a>
                            <button type="submit" 
                                class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors flex items-center">
                                <i class="fas fa-save mr-2"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
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
        autoResize.call(textarea);
    });

    // File Upload Preview
    const documentInput = document.getElementById('document');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const removeFile = document.getElementById('removeFile');

    documentInput.addEventListener('change', function(e) {
        const file = this.files[0];
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

        if (file) {
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Ukuran file tidak boleh lebih dari 5MB'
                });
                this.value = '';
                filePreview.classList.add('hidden');
                return;
            }

            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Format file tidak didukung'
                });
                this.value = '';
                filePreview.classList.add('hidden');
                return;
            }

            fileName.textContent = file.name;
            filePreview.classList.remove('hidden');
        } else {
            filePreview.classList.add('hidden');
        }
    });

    // Remove file button
    removeFile.addEventListener('click', function() {
        documentInput.value = '';
        filePreview.classList.add('hidden');
    });

    // Drag and drop support
    const dropZone = document.querySelector('label[for="document"]');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropZone.classList.add('border-blue-500', 'bg-blue-50');
    }

    function unhighlight(e) {
        dropZone.classList.remove('border-blue-500', 'bg-blue-50');
    }

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const file = dt.files[0];
        documentInput.files = dt.files;
        
        // Trigger change event manually
        const event = new Event('change');
        documentInput.dispatchEvent(event);
    }
});
</script>
@endpush
@endsection 