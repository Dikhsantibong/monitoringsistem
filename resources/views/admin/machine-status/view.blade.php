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
                        aria-controls="desktop-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true">
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

        <!-- Breadcrumbs -->
        <div class="flex items-center pt-2">
            <x-admin-breadcrumb :breadcrumbs="[
                ['name' => 'Kesiapan Pembangkit', 'url' => route('admin.pembangkit.ready')],
                ['name' => 'Status Mesin', 'url' => null]
            ]" />
        </div>

        <!-- Main Content -->
        <div class="container mx-auto px-4 py-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Status Mesin</h2>
                    
                    <!-- Filter Area -->
                    <div class="flex items-center space-x-4">
                        <div>
                            <input type="date" 
                                   id="date-picker" 
                                   class="border rounded px-3 py-2" 
                                   value="{{ $date }}"
                                   onchange="updateTable()">
                        </div>
                        
                        <div class="relative">
                            <input type="text" 
                                   id="searchInput" 
                                   placeholder="Cari unit atau mesin..."
                                   class="w-64 pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="overflow-x-auto">
                    @include('admin.machine-status._table')
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function filterData() {
    const date = document.getElementById('filterDate').value;
    fetchData(date);
}

function fetchData(date) {
    console.log('Fetching data:', { date });
    document.getElementById('loading').classList.add('show');

    fetch(`{{ route('admin.machine-status.view') }}?date=${date}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            const container = document.querySelector('.overflow-x-auto');
            if (container) {
                container.innerHTML = data.html;
            } else {
                console.error('Container not found');
            }
        } else {
            throw new Error(data.message || 'Terjadi kesalahan saat memuat data');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
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

// Panggil fetchData saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    const date = document.getElementById('filterDate').value;
    fetchData(date);
});

// Fungsi pencarian
document.getElementById('searchInput').addEventListener('keyup', function(e) {
    const searchText = this.value.toLowerCase();
    const unitContainers = document.querySelectorAll('.bg-white.rounded-lg.shadow.p-6.mb-4');
    
    unitContainers.forEach(container => {
        const unitName = container.querySelector('h1').textContent.toLowerCase();
        const machineNames = Array.from(container.querySelectorAll('tbody tr td:nth-child(2)')).map(td => td.textContent.toLowerCase());
        
        // Cek apakah searchText cocok dengan nama unit atau nama mesin
        const matchUnit = unitName.includes(searchText);
        const matchMachine = machineNames.some(name => name.includes(searchText));
        
        // Tampilkan/sembunyikan container berdasarkan hasil pencarian
        if (matchUnit || matchMachine) {
            container.style.display = '';
            
            // Jika mencari mesin spesifik, sembunyikan mesin yang tidak cocok
            if (!matchUnit && matchMachine) {
                const rows = container.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const machineName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    row.style.display = machineName.includes(searchText) ? '' : 'none';
                });
            } else {
                // Jika mencari unit, tampilkan semua mesin
                const rows = container.querySelectorAll('tbody tr');
                rows.forEach(row => row.style.display = '');
            }
        } else {
            container.style.display = 'none';
        }
    });
});

// Dropdown functionality
function toggleDropdown() {
    const dropdown = document.getElementById('dropdown');
    dropdown.classList.toggle('hidden');
}

document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('dropdown');
    const dropdownToggle = document.getElementById('dropdownToggle');
    
    if (!dropdown.contains(event.target) && !dropdownToggle.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});

function updateTable() {
    const date = document.getElementById('date-picker').value;
    const searchText = document.getElementById('searchInput').value;
    
    document.getElementById('loading').classList.add('show');
    
    fetch(`{{ route('admin.machine-status.view') }}?date=${date}`, {
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
                // Terapkan filter pencarian setelah memperbarui tabel
                if (searchText) {
                    document.getElementById('searchInput').value = searchText;
                    document.getElementById('searchInput').dispatchEvent(new Event('keyup'));
                }
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
    })
    .finally(() => {
        document.getElementById('loading').classList.remove('show');
    });
}
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
</style>
@endsection 