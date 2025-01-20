@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <!-- Sidebar -->
    <x-sidebar />

    <!-- Main Content -->
    <div class="flex-1 overflow-x-hidden overflow-y-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-20">
            <div class="flex justify-between items-center px-6 py-3">
                <div class="flex items-center gap-x-3">
                    <h1 class="text-xl font-semibold text-gray-800">Tambah Pembahasan Baru</h1>
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
                ['name' => 'Tambah Pembahasan', 'url' => null]
            ]" />
        </div>

        <!-- Konten utama -->
        <div class="container mx-auto px-6 py-8">
            <h3 class="text-gray-700 text-3xl font-medium">Tambah Pembahasan Baru</h3>

            <div class="mt-8">
                <form id="createDiscussionForm" action="{{ route('admin.other-discussions.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" onsubmit="return validateForm()">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- No SR (manual input) -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="sr_number">
                                No SR <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <input type="text" 
                                       name="sr_number" 
                                       id="sr_number" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                       required>
                            </div>
                            @error('sr_number')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- No Pembahasan dengan tombol generate -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="no_pembahasan">
                                No Pembahasan <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <input type="text" 
                                       name="no_pembahasan" 
                                       id="no_pembahasan" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 bg-gray-100"
                                       required
                                       readonly>
                                <button type="button" 
                                        id="generateButton"
                                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                    Generate No
                                </button>
                            </div>
                            @error('no_pembahasan')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
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
                                    @php
                                        $shortName = Str::limit($powerPlant->name, 50, '');  // Memotong nama unit jika terlalu panjang
                                    @endphp
                                    <option value="{{ $shortName }}" {{ old('unit') == $shortName ? 'selected' : '' }}
                                            class="bg-white">
                                        {{ $powerPlant->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unit')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Topik -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="topic">
                                Topik <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="topic" 
                                   id="topic" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                   value="{{ old('topic') }}"
                                   required>
                            @error('topic')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
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
                                      required>{{ old('target') }}</textarea>
                            @error('target')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                         <!-- Sasaran Deadline -->
                         <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="target_deadline">
                                Deadline Sasaran <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="target_deadline" 
                                   id="target_deadline" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                   value="{{ old('target_deadline') }}"
                                   required>
                            @error('target_deadline')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
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
                                    <option value="">Pilih Departemen</option>
                                    @foreach(\App\Models\Department::all() as $department)
                                        <option value="{{ $department->id }}" 
                                                {{ old('department_id') == $department->id ? 'selected' : '' }}>
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
                            @error('section_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
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
                                    <option value="{{ $key }}" {{ old('risk_level') == $key ? 'selected' : '' }}
                                            class="bg-white">
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('risk_level')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
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
                                    <option value="{{ $priority }}" {{ old('priority_level') == $priority ? 'selected' : '' }}
                                            class="bg-white">
                                        {{ $priority }}
                                    </option>
                                @endforeach
                            </select>
                            @error('priority_level')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                       

                        <!-- Komitmen dengan Status -->
                        <div class="mb-4 md:col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Komitmen <span class="text-red-500">*</span>
                            </label>
                            <div id="commitments-container">
                                <div class="commitment-entry grid grid-cols-1 md:grid-cols-12 gap-4 mb-2">
                                    <div class="md:col-span-8">
                                        <!-- Header Section with Status and Deadline -->
                                        <div class="flex justify-between items-center mb-2">
                                            <!-- Status Badge -->
                                            <div class="flex items-center">
                                                <span class="text-sm font-medium mr-2">Status:</span>
                                                <select name="commitment_status[]" 
                                                        class="status-select text-sm px-3 py-1.5 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                        onchange="updateStatusStyle(this)"
                                                        required>
                                                    <option value="Open">Open</option>
                                                    <option value="Closed">Closed</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Deadline Input -->
                                            <div class="flex items-center">
                                                <span class="text-sm font-medium mr-2">Deadline:</span>
                                                <input type="date" 
                                                       name="commitment_deadlines[]" 
                                                       class="text-sm px-3 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                       required>
                                            </div>
                                        </div>

                                        <!-- Commitment Textarea -->
                                        <div class="relative">
                                            <textarea name="commitments[]" 
                                                      class="commitment-text w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                      rows="3"
                                                      placeholder="Masukkan komitmen"
                                                      required></textarea>
                                        </div>
                                    </div>
                                    <div class="md:col-span-4">
                                        <div class="relative">
                                            <select name="commitment_department_ids[]" 
                                                    class="department-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 mb-2"
                                                    onchange="updateCommitmentSections(this)"
                                                    required>
                                                <option value="">Pilih Bagian</option>
                                                @foreach(\App\Models\Department::all() as $department)
                                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                                @endforeach
                                            </select>

                                            <select name="commitment_section_ids[]" 
                                                    class="section-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    required>
                                                <option value="">Pilih Seksi</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
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
                                    <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}
                                            class="bg-white">
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
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


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('createDiscussionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Disable submit button
    const submitButton = this.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    
    // Create form data
    const form = this;
    const formData = new FormData(form);
    
    // Submit form using traditional form submission
    const tempForm = document.createElement('form');
    tempForm.method = 'POST';
    tempForm.action = form.action;
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    tempForm.appendChild(csrfInput);
    
    // Add form data
    for (let pair of formData.entries()) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = pair[0];
        input.value = pair[1];
        tempForm.appendChild(input);
    }
    
    // Add to document and submit
    document.body.appendChild(tempForm);
    
    Swal.fire({
        title: 'Memproses...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    tempForm.submit();
});

// Handle response messages
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 1500
    }).then(() => {
        window.location.href = "{{ route('admin.other-discussions.index') }}";
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: "{{ session('error') }}",
        confirmButtonText: 'Tutup'
    });
