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
                <form id="editDiscussionForm" 
                      action="{{ route('admin.other-discussions.update', $discussion->id) }}" 
                      method="POST" 
                      class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" 
                      enctype="multipart/form-data"
                      data-discussion-id="{{ $discussion->id }}">
                    @csrf
                    @method('PUT')

                    <!-- Ganti bagian form upload dokumen dengan yang baru -->
                    <div class="mb-6 border-b pb-6">
                        <h4 class="text-lg font-semibold mb-4">Upload Dokumen Pendukung <span class="text-red-500">*</span></h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- File Upload dengan Drag & Drop -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="documents">
                                    Dokumen (PDF/Word/Gambar)
                                </label>
                                <div 
                                    id="drop-zone"
                                    class="w-full min-h-[200px] px-3 py-2 border-2 border-dashed border-gray-300 rounded-md 
                                           hover:border-blue-500 transition-colors duration-200 ease-in-out
                                           flex flex-col items-center justify-center cursor-pointer bg-gray-50">
                                    <input type="file" 
                                           name="documents[]" 
                                           id="documents" 
                                           class="hidden"
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                           multiple>
                                    <div class="text-center" id="drop-zone-content">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                        <p class="text-gray-600 mb-2">Drag & drop file di sini atau</p>
                                        <button type="button" 
                                                id="selectFileBtn"
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm">
                                            Pilih File
                                        </button>
                                    </div>
                                    <div id="files-preview" class="hidden w-full">
                                        <div id="files-list" class="space-y-2">
                                            <!-- File previews will be added here -->
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">
                                        Format yang diizinkan: PDF, Word (doc/docx), Gambar (jpg/jpeg/png). Maksimal 5MB per file
                                    </p>
                                </div>
                            </div>

                            <!-- Deskripsi Dokumen -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Deskripsi Dokumen
                                </label>
                                <div id="descriptions-container">
                                    <!-- Deskripsi akan ditambahkan secara dinamis via JavaScript -->
                                </div>
                            </div>
                        </div>

                        <!-- Tampilkan dokumen yang sudah ada -->
                        @if($discussion->document_path)
                        <div class="mt-4">
                            <h5 class="text-sm font-semibold mb-2">Dokumen Saat Ini:</h5>
                            <div class="space-y-2">
                                @php
                                    $paths = json_decode($discussion->document_path) ?? [$discussion->document_path];
                                    $names = json_decode($discussion->document_description) ?? [$discussion->document_description];
                                @endphp
                                @foreach($paths as $index => $path)
                                <div class="flex items-center justify-between bg-white p-3 rounded-md shadow-sm">
                                    <div class="flex items-center">
                                        @php
                                            $extension = pathinfo($path, PATHINFO_EXTENSION);
                                            $iconClass = 'fa-file';
                                            $iconColor = 'text-blue-500';
                                            
                                            
                                            if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])) {
                                                $iconClass = 'fa-file-image';
                                                $iconColor = 'text-green-500';
                                            } elseif ($extension === 'pdf') {
                                                $iconClass = 'fa-file-pdf';
                                                $iconColor = 'text-red-500';
                                            } elseif (in_array($extension, ['doc', 'docx'])) {
                                                $iconClass = 'fa-file-word';
                                                $iconColor = 'text-blue-500';
                                            }
                                        @endphp
                                        <i class="fas {{ $iconClass }} {{ $iconColor }} mr-2"></i>
                                        <a href="{{ asset('storage/' . $path) }}" 
                                           target="_blank"
                                           class="text-blue-500 hover:text-blue-700">
                                            {{ $names[$index] ?? basename($path) }}
                                        </a>
                                    </div>
                                    <button type="button"
                                            onclick="removeExistingFile({{ $discussion->id }}, {{ $index }})"
                                            class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                                @endforeach
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

<!-- Password Verification Modal -->
<div id="passwordModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-xl w-96">
        <h3 class="text-lg font-semibold mb-4">Verifikasi Password</h3>
        <p class="text-sm text-gray-600 mb-4">Masukkan password Anda untuk melanjutkan penghapusan</p>
        
        <div class="mb-4">
            <input type="password" 
                   id="verificationPassword" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                   placeholder="Masukkan password">
            <p id="passwordError" class="text-red-500 text-sm mt-1 hidden"></p>
        </div>

        <div class="flex justify-end gap-2">
            <button onclick="closePasswordModal()" 
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                Batal
            </button>
            <button onclick="verifyPasswordAndDelete()" 
                    class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                Hapus
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let deleteAction = null;
let deleteParams = null;

// Debug info di awal file
console.log('Available Routes:', {
    'verify_password': "{{ route('admin.verify-password') }}",
    'base_url': "{{ url('/') }}",
    'current_url': "{{ request()->url() }}"
});

// Fungsi untuk menampilkan modal password
function showPasswordModal() {
    document.getElementById('passwordModal').classList.remove('hidden');
    document.getElementById('passwordModal').classList.add('flex');
    document.getElementById('verificationPassword').value = '';
    document.getElementById('passwordError').classList.add('hidden');
}

