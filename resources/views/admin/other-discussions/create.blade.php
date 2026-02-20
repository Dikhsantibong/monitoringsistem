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
            <div class="flex items-center justify-between">
                <h3 class="text-gray-700 text-3xl font-medium">Manajemen Pembahasan</h3>
                
                <!-- Tab Switching -->
                <div class="flex bg-gray-200 rounded-lg p-1">
                    <button onclick="switchTab('form')" id="tab-form" class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 bg-white text-blue-600 shadow-sm">
                        <i class="fas fa-plus-circle mr-2"></i>Tambah Baru
                    </button>
                    <button onclick="switchTab('data')" id="tab-data" class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 text-gray-600 hover:text-gray-800">
                        <i class="fas fa-list mr-2"></i>Data Pembahasan
                    </button>
                    <button onclick="switchTab('weekly')" id="tab-weekly" class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 text-gray-600 hover:text-gray-800">
                        <i class="fas fa-calendar-alt mr-2"></i>Data Weekly
                    </button>
                </div>
            </div>

            <div id="form-container" class="mt-8">
                @php
                    // Ambil default unit dari parameter atau fallback ke 'UP KENDARI'
                    $selectedUnit = $defaultMachineId ? 'UP KENDARI' : old('unit', $defaultUnit ?? null);
                    $autoTopic = $defaultMachineName ? 'Issue pada ' . $defaultMachineName : old('topic', request('topic'));
                    $autoCommitment = $defaultMachineName ? 'Penyelesaian issue pada ' . $defaultMachineName : old('commitments.0', request('default_commitment'));
                @endphp

                <form id="createDiscussionForm" action="{{ route('admin.other-discussions.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" onsubmit="return validateForm()">
                    @csrf

                    @if($defaultMachineId)
                        <input type="hidden" name="machine_id" value="{{ $defaultMachineId }}">
                        <input type="hidden" name="machine_reference" value="{{ $defaultMachineName }}">
                        <input type="hidden" name="issue_active" value="1">
                        @php
                            // Ambil unit_asal dari PowerPlant berdasarkan machine_id
                            $machine = \App\Models\Machine::find($defaultMachineId);
                            $unitAsal = $machine && $machine->power_plant ? $machine->power_plant->name : null;
                        @endphp
                        <input type="hidden" name="unit_asal" value="{{ $unitAsal }}">
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- No SR (manual input) -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="sr_number">
                                No SR/WO <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <input type="text" 
                                       name="sr_number" 
                                       id="sr_number" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                       placeholder="WO12345/SR12345"
                                       required>
                            </div>
                            @error('sr_number')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Oracle Data Lookup Results -->
                        <div id="oracle_lookup_container" class="mb-4 md:col-span-2 hidden">
                            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-md shadow-sm">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-database text-blue-500 mr-2"></i>
                                    <h4 class="text-blue-800 font-bold text-sm">Data Maximo (Oracle) Terdeteksi</h4>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mt-2">
                                    <div>
                                        <p class="text-gray-600 font-semibold text-xs">Deskripsi:</p>
                                        <p id="oracle_description" class="text-gray-800 font-medium">-</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 font-semibold text-xs">Status Oracle:</p>
                                        <p id="oracle_status" class="text-gray-800 font-medium">-</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 font-semibold text-xs">Lokasi:</p>
                                        <p id="oracle_location" class="text-gray-800 font-medium">-</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 font-semibold text-xs">Tanggal Report:</p>
                                        <p id="oracle_report_date" class="text-gray-800 font-medium">-</p>
                                    </div>
                                </div>
                            </div>
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
                                @foreach($units as $unit)
                                    <option value="{{ $unit }}" {{ $selectedUnit == $unit ? 'selected' : '' }}>{{ $unit }}</option>
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
                                   value="{{ $autoTopic }}"
                                   required>
                            @error('topic')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Weekly Label -->
                        @if(session('unit') === 'mysql')
                        <div class="mb-4 flex items-center h-full pt-6">
                            <label class="inline-flex items-center cursor-pointer">
                                <span class="relative">
                                    <input type="checkbox" 
                                           name="is_weekly" 
                                           id="is_weekly" 
                                           value="1"
                                           class="sr-only peer"
                                           {{ old('is_weekly', request('is_weekly')) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </span>
                                <span class="ml-3 text-sm font-bold text-gray-700">Pembahasan Weekly</span>
                            </label>
                             @error('is_weekly')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        @else
                        <input type="hidden" name="is_weekly" value="0">
                        @endif

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
                                                    <option value="Open" selected>Open</option>
                                                    <option value="Closed">Closed</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Deadline Input -->
                                            <div class="flex items-center">
                                                <span class="text-sm font-medium mr-2">Deadline:</span>
                                                <input type="date" 
                                                       name="commitment_deadlines[]" 
                                                       class="text-sm px-3 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                       value="{{ date('Y-m-d', strtotime('+7 days')) }}"
                                                       required>
                                            </div>
                                        </div>

                                        <!-- Commitment Textarea -->
                                        <div class="relative">
                                            <textarea name="commitments[]" 
                                                      class="commitment-text w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                      rows="3"
                                                      placeholder="Masukkan komitmen"
                                                      required>{{ $autoCommitment }}</textarea>
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

            <!-- Table View Container (Hidden by default) -->
            <div id="data-container" class="mt-8 hidden animate-fade-in">
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h4 class="text-lg font-semibold text-gray-800">Daftar Pembahasan Terakhir</h4>
                        <div class="flex gap-2">
                            <input type="text" id="table-search" placeholder="Cari topik/nomor..." 
                                   class="px-3 py-1.5 border border-gray-300 rounded-md text-xs focus:ring-1 focus:ring-blue-500 outline-none">
                            <button onclick="loadDiscussionsData()" class="bg-blue-50 text-blue-600 p-2 rounded-md hover:bg-blue-100 transition-colors">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Topik</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Unit</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Target</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">PIC</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody id="discussions-table-body" class="bg-white divide-y divide-gray-200">
                                <!-- Data will be loaded via AJAX -->
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-spinner fa-spin text-3xl mb-2 text-blue-500"></i>
                                            <p>Memuat data...</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Weekly Data View Container (Hidden by default) -->
            <div id="weekly-container" class="mt-8 hidden animate-fade-in">
                <!-- Review Minggu Kemarin -->
                <div class="mb-8">
                    <h4 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">
                        <i class="fas fa-history text-blue-500 mr-2"></i>Review Minggu Kemarin ({{ $lastWeekStart->format('d M') }} - {{ $lastWeekEnd->format('d M Y') }})
                    </h4>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Completed WOs -->
                        <div class="bg-white shadow-md rounded-lg overflow-hidden">
                            <div class="px-4 py-3 bg-green-50 border-b border-green-100">
                                <h5 class="font-semibold text-green-800">Pekerjaan Selesai (Completed)</h5>
                            </div>
                            <div class="overflow-x-auto max-h-96">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left font-medium text-gray-500">WO</th>
                                            <th class="px-4 py-2 text-left font-medium text-gray-500">Deskripsi</th>
                                            <th class="px-4 py-2 text-left font-medium text-gray-500">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @forelse($reviewCompletedWOs as $wo)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2 font-medium text-blue-600">{{ $wo->wonum }}</td>
                                                <td class="px-4 py-2">{{ $wo->description }}</td>
                                                <td class="px-4 py-2">
                                                    <span class="px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800">{{ $wo->status }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="px-4 py-4 text-center text-gray-500">Tidak ada data</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Created WOs -->
                        <div class="bg-white shadow-md rounded-lg overflow-hidden">
                            <div class="px-4 py-3 bg-blue-50 border-b border-blue-100">
                                <h5 class="font-semibold text-blue-800">WO Terbit (Created)</h5>
                            </div>
                            <div class="overflow-x-auto max-h-96">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left font-medium text-gray-500">WO</th>
                                            <th class="px-4 py-2 text-left font-medium text-gray-500">Deskripsi</th>
                                            <th class="px-4 py-2 text-left font-medium text-gray-500">Pelapor</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @forelse($reviewCreatedWOs as $wo)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2 font-medium text-blue-600">{{ $wo->wonum }}</td>
                                                <td class="px-4 py-2">{{ $wo->description }}</td>
                                                <td class="px-4 py-2 text-gray-500">{{ $wo->worktype }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="px-4 py-4 text-center text-gray-500">Tidak ada data</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Planning Minggu Depan -->
                <div>
                    <h4 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">
                        <i class="fas fa-calendar-check text-purple-500 mr-2"></i>Planning Minggu Depan ({{ $nextWeekStart->format('d M') }} - {{ $nextWeekEnd->format('d M Y') }})
                    </h4>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- PM -->
                        <div class="bg-white shadow-md rounded-lg overflow-hidden">
                            <div class="px-4 py-3 bg-purple-50 border-b border-purple-100">
                                <h5 class="font-semibold text-purple-800">Rencana PM</h5>
                            </div>
                            <div class="overflow-x-auto max-h-96">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left font-medium text-gray-500">WO</th>
                                            <th class="px-4 py-2 text-left font-medium text-gray-500">Deskripsi</th>
                                            <th class="px-4 py-2 text-left font-medium text-gray-500">Jadwal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @forelse($planPMs as $wo)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2 font-medium text-blue-600">{{ $wo->wonum }}</td>
                                                <td class="px-4 py-2">{{ $wo->description }}</td>
                                                <td class="px-4 py-2 text-xs">
                                                    Start: {{ date('d-M', strtotime($wo->schedstart)) }}<br>
                                                    Fin: {{ date('d-M', strtotime($wo->schedfinish)) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="px-4 py-4 text-center text-gray-500">Tidak ada data</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Backlog -->
                        <div class="bg-white shadow-md rounded-lg overflow-hidden">
                            <div class="px-4 py-3 bg-orange-50 border-b border-orange-100">
                                <h5 class="font-semibold text-orange-800">Backlog / Corrective</h5>
                            </div>
                            <div class="overflow-x-auto max-h-96">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left font-medium text-gray-500">WO</th>
                                            <th class="px-4 py-2 text-left font-medium text-gray-500">Deskripsi</th>
                                            <th class="px-4 py-2 text-left font-medium text-gray-500">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @forelse($planBacklog as $wo)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2 font-medium text-blue-600">{{ $wo->wonum }}</td>
                                                <td class="px-4 py-2">{{ $wo->description }}</td>
                                                <td class="px-4 py-2">
                                                    <span class="px-2 py-0.5 rounded text-xs font-semibold bg-orange-100 text-orange-800">{{ $wo->status }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="px-4 py-4 text-center text-gray-500">Tidak ada data</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
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

    // Oracle Lookup Logic
    let lookupTimer;
    const srNumberInput = document.getElementById('sr_number');
    const isWeeklyCheckbox = document.getElementById('is_weekly');
    const oracleContainer = document.getElementById('oracle_lookup_container');

    function performOracleLookup() {
        const srNumber = srNumberInput.value.trim();
        const isWeekly = isWeeklyCheckbox.checked;

        // Reset visibility if criteria not met
        if (!isWeekly || srNumber.length < 3) {
            oracleContainer.classList.add('hidden');
            return;
        }

        clearTimeout(lookupTimer);
        lookupTimer = setTimeout(async () => {
            try {
                const response = await fetch(`{{ route('admin.other-discussions.search-oracle') }}?number=${encodeURIComponent(srNumber)}`);
                const result = await response.json();

                if (result.success) {
                    const data = result.data;
                    document.getElementById('oracle_description').textContent = data.DESCRIPTION || data.description || '-';
                    document.getElementById('oracle_status').textContent = data.STATUS || data.status || '-';
                    document.getElementById('oracle_location').textContent = data.LOCATION || data.location || '-';
                    document.getElementById('oracle_report_date').textContent = data.REPORTDATE || data.reportdate || '-';
                    oracleContainer.classList.remove('hidden');
                } else {
                    oracleContainer.classList.add('hidden');
                }
            } catch (error) {
                console.error('Oracle lookup error:', error);
                oracleContainer.classList.add('hidden');
            }
        }, 500); // Debounce 500ms
    }

    if (srNumberInput && isWeeklyCheckbox) {
        srNumberInput.addEventListener('input', performOracleLookup);
        isWeeklyCheckbox.addEventListener('change', performOracleLookup);
        
        // Check on initial load if pre-filled
        if (srNumberInput.value) {
            performOracleLookup();
        }
    }
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
        console.error('JavaScript Error:', error);
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
@push('scripts')
<script>
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
@endpush
@push('scripts')
@endpush

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if redirected from login
    const redirectUrl = sessionStorage.getItem('redirectAfterLogin');
    if (redirectUrl) {
        // Clear the stored URL
        sessionStorage.removeItem('redirectAfterLogin');
        
        // Parse the URL parameters
        const urlParams = new URLSearchParams(new URL(redirectUrl).search);
        
        // Auto-fill the fields
        if (urlParams.has('topic')) {
            document.getElementById('topic').value = urlParams.get('topic');
        }
        
        if (urlParams.has('default_commitment')) {
            const commitmentTextarea = document.querySelector('textarea[name="commitments[]"]');
            if (commitmentTextarea) {
                commitmentTextarea.value = urlParams.get('default_commitment');
            }
        }
        
        if (urlParams.has('unit')) {
            const unitSelect = document.getElementById('unit');
            if (unitSelect) {
                unitSelect.value = urlParams.get('unit');
            }
        }
    }
    
    // Handle the existing form fields
    const urlParams = new URLSearchParams(window.location.search);
    
    // Auto-fill topic if provided
    const topic = urlParams.get('topic');
    if (topic) {
        document.getElementById('topic').value = topic;
    }
    
    // Auto-fill first commitment if provided
    const defaultCommitment = urlParams.get('default_commitment');
    if (defaultCommitment) {
    const firstCommitmentTextarea = document.querySelector('textarea[name="commitments[]"]');
        if (firstCommitmentTextarea) {
        firstCommitmentTextarea.value = defaultCommitment;
        }
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    
    // If coming from issue engine (check for issue_active parameter)
    if (urlParams.get('issue_active') === '1') {
        const unitSelect = document.getElementById('unit');
        // Set default unit to UP KENDARI if no unit is selected
        if (!urlParams.get('unit')) {
            const upKendariOption = Array.from(unitSelect.options).find(option => option.text.includes('UP KENDARI'));
            if (upKendariOption) {
                upKendariOption.selected = true;
            }
        }
    }
});
</script>
@push('scripts')
<script>
    function switchTab(tab) {
        const formBtn = document.getElementById('tab-form');
        const dataBtn = document.getElementById('tab-data');
        const weeklyBtn = document.getElementById('tab-weekly');
        
        const formCont = document.getElementById('form-container');
        const dataCont = document.getElementById('data-container');
        const weeklyCont = document.getElementById('weekly-container');

        // Hide all
        formCont.classList.add('hidden');
        dataCont.classList.add('hidden');
        weeklyCont.classList.add('hidden');

        // Reset buttons
        [formBtn, dataBtn, weeklyBtn].forEach(btn => {
            if (btn) {
                btn.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
                btn.classList.add('text-gray-600');
            }
        });

        // Show selected
        if (tab === 'form') {
            formBtn.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
            formBtn.classList.remove('text-gray-600');
            formCont.classList.remove('hidden');
        } else if (tab === 'data') {
            dataBtn.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
            dataBtn.classList.remove('text-gray-600');
            dataCont.classList.remove('hidden');
            loadDiscussionsData();
        } else if (tab === 'weekly') {
            weeklyBtn.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
            weeklyBtn.classList.remove('text-gray-600');
            weeklyCont.classList.remove('hidden');
        }
    }

    async function loadDiscussionsData() {
        const tbody = document.getElementById('discussions-table-body');
        const searchInput = document.getElementById('table-search').value;
        
        try {
            const url = `{{ route('admin.other-discussions.api-list') }}?is_weekly=1&status=Open&search=${encodeURIComponent(searchInput)}`;
            const response = await fetch(url);
            const result = await response.json();

            if (result.success) {
                if (result.data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">Tidak ada data ditemukan</td></tr>`;
                    return;
                }

                tbody.innerHTML = result.data.map(item => `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-900">${item.topic}</div>
                            <div class="text-[10px] text-gray-500">${item.sr_number || '-'}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">${item.unit}</td>
                        <td class="px-6 py-4">
                            <div class="text-[11px] text-gray-800 line-clamp-2">${item.target}</div>
                            <div class="text-[10px] text-blue-600 font-medium mt-1">Deadline: ${new Date(item.target_deadline).toLocaleDateString('id-ID')}</div>
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-600">${item.pic}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold ${item.status === 'Open' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-green-100 text-green-700 border border-green-200'}">
                                ${item.status}
                            </span>
                        </td>
                    </tr>
                `).join('');
            }
        } catch (error) {
            console.error('Error loading data:', error);
            tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-4 text-center text-red-500 text-sm"><i class="fas fa-exclamation-triangle mr-2"></i>Gagal memuat data</td></tr>`;
        }
    }

    // Bind search input with debounce
    let searchTimeout;
    document.getElementById('table-search')?.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadDiscussionsData, 400);
    });
</script>

<style>
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
@endsection
     