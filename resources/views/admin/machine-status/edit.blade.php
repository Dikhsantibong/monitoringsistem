@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <!-- Sidebar -->
    @include('components.sidebar')

    <!-- Main Content -->
    <div id="main-content" class="flex-1 main-content">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-10">
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

                    <!--  Menu Toggle Sidebar-->
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

                    <h1 class="text-xl font-semibold text-gray-800">Edit Status Mesin</h1>
                </div>

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
        <div class="flex items-center pt-2">
            <x-admin-breadcrumb :breadcrumbs="[
                ['name' => 'Kesiapan Pembangkit', 'url' => route('admin.machine-status.view')],
                ['name' => 'Edit Status Mesin', 'url' => null]
            ]" />
        </div>

        <!-- Content -->
        <div class="container mx-auto px-4 py-6">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Edit Status Mesin - {{ $machine->name }}</h1>
                    <p class="text-gray-600">Unit: {{ $machine->powerPlant->name }}</p>
                </div>

                <form id="editForm" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2 ">Status</label>
                            <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 h-10 text-left pl-2">
                                <option value="Operasi" {{ $log->status === 'Operasi' ? 'selected' : '' }}>Operasi</option>
                                <option value="Standby" {{ $log->status === 'Standby' ? 'selected' : '' }}>Standby</option>
                                <option value="Gangguan" {{ $log->status === 'Gangguan' ? 'selected' : '' }}>Gangguan</option>
                                <option value="Pemeliharaan" {{ $log->status === 'Pemeliharaan' ? 'selected' : '' }}>Pemeliharaan</option>
                                <option value="Overhaul" {{ $log->status === 'Overhaul' ? 'selected' : '' }}>Overhaul</option>
                                <option value="Mothballed" {{ $log->status === 'Mothballed' ? 'selected' : '' }}>Mothballed</option>
                            </select>
                        </div>

                        <!-- DMP -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Daya Mampu Slim (MW)</label>
                            <input type="number" step="0.01" name="dmp" value="{{ $log->dmp }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>

                        <!-- DMN -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Daya Mampu pasok (MW)</label>
                            <input type="number" step="0.01" name="dmn" value="{{ $log->dmn }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>

                        <!-- Beban -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Beban (MW)</label>
                            <input type="number" step="0.01" name="load_value" value="{{ $log->load_value }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>

                        <!-- issue engine -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Issue Engine</label>
                            <select name="component" 
                                    class="system-select w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:border-blue-400">
                                <option value="">Pilih issue</option>
                                <option value="Ada" {{ $log?->component === 'Ada' ? 'selected' : '' }}>Ada</option>
                                <option value="Tidak Ada" {{ $log?->component === 'Tidak Ada' ? 'selected' : '' }}>Tidak Ada</option>
                            </select>
                        </div>

                        <!-- catatan issue -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan issue</label>
                            <input type="text" name="equipment" value="{{ $log->equipment }}"
                                   class="p-2 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>

                        <!-- Tanggal Mulai -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" value="{{ $log->tanggal_mulai ? \Carbon\Carbon::parse($log->tanggal_mulai)->format('Y-m-d') : '' }}"
                                   class="p-2 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>

                        <!-- Target Selesai -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Target Selesai</label>
                            <input type="date" name="target_selesai" value="{{ $log->target_selesai ? \Carbon\Carbon::parse($log->target_selesai)->format('Y-m-d') : '' }}"
                                   class="p-2 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" 
                                  class="p-2 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">{{ $log->deskripsi }}</textarea>
                    </div>

                    <!-- Kronologi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kronologi</label>
                        <textarea name="kronologi" rows="3"
                                  class="p-2 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">{{ $log->kronologi }}</textarea>
                    </div>

                    <!-- Action Plan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Action Plan</label>
                        <textarea name="action_plan" rows="3"
                                  class="p-2 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">{{ $log->action_plan }}</textarea>
                    </div>

                    <!-- Progress -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Progress</label>
                        <textarea name="progres" rows="3"
                                  class="p-2 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">{{ $log->progres }}</textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('admin.machine-status.view') }}" 
                           class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/toggle.js') }}"></script>
<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('dropdown');
        dropdown.classList.toggle('hidden');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('dropdown');
        const button = event.target.closest('#dropdownToggle');
        
        if (!button && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Mobile menu functionality
    document.getElementById('mobile-menu-toggle').addEventListener('click', function() {
        const sidebar = document.querySelector('aside');
        sidebar.classList.toggle('hidden');
    });

    // Desktop menu functionality
    document.getElementById('desktop-menu-toggle').addEventListener('click', function() {
        const mainContent = document.getElementById('main-content');
        const sidebar = document.querySelector('aside');
        
        sidebar.classList.toggle('hidden');
        mainContent.classList.toggle('md:ml-0');
    });
</script>

@push('scripts')
<script>
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    fetch(`/admin/machine-status/{{ $machine->id }}/update/{{ $log->id }}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Data berhasil diperbarui');
            window.location.href = '{{ route("admin.machine-status.view") }}';
        } else {
            alert(data.message || 'Terjadi kesalahan saat memperbarui data');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memperbarui data');
    });
});
</script>
@endpush
@endsection 