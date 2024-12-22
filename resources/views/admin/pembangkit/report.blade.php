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
    <div class="flex-1 overflow-auto">
        <header class="bg-white shadow-sm sticky top-0 z-10">
            <div class="flex justify-between items-center px-6 py-3">
                <h1 class="text-xl font-semibold text-gray-800">Laporan Kesiapan Pembangkit</h1>
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
                    <form id="filterForm" class="flex items-center">
                        <input type="date" name="date" id="filterDate" class="border border-gray-300 rounded-md p-2" value="{{ request('date', now()->format('Y-m-d')) }}">
                        <button type="submit" class="ml-2 bg-blue-500 text-white rounded-md px-4 py-2">Filter</button>
                    </form>
                    <div>
                        <a href="{{ route('admin.pembangkit.downloadReport', ['date' => request('date')]) }}" class="ml-2 bg-green-500 text-white rounded-md px-4 py-2">Download PDF</a>
                        <a href="{{ route('admin.pembangkit.printReport', ['date' => request('date')]) }}" class="ml-2 bg-yellow-500 text-white rounded-md px-4 py-2">Print</a>
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
</script>
@push('scripts')
    
@endpush
@endsection