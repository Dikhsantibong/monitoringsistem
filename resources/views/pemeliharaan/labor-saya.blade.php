@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    @include('components.pemeliharaan-sidebar')
    <!-- Main Content -->
    <div id="main-content" class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-10">
            <div class="flex justify-between items-center px-6 py-3">
                <div class="flex items-center gap-x-3">
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
                    <h1 class="text-xl font-semibold text-gray-800">Work Order - Labor Saya</h1>
                </div>
                <div class="flex items-center gap-x-4 relative">
                    <!-- User Dropdown -->
                    <div class="relative">
                        <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                            <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                                class="w-8 h-8 rounded-full mr-2">
                            <span class="text-gray-700">{{ Auth::user()->name }}</span>
                            <i class="fas fa-caret-down ml-2"></i>
                        </button>
                        <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                            <a href="{{ route('user.profile') }}"
                                class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <script>
            function toggleDropdown() {
                var dropdown = document.getElementById('dropdown');
                dropdown.classList.toggle('hidden');
            }
            document.addEventListener('click', function(event) {
                var userDropdown = document.getElementById('dropdown');
                var userBtn = document.getElementById('dropdownToggle');
                if (userDropdown && !userDropdown.classList.contains('hidden') && !userBtn.contains(event.target) && !userDropdown.contains(event.target)) {
                    userDropdown.classList.add('hidden');
                }
            });
        </script>
        <!-- Tab Navigation -->
        <main class="px-6 pt-6">
            <form method="GET" action="{{ route('pemeliharaan.labor-saya') }}" class="mb-4 flex items-center gap-2">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari WO (WONUM, Description, Status, Type, Priority, Location, Asset)" class="flex-1 border rounded px-3 py-2" />
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Cari</button>
                @if(!empty($q))
                    <a href="{{ route('pemeliharaan.labor-saya') }}" class="px-3 py-2 rounded border hover:bg-gray-100">Reset</a>
                @endif
            </form>

            <div class="bg-white rounded shadow p-4 overflow-x-auto">
                    <table class="min-w-full table-fixed divide-y divide-gray-200 border whitespace-nowrap">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-center">No</th>
                                <th class="px-4 py-2 text-center">Aksi</th>
                                <th class="px-4 py-2 text-center">WO Number</th>
                                <th class="px-4 py-2 text-center">Parent</th>
                                <th class="px-4 py-2 text-center">Description</th>
                                <th class="px-4 py-2 text-center">Type</th>
                                <th class="px-4 py-2 text-center">Status</th>
                                <th class="px-4 py-2 text-center">Priority</th>
                                <th class="px-4 py-2 text-center">Location</th>
                                <th class="px-4 py-2 text-center">Asset</th>
                                <th class="px-4 py-2 text-center">Schedule Start</th>
                                <th class="px-4 py-2 text-center">Schedule Finish</th>
                                <th class="px-4 py-2 text-center">Report Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($workOrders as $wo)
                            @php
                                $woStatus = strtoupper($wo['status'] ?? '');
                                $isClosed = in_array($woStatus, ['COMP', 'CLOSE', 'CLOSED']);
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-center border border-gray-200">
                                    {{ ($workOrdersPaginator->currentPage() - 1) * $workOrdersPaginator->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-4 py-2 border border-gray-200 text-center">
                                    @if($isClosed)
                                        <span class="inline-block px-3 py-1 bg-gray-400 text-white rounded text-xs cursor-not-allowed">
                                            <i class="fas fa-lock mr-1"></i> Closed
                                        </span>
                                    @else
                                        <a href="{{ route('pemeliharaan.labor-saya.edit', $wo['wonum']) }}" class="inline-block px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    @endif
                                </td>
                                <td class="px-4 py-2 border border-gray-200">{{ $wo['wonum'] }}</td>
                                <td class="px-4 py-2 border border-gray-200">{{ $wo['parent'] ?? '-' }}</td>
                                <td class="px-4 py-2 border border-gray-200 max-w-[250px] overflow-hidden text-ellipsis">
                                    {{ $wo['description'] ?? '-' }}
                                </td>
                                <td class="px-4 py-2 border border-gray-200 text-center">
                                    <span class="px-2 py-1 rounded-full {{ ($wo['worktype'] ?? '') == 'PM' ? 'bg-blue-100 text-blue-600' : 'bg-yellow-100 text-yellow-600' }}">
                                        {{ $wo['worktype'] ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 border border-gray-200 text-center">
                                    @php
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                        if (in_array($woStatus, ['COMP', 'CLOSE', 'CLOSED'])) {
                                            $statusClass = 'bg-green-100 text-green-800';
                                        } elseif (in_array($woStatus, ['WAPPR', 'APPR'])) {
                                            $statusClass = 'bg-blue-100 text-blue-800';
                                        } elseif (in_array($woStatus, ['INPRG', 'IN PROGRESS'])) {
                                            $statusClass = 'bg-yellow-100 text-yellow-800';
                                        }
                                    @endphp
                                    <span class="px-2 py-1 rounded-full {{ $statusClass }}">
                                        {{ $wo['status'] ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 border border-gray-200 text-center">
                                    @php
                                        $priorityLower = strtolower($wo['wopriority'] ?? '');
                                        $priorityClass = 'bg-gray-100 text-gray-600';
                                        if (in_array($priorityLower, ['low', '3'])) {
                                            $priorityClass = 'bg-green-100 text-green-600';
                                        } elseif (in_array($priorityLower, ['high', 'urgent', '1', '2'])) {
                                            $priorityClass = 'bg-red-100 text-red-600';
                                        }
                                    @endphp
                                    <span class="px-2 py-1 rounded-full {{ $priorityClass }}">
                                        {{ $wo['wopriority'] ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 border border-gray-200">{{ $wo['location'] ?? '-' }}</td>
                                <td class="px-4 py-2 border border-gray-200">{{ $wo['assetnum'] ?? '-' }}</td>
                                <td class="px-4 py-2 border border-gray-200">
                                    @if(isset($wo['schedstart']) && $wo['schedstart'])
                                        {{ \Carbon\Carbon::parse($wo['schedstart'])->format('d-m-Y H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-2 border border-gray-200">
                                    @if(isset($wo['schedfinish']) && $wo['schedfinish'])
                                        {{ \Carbon\Carbon::parse($wo['schedfinish'])->format('d-m-Y H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-2 border border-gray-200">
                                    @if(isset($wo['reportdate']) && $wo['reportdate'])
                                        {{ \Carbon\Carbon::parse($wo['reportdate'])->format('d-m-Y H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="13" class="text-center py-4">Tidak ada data work order.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Work Order --}}
                @if($workOrdersPaginator && $workOrdersPaginator->hasPages())
                <div class="mt-4 flex justify-between items-center">
                    <div class="text-sm text-gray-700">
                        Menampilkan 
                        {{ ($workOrdersPaginator->currentPage() - 1) * $workOrdersPaginator->perPage() + 1 }} 
                        hingga 
                        {{ min($workOrdersPaginator->currentPage() * $workOrdersPaginator->perPage(), $workOrdersPaginator->total()) }} 
                        dari 
                        {{ $workOrdersPaginator->total() }} 
                        entri
                    </div>
                    <div class="flex items-center gap-1">
                        @if (!$workOrdersPaginator->onFirstPage())
                            <a href="{{ $workOrdersPaginator->appends(['q' => request('q')])->previousPageUrl() }}" 
                               class="px-3 py-1 bg-[#0A749B] text-white rounded hover:bg-[#085a75]">Sebelumnya</a>
                        @endif

                        @foreach ($workOrdersPaginator->getUrlRange(1, min($workOrdersPaginator->lastPage(), 10)) as $page => $url)
                            @if ($page == $workOrdersPaginator->currentPage())
                                <span class="px-3 py-1 bg-[#0A749B] text-white rounded">{{ $page }}</span>
                            @else
                                <a href="{{ $workOrdersPaginator->appends(['q' => request('q')])->url($page) }}" 
                                   class="px-3 py-1 rounded bg-white text-[#0A749B] border border-[#0A749B] hover:bg-[#0A749B] hover:text-white">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        @if ($workOrdersPaginator->hasMorePages())
                            <a href="{{ $workOrdersPaginator->appends(['q' => request('q')])->nextPageUrl() }}" 
                               class="px-3 py-1 bg-[#0A749B] text-white rounded hover:bg-[#085a75]">Selanjutnya</a>
                        @endif
                    </div>
                </div>
                @endif
        </main>
    </div>
</div>
@endsection