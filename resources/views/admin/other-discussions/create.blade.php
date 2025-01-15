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
                <form id="createDiscussionForm" action="{{ route('admin.other-discussions.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- No SR -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="sr_number">
                                No SR
                            </label>
                            <input type="number" 
                                   name="sr_number" 
                                   id="sr_number" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                   value="{{ old('sr_number') }}">
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
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                   value="{{ old('wo_number') }}">
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

                       

                        <!-- Komitmen -->
                        <div class="mb-4 md:col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Komitmen <span class="text-red-500">*</span>
                            </label>
                            <div id="commitments-container">
                                <div class="commitment-entry grid grid-cols-1 md:grid-cols-12 gap-4 mb-2">
                                    <div class="md:col-span-8 relative">
                                        <input type="date" 
                                               name="commitment_deadlines[]" 
                                               class="absolute top-2 right-2 px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white z-10"
                                               value="{{ old('commitment_deadlines.0') }}"
                                               required>
                                        <textarea name="commitments[]" 
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                  rows="3"
                                                  placeholder="Masukkan komitmen"
                                                  required>{{ old('commitments.0') }}</textarea>
                                    </div>
                                    <div class="md:col-span-3">
                                        <!-- Dropdown Bertingkat untuk PIC -->
                                        <div class="space-y-2">
                                            <select class="department-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    onchange="updateSections(this, 0)">
                                                <option value="">Pilih Bagian/Unit</option>
                                                @foreach(\App\Models\Department::orderBy('name')->get() as $department)
                                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                                @endforeach
                                            </select>
                                            <select class="section-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    onchange="updatePics(this, 0)" disabled>
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
                                </div>
                            </div>
                            <button type="button" 
                                    onclick="addCommitment()"
                                    class="mt-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm flex items-center">
                                <i class="fas fa-plus mr-2"></i> Tambah Komitmen
                            </button>
                        </div>

                        <!-- PIC -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="pic">
                                PIC <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="pic" 
                                   id="pic" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                   value="{{ old('pic') }}"
                                   required>
                            @error('pic')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
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
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            window.location.href = "{{ route('admin.other-discussions.index') }}";
        });
    });
@endif

@if(session('error'))
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "{{ session('error') }}",
            confirmButtonText: 'Tutup'
        }).then(() => {
            submitButton.disabled = false;
        });
    });
@endif

function addCommitment() {
    const container = document.getElementById('commitments-container');
    const commitmentCount = container.children.length;
    
    const newEntry = document.createElement('div');
    newEntry.className = 'commitment-entry grid grid-cols-1 md:grid-cols-12 gap-4 mb-2';
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

function updateSections(departmentSelect, index) {
    const row = departmentSelect.closest('.commitment-entry');
    const sectionSelect = row.querySelector('.section-select');
    const picSelect = row.querySelector('.pic-select');
    
    // Reset dan disable dropdown
    sectionSelect.innerHTML = '<option value="">Pilih Seksi</option>';
    picSelect.innerHTML = '<option value="">Pilih PIC</option>';
    
    // Enable/disable dropdown seksi berdasarkan pilihan department
    if (departmentSelect.value) {
        sectionSelect.disabled = false;
        
        // Fetch data seksi
        fetch(`/api/sections/${departmentSelect.value}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(sections => {
            sections.forEach(section => {
                sectionSelect.add(new Option(section.name, section.id));
            });
        })
        .catch(error => {
            console.error('Error:', error);
            sectionSelect.disabled = true;
        });
    } else {
        sectionSelect.disabled = true;
        picSelect.disabled = true;
    }
}

function updatePics(sectionSelect, index) {
    const row = sectionSelect.closest('.commitment-entry');
    const picSelect = row.querySelector('.pic-select');
    
    // Reset dan disable dropdown PIC
    picSelect.innerHTML = '<option value="">Pilih PIC</option>';
    
    // Enable/disable dropdown PIC berdasarkan pilihan seksi
    if (sectionSelect.value) {
        picSelect.disabled = false;
        
        // Fetch data PIC
        fetch(`/api/pics/${sectionSelect.value}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(pics => {
            pics.forEach(pic => {
                picSelect.add(new Option(`${pic.name} (${pic.position})`, pic.id));
            });
        })
        .catch(error => {
            console.error('Error:', error);
            picSelect.disabled = true;
        });
    } else {
        picSelect.disabled = true;
    }
}

// Inisialisasi event listeners saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi dropdown untuk komitmen pertama
    const firstDepartmentSelect = document.querySelector('.department-select');
    if (firstDepartmentSelect) {
        firstDepartmentSelect.addEventListener('change', function() {
            updateSections(this, 0);
        });
    }

    const firstSectionSelect = document.querySelector('.section-select');
    if (firstSectionSelect) {
        firstSectionSelect.addEventListener('change', function() {
            updatePics(this, 0);
        });
    }
});

function removeCommitment(button) {
    button.closest('.commitment-entry').remove();
}
</script>
@push('scripts')
@endpush
@endsection 