@endif

// Fungsi untuk mengupdate status komitmen
function updateCommitmentStatus(statusSelect) {
    const status = statusSelect.value;
    const commitmentEntry = statusSelect.closest('.commitment-entry');
    const textarea = commitmentEntry.querySelector('.commitment-text');
    
    // Hapus status yang mungkin sudah ada
    let commitmentText = textarea.value.replace(/\[Status: (Open|Closed)\]\n/, '');
    
    // Tambahkan status baru
    textarea.value = `[Status: ${status.charAt(0).toUpperCase() + status.slice(1)}]\n${commitmentText}`;
    
    // Update style status
    statusSelect.className = 'status-select text-sm px-3 py-1.5 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 ' + 
        (status === 'open' ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : 
         'bg-green-100 text-green-800 border border-green-300');
}

// Fungsi untuk menambah komitmen baru
function addCommitment() {
    const container = document.getElementById('commitments-container');
    const newEntry = document.createElement('div');
    newEntry.className = 'commitment-entry grid grid-cols-1 md:grid-cols-12 gap-4 mb-8 pt-4 relative';
    
    let departmentOptions = `<option value="">Pilih Bagian</option>`;
    @foreach(\App\Models\Department::all() as $department)
        departmentOptions += `<option value="{{ $department->id }}">{{ $department->name }}</option>`;
    @endforeach

    newEntry.innerHTML = `
        <!-- Tombol Hapus -->
        <button type="button" 
                onclick="removeCommitment(this)" 
                class="absolute right-0 top-0 z-50 bg-red-500 hover:bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center focus:outline-none shadow-md transform hover:scale-110 transition-transform duration-200"
                style="margin-top: -12px; margin-right: -12px;">
            <i class="fas fa-trash-alt"></i>
        </button>

        <div class="md:col-span-8">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-2">
                <!-- Status Badge -->
                <div class="flex items-center">
                    <span class="text-sm font-medium mr-2">Status:</span>
                    <select name="commitment_status[]" 
                            class="status-select text-sm px-3 py-1.5 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                            onchange="updateStatusStyle(this)"
                            required>
                        <option value="Open">Open</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>
                
                <!-- Deadline Input -->
                <div class="flex items-center">
                    <span class="text-sm font-medium mr-2">Deadline:</span>
                    <input type="date" 
                           name="commitment_deadlines[]" 
                           class="text-sm px-3 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                           required>
                </div>
            </div>

            <!-- Commitment Textarea -->
            <div class="relative">
                <textarea name="commitments[]" 
                          class="commitment-text w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                          rows="3"
                          placeholder="Masukkan komitmen"
                          required></textarea>
            </div>
        </div>
        
        <div class="md:col-span-4">
            <div class="relative">
                <select name="commitment_department_ids[]" 
                        class="department-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 mb-2"
                        onchange="updateCommitmentSections(this)"
                        required>
                    ${departmentOptions}
                </select>

                <select name="commitment_section_ids[]" 
                        class="section-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                        required>
                    <option value="">Pilih Seksi</option>
                </select>
            </div>
        </div>
    `;
    
    container.appendChild(newEntry);
    
    // Initialize status
    const statusSelect = newEntry.querySelector('.status-select');
    updateCommitmentStatus(statusSelect);
    
    // Tambahkan event listener untuk status komitmen baru
    const newStatusSelect = newEntry.querySelector('.status-select');
    newStatusSelect.addEventListener('change', function() {
        const mainStatus = document.getElementById('status');
        if (mainStatus.value === 'Closed' && this.value === 'Open') {
            mainStatus.value = 'Open';
            Swal.fire({
                icon: 'info',
                title: 'Info',
                text: 'Status pembahasan diubah ke Open karena ada komitmen yang Open',
                confirmButtonText: 'OK'
            });
        }
    });
}

