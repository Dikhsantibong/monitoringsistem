@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <!-- Sidebar -->
    @include('components.sidebar')

    <!-- Main Content -->
    <div id="main-content" class="flex-1 main-content">
        <header class="bg-white shadow-sm">
            <div class="flex justify-between items-center px-6 py-3">
                <div class="flex items-center gap-x-3">
                    <!-- Mobile Menu Toggle -->
                    <button id="mobile-menu-toggle"
                        class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#009BB9] hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    <!-- Menu Toggle Sidebar -->
                    <button id="desktop-menu-toggle"
                        class="hidden md:block relative items-center justify-center rounded-md text-gray-400 hover:bg-[#009BB9] p-2 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800">Edit WO Backlog</h1>
                </div>

                @include('components.timer')
                <div class="relative">
                    <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                        <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                            class="w-7 h-7 rounded-full mr-2">
                        <span class="text-gray-700 text-sm">{{ Auth::user()->name }}</span>
                        <i class="fas fa-caret-down ml-2 text-gray-600"></i>
                    </button>
                    <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                        <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="pt-2">
            <x-admin-breadcrumb :breadcrumbs="[
                ['name' => 'Laporan SR/WO', 'url' => route('admin.laporan.sr_wo')],
                ['name' => 'Edit WO Backlog', 'url' => null]
            ]" />
        </div>

        <main class="px-6">
            <!-- Konten Edit WO Backlog -->
            <div class="bg-white rounded-lg shadow p-6 sm:p-3">
                <div class="pt-2">
                    <h2 class="text-2xl font-bold mb-4">Edit Work Order (WO) Backlog</h2>
                    <form action="{{ route('admin.laporan.update-wo-backlog', $backlog->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Grid container untuk 2 kolom -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kolom Kiri -->
                            <div>
                                <div class="mb-4">
                                    <label for="no_wo" class="block text-gray-700 font-medium mb-2">No WO</label>
                                    <input type="text" name="no_wo" id="no_wo" 
                                        value="{{ $backlog->no_wo }}"
                                        class="w-full px-3 py-2 border rounded-md bg-gray-100"
                                        readonly>
                                </div>

                                <div class="mb-4">
                                    <label for="status" class="block text-gray-700 font-medium mb-2">Status</label>
                                    <select name="status" id="status" 
                                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                        {{ !$backlog->document_path && !old('document') ? 'disabled' : '' }}
                                        required>
                                        <option value="Open" {{ $backlog->status == 'Open' ? 'selected' : '' }}>Open</option>
                                        <option value="Closed" {{ $backlog->status == 'Closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                    @if(!$backlog->document_path)
                                    <p class="text-red-500 text-sm mt-1">*Upload dokumen terlebih dahulu sebelum mengubah status menjadi Closed</p>
                                    @endif
                                    <input type="hidden" name="status" value="{{ $backlog->status }}" id="hidden_status">
                                </div>

                                <div class="mb-4">
                                    <label for="deskripsi" class="block text-gray-700 font-medium mb-2">Deskripsi</label>
                                    <textarea name="deskripsi" id="deskripsi" 
                                        class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500 h-24" 
                                        required>{{ $backlog->deskripsi }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="keterangan" class="block text-gray-700 font-medium mb-2">Keterangan</label>
                                    <textarea name="keterangan" id="keterangan" 
                                        class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500 h-24">{{ $backlog->keterangan }}</textarea>
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div>
                                <div class="mb-4">
                                    <label for="kendala" class="block text-gray-700 font-medium mb-2">Kendala</label>
                                    <textarea name="kendala" id="kendala" 
                                        class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500 h-24">{{ $backlog->kendala }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="tindak_lanjut" class="block text-gray-700 font-medium mb-2">Tindak Lanjut</label>
                                    <textarea name="tindak_lanjut" id="tindak_lanjut" 
                                        class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500 h-24">{{ $backlog->tindak_lanjut }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="document" class="block text-gray-700 font-medium mb-2">Upload Dokumen</label>
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
                                        </div>

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
@endsection

<script src="{{ asset('js/toggle.js') }}"></script>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitButton = form.querySelector('button[type="submit"]');
    const statusSelect = document.getElementById('status');
    const hiddenStatus = document.getElementById('hidden_status');
    const documentInput = document.getElementById('document');
    let isSubmitting = false;

    // Check if document exists
    const hasDocument = {{ $backlog->document_path ? 'true' : 'false' }};
    
    // Enable/disable status based on document
    function updateStatusSelect() {
        const hasFile = documentInput.files.length > 0 || hasDocument;
        statusSelect.disabled = !hasFile;
        if (!hasFile && statusSelect.value === 'Closed') {
            statusSelect.value = 'Open';
        }
        // Update hidden status value
        hiddenStatus.value = statusSelect.value;
    }

    // Update hidden status when select changes
    statusSelect.addEventListener('change', function() {
        hiddenStatus.value = this.value;
    });

    documentInput.addEventListener('change', updateStatusSelect);
    updateStatusSelect();

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        if (isSubmitting) return;
        
        // Validate document requirement for Closed status
        if (statusSelect.value === 'Closed' && !hasDocument && !documentInput.files.length) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Anda harus mengupload dokumen sebelum mengubah status menjadi Closed'
            });
            return;
        }
        
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
                body: formData,
                redirect: 'follow'
            });
            
            if (response.redirected) {
                window.location.href = response.url;
                return;
            }
            
            const data = await response.json();
            
            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message || 'WO Backlog berhasil diupdate',
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
                text: error.message || 'Terjadi kesalahan saat update WO Backlog'
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
    const textareas = ['deskripsi', 'kendala', 'tindak_lanjut', 'keterangan'];
    textareas.forEach(id => {
        const textarea = document.getElementById(id);
        textarea.addEventListener('input', autoResize);
        autoResize.call(textarea);
    });

    // File Upload Preview
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
        updateStatusSelect();
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