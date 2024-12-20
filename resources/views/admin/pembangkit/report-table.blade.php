<table class="min-w-full divide-y divide-gray-200 border border-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Unit</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Mesin</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Beban</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">DMN</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">DMP</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Kronologi</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Deskripsi</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Action Plan</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Progres</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Target Selesai</th>
        </tr>
    </thead>
    <tbody id="reportTableBody">
        @forelse($logs as $log)
            <tr class="hover:bg-gray-50 border border-gray-200">
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->machine->powerPlant->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->machine->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $log->status === 'Operasi' ? 'bg-green-100 text-green-800' : 
                           ($log->status === 'Gangguan' ? 'bg-red-100 text-red-800' : 
                           'bg-yellow-100 text-yellow-800') }}">
                        {{ $log->status }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->load_value }}</td>
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->dmn }}</td>
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->dmp }}</td>
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->kronologi }}</td>
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->deskripsi }}</td>
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->action_plan }}</td>
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->progres }}</td>
                <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->target_selesai }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="px-6 py-4 text-center text-gray-500 border border-gray-200">
                    Tidak ada data untuk ditampilkan
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

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