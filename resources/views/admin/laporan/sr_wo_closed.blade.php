@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <!-- Sidebar -->
    @include('components.sidebar')

    <!-- Main Content -->
    <div id="main-content" class="flex-1 main-content">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="flex justify-between items-center px-6 py-3">
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
                <h1 class="text-xl font-semibold text-gray-800">Dashboard Admin</h1>
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
                ['name' => 'Laporan SR/WO', 'url' => route('admin.laporan.sr_wo')],
                ['name' => 'Closed', 'url' => null]
            ]" />
        </div>

    <div class="flex-1 main-content px-4 py-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Laporan SR/WO Closed</h2>
                <div class="flex gap-4">
                    <a href="{{ route('admin.laporan.sr_wo.closed.download') }}" 
                       class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 flex items-center">
                        <i class="fas fa-download mr-2"></i>Download PDF
                    </a>
                    
                    <button onclick="window.open('{{ route('admin.laporan.sr_wo.closed.print') }}', '_blank')"
                            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 flex items-center">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="mb-4 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                    <li class="mr-2">
                        <a href="#" onclick="switchTab('sr'); return false;" 
                           class="inline-block p-4 border-b-2 rounded-t-lg tab-btn active" 
                           data-tab="sr">
                            Service Request (SR) Closed
                            <span class="ml-2 bg-green-400 text-gray-700 px-2 py-1 rounded-full text-xs">
                                {{ App\Models\ServiceRequest::where('status', 'Closed')->count() }}
                            </span>
                        </a>
                    </li>
                    <li class="mr-2">
                        <a href="#" onclick="switchTab('wo'); return false;" 
                           class="inline-block p-4 border-b-2 rounded-t-lg tab-btn" 
                           data-tab="wo">
                            Work Order (WO) Closed
                            <span class="ml-2 bg-blue-400 text-gray-700 px-2 py-1 rounded-full text-xs">
                                {{ App\Models\WorkOrder::where('status', 'Closed')->count() }}
                            </span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- SR Table Content -->
            <div id="sr-tab" class="tab-content active">
                <!-- Search dan Counter untuk SR -->
                <div class="flex justify-between items-center mb-4">
                    <div class="w-1/3">
                        <div class="relative">
                            <input type="text" 
                                   id="searchSR" 
                                   placeholder="Cari SR..."
                                   onkeyup="if(event.key === 'Enter') searchTable('srTable', this.value)"
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    <div class="text-gray-600">
                        Menampilkan <span id="srVisibleCount">0</span> dari <span id="srTotalCount">0</span> data
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr style="background-color: #0A749B; color: white;">
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nomor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Deskripsi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 border">
                            @foreach(App\Models\ServiceRequest::where('status', 'Closed')->get() as $index => $report)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap border">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap border">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        SR
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap border">{{ $report->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap border">{{ Carbon\Carbon::parse($report->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 border">{{ $report->description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap border">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $report->status }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- WO Table Content -->
            <div id="wo-tab" class="tab-content hidden">
                <!-- Search dan Counter untuk WO -->
                <div class="flex justify-between items-center mb-4">
                    <div class="w-1/3">
                        <div class="relative">
                            <input type="text" 
                                   id="searchWO" 
                                   placeholder="Cari WO..."
                                   onkeyup="if(event.key === 'Enter') searchTable('woTable', this.value)"
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    <div class="text-gray-600">
                        Menampilkan <span id="woVisibleCount">0</span> dari <span id="woTotalCount">0</span> data
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr style="background-color: #0A749B; color: white;">
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nomor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Deskripsi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 border">
                            @foreach(App\Models\WorkOrder::where('status', 'Closed')->get() as $index => $report)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap border">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap border">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        WO
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap border">{{ $report->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap border">{{ Carbon\Carbon::parse($report->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 border">{{ $report->description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap border">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $report->status }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add this style -->
<style>
.tab-btn.active {
    border-bottom-color: #3b82f6;
    color: #3b82f6;
}
.tab-content {
    transition: all 0.3s ease-in-out;
}
</style>

<script>
// Tab switching functionality
function switchTab(tabId) {
    // Remove active class from all tabs
    document.querySelectorAll('.tab-btn').forEach(tab => {
        tab.classList.remove('active', 'border-blue-500');
    });
    
    // Add active class to clicked tab
    const selectedTab = document.querySelector(`.tab-btn[data-tab="${tabId}"]`);
    if (selectedTab) {
        selectedTab.classList.add('active', 'border-blue-500');
    }
    
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Show selected tab content
    const selectedContent = document.getElementById(`${tabId}-tab`);
    if (selectedContent) {
        selectedContent.classList.remove('hidden');
    }
}

// Add event listener when document loads
document.addEventListener('DOMContentLoaded', function() {
    // Set first tab as active
    const firstTab = document.querySelector('.tab-btn');
    if (firstTab) {
        const tabId = firstTab.getAttribute('data-tab');
        switchTab(tabId);
    }
});

// Fungsi pencarian untuk tabel
function searchTable(tableId, searchValue) {
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName('tr');
    let visibleCount = 0;
    const totalCount = rows.length - 1; // Kurangi 1 untuk header

    searchValue = searchValue.toLowerCase();

    // Mulai dari indeks 1 untuk melewati header
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;

        for (let j = 0; j < cells.length; j++) {
            const cellText = cells[j].textContent.toLowerCase();
            if (cellText.includes(searchValue)) {
                found = true;
                break;
            }
        }

        if (found) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    }

    // Update counter
    const visibleCountId = tableId === 'srTable' ? 'srVisibleCount' : 'woVisibleCount';
    const totalCountId = tableId === 'srTable' ? 'srTotalCount' : 'woTotalCount';
    
    document.getElementById(visibleCountId).textContent = visibleCount;
    document.getElementById(totalCountId).textContent = totalCount;
}

// Debounce function untuk mencegah terlalu banyak pencarian
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Event listeners untuk pencarian real-time
document.addEventListener('DOMContentLoaded', function() {
    const searchInputs = {
        'searchSR': 'srTable',
        'searchWO': 'woTable'
    };

    Object.entries(searchInputs).forEach(([inputId, tableId]) => {
        const input = document.getElementById(inputId);
        if (input) {
            const debouncedSearch = debounce(() => {
                searchTable(tableId, input.value);
            }, 300);

            input.addEventListener('input', debouncedSearch);
        }
    });

    // Inisialisasi counter
    ['srTable', 'woTable'].forEach(tableId => {
        const table = document.getElementById(tableId);
        if (table) {
            const totalRows = table.getElementsByTagName('tr').length - 1;
            const visibleCountId = tableId === 'srTable' ? 'srVisibleCount' : 'woVisibleCount';
            const totalCountId = tableId === 'srTable' ? 'srTotalCount' : 'woTotalCount';
            
            document.getElementById(visibleCountId).textContent = totalRows;
            document.getElementById(totalCountId).textContent = totalRows;
        }
    });
});
</script>
@endsection 