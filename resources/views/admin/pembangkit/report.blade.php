@extends('layouts.app')

@section('content')
<style>
    /* Tambahkan ini di file CSS Anda */
.loading {
    display: none; /* Sembunyikan loading secara default */
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
    display: flex; /* Tampilkan loading saat diperlukan */
}
</style>




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

                    <h1 class="text-xl font-semibold text-gray-800">Dashboard Admin</h1>
                </div>

                <!-- Dropdown User -->
                <div class="relative ml-auto">
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

        <!-- Loading Animation -->
        <div class="loading" id="loading">
            <div class="loader">Loading...</div> <!-- Anda bisa mengganti ini dengan animasi loading yang lebih baik -->
        </div>

        <div class="p-6">
            <!-- Grouping Filter and Table -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="flex justify-between items-center px-6 py-3">
                    <div class="flex items-center gap-x-3">
                        <h1 class="text-xl font-semibold text-gray-800">Laporan Kesiapan Pembangkit</h1>
                        
                        <form id="filterForm" class="flex items-center space-x-2">
                            <input type="date" name="date" id="filterDate" 
                                   class="border border-gray-300 rounded-md p-2" 
                                   value="{{ request('date', now()->format('Y-m-d')) }}"> 
                        </form>
                    </div>
                    <div class="flex space-x-2">
                        
                        <a href="{{ route('admin.pembangkit.downloadReport', ['date' => request('date')]) }}" 
                           class="bg-green-500 text-white rounded-md px-4 py-2">
                            <i class="fas fa-download mr-2"></i>Download PDF
                        </a>
                        <a href="{{ route('admin.pembangkit.printReport', ['date' => request('date')]) }}" 
                           class="bg-yellow-500 text-white rounded-md px-4 py-2">
                            <i class="fas fa-print mr-2"></i>Print
                        </a>
                       
                    </div>
                </div>

                <div class="overflow-x-auto" id="reportTableContainer">
                    @include('admin.pembangkit.report-table', ['logs' => $logs])
                </div>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    @if ($logs->previousPageUrl())
                        <a href="{{ $logs->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    @endif
                    @if ($logs->nextPageUrl())
                        <a href="{{ $logs->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    @endif
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Menampilkan
                            <span class="font-medium">{{ $logs->firstItem() ?? 0 }}</span>
                            sampai
                            <span class="font-medium">{{ $logs->lastItem() ?? 0 }}</span>
                            dari
                            <span class="font-medium">{{ $logs->total() }}</span>
                            hasil
                        </p>
                    </div>
                    <div>
                        @if ($logs->hasPages())
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                {{-- Previous Page Link --}}
                                @if ($logs->onFirstPage())
                                    <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-300">
                                        <span class="sr-only">Previous</span>
                                        <i class="fas fa-chevron-left h-5 w-5"></i>
                                    </span>
                                @else
                                    <a href="{{ $logs->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Previous</span>
                                        <i class="fas fa-chevron-left h-5 w-5"></i>
                                    </a>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($logs->getUrlRange(max($logs->currentPage() - 2, 1), min($logs->currentPage() + 2, $logs->lastPage())) as $page => $url)
                                    @if ($page == $logs->currentPage())
                                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-blue-50 text-sm font-medium text-blue-600">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($logs->hasMorePages())
                                    <a href="{{ $logs->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Next</span>
                                        <i class="fas fa-chevron-right h-5 w-5"></i>
                                    </a>
                                @else
                                    <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-300">
                                        <span class="sr-only">Next</span>
                                        <i class="fas fa-chevron-right h-5 w-5"></i>
                                    </span>
                                @endif
                            </nav>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/toggle.js') }}"></script>

<script>
    // Function to fetch data based on the selected date
    function fetchData(date) {
        // Tampilkan loading
        document.getElementById('loading').classList.add('show');

        // Mengambil data baru berdasarkan tanggal
        fetch(`{{ route('admin.pembangkit.report') }}?date=${date}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('reportTableContainer').innerHTML = data.html; // Update tabel dengan data baru
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        })
        .finally(() => {
            // Sembunyikan loading
            document.getElementById('loading').classList.remove('show');
        });
    }

    // Event listener untuk perubahan tanggal
    document.getElementById('filterDate').addEventListener('change', function() {
        const date = this.value; // Ambil nilai tanggal
        fetchData(date); // Panggil fungsi untuk mengambil data
    });

    // Event listener untuk submit form
    document.getElementById('filterForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Mencegah form dari submit default
        const date = document.getElementById('filterDate').value;
        fetchData(date); // Panggil fungsi untuk mengambil data
    });

    function searchTable() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const tables = document.querySelectorAll('.report-table tbody tr');
        let found = false;

        tables.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchInput)) {
                row.style.display = '';
                found = true;
            } else {
                row.style.display = 'none';
            }
        });

        // Tampilkan atau sembunyikan pesan "Data tidak ditemukan"
        let noDataMessage = document.getElementById('noDataMessage');
        if (!found) {
            if (!noDataMessage) {
                noDataMessage = document.createElement('div');
                noDataMessage.id = 'noDataMessage';
                noDataMessage.className = 'text-center py-8 text-gray-600 font-semibold text-lg';
                noDataMessage.textContent = 'DATA TIDAK DITEMUKAN';
                document.querySelector('#reportTableContainer').appendChild(noDataMessage);
            }
        } else if (noDataMessage) {
            noDataMessage.remove();
        }
    }

    // Event listener untuk search input dengan debounce
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchTable();
        }, 300);
    });
</script>
@push('scripts')
    
@endpush
@endsection