// Fungsi untuk menutup modal
function closePasswordModal() {
    document.getElementById('passwordModal').classList.add('hidden');
    document.getElementById('passwordModal').classList.remove('flex');
    deleteAction = null;
    deleteParams = null;
}

// Fungsi untuk verifikasi password dan melakukan penghapusan
async function verifyPasswordAndDelete() {
    try {
        const password = document.getElementById('verificationPassword').value;
        
        // Debug info
        const baseUrl = "{{ url('/admin') }}"; // Pastikan base URL benar
        console.log('Debug Info:', {
            'Base URL': baseUrl,
            'Current Path': window.location.pathname,
            'Delete Action': deleteAction,
            'Delete Params': deleteParams
        });

        // Verifikasi password
        const verifyResponse = await fetch(`${baseUrl}/verify-password`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ password })
        });

        const verifyData = await verifyResponse.json();

        if (!verifyData.success) {
            document.getElementById('passwordError').innerHTML = `
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <p>${verifyData.message || 'Password tidak valid'}</p>
                </div>
            `;
            return;
        }

        // Proses penghapusan dengan URL yang benar
        let deleteUrl;
        if (deleteAction === 'removeFile') {
            deleteUrl = `${baseUrl}/other-discussions/${deleteParams.discussionId}/remove-file/${deleteParams.fileIndex}`;
        } else if (deleteAction === 'removeCommitment') {
            const commitmentEntry = deleteParams.element.closest('.commitment-entry');
            const commitmentId = commitmentEntry.dataset.commitmentId;
            const discussionId = document.querySelector('form#editDiscussionForm').dataset.discussionId;
            deleteUrl = `${baseUrl}/other-discussions/${discussionId}/commitments/${commitmentId}`;
        }

        // Debug delete request
        console.log('Delete Request:', {
            'URL': deleteUrl,
            'Method': 'DELETE',
            'Headers': {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const deleteResponse = await fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!deleteResponse.ok) {
            throw new Error(`Delete failed with status: ${deleteResponse.status}`);
        }

        const deleteData = await deleteResponse.json();
        
        if (deleteData.success) {
            closePasswordModal();
            window.location.reload();
        } else {
            throw new Error(deleteData.message || 'Gagal melakukan penghapusan');
        }

    } catch (error) {
        console.error('Full Error Details:', {
            message: error.message,
            action: deleteAction,
            url: error.url || 'N/A',
            stack: error.stack
        });

        document.getElementById('passwordError').innerHTML = `
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <p class="font-bold">Error:</p>
                <p class="text-sm">${error.message}</p>
                <p class="text-sm mt-2">Action: ${deleteAction}</p>
                <p class="text-sm">URL: ${error.url || 'N/A'}</p>
            </div>
        `;
    }
}

// Tambahkan fungsi helper untuk debug
function debugRoutes() {
    console.log('Route Debug:', {
        'Base URL': "{{ url('/admin') }}",
        'Current URL': window.location.href,
        'Discussion ID': document.querySelector('form#editDiscussionForm')?.dataset?.discussionId
    });
}

// Panggil debug saat halaman dimuat
document.addEventListener('DOMContentLoaded', debugRoutes);

// Fungsi untuk menghapus file
function removeExistingFile(discussionId, fileIndex) {
    deleteAction = 'removeFile';
    deleteParams = { discussionId, fileIndex };
    showPasswordModal();
}

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
    const commitmentEntry = button.closest('.commitment-entry');
    // Jika ini komitmen baru (belum disimpan)
    if (!commitmentEntry.dataset.commitmentId) {
        commitmentEntry.remove();
        return;
    }
    
    // Jika ini komitmen yang sudah ada
    deleteAction = 'removeCommitment';
    deleteParams = { element: button };
    showPasswordModal();
}

// Submit handler yang dioptimasi
document.getElementById('editDiscussionForm').addEventListener('submit', function(e) {
    const status = document.getElementById('status').value;
    if (status === 'Closed' && !validateStatus(document.getElementById('status'))) {
        e.preventDefault();
    }

    const fileInput = document.getElementById('documents');
    const files = fileInput.files;
    
    // Validasi ukuran file (5MB per file)
    const maxSize = 5 * 1024 * 1024; // 5MB dalam bytes
    for (const file of files) {
        if (file.size > maxSize) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Ukuran File Terlalu Besar',
                text: `File "${file.name}" melebihi batas maksimal 5MB.`,
                confirmButtonText: 'OK'
            });
            return;
        }
    }

    // Validasi tipe file
    const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
    for (const file of files) {
        if (!allowedTypes.includes(file.type)) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Format File Tidak Didukung',
                text: `File "${file.name}" harus berformat PDF, Word, atau Gambar (JPG/PNG).`,
                confirmButtonText: 'OK'
            });
            return;
        }
    }
});