// Tambahkan fungsi untuk menghapus komitmen
function removeCommitment(button) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Komitmen ini akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const commitmentEntry = button.closest('.commitment-entry');
            commitmentEntry.remove();
            
            Swal.fire(
                'Terhapus!',
                'Komitmen telah dihapus.',
                'success'
            );
        }
    });
}

// Update fungsi untuk menambahkan tombol hapus ke komitmen yang sudah ada
document.addEventListener('DOMContentLoaded', function() {
    const existingCommitments = document.querySelectorAll('.commitment-entry');
    existingCommitments.forEach(commitment => {
        if (!commitment.querySelector('button[onclick="removeCommitment(this)"]')) {
            // Tambahkan padding dan margin
            commitment.classList.add('pt-4', 'mb-8');
            commitment.style.position = 'relative';

            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.onclick = function() { removeCommitment(this); };
            deleteButton.className = 'absolute right-0 top-0 z-50 bg-red-500 hover:bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center focus:outline-none shadow-md transform hover:scale-110 transition-transform duration-200';
            deleteButton.style.marginTop = '-12px';
            deleteButton.style.marginRight = '-12px';
            deleteButton.innerHTML = '<i class="fas fa-trash-alt"></i>';
            
            commitment.insertBefore(deleteButton, commitment.firstChild);
        }
    });
});

// Data sections berdasarkan department
const sectionsData = {
    @foreach(\App\Models\Department::with('sections')->get() as $department)
        '{{ $department->id }}': [
            @foreach($department->sections as $section)
                {id: {{ $section->id }}, name: '{{ $section->name }}'},
            @endforeach
        ],
    @endforeach
};

function updateSections(departmentId) {
    const sectionSelect = document.getElementById('section_select');
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
        sectionSelect.appendChild(option);
    });
    
    sectionSelect.disabled = false;

    // Debug
    console.log('Department ID:', departmentId);
    console.log('Available sections:', sections);
}

// Initialize sections if department is pre-selected
document.addEventListener('DOMContentLoaded', function() {
    const departmentSelect = document.getElementById('department_select');
    if (departmentSelect.value) {
        updateSections(departmentSelect.value);
        
        // If there's an old section value, select it
        const oldSectionId = '{{ old("section_id") }}';
        if (oldSectionId) {
            const sectionSelect = document.getElementById('section_select');
            if (sectionSelect) {
                sectionSelect.value = oldSectionId;
            }
        }
    }
});

// Data sections untuk komitmen
const commitmentSectionsData = {
    @foreach(\App\Models\Department::with('sections')->get() as $department)
        '{{ $department->id }}': [
            @foreach($department->sections as $section)
                {id: {{ $section->id }}, name: '{{ $section->name }}'},
            @endforeach
        ],
    @endforeach
};

function updateCommitmentSections(departmentSelect) {
    const commitmentEntry = departmentSelect.closest('.commitment-entry');
    const sectionSelect = commitmentEntry.querySelector('.section-select');
    sectionSelect.innerHTML = '<option value="">Pilih Seksi</option>';
    
    const departmentId = departmentSelect.value;
    if (!departmentId) {
        sectionSelect.disabled = true;
        return;
    }

    const sections = commitmentSectionsData[departmentId] || [];
    sections.forEach(section => {
        const option = document.createElement('option');
        option.value = section.id;
        option.textContent = section.name;
        sectionSelect.appendChild(option);
    });
    
    sectionSelect.disabled = false;

    // Debug
    console.log('Department ID:', departmentId);
    console.log('Available sections:', sections);
}

// Inisialisasi sections untuk komitmen yang sudah ada
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.department-select').forEach(select => {
        if (select.value) {
            updateCommitmentSections(select);
        }
    });
});

// Tambahkan fungsi updateStatusStyle
function updateStatusStyle(select) {
    select.classList.remove(
        'bg-red-100', 'text-red-800', 'border-red-200',
        'bg-green-100', 'text-green-800', 'border-green-200'
    );
    
    if (select.value === 'Open') {
        select.classList.add('bg-red-100', 'text-red-800', 'border-red-200');
    } else if (select.value === 'Closed') {
        select.classList.add('bg-green-100', 'text-green-800', 'border-green-200');
    }
}

