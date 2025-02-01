<div class="bg-white rounded-lg shadow p-6">
    <!-- Table Header -->
    

    <!-- Search dan Counter -->
    <div class="flex justify-between items-center mb-4">
        <div class="w-1/3">
            <div class="relative">
                <input type="text" 
                       id="searchReport" 
                       placeholder="Cari data..."
                       class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </div>
        <div class="text-gray-600">
            Menampilkan <span id="visibleCount">{{ $logs->count() }}</span> dari <span id="totalCount">{{ $logs->total() }}</span> data
        </div>
    </div>

    <!-- Table Content -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr style="background-color: #0A749B; color: white;">
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Unit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Mesin</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">DMN</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">DMP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Beban</th>
                    
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 border">
                @forelse($logs as $index => $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap border">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap border">{{ $log->machine->powerPlant->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap border">{{ $log->machine->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap border">{{ $log->dmn ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap border">{{ $log->dmp ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap border">{{ $log->load_value ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap border">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $log->status === 'Operasi' ? 'bg-green-100 text-green-800' : 
                                   ($log->status === 'Gangguan' ? 'bg-red-100 text-red-800' : 
                                   'bg-yellow-100 text-yellow-800') }}">
                                {{ $log->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 border">
                            Tidak ada data untuk ditampilkan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi pencarian untuk tabel
function searchTable(searchValue) {
    const rows = document.querySelectorAll('tbody tr');
    let visibleCount = 0;
    const totalCount = rows.length;

    searchValue = searchValue.toLowerCase();

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchValue)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    document.getElementById('visibleCount').textContent = visibleCount;
}

// Event listener untuk pencarian
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchReport');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            searchTable(this.value);
        });
    }
});
</script>