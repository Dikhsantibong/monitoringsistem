@if($notulen->isEmpty())
    <div class="text-center py-4 text-gray-600">
        Tidak ada notulen yang ditemukan
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($notulen as $item)
            <div class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow duration-200">
                <div class="text-sm text-gray-500 mb-2">{{ $item->format_nomor }}</div>
                <h3 class="font-semibold text-lg mb-2 text-gray-800">{{ $item->agenda }}</h3>
                <div class="text-sm text-gray-600 mb-2">
                    <i class="fas fa-building mr-2"></i>{{ $item->unit }}
                </div>
                <div class="text-sm text-gray-600 mb-2">
                    <i class="fas fa-folder mr-2"></i>{{ $item->bidang }} - {{ $item->sub_bidang }}
                </div>
                <div class="text-sm text-gray-600 mb-2">
                    <i class="fas fa-calendar mr-2"></i>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                </div>
                <div class="mt-4">
                    <a href="{{ route('notulen.show', $item->id) }}"
                       class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors duration-200 text-sm">
                        Lihat Detail
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    @if($notulen->hasPages())
        <div class="mt-8">
            <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
                <div class="flex justify-between flex-1 sm:hidden">
                    @if ($notulen->onFirstPage())
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-md">
                            Previous
                        </span>
                    @else
                        <button type="button" onclick="changePage('{{ $notulen->previousPageUrl() }}')" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:text-gray-500">
                            Previous
                        </button>
                    @endif

                    @if ($notulen->hasMorePages())
                        <button type="button" onclick="changePage('{{ $notulen->nextPageUrl() }}')" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:text-gray-500">
                            Next
                        </button>
                    @else
                        <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-md">
                            Next
                        </span>
                    @endif
                </div>

                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700 leading-5">
                            Showing
                            <span class="font-medium">{{ $notulen->firstItem() }}</span>
                            to
                            <span class="font-medium">{{ $notulen->lastItem() }}</span>
                            of
                            <span class="font-medium">{{ $notulen->total() }}</span>
                            results
                        </p>
                    </div>

                    <div>
                        <span class="relative z-0 inline-flex shadow-sm rounded-md">
                            {{-- Previous Page Link --}}
                            @if ($notulen->onFirstPage())
                                <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-l-md">
                                    <span class="sr-only">Previous</span>
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            @else
                                <button type="button" onclick="changePage('{{ $notulen->previousPageUrl() }}')" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50">
                                    <span class="sr-only">Previous</span>
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($notulen->getUrlRange(1, $notulen->lastPage()) as $page => $url)
                                @if ($page == $notulen->currentPage())
                                    <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-blue-600 bg-blue-50 border border-gray-300">
                                        {{ $page }}
                                    </span>
                                @else
                                    <button type="button" onclick="changePage('{{ $url }}')" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                                        {{ $page }}
                                    </button>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($notulen->hasMorePages())
                                <button type="button" onclick="changePage('{{ $notulen->nextPageUrl() }}')" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50">
                                    <span class="sr-only">Next</span>
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @else
                                <span class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-r-md">
                                    <span class="sr-only">Next</span>
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            @endif
                        </span>
                    </div>
                </div>
            </nav>
        </div>
    @endif
@endif
