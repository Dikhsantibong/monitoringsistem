@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <x-sidebar />   

    <div class="flex-1 overflow-x-hidden overflow-y-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-20">
            <div class="flex justify-between items-center px-6 py-3">
                <div class="flex items-center gap-x-3">
                    <h1 class="text-xl font-semibold text-gray-800">Edit Pembahasan</h1>
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

        <!-- Breadcrumbs -->
        <div class="mt-3">
            <x-admin-breadcrumb :breadcrumbs="[
                ['name' => 'Pembahasan Lain-lain', 'url' => route('admin.other-discussions.index')],
                ['name' => 'Edit Pembahasan', 'url' => null]
            ]" />
        </div>

        <!-- Main Content -->
        <div class="container mx-auto px-6 py-8">
            <h3 class="text-gray-700 text-3xl font-medium">Edit Pembahasan</h3>

            <div class="mt-8">
                <form id="editDiscussionForm" action="{{ route('admin.other-discussions.update', $discussion->id) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Ganti bagian form upload dokumen dengan yang baru -->
                    <div class="mb-6 border-b pb-6">
                        <h4 class="text-lg font-semibold mb-4">Upload Dokumen Pendukung <span class="text-red-500">*</span></h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- File Upload dengan Drag & Drop -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="document">
                                    Dokumen (PDF/Word/Gambar)
                                </label>
                                <div 
                                    id="drop-zone"
                                    class="w-full min-h-[200px] px-3 py-2 border-2 border-dashed border-gray-300 rounded-md 
                                           hover:border-blue-500 transition-colors duration-200 ease-in-out
                                           flex flex-col items-center justify-center cursor-pointer bg-gray-50">
                                    <input type="file" 
                                           name="document" 
                                           id="document" 
                                           class="hidden"
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                           required>
                                    <div class="text-center" id="drop-zone-content">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                        <p class="text-gray-600 mb-2">Drag & drop file di sini atau</p>
                                        <button type="button" 
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm"
                                                onclick="document.getElementById('document').click()">
                                            Pilih File
                                        </button>
                                    </div>
                                    <div id="file-preview" class="hidden w-full">
                                        <div class="flex items-center justify-between bg-white p-3 rounded-md shadow-sm">
                                            <div class="flex items-center">
                                                <i class="fas fa-file text-blue-500 mr-2"></i>
                                                <span id="file-name" class="text-sm text-gray-600"></span>
                                            </div>
                                            <button type="button" 
                                                    onclick="removeFile()"
                                                    class="text-red-500 hover:text-red-700">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">
                                        Format yang diizinkan: PDF, Word (doc/docx), Gambar (jpg/jpeg/png). Maksimal 5MB
                                    </p>
                                </div>
                            </div>

                            <!-- Deskripsi Dokumen -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="document_description">
                                    Deskripsi Dokumen
                                </label>
                                <textarea name="document_description" 
                                          id="document_description" 
                                          rows="8"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                          required
                                          placeholder="Berikan deskripsi singkat tentang dokumen yang diupload"></textarea>
                            </div>
                        </div>

                        @if($discussion->document_path)
                        <div class="mt-4">
                            <h5 class="text-sm font-semibold mb-2">Dokumen Saat Ini:</h5>
                            <div class="flex items-center gap-2 bg-white p-3 rounded-md shadow-sm">
                                <i class="fas fa-file text-blue-500"></i>
                                <a href="{{ asset('storage/' . $discussion->document_path) }}" 
                                   target="_blank"
                                   class="text-blue-500 hover:text-blue-700">
                                    {{ $discussion->document_description ?? 'Lihat Dokumen' }}
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- No SR -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="sr_number">
                                No SR <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="sr_number" 
                                   id="sr_number" 
                                   value="{{ old('sr_number', $discussion->sr_number) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                   required>
                        </div>

                        <!-- No Pembahasan (read-only) -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="no_pembahasan">
                                No Pembahasan
                            </label>
                            <input type="text" 
                                   name="no_pembahasan" 
                                   id="no_pembahasan" 
                                   value="{{ old('no_pembahasan', $discussion->no_pembahasan) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 bg-gray-100"
                                   readonly>
                        </div>

                        <!-- Unit -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="unit">
                                Unit <span class="text-red-500">*</span>
                            </label>
                            <select name="unit" 
                                    id="unit" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 bg-gray-50"
                                    required>
                                <option value="">Pilih Unit</option>
                                @foreach(\App\Models\PowerPlant::select('name')->distinct()->get() as $powerPlant)
                                    <option value="{{ $powerPlant->name }}" {{ old('unit', $discussion->unit) == $powerPlant->name ? 'selected' : '' }}>
                                        {{ $powerPlant->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Topik -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="topic">
                                Topik <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="topic" 
                                   id="topic" 
                                   value="{{ old('topic', $discussion->topic) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                   required>
                        </div>

                        <!-- Sasaran -->
                        <div class="mb-4 md:col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="target">
                                Sasaran <span class="text-red-500">*</span>
                            </label>
                            <textarea name="target" 
                                      id="target" 
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                      required>{{ old('target', $discussion->target) }}</textarea>
                        </div>

                        <!-- Deadline Sasaran -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="target_deadline">
                                Deadline Sasaran <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="target_deadline" 
                                   id="target_deadline" 
                                   value="{{ old('target_deadline', $discussion->target_deadline ? date('Y-m-d', strtotime($discussion->target_deadline)) : '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                   required>
                        </div>

                        <!-- PIC -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="pic">
                                PIC <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="department_id" 
                                        id="department_select" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 mb-2"
                                        onchange="updateSections(this.value)"
                                        required>
                                    <option value="">Pilih Bagian</option>
                                    @foreach(\App\Models\Department::all() as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id', $discussion->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <select name="section_id" 
                                        id="section_select" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        required>
                                    <option value="">Pilih Seksi</option>
                                </select>
                            </div>
                        </div>

                        <!-- Tingkat Resiko -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="risk_level">
                                Tingkat Resiko <span class="text-red-500">*</span>
                            </label>
                            <select name="risk_level" 
                                    id="risk_level" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 bg-gray-50"
                                    required>
                                <option value="">Pilih Tingkat Resiko</option>
                                @foreach(\App\Models\OtherDiscussion::RISK_LEVELS as $key => $value)
                                    <option value="{{ $key }}" {{ old('risk_level', $discussion->risk_level) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tingkat Prioritas -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="priority_level">
                                Tingkat Prioritas <span class="text-red-500">*</span>
                            </label>
                            <select name="priority_level" 
                                    id="priority_level" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 bg-gray-50"
                                    required>
                                <option value="">Pilih Tingkat Prioritas</option>
                                @foreach(\App\Models\OtherDiscussion::PRIORITY_LEVELS as $priority)
                                    <option value="{{ $priority }}" {{ old('priority_level', $discussion->priority_level) == $priority ? 'selected' : '' }}>
                                        {{ $priority }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Komitmen dengan Status -->
                        <div class="mb-4 md:col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Komitmen <span class="text-red-500">*</span>
                            </label>
                            <div id="commitments-container">
                                @foreach($discussion->commitments as $commitment)
                                <div class="commitment-entry grid grid-cols-1 md:grid-cols-12 gap-4 mb-8 pt-4 relative" data-commitment-id="{{ $commitment->id }}">
                                    <div class="md:col-span-8">
                                        <!-- Header Section with Status and Deadline -->
                                        <div class="flex justify-between items-center mb-2">
                                            <!-- Status Badge -->
                                            <div class="flex items-center">
                                                <span class="text-sm font-medium mr-2">Status:</span>
                                                <select name="commitment_status[{{ $commitment->id }}]" 
                                                        class="status-select text-sm px-3 py-1.5 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                        onchange="updateStatusStyle(this)"
                                                        required>
                                                    <option value="Open" {{ $commitment->status == 'Open' ? 'selected' : '' }}>Open</option>
                                                    <option value="Closed" {{ $commitment->status == 'Closed' ? 'selected' : '' }}>Closed</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Deadline Input -->
                                            <div class="flex items-center">
                                                <span class="text-sm font-medium mr-2">Deadline:</span>
                                                <input type="date" 
                                                       name="commitment_deadlines[{{ $commitment->id }}]" 
                                                       value="{{ $commitment->deadline ? date('Y-m-d', strtotime($commitment->deadline)) : '' }}"
                                                       class="text-sm px-3 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                       required>
                                            </div>
                                        </div>

                                        <!-- Commitment Textarea -->
                                        <div class="relative">
                                            <textarea name="commitments[{{ $commitment->id }}]" 
                                                      class="commitment-text w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                      rows="3"
                                                      required>{{ old('commitments.'.$commitment->id, $commitment->description) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="md:col-span-4">
                                        <div class="relative">
                                            <select name="commitment_department_ids[{{ $commitment->id }}]" 
                                                    class="department-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 mb-2"
                                                    onchange="updateCommitmentSections(this)"
                                                    required>
                                                <option value="">Pilih Bagian</option>
                                                @foreach(\App\Models\Department::all() as $department)
                                                    <option value="{{ $department->id }}" {{ $commitment->department_id == $department->id ? 'selected' : '' }}>
                                                        {{ $department->name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <select name="commitment_section_ids[{{ $commitment->id }}]" 
                                                    class="section-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    data-selected="{{ $commitment->section_id }}"
                                                    required>
                                                <option value="">Pilih Seksi</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Remove Button -->
                                    <button type="button" 
                                            onclick="removeCommitment(this)"
                                            class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" 
                                    onclick="addCommitment()"
                                    class="mt-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm flex items-center">
                                <i class="fas fa-plus mr-2"></i> Tambah Komitmen
                            </button>
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" 
                                    id="status" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 bg-gray-50"
                                    onchange="validateStatus(this)"
                                    required>
                                <option value="">Pilih Status</option>
                                @foreach(\App\Models\OtherDiscussion::STATUSES as $status)
                                    <option value="{{ $status }}" {{ old('status', $discussion->status) == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('admin.other-discussions.index') }}" 
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i> Batal
                        </a>
                        <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline flex items-center ml-4">
                            <i class="fas fa-save mr-2"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Simpan data sections dalam cache
const sectionsData = @json(\App\Models\Department::with('sections')->get()->mapWithKeys(function($dept) {
    return [$dept->id => $dept->sections->map(function($section) {
        return ['id' => $section->id, 'name' => $section->name];
    })];
}));

// Fungsi untuk update sections yang dioptimasi
function updateSections(departmentId, sectionSelect, selectedSectionId = null) {
    if (!sectionSelect) return;
    
    sectionSelect.innerHTML = '<option value="">Pilih Seksi</option>';
    
    if (!departmentId) {
        sectionSelect.disabled = true;
        return;
    }

    const sections = sectionsData[departmentId] || [];
    sections.forEach(section => {
        const option = document.createElement('option');
        option.value = section.id;
        option.textContent = section.name;
        if (selectedSectionId && String(selectedSectionId) === String(section.id)) {
            option.selected = true;
        }
        sectionSelect.appendChild(option);
    });
    
    sectionSelect.disabled = false;
}

// Fungsi khusus untuk update sections pada komitmen
function updateCommitmentSections(departmentSelect) {
    const commitmentEntry = departmentSelect.closest('.commitment-entry');
    const sectionSelect = commitmentEntry.querySelector('.section-select');
    const selectedSectionId = sectionSelect.dataset.selected;
    
    updateSections(departmentSelect.value, sectionSelect, selectedSectionId);
}

// Event listener yang dioptimasi
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi sections untuk PIC utama
    const departmentSelect = document.getElementById('department_select');
    const sectionSelect = document.getElementById('section_select');
    
    if (departmentSelect && sectionSelect) {
        departmentSelect.addEventListener('change', function() {
            updateSections(this.value, sectionSelect);
        });
        
        if (departmentSelect.value) {
            updateSections(departmentSelect.value, sectionSelect, '{{ old("section_id", $discussion->section_id) }}');
        }
    }

    // Inisialisasi sections untuk komitmen yang sudah ada
    document.querySelectorAll('.department-select').forEach(select => {
        const sectionSelect = select.closest('.commitment-entry').querySelector('.section-select');
        if (select.value) {
            const selectedSectionId = sectionSelect.dataset.selected;
            updateSections(select.value, sectionSelect, selectedSectionId);
        }
    });
});

// Fungsi untuk validasi status yang dioptimasi
function validateStatus(select) {
    const hasOpenCommitments = Array.from(document.querySelectorAll('.status-select'))
        .some(statusSelect => statusSelect.value === 'Open');

    const hasOpenNewCommitments = Array.from(document.querySelectorAll('select[name^="new_commitment_status"]'))
        .some(statusSelect => statusSelect.value === 'Open');

    if (select.value === 'Closed' && (hasOpenCommitments || hasOpenNewCommitments)) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan!',
            text: 'Semua komitmen harus Closed sebelum mengubah status menjadi Closed',
            confirmButtonText: 'OK'
        });
        select.value = 'Open';
        return false;
    }
    return true;
}

// Fungsi untuk menghapus komitmen
function removeCommitment(button) {
    button.closest('.commitment-entry').remove();
}

// Submit handler yang dioptimasi
document.getElementById('editDiscussionForm').addEventListener('submit', function(e) {
    const status = document.getElementById('status').value;
    if (status === 'Closed' && !validateStatus(document.getElementById('status'))) {
        e.preventDefault();
    }

    const fileInput = document.getElementById('document');
    const file = fileInput.files[0];
    
    if (!file) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Upload Dokumen Diperlukan',
            text: 'Silakan upload dokumen pendukung sebelum menyimpan perubahan.',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Validasi ukuran file (5MB)
    const maxSize = 5 * 1024 * 1024; // 5MB dalam bytes
    if (file.size > maxSize) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Ukuran File Terlalu Besar',
            text: 'Ukuran file maksimal adalah 5MB.',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Validasi tipe file
    const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
    if (!allowedTypes.includes(file.type)) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Format File Tidak Didukung',
            text: 'Format file yang diizinkan: PDF, Word, atau Gambar (JPG/PNG).',
            confirmButtonText: 'OK'
        });
        return;
    }
});

// Fungsi untuk menambah komitmen baru
function addCommitment() {
    const container = document.getElementById('commitments-container');
    const newId = 'new_' + Date.now(); // Generate unique ID untuk komitmen baru
    
    const template = `
        <div class="commitment-entry grid grid-cols-1 md:grid-cols-12 gap-4 mb-8 pt-4 relative border-t border-gray-200">
            <div class="md:col-span-8">
                <!-- Header Section with Status and Deadline -->
                <div class="flex justify-between items-center mb-2">
                    <!-- Status Badge -->
                    <div class="flex items-center">
                        <span class="text-sm font-medium mr-2">Status:</span>
                        <select name="new_commitment_status[]" 
                                class="status-select text-sm px-3 py-1.5 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                required>
                            <option value="Open" selected>Open</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>
                    
                    <!-- Deadline Input -->
                    <div class="flex items-center">
                        <span class="text-sm font-medium mr-2">Deadline:</span>
                        <input type="date" 
                               name="new_commitment_deadlines[]" 
                               class="text-sm px-3 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                               required>
                    </div>
                </div>

                <!-- Commitment Textarea -->
                <div class="relative">
                    <textarea name="new_commitments[]" 
                              class="commitment-text w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                              rows="3"
                              required
                              placeholder="Masukkan komitmen baru"></textarea>
                </div>
            </div>
            <div class="md:col-span-4">
                <div class="relative">
                    <select name="new_commitment_department_ids[]" 
                            class="department-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 mb-2"
                            onchange="updateCommitmentSections(this)"
                            required>
                        <option value="">Pilih Bagian</option>
                        ${generateDepartmentOptions()}
                    </select>

                    <select name="new_commitment_section_ids[]" 
                            class="section-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                            required>
                        <option value="">Pilih Seksi</option>
                    </select>
                </div>
            </div>
            <!-- Remove Button -->
            <button type="button" 
                    onclick="removeCommitment(this)"
                    class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', template);
}

// Helper function untuk generate department options
function generateDepartmentOptions() {
    const departments = @json(\App\Models\Department::all());
    return departments.map(dept => 
        `<option value="${dept.id}">${dept.name}</option>`
    ).join('');
}

const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('document');
const dropZoneContent = document.getElementById('drop-zone-content');
const filePreview = document.getElementById('file-preview');
const fileName = document.getElementById('file-name');

// Prevent default drag behaviors
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
    document.body.addEventListener(eventName, preventDefaults, false);
});

// Highlight drop zone when dragging over it
['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, unhighlight, false);
});

// Handle dropped files
dropZone.addEventListener('drop', handleDrop, false);

// Handle file input change
fileInput.addEventListener('change', handleFiles);

function preventDefaults (e) {
    e.preventDefault();
    e.stopPropagation();
}

function highlight(e) {
    dropZone.classList.add('border-blue-500', 'bg-blue-50');
}

function unhighlight(e) {
    dropZone.classList.remove('border-blue-500', 'bg-blue-50');
}

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    handleFiles({ target: { files: files } });
}

function handleFiles(e) {
    const file = e.target.files[0];
    if (file) {
        // Validasi tipe file
        const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Format File Tidak Didukung',
                text: 'Format file yang diizinkan: PDF, Word, atau Gambar (JPG/PNG).',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Validasi ukuran file (5MB)
        const maxSize = 5 * 1024 * 1024; // 5MB dalam bytes
        if (file.size > maxSize) {
            Swal.fire({
                icon: 'error',
                title: 'Ukuran File Terlalu Besar',
                text: 'Ukuran file maksimal adalah 5MB.',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Update tampilan
        dropZoneContent.classList.add('hidden');
        filePreview.classList.remove('hidden');
        fileName.textContent = file.name;
    }
}

function removeFile() {
    fileInput.value = '';
    dropZoneContent.classList.remove('hidden');
    filePreview.classList.add('hidden');
    fileName.textContent = '';
}
</script>
@endpush
@endsection             