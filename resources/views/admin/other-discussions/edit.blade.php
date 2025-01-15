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
                <form id="editDiscussionForm" action="{{ route('admin.other-discussions.update', $discussion->id) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- No SR -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="sr_number">
                                No SR
                            </label>
                            <input type="number" 
                                   name="sr_number" 
                                   id="sr_number" 
                                   value="{{ old('sr_number', $discussion->sr_number) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                            @error('sr_number')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- No WO -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="wo_number">
                                No WO
                            </label>
                            <input type="number" 
                                   name="wo_number" 
                                   id="wo_number" 
                                   value="{{ old('wo_number', $discussion->wo_number) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                            @error('wo_number')
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
                                @foreach(\App\Models\OtherDiscussion::getUnits() as $unit)
                                    <option value="{{ $unit }}" {{ old('unit', $discussion->unit) == $unit ? 'selected' : '' }}>
                                        {{ $unit }}
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
                                   value="{{ old('topic', $discussion->topic) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                   required>
                            @error('topic')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sasaran -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="target">
                                Sasaran <span class="text-red-500">*</span>
                            </label>
                            <textarea name="target" 
                                      id="target" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                      required
                                      rows="3">{{ old('target', $discussion->target) }}</textarea>
                            @error('target')
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
                                    <option value="{{ $key }}" {{ old('risk_level', $discussion->risk_level) == $key ? 'selected' : '' }}>
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
                                    <option value="{{ $priority }}" {{ old('priority_level', $discussion->priority_level) == $priority ? 'selected' : '' }}>
                                        {{ $priority }}
                                    </option>
                                @endforeach
                            </select>
                            @error('priority_level')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Komitmen -->
                        <div class="mb-4 md:col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Komitmen <span class="text-red-500">*</span>
                            </label>
                            <div id="commitments-container">
                                @foreach($discussion->commitments as $index => $commitment)
                                    <div class="commitment-entry grid grid-cols-1 md:grid-cols-12 gap-4 mb-2">
                                        <div class="md:col-span-8 relative">
                                            <input type="date" 
                                                   name="commitment_deadlines[]" 
                                                   class="absolute top-2 right-2 px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white z-10"
                                                   value="{{ old('commitment_deadlines.'.$index, $commitment->deadline ? date('Y-m-d', strtotime($commitment->deadline)) : '') }}"
                                                   required>
                                            <textarea name="commitments[]" 
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                      rows="3"
                                                      required>{{ old('commitments.'.$index, $commitment->description) }}</textarea>
                                        </div>
                                        <div class="md:col-span-3">
                                            <select name="commitment_pics[]"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    required>
                                                <option value="">Pilih PIC</option>
                                                
                                                <!-- Unit Pembangkitan -->
                                                <optgroup label="UNIT PEMBANGKITAN">
                                                    @foreach(\App\Models\Pic::where('department', 'UNIT PEMBANGKITAN')->get() as $pic)
                                                        <option value="{{ $pic->id }}" {{ old('commitment_pics.'.$index, $commitment->pic_id) == $pic->id ? 'selected' : '' }}>
                                                            {{ $pic->name }} - {{ $pic->position }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>

                                                <!-- Seksi HSE -->
                                                <optgroup label="HSE">
                                                    @foreach(\App\Models\Pic::where('department', 'HSE')->orderBy('section')->get() as $pic)
                                                        <option value="{{ $pic->id }}" {{ old('commitment_pics.'.$index, $commitment->pic_id) == $pic->id ? 'selected' : '' }}>
                                                            {{ $pic->name }} - {{ $pic->section }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>

                                                <!-- Bagian Operasi -->
                                                <optgroup label="BAGIAN OPERASI">
                                                    @foreach(\App\Models\Pic::where('department', 'BAGIAN OPERASI')->orderBy('section')->get() as $pic)
                                                        <option value="{{ $pic->id }}" {{ old('commitment_pics.'.$index, $commitment->pic_id) == $pic->id ? 'selected' : '' }}>
                                                            {{ $pic->name }} - {{ $pic->section }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>

                                                <!-- Bagian Pemeliharaan -->
                                                <optgroup label="BAGIAN PEMELIHARAAN">
                                                    @foreach(\App\Models\Pic::where('department', 'BAGIAN PEMELIHARAAN')->orderBy('section')->get() as $pic)
                                                        <option value="{{ $pic->id }}" {{ old('commitment_pics.'.$index, $commitment->pic_id) == $pic->id ? 'selected' : '' }}>
                                                            {{ $pic->name }} - {{ $pic->section }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>

                                                <!-- Bagian Enjiniring -->
                                                <optgroup label="BAGIAN ENJINIRING & QUALITY ASSURANCE">
                                                    @foreach(\App\Models\Pic::where('department', 'BAGIAN ENJINIRING & QUALITY ASSURANCE')->orderBy('section')->get() as $pic)
                                                        <option value="{{ $pic->id }}" {{ old('commitment_pics.'.$index, $commitment->pic_id) == $pic->id ? 'selected' : '' }}>
                                                            {{ $pic->name }} - {{ $pic->section }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>

                                                <!-- Bagian Business Support -->
                                                <optgroup label="BAGIAN BUSINESS SUPPORT">
                                                    @foreach(\App\Models\Pic::where('department', 'BAGIAN BUSINESS SUPPORT')->orderBy('section')->get() as $pic)
                                                        <option value="{{ $pic->id }}" {{ old('commitment_pics.'.$index, $commitment->pic_id) == $pic->id ? 'selected' : '' }}>
                                                            {{ $pic->name }} - {{ $pic->section }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>

                                                <!-- Unit Layanan PLTD -->
                                                <optgroup label="UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL">
                                                    @foreach(\App\Models\Pic::where('department', 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL')->orderBy('section')->get() as $pic)
                                                        <option value="{{ $pic->id }}" {{ old('commitment_pics.'.$index, $commitment->pic_id) == $pic->id ? 'selected' : '' }}>
                                                            {{ $pic->name }} - {{ $pic->section }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            </select>
                                        </div>
                                        <div class="md:col-span-1 flex items-center">
                                            @if(!$loop->first)
                                                <button type="button" 
                                                        onclick="removeCommitment(this)"
                                                        class="text-red-500 hover:text-red-700">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" 
                                    id="add-commitment"
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
                                    required>
                                <option value="Open" {{ old('status', $discussion->status) === 'Open' ? 'selected' : '' }}>Open</option>
                                <option value="Closed" {{ old('status', $discussion->status) === 'Closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Target Deadline -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="target_deadline">
                                Target Deadline <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="target_deadline" 
                                   id="target_deadline" 
                                   value="{{ old('target_deadline', $discussion->target_deadline ? date('Y-m-d', strtotime($discussion->target_deadline)) : '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                   required>
                            @error('target_deadline')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dropdown untuk memilih PIC -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <!-- Level 1: Pilih Bagian/Unit -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Pilih Bagian/Unit
                                </label>
                                <select id="department" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="">Pilih Bagian/Unit</option>
                                    @foreach(\App\Models\Department::orderBy('name')->get() as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Level 2: Pilih Seksi -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Pilih Seksi
                                </label>
                                <select id="section" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" disabled>
                                    <option value="">Pilih Seksi</option>
                                </select>
                            </div>

                            <!-- Level 3: Pilih PIC -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Pilih PIC
                                </label>
                                <select name="pic_id" id="pic" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" disabled>
                                    <option value="">Pilih PIC</option>
                                </select>
                            </div>
                        </div>

                        <!-- Deadline -->
                        
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
document.getElementById('editDiscussionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    Swal.fire({
        title: 'Mohon tunggu...',
        text: 'Sedang menyimpan data',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('{{ route('admin.other-discussions.update', $discussion->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = '{{ route('admin.other-discussions.index') }}';
            });
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'Terjadi kesalahan saat menyimpan data',
            confirmButtonText: 'Coba Lagi'
        });
    });
});

// Script untuk menangani penambahan komitmen
document.getElementById('add-commitment').addEventListener('click', function() {
    const container = document.getElementById('commitments-container');
    const newEntry = document.createElement('div');
    newEntry.className = 'commitment-entry grid grid-cols-1 md:grid-cols-12 gap-4 mb-2';
    
    // Dapatkan daftar PIC untuk dropdown
    const picOptions = `
        <option value="">Pilih PIC</option>
        @foreach(\App\Models\Pic::orderBy('name')->get() as $pic)
            <option value="{{ $pic->id }}">{{ $pic->name }} - {{ $pic->position }}</option>
        @endforeach
    `;

    newEntry.innerHTML = `
        <div class="md:col-span-8 relative">
            <input type="date" 
                   name="commitment_deadlines[]" 
                   class="absolute top-2 right-2 px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white z-10"
                   required>
            <textarea name="commitments[]" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                      rows="3"
                      placeholder="Masukkan komitmen"
                      required></textarea>
        </div>
        <div class="md:col-span-3">
            <select name="commitment_pics[]"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                    required>
                ${picOptions}
            </select>
        </div>
        <div class="md:col-span-1 flex items-center">
            <button type="button" 
                    onclick="removeCommitment(this)"
                    class="text-red-500 hover:text-red-700">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(newEntry);
});

// Event delegation untuk tombol hapus komitmen
document.getElementById('commitments-container').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-commitment') || e.target.closest('.remove-commitment')) {
        const entry = e.target.closest('.commitment-entry');
        if (document.querySelectorAll('.commitment-entry').length > 1) {
            entry.remove();
        } else {
            alert('Minimal harus ada satu komitmen');
        }
    }
});

document.getElementById('department').addEventListener('change', function() {
    const departmentId = this.value;
    const sectionSelect = document.getElementById('section');
    const picSelect = document.getElementById('pic');
    
    // Reset dan disable dropdown seksi dan PIC
    sectionSelect.innerHTML = '<option value="">Pilih Seksi</option>';
    picSelect.innerHTML = '<option value="">Pilih PIC</option>';
    sectionSelect.disabled = !departmentId;
    picSelect.disabled = true;

    if (departmentId) {
        // Fetch sections berdasarkan department
        fetch(`/api/sections/${departmentId}`)
            .then(response => response.json())
            .then(sections => {
                sections.forEach(section => {
                    const option = new Option(section.name, section.id);
                    sectionSelect.add(option);
                });
                sectionSelect.disabled = false;
            });
    }
});

document.getElementById('section').addEventListener('change', function() {
    const sectionId = this.value;
    const picSelect = document.getElementById('pic');
    
    // Reset dan disable dropdown PIC
    picSelect.innerHTML = '<option value="">Pilih PIC</option>';
    picSelect.disabled = !sectionId;

    if (sectionId) {
        // Fetch PICs berdasarkan section
        fetch(`/api/pics/${sectionId}`)
            .then(response => response.json())
            .then(pics => {
                pics.forEach(pic => {
                    const option = new Option(`${pic.name} (${pic.position})`, pic.id);
                    picSelect.add(option);
                });
                picSelect.disabled = false;
            });
    }
});

// Fungsi untuk menambah komitmen baru dengan dropdown PIC bertingkat
function addCommitment() {
    const container = document.getElementById('commitments-container');
    const commitmentCount = container.children.length;
    
    const newEntry = document.createElement('div');
    newEntry.className = 'commitment-entry grid grid-cols-1 md:grid-cols-12 gap-4 mb-4';
    newEntry.innerHTML = `
        <div class="md:col-span-8 relative">
            <input type="date" 
                   name="commitment_deadlines[]" 
                   class="absolute top-2 right-2 px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white z-10"
                   required>
            <textarea name="commitments[]" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                      rows="3"
                      placeholder="Masukkan komitmen"
                      required></textarea>
        </div>
        <div class="md:col-span-3">
            <!-- Nested dropdowns for PIC selection -->
            <div class="space-y-2">
                <select class="department-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                        onchange="updateSections(this, ${commitmentCount})">
                    <option value="">Pilih Bagian/Unit</option>
                    @foreach(\App\Models\Department::orderBy('name')->get() as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
                <select class="section-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                        onchange="updatePics(this, ${commitmentCount})" disabled>
                    <option value="">Pilih Seksi</option>
                </select>
                <select name="commitment_pics[]" 
                        class="pic-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                        required disabled>
                    <option value="">Pilih PIC</option>
                </select>
            </div>
        </div>
        <div class="md:col-span-1 flex items-center">
            <button type="button" 
                    onclick="removeCommitment(this)"
                    class="text-red-500 hover:text-red-700">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    container.appendChild(newEntry);
}

// Fungsi untuk update sections berdasarkan department yang dipilih
function updateSections(departmentSelect, index) {
    const row = departmentSelect.closest('.commitment-entry');
    const sectionSelect = row.querySelector('.section-select');
    const picSelect = row.querySelector('.pic-select');
    
    sectionSelect.innerHTML = '<option value="">Pilih Seksi</option>';
    picSelect.innerHTML = '<option value="">Pilih PIC</option>';
    sectionSelect.disabled = !departmentSelect.value;
    picSelect.disabled = true;

    if (departmentSelect.value) {
        fetch(`/api/sections/${departmentSelect.value}`)
            .then(response => response.json())
            .then(sections => {
                sections.forEach(section => {
                    const option = new Option(section.name, section.id);
                    sectionSelect.add(option);
                });
                sectionSelect.disabled = false;
            });
    }
}

// Fungsi untuk update PICs berdasarkan section yang dipilih
function updatePics(sectionSelect, index) {
    const row = sectionSelect.closest('.commitment-entry');
    const picSelect = row.querySelector('.pic-select');
    
    picSelect.innerHTML = '<option value="">Pilih PIC</option>';
    picSelect.disabled = !sectionSelect.value;

    if (sectionSelect.value) {
        fetch(`/api/pics/${sectionSelect.value}`)
            .then(response => response.json())
            .then(pics => {
                pics.forEach(pic => {
                    const option = new Option(`${pic.name} (${pic.position})`, pic.id);
                    picSelect.add(option);
                });
                picSelect.disabled = false;
            });
    }
}
</script>
@endpush
@endsection         