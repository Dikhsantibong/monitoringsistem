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
        <!-- Summary Cards -->
        <main class="px-6 pt-6">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                <!-- Total WO -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center hover:shadow-md transition-shadow">
                    <div class="h-10 w-10 bg-blue-50 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-clipboard-list text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total WO</p>
                        <h3 class="text-lg font-bold text-gray-800">{{ $stats['total'] }}</h3>
                    </div>
                </div>
                <!-- APPR -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center hover:shadow-md transition-shadow">
                    <div class="h-10 w-10 bg-green-50 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">APPR</p>
                        <h3 class="text-lg font-bold text-gray-800">{{ $stats['appr'] }}</h3>
                    </div>
                </div>
                <!-- WMATL -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center hover:shadow-md transition-shadow">
                    <div class="h-10 w-10 bg-yellow-50 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-box text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">WMATL</p>
                        <h3 class="text-lg font-bold text-gray-800">{{ $stats['wmatl'] }}</h3>
                    </div>
                </div>
                <!-- INPRG -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center hover:shadow-md transition-shadow">
                    <div class="h-10 w-10 bg-purple-50 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-spinner text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">INPRG</p>
                        <h3 class="text-lg font-bold text-gray-800">{{ $stats['inprg'] }}</h3>
                    </div>
                </div>
                <!-- CLOSED -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center hover:shadow-md transition-shadow">
                    <div class="h-10 w-10 bg-gray-50 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-lock text-gray-600"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">CLOSED</p>
                        <h3 class="text-lg font-bold text-gray-800">{{ $stats['closed'] }}</h3>
                    </div>
                </div>
                <!-- New Today -->
                <div class="bg-white rounded-xl shadow-sm border border-blue-100 p-4 flex items-center hover:shadow-md transition-shadow bg-blue-50/30">
                    <div class="h-10 w-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-calendar-day text-white"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-blue-600 uppercase tracking-wider">New Today</p>
                        <h3 class="text-lg font-bold text-gray-800">{{ $stats['new_today'] }}</h3>
                    </div>
                </div>
            </div>

            <form id="filterForm" method="GET" action="{{ route('pemeliharaan.labor-saya') }}" class="mb-4 flex items-center gap-2">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari WO/Backlog (id, deskripsi, status, type, priority...)" class="w-full pl-10 border rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" />
                </div>
                <input type="hidden" name="wo_page" value="1" />
                <input type="hidden" name="status" id="formStatus" value="{{ $statusFilter }}" />
                <input type="hidden" name="unit" id="formUnit" value="{{ $unitFilter }}" />
                <button type="submit" class="bg-[#0A749B] text-white px-6 py-2 rounded text-sm font-semibold hover:bg-[#009BB9] transition-colors">Cari</button>
                @if(!empty($q) || !empty($statusFilter) || !empty($unitFilter))
                    <a href="{{ route('pemeliharaan.labor-saya') }}" class="px-4 py-2 rounded border border-gray-300 text-gray-600 text-sm hover:bg-gray-50 transition-colors">Reset</a>
                @endif
            </form>

            <div class="mb-4 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                    <li class="mr-2">
                        <a href="#" class="inline-block p-4 border-b-2 border-blue-500 rounded-t-lg text-blue-600 active" aria-current="page">
                            Work Order (Maximo)
                            <span class="ml-2 bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">
                                {{ $workOrdersPaginator ? $workOrdersPaginator->total() : count($workOrders) }}
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Tab Content -->
            <div id="wo-tab" class="tab-content active">
                <div class="bg-white rounded shadow p-4 overflow-x-auto">
                    <table class="min-w-full table-fixed divide-y divide-gray-200 border whitespace-nowrap">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Deskripsi</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center justify-between gap-2">
                                        <span>Unit</span>
                                        <div class="relative group">
                                            <i class="fas fa-filter cursor-pointer hover:text-blue-500 text-gray-400"></i>
                                            <div class="hidden group-hover:block absolute right-0 top-full w-48 pt-2 z-20">
                                                <div class="bg-white border border-gray-200 rounded shadow-lg overflow-hidden">
                                                    <select onchange="updateFilter('unit', this.value)" class="w-full p-2 text-xs border-none focus:ring-0">
                                                        <option value="">Semua Unit</option>
                                                        @foreach($powerPlants as $plant)
                                                            <option value="{{ $plant->id }}" {{ ($unitFilter == $plant->id) ? 'selected' : '' }}>{{ $plant->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center justify-between gap-2">
                                        <span>Status</span>
                                        <div class="relative group">
                                            <i class="fas fa-filter cursor-pointer hover:text-blue-500 text-gray-400"></i>
                                            <div class="hidden group-hover:block absolute right-0 top-full w-48 pt-2 z-20">
                                                <div class="bg-white border border-gray-200 rounded shadow-lg overflow-hidden">
                                                    <select onchange="updateFilter('status', this.value)" class="w-full p-2 text-xs border-none focus:ring-0">
                                                        <option value="">Semua Status</option>
                                                        <option value="OPEN_GROUP" {{ ($statusFilter == 'OPEN_GROUP') ? 'selected' : '' }}>Open (Group)</option>
                                                        <option value="CLOSED_GROUP" {{ ($statusFilter == 'CLOSED_GROUP') ? 'selected' : '' }}>Closed (Group)</option>
                                                        <option disabled>──────────</option>
                                                        <option value="APPR" {{ ($statusFilter == 'APPR') ? 'selected' : '' }}>APPR</option>
                                                        <option value="WMATL" {{ ($statusFilter == 'WMATL') ? 'selected' : '' }}>WMATL</option>
                                                        <option value="INPRG" {{ ($statusFilter == 'INPRG') ? 'selected' : '' }}>INPRG</option>
                                                        <option value="COMP" {{ ($statusFilter == 'COMP') ? 'selected' : '' }}>COMP</option>
                                                        <option value="CLOSE" {{ ($statusFilter == 'CLOSE') ? 'selected' : '' }}>CLOSE</option>
                                                        <option value="WAPPR" {{ ($statusFilter == 'WAPPR') ? 'selected' : '' }}>WAPPR</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Kendala</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Tindak Lanjut</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Document</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Priority</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jadwal Mulai</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jadwal Selesai</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Labor</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Labor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($workOrders as $wo)
                            @php
                                $woStatus = strtoupper($wo['status'] ?? '');
                                $isClosed = in_array($woStatus, ['COMP', 'CLOSE']);
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-center border border-gray-200">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-center">
                                    @if($isClosed)
                                        <span class="inline-block px-3 py-1 bg-gray-400 text-white rounded text-xs cursor-not-allowed">
                                            <i class="fas fa-lock mr-1"></i> Closed
                                        </span>
                                    @else
                                        <a href="{{ route('pemeliharaan.labor-saya.edit', $wo['wonum']) }}" class="inline-block px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 text-xs"><i class="fas fa-edit"></i> Edit</a>
                                    @endif
                                </td>
                                <td class="px-4 py-2 border border-gray-200">
                                    <div class="flex items-center gap-2">
                                        {{ $wo['wonum'] }}
                                        @if(isset($wo['reportdate']) && \Carbon\Carbon::parse($wo['reportdate'])->diffInHours(now()) < 24)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                <span class="w-1 h-1 rounded-full bg-blue-500 mr-1 animate-pulse"></span>
                                                New
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-2 border border-gray-200 max-w-[200px] overflow-hidden text-ellipsis whitespace-nowrap">{{ $wo['description'] ?? '-' }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-center">
                                    <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded text-xs font-medium border border-blue-100">
                                        {{ $wo['power_plant_name'] ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 border border-gray-200 text-center">
                                    <span class="px-2 py-1 rounded-full {{ ($wo['worktype'] ?? '') == 'PM' ? 'bg-blue-100 text-blue-600' : 'bg-yellow-100 text-yellow-600' }}">
                                        {{ $wo['worktype'] ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 border border-gray-200 text-center">
                                    @php
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                        if (in_array($woStatus, ['COMP', 'CLOSE'])) {
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
                                <td class="px-4 py-2 border border-gray-200">{{ $wo['kendala'] ?? '-' }}</td>
                                <td class="px-4 py-2 border border-gray-200">{{ $wo['tindak_lanjut'] ?? '-' }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-center">
                                    @if(isset($wo['document_path']) && $wo['document_path'])
                                        <a href="{{ url('storage/' . $wo['document_path']) }}" target="_blank" class="text-blue-600 underline text-xs">Lihat Dokumen</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-2 border border-gray-200 text-center">
                                    <span class="px-2 py-1 rounded-full {{ ($wo['wopriority'] ?? '') == 'Low' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                        {{ $wo['wopriority'] ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 border border-gray-200">
                                    @if(isset($wo['schedule_start']) && $wo['schedule_start'])
                                        {{ \Carbon\Carbon::parse($wo['schedule_start'])->format('d-m-Y H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-2 border border-gray-200">
                                    @if(isset($wo['schedule_finish']) && $wo['schedule_finish'])
                                        {{ \Carbon\Carbon::parse($wo['schedule_finish'])->format('d-m-Y H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-2 border border-gray-200">{{ $wo['labor'] ?? '-' }}</td>
                                <td class="px-4 py-2 border border-gray-200">
                                    @if(isset($wo['labors']) && is_array($wo['labors']))
                                        {{ implode(', ', $wo['labors']) }}
                                    @elseif(isset($wo['labors']) && is_string($wo['labors']))
                                        {{ $wo['labors'] }}
                                    @else
                                        -
                                    @endif
                                </td>
                               
                            </tr>
                            @empty
                            <tr>
                                <td colspan="14" class="text-center py-4">Tidak ada data work order untuk labor Anda.</td>
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
                            <a href="{{ $workOrdersPaginator->appends(array_filter([
                                'q' => request('q'),
                                'status' => request('status'),
                                'unit' => request('unit')
                            ]))->previousPageUrl() }}" 
                               class="px-3 py-1 bg-[#0A749B] text-white rounded">Sebelumnya</a>
                        @endif

                        @foreach ($workOrdersPaginator->getUrlRange(1, min($workOrdersPaginator->lastPage(), 10)) as $page => $url)
                            @php
                                $pUrl = $workOrdersPaginator->appends(array_filter([
                                    'q' => request('q'),
                                    'status' => request('status'),
                                    'unit' => request('unit')
                                ]))->url($page);
                            @endphp
                            @if ($page == $workOrdersPaginator->currentPage())
                                <span class="px-3 py-1 bg-[#0A749B] text-white rounded">{{ $page }}</span>
                            @else
                                <a href="{{ $pUrl }}" class="px-3 py-1 rounded bg-white text-[#0A749B] border border-[#0A749B]">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        @if ($workOrdersPaginator->hasMorePages())
                            <a href="{{ $workOrdersPaginator->appends(array_filter([
                                'q' => request('q'),
                                'status' => request('status'),
                                'unit' => request('unit')
                            ]))->nextPageUrl() }}" 
                               class="px-3 py-1 bg-[#0A749B] text-white rounded">Selanjutnya</a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </main>
    </div>
</div>
<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-btn').forEach(tab => {
            tab.classList.remove('active', 'border-blue-500');
        });
        const selectedTab = document.querySelector(`.tab-btn[data-tab="${tabId}"]`);
        if (selectedTab) {
            selectedTab.classList.add('active', 'border-blue-500');
        }
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        const selectedContent = document.getElementById(`${tabId}-tab`);
        if (selectedContent) {
            selectedContent.classList.remove('hidden');
        }
    }

    function updateFilter(type, value) {
        if (type === 'status') {
            document.getElementById('formStatus').value = value;
        } else if (type === 'unit') {
            document.getElementById('formUnit').value = value;
        }
        document.getElementById('filterForm').submit();
    }
</script>
<style>
.tab-btn.active {
    border-bottom-color: #3b82f6;
    color: #3b82f6;
}
.tab-content {
    transition: all 0.3s ease-in-out;
}
th .group:hover .hidden {
    display: block !important;
}
</style>
@endsection