function handleFiles(e) {
    const files = Array.from(e.target.files);
    
    // Validasi file
    const invalidFiles = files.filter(file => {
        const validTypes = ['application/pdf', 'application/msword', 
                          'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                          'image/jpeg', 'image/png'];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!validTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Format File Tidak Didukung',
                text: `File "${file.name}" harus berformat PDF, Word, atau Gambar (JPG/PNG).`
            });
            return true;
        }
        
        if (file.size > maxSize) {
            Swal.fire({
                icon: 'error',
                title: 'Ukuran File Terlalu Besar',
                text: `File "${file.name}" melebihi batas maksimal 5MB.`
            });
            return true;
        }
        
        return false;
    });

    if (invalidFiles.length > 0) {
        fileInput.value = '';
        return;
    }

    // Show preview
    filesPreview.classList.remove('hidden');
    dropZoneContent.classList.add('hidden');
    filesList.innerHTML = '';
    
    // Clear existing descriptions
    const descriptionsContainer = document.getElementById('descriptions-container');
    descriptionsContainer.innerHTML = '';

    files.forEach((file, index) => {
        // Add description input for each file
        const descriptionInput = document.createElement('div');
        descriptionInput.className = 'mb-2';
        descriptionInput.innerHTML = `
            <label class="block text-sm text-gray-600 mb-1">${file.name}</label>
            <input type="text" 
                   name="document_descriptions[]" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Masukkan deskripsi dokumen"
                   required>
        `;
        descriptionsContainer.appendChild(descriptionInput);

        // Create file preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = createFilePreview(file, index, e.target.result);
            filesList.appendChild(preview);
        }
        if (file.type.startsWith('image/')) {
            reader.readAsDataURL(file);
        } else {
            reader.readAsArrayBuffer(file);
        }
    });
}

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

// Inisialisasi variabel di awal
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('documents');
const dropZoneContent = document.getElementById('drop-zone-content');
const filesPreview = document.getElementById('files-preview');
const filesList = document.getElementById('files-list');

// Event listener untuk drag & drop
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
    document.body.addEventListener(eventName, preventDefaults, false);
});

// Highlight drop zone saat drag over
['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener('dragleave', unhighlight, false);
});

// Handle dropped files
dropZone.addEventListener('drop', handleDrop, false);

// Handle file input change
fileInput.addEventListener('change', handleFiles);

// Handle click pada drop zone
dropZone.addEventListener('click', function(e) {
    // Jika yang diklik adalah area preview atau tombol remove, jangan trigger input file
    if (e.target.closest('#files-preview') || e.target.closest('button[onclick*="removeFile"]')) {
        return;
    }
    
    // Untuk klik di area lain, trigger input file
    fileInput.click();
});

// Tambahkan event listener untuk tombol pilih file
document.getElementById('selectFileBtn').addEventListener('click', function(e) {
    e.stopPropagation();
    fileInput.click();
});

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

function validateFile(file) {
    // Validasi tipe file
    const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
    if (!allowedTypes.includes(file.type)) {
        Swal.fire({
            icon: 'error',
            title: 'Format File Tidak Didukung',
            text: 'Format file yang diizinkan: PDF, Word, atau Gambar (JPG/PNG).',
            confirmButtonText: 'OK'
        });
        return false;
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
        return false;
    }
    
    return true;
}

function getFileIconClass(fileType) {
    if (fileType.startsWith('image/')) {
        return 'fa-file-image text-green-500';
    } else if (fileType === 'application/pdf') {
        return 'fa-file-pdf text-red-500';
    } else if (fileType.includes('word') || fileType.includes('document')) {
        return 'fa-file-word text-blue-500';
    }
    return 'fa-file text-blue-500';
}

function removeFile() {
    fileInput.value = '';
    dropZoneContent.classList.remove('hidden');
    filesPreview.classList.add('hidden');
    filesList.innerHTML = '';
}
</script>
@endpush

@push('styles')
<style>
/* Animasi untuk drag & drop */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

.animate-pulse {
    animation: pulse 1s infinite;
}

/* Animasi untuk fade in */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}

/* Styling untuk drop zone */
#drop-zone {
    transition: all 0.3s ease;
}

#drop-zone.drag-over {
    border-color: #3B82F6;
    background-color: #EFF6FF;
}

/* Styling untuk file preview */
.file-preview {
    transition: all 0.2s ease;
}

.file-preview:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Progress bar styling */
.upload-progress {
    width: 100%;
    height: 4px;
    background-color: #E5E7EB;
    border-radius: 2px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background-color: #3B82F6;
    transition: width 0.3s ease;
}

/* File type badges */
.file-type-badge {
    padding: 2px 8px;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.file-type-badge.pdf {
    background-color: #FEE2E2;
    color: #DC2626;
}

.file-type-badge.image {
    background-color: #E0E7FF;
    color: #4F46E5;
}

.file-type-badge.word {
    background-color: #DBEAFE;
    color: #2563EB;
}

/* Hover effects for buttons */
.action-button {
    transition: all 0.2s ease;
}

.action-button:hover {
    transform: scale(1.05);
}

/* Loading spinner */
.spinner {
    width: 20px;
    height: 20px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3B82F6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush
@endsection