@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.sidebar')

    <div id="main-content" class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-20">
            <div class="flex justify-between items-center px-6 py-3">
                <div class="flex items-center gap-x-3">
                    <!-- Mobile Menu Toggle -->
                    <button id="mobile-menu-toggle"
                        class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#009BB9] hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true">
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

                    <h1 class="text-xl font-semibold text-gray-800">Status Mesin</h1>
                </div>

                @include('components.timer')
                
                <!-- User Dropdown -->
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

        <!-- Loading indicator -->
        <div id="loading" class="loading">
            <div class="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-500"></div>
        </div>

        <!-- Main Content -->
        <div class="container mx-auto px-4 py-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Status Mesin</h2>
                    
                    <!-- Filter Area -->
                    <div class="flex items-center space-x-4">
                        <!-- Unit Source Filter - hanya tampil untuk session mysql -->
                        @if(session('unit') === 'mysql')
                        <div class="flex items-center">
                            <label for="unit-source" class="text-sm text-gray-700 font-medium mr-2">Filter Unit:</label>
                            <select id="unit-source" 
                                class="border rounded px-3 py-2 text-sm w-40"
                                onchange="updateTable()">
                                <option value="">Semua Unit</option>
                                <option value="mysql" {{ request('unit_source') == 'mysql' ? 'selected' : '' }}>UP Kendari</option>
                                <option value="mysql_wua_wua" {{ request('unit_source') == 'mysql_wua_wua' ? 'selected' : '' }}>Wua Wua</option>
                                <option value="mysql_poasia" {{ request('unit_source') == 'mysql_poasia' ? 'selected' : '' }}>Poasia</option>
                                <option value="mysql_kolaka" {{ request('unit_source') == 'mysql_kolaka' ? 'selected' : '' }}>Kolaka</option>
                                <option value="mysql_bau_bau" {{ request('unit_source') == 'mysql_bau_bau' ? 'selected' : '' }}>Bau Bau</option>
                            </select>
                        </div>
                        @endif

                        <!-- Date Filter -->
                        <div>
                            <input type="date" id="date-picker" 
                                class="border rounded px-3 py-2 text-sm"
                                value="{{ $date }}"
                                onchange="updateTable()">
                        </div>
                        
                        <!-- Search dengan debounce -->
                        <div>
                            <input type="text" id="searchInput" 
                                placeholder="Cari unit/mesin/status..." 
                                class="border rounded px-3 py-2 text-sm w-64"
                                value="{{ request('search') }}">
                        </div>

                        <!-- Update Mesin Button -->
                        <div>
                            <a href="{{ route('admin.pembangkit.ready') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-sync-alt mr-2"></i>
                                Update Mesin
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="overflow-x-auto">
                    @include('admin.machine-status._table')
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/toggle.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function toggleDropdown() {
    document.getElementById('dropdown').classList.toggle('hidden');
}

document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('dropdown');
    const dropdownToggle = document.getElementById('dropdownToggle');
    
    if (!dropdown.contains(event.target) && !dropdownToggle.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});

let searchTimeout;

document.getElementById('searchInput').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        updateTable();
    }, 500); // Debounce 500ms
});

function updateTable() {
    const date = document.getElementById('date-picker').value;
    const unitSource = @json(session('unit')) === 'mysql' ? document.getElementById('unit-source')?.value : null;
    const searchText = document.getElementById('searchInput').value;
    
    document.getElementById('loading').classList.add('show');
    
    const params = new URLSearchParams({
        date: date,
        search: searchText,
        ...(unitSource && { unit_source: unitSource })
    });
    
    fetch(`{{ route('admin.machine-status.view') }}?${params.toString()}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const container = document.querySelector('.overflow-x-auto');
            if (container) {
                container.innerHTML = data.html;
            }
        } else {
            throw new Error(data.message || 'Terjadi kesalahan saat memuat data');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const container = document.querySelector('.overflow-x-auto');
        if (container) {
            container.innerHTML = `
                <div class="text-center py-4 text-red-500">
                    Terjadi kesalahan saat memuat data: ${error.message}
                </div>
            `;
        }
    })
    .finally(() => {
        document.getElementById('loading').classList.remove('show');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Desktop menu toggle
    const desktopMenuToggle = document.getElementById('desktop-menu-toggle');
    const sidebar = document.querySelector('.sidebar'); // Sesuaikan dengan class sidebar Anda
    
    desktopMenuToggle.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        document.getElementById('main-content').classList.toggle('sidebar-collapsed');
    });
});
</script>

<style>
.loading {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    z-index: 9999;
    justify-content: center;
    align-items: center;
}

.loading.show {
    display: flex;
}

.sidebar {
    transition: width 0.3s ease;
}

.sidebar.collapsed {
    width: 0;
    overflow: hidden;
}

#main-content {
    transition: margin-left 0.3s ease;
}

#main-content.sidebar-collapsed {
    margin-left: 0;
}
</style>
@endsection 