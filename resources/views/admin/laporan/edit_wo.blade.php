@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.sidebar')

    <div id="main-content" class="flex-1 main-content">
        <header class="bg-white shadow-sm sticky top-0 z-20">
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
                    <!-- Desktop Menu Toggle -->
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
                    <h1 class="text-xl font-semibold text-gray-800">Edit Work Order (WO)</h1>
                </div>
                <div class="flex items-center gap-x-4 relative">
                    <!-- User Dropdown -->
                    <div class="relative">
                        <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                            <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                                class="w-8 h-8 rounded-full mr-2">
                            <span class="text-gray-700">{{ Auth::user()->name }}</span>
                            <i class="fas fa-caret-down ml-2"></i>
                        </button>
                        <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                            <a href="{{ route('user.profile') }}"
                                class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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
                                    <label for="labor" class="block text-gray-700 font-medium mb-2">Labor (Nama & Jabatan)</label>
                                    <select name="labor" id="labor" class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                                        <option value="">Pilih Labor...</option>
                                        @foreach($users as $user)
                                            @if(strtolower($user->role) === 'pemeliharaan')
                                                @php
                                                    $laborName = strtoupper(explode(' ', trim($user->name))[0]) . ' PEMELIHARAAN';
                                                @endphp
                                                <option value="{{ $laborName }}" {{ old('labor', $workOrder->labor) == $laborName ? 'selected' : '' }}>{{ explode(' ', trim($user->name))[0] }} - Pemeliharaan</option>
                                            @endif
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

                                @if($workOrder->status == 'WMATL')
                                <div id="materialsSection" class="mb-4">
                                    <label class="block text-gray-700 font-medium mb-2">Material (dari Material Master)</label>
                                    <div class="mb-2">
                                        <input type="text" id="materialSearch" placeholder="Cari material..." class="w-full px-3 py-2 border rounded-md" />
                                    </div>
                                    <div id="materialList" class="max-h-60 overflow-auto border rounded p-2 bg-white">
                                        @foreach($materials as $m)
                                            @php
                                                $keywords = strtolower($m->warehouse);
                                                $warehouseKeywords = [
                                                    '2020' => 'updk',
                                                    '3011' => 'pltd wua wua',
                                                    '3012' => 'pltd bau bau',
                                                    '3013' => 'pltd kolaka',
                                                    '3014' => 'pltd poasia',
                                                    '3015' => 'pltu tanasa',
                                                    '3016' => 'pltd raha',
                                                    '3017' => 'pltd wangi',
                                                    '3018' => 'pltd lambuya',
                                                    '3022' => 'pltmg tanasa',
                                                    '3023' => 'pltm mikuasi',
                                                    '3035' => 'pltd pasarwajo',
                                                    '3047' => 'pltd ladumpi',
                                                    '4048' => 'pltd lanipa',
                                                    '3049' => 'pltd ereke',
                                                    '3050' => 'pltd langara',
                                                    '3054' => 'pltm rongi',
                                                    '3053' => 'pltmg bau bau',
                                                ];
                                                foreach ($warehouseKeywords as $code => $kw) {
                                                    if ($m->warehouse == $code) {
                                                        $keywords .= ' ' . $kw;
                                                    }
                                                }
                                            @endphp
                                            <div class="flex items-center justify-between py-1 border-b last:border-b-0" data-keywords="{{ $keywords }}">
                                                <div>
                                                    <span class="font-mono text-sm">{{ $m->stock_code }}</span>
                                                    <span class="ml-2">{{ $m->inventory_statistic_desc }}</span>
                                                    <span class="ml-2">{{ $m->warehouse }}</span>
                                                </div>
                                                <button type="button" class="text-blue-600 text-sm add-material"
                                                    data-code="{{ $m->stock_code }}"
                                                    data-statdesc="{{ $m->inventory_statistic_desc }}"
                                                    data-warehouse="{{ $m->warehouse }}"
                                                    data-description="{{ $m->description }}"
                                                    data-statcode="{{ $m->inventory_statistic_code }}">
                                                    Tambah
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-3">
                                        <h4 class="font-semibold mb-2">Material dipilih</h4>
                                        <div id="selectedMaterials" class="space-y-2">
                                            @php
                                                // Helper untuk melengkapi data lama
                                                function completeMaterial($item, $materials) {
                                                    $found = $materials->firstWhere('stock_code', $item['code'] ?? null);
                                                    $item['description'] = $item['description'] ?? ($found->description ?? '-');
                                                    $item['inventory_statistic_code'] = $item['inventory_statistic_code'] ?? ($found->inventory_statistic_code ?? '-');
                                                    return $item;
                                                }
                                            @endphp
                                            @php $existingMaterials = old('materials', $workOrder->materials ?? []); @endphp
                                            @if(is_array($existingMaterials))
                                                @foreach($existingMaterials as $idx => $item)
                                                    @php $item = completeMaterial($item, $materials); @endphp
                                                    <div class="flex items-center gap-2">
                                                        <input type="hidden" name="materials[{{ $idx }}][code]" value="{{ $item['code'] ?? '' }}" />
                                                        <input type="hidden" name="materials[{{ $idx }}][inventory_statistic_desc]" value="{{ $item['inventory_statistic_desc'] ?? '' }}" />
                                                        <input type="hidden" name="materials[{{ $idx }}][warehouse]" value="{{ $item['warehouse'] ?? '' }}" />
                                                        <input type="hidden" name="materials[{{ $idx }}][description]" value="{{ $item['description'] ?? '' }}" />
                                                        <input type="hidden" name="materials[{{ $idx }}][inventory_statistic_code]" value="{{ $item['inventory_statistic_code'] ?? '' }}" />
                                                        <span class="px-2 py-1 bg-gray-100 rounded text-sm">{{ $item['code'] ?? '' }} - {{ $item['inventory_statistic_desc'] ?? '' }} - {{ $item['warehouse'] ?? '' }}</span>
                                                        <input type="number" step="0.01" name="materials[{{ $idx }}][qty]" value="{{ $item['qty'] ?? 1 }}" class="w-24 px-2 py-1 border rounded" placeholder="Qty" />
                                                        <button type="button" class="text-red-600 remove-material">Hapus</button>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif

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

                        <!-- Tambah Status -->
                        <div class="mb-4">
                            <label for="status" class="block text-gray-700 font-medium mb-2">Status</label>
                            <select name="status" id="status" 
                                class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                                @foreach(['Open', 'Closed', 'Comp', 'APPR', 'WAPPR', 'WMATL'] as $status)
                                    <option value="{{ $status }}" {{ $workOrder->status == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
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

    const unitSelect = document.getElementById('unit');
    const laborSelect = document.getElementById('labor');
    // Mapping id unit ke laborName
    const unitLaborMap = {
        11: 'MIKUASI PEMELIHARAAN',
        10: 'SABILAMBO PEMELIHARAAN',
        15: 'WINNING PEMELIHARAAN',
        32: 'RONGI PEMELIHARAAN',
        20: 'EREKE PEMELIHARAAN',
        13: 'PASARWAJO PEMELIHARAAN',
        12: 'BAU-BAU PEMELIHARAAN',
        17: 'RAHA PEMELIHARAAN',
        4: 'POASIA PEMELIHARAAN',
        3: 'WUA-WUA PEMELIHARAAN',
        19: 'LANGARA PEMELIHARAAN',
        7: 'KOLAKA PEMELIHARAAN'
    };
    unitSelect.addEventListener('change', function() {
        const laborName = unitLaborMap[this.value];
        if (laborName) {
            for (let i = 0; i < laborSelect.options.length; i++) {
                if (laborSelect.options[i].value.toUpperCase() === laborName.toUpperCase()) {
                    laborSelect.selectedIndex = i;
                    break;
                }
            }
        }
    });

    // Show materials section only when current status is WMATL
    const materialsSection = document.getElementById('materialsSection');
    if (materialsSection) {
        const initialStatus = '{{ $workOrder->status }}';
        materialsSection.style.display = initialStatus === 'WMATL' ? 'block' : 'none';
    }

    // Simple client-side filter for material list
    const materialSearch = document.getElementById('materialSearch');
    if (materialSearch) {
        materialSearch.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#materialList > div').forEach(row => {
                const keywords = row.getAttribute('data-keywords') || '';
                row.style.display = row.textContent.toLowerCase().includes(q) || keywords.includes(q) ? '' : 'none';
            });
        });
    }

    // Add/remove selected materials
    let materialsIndex = document.querySelectorAll('#selectedMaterials > div').length;
    document.querySelectorAll('.add-material').forEach(btn => {
        btn.addEventListener('click', function() {
            const code = this.dataset.code;
            const statDesc = this.dataset.statdesc;
            const warehouse = this.dataset.warehouse;
            const description = this.dataset.description;
            const statCode = this.dataset.statcode;

            const wrap = document.createElement('div');
            wrap.className = 'flex items-center gap-2';
            wrap.innerHTML = `
                <input type="hidden" name="materials[${materialsIndex}][code]" value="${code}" />
                <input type="hidden" name="materials[${materialsIndex}][inventory_statistic_desc]" value="${statDesc}" />
                <input type="hidden" name="materials[${materialsIndex}][warehouse]" value="${warehouse}" />
                <input type="hidden" name="materials[${materialsIndex}][description]" value="${description}" />
                <input type="hidden" name="materials[${materialsIndex}][inventory_statistic_code]" value="${statCode}" />
                <span class="px-2 py-1 bg-gray-100 rounded text-sm">${code} - ${statDesc} - ${warehouse}</span>
                <input type="number" step="0.01" name="materials[${materialsIndex}][qty]" value="1" class="w-24 px-2 py-1 border rounded" placeholder="Qty" />
                <button type="button" class="text-red-600 remove-material">Hapus</button>
            `;
            const container = document.getElementById('selectedMaterials');
            if (container) {
                container.appendChild(wrap);
                materialsIndex++;
                wrap.querySelector('.remove-material').addEventListener('click', () => wrap.remove());
            }
        });
    });
    document.querySelectorAll('#selectedMaterials .remove-material').forEach(btn => {
        btn.addEventListener('click', function() { this.closest('div').remove(); });
    });
});
</script>
@endpush
@endsection 