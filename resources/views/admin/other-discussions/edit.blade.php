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
                                        $shortName = Str::limit($powerPlant->name, 50, '');
                                    @endphp
                                    <option value="{{ $shortName }}" {{ old('unit', $discussion->unit) == $shortName ? 'selected' : '' }}>
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
                                    <option value="1" {{ old('department_id', $discussion->department_id) == 1 ? 'selected' : '' }}>BAGIAN OPERASI</option>
                                    <option value="2" {{ old('department_id', $discussion->department_id) == 2 ? 'selected' : '' }}>BAGIAN PEMELIHARAAN</option>
                                    <option value="3" {{ old('department_id', $discussion->department_id) == 3 ? 'selected' : '' }}>BAGIAN ENJINIRING & QUALITY ASSURANCE</option>
                                    <option value="4" {{ old('department_id', $discussion->department_id) == 4 ? 'selected' : '' }}>BAGIAN BUSINESS SUPPORT</option>
                                    <option value="5" {{ old('department_id', $discussion->department_id) == 5 ? 'selected' : '' }}>HSE</option>
                                    <option value="6" {{ old('department_id', $discussion->department_id) == 6 ? 'selected' : '' }}>UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL</option>
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

                        <!-- Komitmen -->
                        <div class="mb-4 md:col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Komitmen
                            </label>
                            <div id="commitments-container">
                                @foreach($discussion->commitments as $commitment)
                                <div class="commitment-entry grid grid-cols-1 md:grid-cols-6 gap-4 mb-4 p-4 border rounded-lg">
                                    <div class="md:col-span-8">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="flex items-center">
                                                <span class="text-sm font-medium mr-2">Status:</span>
                                                <select name="commitment_status[]" 
                                                        class="status-select text-sm px-3 py-1.5 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                        onchange="updateCommitmentStatus(this)">
                                                    <option value="open" {{ $commitment->status == 'open' ? 'selected' : '' }}>Open</option>
                                                    <option value="closed" {{ $commitment->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                                </select>
                                            </div>
                                            
                                            <div class="flex items-center">
                                                <span class="text-sm font-medium mr-2">Deadline:</span>
                                                <input type="date" 
                                                       name="commitment_deadlines[]" 
                                                       value="{{ $commitment->deadline ? date('Y-m-d', strtotime($commitment->deadline)) : '' }}"
                                                       class="text-sm px-3 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                       required>
                                            </div>
                                        </div>

                                        <div class="relative">
                                            <textarea name="commitments[]" 
                                                      class="commitment-text w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                      rows="3"
                                                      required>{{ $commitment->description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="md:col-span-4">
                                        <div class="relative">
                                            <select name="commitment_department_ids[]" 
                                                    class="department-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 mb-2"
                                                    onchange="updateCommitmentSections(this)"
                                                    required>
                                                <option value="">Pilih Bagian</option>
                                                <option value="1" {{ $commitment->department_id == 1 ? 'selected' : '' }}>BAGIAN OPERASI</option>
                                                <option value="2" {{ $commitment->department_id == 2 ? 'selected' : '' }}>BAGIAN PEMELIHARAAN</option>
                                                <option value="3" {{ $commitment->department_id == 3 ? 'selected' : '' }}>BAGIAN ENJINIRING & QUALITY ASSURANCE</option>
                                                <option value="4" {{ $commitment->department_id == 4 ? 'selected' : '' }}>BAGIAN BUSINESS SUPPORT</option>
                                                <option value="5" {{ $commitment->department_id == 5 ? 'selected' : '' }}>HSE</option>
                                                <option value="6" {{ $commitment->department_id == 6 ? 'selected' : '' }}>UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL</option>
                                            </select>

                                            <select name="commitment_section_ids[]" 
                                                    class="section-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    required
                                                    data-selected="{{ $commitment->section_id }}">
                                                <option value="">Pilih Seksi</option>
                                            </select>
                                        </div>
                                    </div>
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
// Data sections
const sectionsData = {
    '1': [ // BAGIAN OPERASI
        {id: 1, name: 'SEKSI RENDAL OP & NIAGA'},
        {id: 2, name: 'SEKSI BAHAN BAKAR'},
        {id: 3, name: 'SEKSI OUTAGE MGT'}
    ],
    '2': [ // BAGIAN PEMELIHARAAN
        {id: 4, name: 'SEKSI PERENCANAAN PENGENDALIAN PEMELIHARAAN'},
        {id: 5, name: 'SEKSI INVENTORI KONTROL & GUDANG'}
    ],
    '3': [ // BAGIAN ENJINIRING & QUALITY ASSURANCE
        {id: 6, name: 'SEKSI SYSTEM OWNER'},
        {id: 7, name: 'SEKSI CONDITION BASED MAINTENANCE'},
        {id: 8, name: 'SEKSI MMRK'}
    ],
    '4': [ // BAGIAN BUSINESS SUPPORT
        {id: 9, name: 'SEKSI SDM, UMUM & CSR'},
        {id: 10, name: 'SEKSI KEUANGAN'},
        {id: 11, name: 'SEKSI PENGADAAN'}
    ],
    '5': [ // HSE
        {id: 12, name: 'SEKSI LINGKUNGAN'},
        {id: 13, name: 'SEKSI K3 & KEAMANAN'}
    ],
    '6': [ // UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL
        {id: 14, name: 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL BAU-BAU'},
        {id: 15, name: 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL KOLAKA'},
        {id: 16, name: 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL POASIA'},
        {id: 17, name: 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL WUA-WUA'}
    ]
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
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    const departmentSelect = document.getElementById('department_select');
    updateSections(departmentSelect.value);
    
    // Set selected section if exists
    const currentSectionId = '{{ $discussion->section_id }}';
    if (currentSectionId) {
        const sectionSelect = document.getElementById('section_select');
        const options = sectionSelect.options;
        for (let i = 0; i < options.length; i++) {
            if (options[i].value === currentSectionId) {
                options[i].selected = true;
                break;
            }
        }
    }
});

function updateCommitmentSections(departmentSelect) {
    const sectionSelect = departmentSelect.parentElement.querySelector('.section-select');
    const selectedSectionId = sectionSelect.dataset.selected;
    const departmentId = departmentSelect.value;
    
    // Kosongkan dropdown seksi
    sectionSelect.innerHTML = '<option value="">Pilih Seksi</option>';
    
    if (departmentId) {
        // Data mapping seksi untuk setiap departemen
        const sections = {
            '1': [ // BAGIAN OPERASI
                {id: 1, name: 'SEKSI OPERASI PLTD'},
                {id: 2, name: 'SEKSI OPERASI PLTG/U'},
                {id: 3, name: 'SEKSI OPERASI PLTA/M'}
            ],
            '2': [ // BAGIAN PEMELIHARAAN
                {id: 4, name: 'SEKSI PERENCANAAN PENGENDALIAN PEMELIHARAAN'},
                {id: 5, name: 'SEKSI INVENTORI KONTROL & GUDANG'}
            ],
            '3': [ // BAGIAN ENJINIRING & QUALITY ASSURANCE
                {id: 6, name: 'SEKSI SYSTEM OWNER'},
                {id: 7, name: 'SEKSI CONDITION BASED MAINTENANCE'},
                {id: 8, name: 'SEKSI MMRK'}
            ],
            '4': [ // BAGIAN BUSINESS SUPPORT
                {id: 9, name: 'SEKSI SDM, UMUM & CSR'},
                {id: 10, name: 'SEKSI KEUANGAN'},
                {id: 11, name: 'SEKSI LOGISTIK'}
            ],
            '5': [ // HSE
                {id: 12, name: 'SEKSI K3'},
                {id: 13, name: 'SEKSI LINGKUNGAN'},
                {id: 14, name: 'SEKSI KEAMANAN'}
            ],
            '6': [ // UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL
                {id: 15, name: 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL KOLAKA'},
                {id: 16, name: 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL RAHA'},
                {id: 17, name: 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL WANGI-WANGI'},
                {id: 18, name: 'UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL BAUBAU'}
            ]
        };

        // Tambahkan opsi seksi sesuai departemen yang dipilih
        sections[departmentId].forEach(section => {
            const option = document.createElement('option');
            option.value = section.id;
            option.textContent = section.name;
            option.selected = section.id == selectedSectionId;
            sectionSelect.appendChild(option);
        });
    }
}

// Initialize sections untuk setiap komitmen saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.department-select').forEach(select => {
        updateCommitmentSections(select);
    });
});

// Fungsi untuk mengupdate status komitmen
function updateCommitmentStatus(statusSelect) {
    const status = statusSelect.value;
    statusSelect.className = 'status-select text-sm px-3 py-1.5 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 ' + 
        (status === 'open' ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : 
         'bg-green-100 text-green-800 border border-green-300');
}

// Fungsi untuk menambah komitmen baru
function addCommitment() {
    const container = document.getElementById('commitments-container');
    const newEntry = document.createElement('div');
    newEntry.className = 'commitment-entry grid grid-cols-1 md:grid-cols-12 gap-4 mb-2';
    
    newEntry.innerHTML = `
        <div class="md:col-span-8">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-2">
                <div class="flex items-center">
                    <span class="text-sm font-medium mr-2">Status:</span>
                    <select name="commitment_status[]" 
                            class="status-select text-sm px-3 py-1.5 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                            onchange="updateCommitmentStatus(this)">
                        <option value="open">Open</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                
                <div class="flex items-center">
                    <span class="text-sm font-medium mr-2">Deadline:</span>
                    <input type="date" 
                           name="commitment_deadlines[]" 
                           class="text-sm px-3 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                           required>
                </div>
            </div>

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
                    <option value="1">BAGIAN OPERASI</option>
                    <option value="2">BAGIAN PEMELIHARAAN</option>
                    <option value="3">BAGIAN ENJINIRING & QUALITY ASSURANCE</option>
                    <option value="4">BAGIAN BUSINESS SUPPORT</option>
                    <option value="5">HSE</option>
                    <option value="6">UNIT LAYANAN PUSAT LISTRIK TENAGA DIESEL</option>
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
}

// Initialize semua dropdown saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.status-select').forEach(select => {
        updateCommitmentStatus(select);
    });
    
    document.querySelectorAll('.department-select').forEach(select => {
        updateCommitmentSections(select);
    });
});
</script>
@endpush
@endsection         