// Inisialisasi style untuk status yang sudah ada
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.status-select').forEach(select => {
        updateStatusStyle(select);
    });
});

// Tambahkan script untuk auto-generate nomor pembahasan
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const generateButton = document.getElementById('generateButton');
    const unitSelect = document.getElementById('unit');
    
    if (generateButton) {
        generateButton.addEventListener('click', async function() {
            const unit = unitSelect.value;
            
            if (!unit) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih unit terlebih dahulu'
                });
                return;
            }

            try {
                const response = await fetch("{{ route('admin.other-discussions.generate-no-pembahasan') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ unit: unit })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                if (data.success) {
                    document.getElementById('no_pembahasan').value = data.number;
                } else {
                    throw new Error(data.message || 'Gagal generate nomor pembahasan');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal generate nomor pembahasan. Silakan coba lagi.'
                });
            }
        });
    }
});

// Fungsi validasi form sebelum submit
function validateForm() {
    const noPembahasan = document.getElementById('no_pembahasan').value;
    const unit = document.getElementById('unit').value;
    const srNumber = document.getElementById('sr_number').value;

    if (!unit) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Silakan pilih unit terlebih dahulu'
        });
        return false;
    }

    if (!noPembahasan) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Silakan generate nomor pembahasan terlebih dahulu'
        });
        return false;
    }

    if (!srNumber) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Silakan isi nomor SR'
        });
        return false;
    }

    return true;
}

// Jika ada unit yang sudah terpilih saat halaman dimuat (misalnya karena old value)
window.addEventListener('load', function() {
    const unitSelect = document.getElementById('unit');
    if (unitSelect.value) {
        unitSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush

@push('scripts')
<script>
async function generateNoPembahasan() {
    try {
        const unit = document.getElementById('unit').value;
        const generateUrl = "{{ route('admin.other-discussions.generate-no-pembahasan') }}";
        
        // Console log yang aman
        if (window.location.hostname === 'localhost') {
            console.log('Debug - Generate URL:', generateUrl);
            console.log('Debug - Unit:', unit);
        }

        const response = await fetch(generateUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ unit: unit }),
            credentials: 'same-origin'
        });

        // Log response status tanpa expose detail sensitif
        if (!response.ok) {
            throw new Error(`Request failed with status: ${response.status}`);
        }

        const data = await response.json();
        
        if (data.success) {
            document.getElementById('no_pembahasan').value = data.number;
        } else {
            throw new Error(data.message || 'Gagal generate nomor pembahasan');
        }
    } catch (error) {
        // Log error yang aman
        Log::channel('daily')->error('JavaScript Error', [
            'message' => error.message,
            'timestamp' => new Date().toISOString()
        ]);
        alert('Gagal generate nomor pembahasan. Silakan coba lagi.');
    }
}

// Fungsi validasi form
function validateForm() {
    const noPembahasan = document.getElementById('no_pembahasan').value;
    const unit = document.getElementById('unit').value;
    const srNumber = document.getElementById('sr_number').value;

    if (!unit) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Silakan pilih unit terlebih dahulu'
        });
        return false;
    }

    if (!noPembahasan) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Silakan generate nomor pembahasan terlebih dahulu'
        });
        return false;
    }

    if (!srNumber) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Silakan isi nomor SR'
        });
        return false;
    }

    return true;
}

// Reset no_pembahasan saat unit berubah
document.getElementById('unit').addEventListener('change', function() {
    document.getElementById('no_pembahasan').value = '';
});
</script>
@endpush

// Fungsi untuk validasi status
function validateStatus(select) {
    const hasOpenCommitments = Array.from(document.querySelectorAll('.status-select'))
        .some(statusSelect => statusSelect.value === 'Open');

    if (select.value === 'Closed' && hasOpenCommitments) {
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

// Tambahkan event listener untuk status komitmen
document.addEventListener('DOMContentLoaded', function() {
    const statusSelects = document.querySelectorAll('.status-select');
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            // Reset status pembahasan ke Open jika ada komitmen yang Open
            const mainStatus = document.getElementById('status');
            if (mainStatus.value === 'Closed' && this.value === 'Open') {
                mainStatus.value = 'Open';
                Swal.fire({
                    icon: 'info',
                    title: 'Info',
                    text: 'Status pembahasan diubah ke Open karena ada komitmen yang Open',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
});
</script>
@push('scripts')
@endpush
@endsection     