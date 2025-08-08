@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        @include('components.user-sidebar')
        
        <!-- Main Content -->
        <div id="main-content" class="flex-1 overflow-auto">
            <!-- Header -->
            <header class="bg-white shadow-sm sticky top-0">
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
                        <h1 class="text-xl font-semibold text-gray-800">Laporan SR/WO</h1>
                    </div>
                    <div class="flex items-center gap-x-4 relative">
                        <!-- Notification Icon -->
                        <div class="relative">
                            <button id="notificationToggle" class="relative focus:outline-none" onclick="toggleNotificationDropdown()">
                                <i class="fas fa-bell text-gray-500 hover:text-[#009BB9] text-xl"></i>
                                <!-- Example: Red dot for unread notifications -->
                                <span class="absolute top-0 right-0 block h-2 w-2 rounded-full ring-2 ring-white bg-red-500"></span>
                            </button>
                            <!-- Notification Dropdown (hidden by default) -->
                            <div id="notificationDropdown" class="absolute right-0 mt-2 w-72 bg-white rounded-md shadow-lg hidden z-20">
                                <div class="p-4 border-b font-semibold text-gray-700">Notifikasi</div>
                                <div class="max-h-60 overflow-y-auto">
                                    <!-- Example notification item -->
                                    <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">
                                        <div class="flex items-center">
                                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                            <span>Tidak ada notifikasi baru</span>
                                        </div>
                                    </a>
                                    <!-- Tambahkan notifikasi dinamis di sini -->
                                </div>
                            </div>
                        </div>
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
                // Toggle user dropdown
                function toggleDropdown() {
                    var dropdown = document.getElementById('dropdown');
                    dropdown.classList.toggle('hidden');
                }
                // Toggle notification dropdown
                function toggleNotificationDropdown() {
                    var dropdown = document.getElementById('notificationDropdown');
                    dropdown.classList.toggle('hidden');
                }
                // Optional: close dropdowns when clicking outside
                document.addEventListener('click', function(event) {
                    var userDropdown = document.getElementById('dropdown');
                    var userBtn = document.getElementById('dropdownToggle');
                    var notifDropdown = document.getElementById('notificationDropdown');
                    var notifBtn = document.getElementById('notificationToggle');
                    if (userDropdown && !userDropdown.classList.contains('hidden') && !userBtn.contains(event.target) && !userDropdown.contains(event.target)) {
                        userDropdown.classList.add('hidden');
                    }
                    if (notifDropdown && !notifDropdown.classList.contains('hidden') && !notifBtn.contains(event.target) && !notifDropdown.contains(event.target)) {
                        notifDropdown.classList.add('hidden');
                    }
                });
            </script>

            <!-- Dashboard Content -->
            <main class="px-6">
                @include('layouts.breadcrumbs', ['breadcrumbs' => [['title' => 'Laporan SR/WO']]])
                
                <!-- Tabs Navigation -->
                <div class="mb-6 border-b border-gray-200">
                    <nav class="flex space-x-4" aria-label="Tabs">
                        <button id="tab-sr" class="px-4 py-2 text-sm font-medium text-blue-700 border-b-2 border-blue-600 focus:outline-none" onclick="showTab('sr')">Service Request (SR)</button>
                        <button id="tab-wo" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-700 hover:border-blue-600 border-b-2 border-transparent focus:outline-none" onclick="showTab('wo')">Work Order (WO)</button>
                        <button id="tab-backlog" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-700 hover:border-blue-600 border-b-2 border-transparent focus:outline-none" onclick="showTab('backlog')">WO Backlog</button>
                    </nav>
                </div>

                <!-- Tab Content: SR -->
                <div id="content-sr">
                    <div class="bg-white rounded-lg shadow p-6 mb-8">
                        <h2 class="text-lg font-semibold mb-4">Daftar Service Request (SR)</h2>
                        <div class="flex flex-col md:flex-row md:items-center md:space-x-2 space-y-2 md:space-y-0 mb-4">
                            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                                <label for="date-filter-sr" class="text-sm font-medium text-gray-700">Filter Tanggal:</label>
                                <input type="date" id="date-filter-sr" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-[150px]" onchange="filterSRTable()">
                                <select id="unit-filter-sr" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-[180px]" onchange="filterSRTable()">
                                    <option value="">Semua Unit</option>
                                    @foreach($powerPlants as $powerPlant)
                                        <option value="{{ $powerPlant->id }}">{{ $powerPlant->name }}</option>
                                    @endforeach
                                </select>
                                <select id="status-filter-sr" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-[140px]" onchange="filterSRTable()">
                                    <option value="">Semua Status</option>
                                    <option value="Open">Open</option>
                                    <option value="Closed">Closed</option>
                                </select>
                                <select id="downtime-filter-sr" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-[160px]" onchange="filterSRTable()">
                                    <option value="">Semua Downtime</option>
                                    <option value="Ya">Ya</option>
                                    <option value="Tidak">Tidak</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-2 w-full md:w-auto mt-2 md:mt-0">
                                <input type="text" id="search-input-sr" placeholder="Cari Service Request..." class="border border-gray-300 rounded-md px-3 py-2 text-sm w-full md:w-[220px]" oninput="filterSRTable()">
                                <button id="search-button-sr" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-fixed divide-y divide-gray-200 border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-center">No</th>
                                        <th class="px-4 py-2 text-center">ID SR</th>
                                        <th class="px-4 py-2 text-center w-[300px]">Unit</th>
                                        <th class="px-4 py-2 text-center">Deskripsi</th>
                                        <th class="px-4 py-2 text-center">Status</th>
                                        <th class="px-4 py-2 text-center">Tanggal</th>
                                        <th class="px-4 py-2 text-center">Downtime</th>
                                        <th class="px-4 py-2 text-center">Tipe SR</th>
                                        <th class="px-4 py-2 text-center">Priority</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($serviceRequests as $index => $sr)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-center border border-gray-200">{{ $index + 1 }}</td>
                                        <td class="px-4 py-2 border border-gray-200">SR{{ $sr->id }}</td>
                                        <td class="px-4 py-2 border border-gray-200 w-[300px]">
                                            <span class="text-gray-500 bg-gray-100 rounded-full px-2 py-1">
                                                @php
                                                    $unitName = $sr->powerPlant->name ?? '-';
                                                    $words = explode(' ', $unitName);
                                                    $firstTwo = implode(' ', array_slice($words, 0, 2));
                                                @endphp
                                                {{ $firstTwo }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 border border-gray-200">{{ $sr->description }}</td>
                                        <td class="px-4 py-2 border border-gray-200">
                                            <span class="px-2 py-1 rounded-full {{ $sr->status == 'Open' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                                {{ $sr->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 border border-gray-200">{{ $sr->created_at ? \Carbon\Carbon::parse($sr->created_at)->format('d/m/Y') : '-' }}</td>
                                        <td class="px-4 py-2 border border-gray-200 text-center">
                                            @if(strtolower($sr->downtime) == 'ya')
                                                <span class="px-2 py-1 rounded-full bg-red-100 text-red-600">
                                                    {{ $sr->downtime }}
                                                </span>
                                            @elseif(strtolower($sr->downtime) == 'tidak')
                                                <span class="px-2 py-1 rounded-full bg-blue-100 text-blue-600">
                                                    {{ $sr->downtime }}
                                                </span>
                                            @else
                                                <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-600">
                                                    {{ $sr->downtime }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 border border-gray-200 text-center">
                                            <span class="px-2 py-1 rounded-full {{ $sr->tipe_sr == 'PM' ? 'bg-blue-100 text-blue-600' : 'bg-yellow-100 text-yellow-600' }}">
                                                {{ $sr->tipe_sr }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 border border-gray-200 text-center">
                                            <span class="px-2 py-1 rounded-full {{ $sr->priority == 'Low' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                                {{ $sr->priority }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="9" class="text-center text-gray-500 py-4">Tidak ada data Service Request</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tab Content: WO (hidden by default) -->
                <div id="content-wo" style="display:none;">
                    <div class="bg-white rounded-lg shadow p-6 mb-8">
                        <h2 class="text-lg font-semibold mb-4">Daftar Work Order (WO)</h2>
                        <div class="flex flex-col md:flex-row md:items-center md:space-x-2 space-y-2 md:space-y-0 mb-4">
                            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                                <label for="date-filter-wo" class="text-sm font-medium text-gray-700">Filter Tanggal:</label>
                                <input type="date" id="date-filter-wo" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-[150px]" onchange="filterWOTable()">
                                <select id="unit-filter-wo" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-[180px]" onchange="filterWOTable()">
                                    <option value="">Semua Unit</option>
                                    @foreach($powerPlants as $powerPlant)
                                        <option value="{{ $powerPlant->id }}">{{ $powerPlant->name }}</option>
                                    @endforeach
                                </select>
                                <select id="status-filter-wo" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-[140px]" onchange="filterWOTable()">
                                    <option value="">Semua Status</option>
                                    <option value="Open">Open</option>
                                    <option value="Closed">Closed</option>
                                </select>
                                <select id="downtime-filter-wo" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-[160px]" onchange="filterWOTable()">
                                    <option value="">Semua Downtime</option>
                                    <option value="Ya">Ya</option>
                                    <option value="Tidak">Tidak</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-2 w-full md:w-auto mt-2 md:mt-0">
                                <input type="text" id="search-input-wo" placeholder="Cari Work Order..." class="border border-gray-300 rounded-md px-3 py-2 text-sm w-full md:w-[220px]" oninput="filterWOTable()">
                                <button id="search-button-wo" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-fixed divide-y divide-gray-200 border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-center">No</th>
                                        <th class="px-4 py-2 text-center">ID WO</th>
                                        <th class="px-4 py-2 text-center w-[300px]">Unit</th>
                                        <th class="px-4 py-2 text-center">Deskripsi</th>
                                        <th class="px-4 py-2 text-center">Status</th>
                                        <th class="px-4 py-2 text-center">Tanggal</th>
                                        <th class="px-4 py-2 text-center">Tipe</th>
                                        <th class="px-4 py-2 text-center">Priority</th>
                                        <th class="px-4 py-2 text-center">Labor</th>
                                        <th class="px-4 py-2 text-center">Schedule Start</th>
                                        <th class="px-4 py-2 text-center">Schedule Finish</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($workOrders as $index => $wo)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-center border border-gray-200">{{ $index + 1 }}</td>
                                        <td class="px-4 py-2 border border-gray-200">WO{{ $wo->id }}</td>
                                        <td class="px-4 py-2 border border-gray-200 w-[300px]">{{ $wo->powerPlant->name ?? '-' }}</td>
                                        <td class="px-4 py-2 border border-gray-200">{{ $wo->description }}</td>
                                        <td class="px-4 py-2 border border-gray-200">
                                            <span class="px-2 py-1 rounded-full {{ $wo->status == 'Open' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                                {{ $wo->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 border border-gray-200">{{ $wo->created_at ? \Carbon\Carbon::parse($wo->created_at)->format('d/m/Y') : '-' }}</td>
                                        <td class="px-4 py-2 border border-gray-200 text-center">
                                            <span class="px-2 py-1 rounded-full {{ $wo->type == 'PM' ? 'bg-blue-100 text-blue-600' : 'bg-yellow-100 text-yellow-600' }}">
                                                {{ $wo->type }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 border border-gray-200 text-center">
                                            <span class="px-2 py-1 rounded-full {{ $wo->priority == 'Low' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                                {{ $wo->priority }}
                                        <td class="px-4 py-2 border border-gray-200 text-center">{{ $wo->labor ?? '-' }}</td>
                                        <td class="px-4 py-2 border border-gray-200">{{ $wo->schedule_start }}</td>
                                        <td class="px-4 py-2 border border-gray-200">{{ $wo->schedule_finish }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="10" class="text-center text-gray-500 py-4">Tidak ada data Work Order</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tab Content: Backlog (hidden by default) -->
                <div id="content-backlog" style="display:none;">
                    <div class="bg-white rounded-lg shadow p-6 mb-8">
                        <h2 class="text-lg font-semibold mb-4">Daftar WO Backlog</h2>
                        <div class="flex flex-col md:flex-row md:items-center md:space-x-2 space-y-2 md:space-y-0 mb-4">
                            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                                <label for="date-filter-backlog" class="text-sm font-medium text-gray-700">Filter Tanggal:</label>
                                <input type="date" id="date-filter-backlog" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-[150px]" onchange="filterBacklogTable()">
                                <select id="unit-filter-backlog" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-[180px]" onchange="filterBacklogTable()">
                                    <option value="">Semua Unit</option>
                                    @foreach($powerPlants as $powerPlant)
                                        <option value="{{ $powerPlant->id }}">{{ $powerPlant->name }}</option>
                                    @endforeach
                                </select>
                                <select id="status-filter-backlog" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-[140px]" onchange="filterBacklogTable()">
                                    <option value="">Semua Status</option>
                                    <option value="Open">Open</option>
                                    <option value="Closed">Closed</option>
                                </select>
                                <select id="downtime-filter-backlog" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-[160px]" onchange="filterBacklogTable()">
                                    <option value="">Semua Downtime</option>
                                    <option value="Ya">Ya</option>
                                    <option value="Tidak">Tidak</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-2 w-full md:w-auto mt-2 md:mt-0">
                                <input type="text" id="search-input-backlog" placeholder="Cari WO Backlog..." class="border border-gray-300 rounded-md px-3 py-2 text-sm w-full md:w-[220px]" oninput="filterBacklogTable()">
                                <button id="search-button-backlog" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-fixed divide-y divide-gray-200 border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2">No</th>
                                        <th class="px-4 py-2">No WO</th>
                                        <th class="px-4 py-2 w-[300px]">Unit</th>
                                        <th class="px-4 py-2">Deskripsi</th>
                                        <th class="px-4 py-2">Status</th>
                                        <th class="px-4 py-2">Tanggal Backlog</th>
                                        <th class="px-4 py-2">Tipe WO</th>
                                        <th class="px-4 py-2">Priority</th>
                                        <th class="px-4 py-2">Schedule Start</th>
                                        <th class="px-4 py-2">Schedule Finish</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($woBacklogs as $index => $backlog)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-center border border-gray-200">{{ $index + 1 }}</td>
                                        <td class="px-4 py-2 border border-gray-200">WO{{ $backlog->no_wo }}</td>
                                        <td class="px-4 py-2 border border-gray-200 w-[300px]">{{ $backlog->powerPlant->name ?? '-' }}</td>
                                        <td class="px-4 py-2 border border-gray-200">{{ $backlog->deskripsi }}</td>
                                        <td class="px-4 py-2 border border-gray-200">
                                            <span class="px-2 py-1 rounded-full {{ $backlog->status == 'Open' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                                {{ $backlog->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 border border-gray-200">{{ $backlog->tanggal_backlog ? \Carbon\Carbon::parse($backlog->tanggal_backlog)->format('d/m/Y') : '-' }}</td>
                                        <td class="px-4 py-2 border border-gray-200 text-center">
                                            <span class="px-2 py-1 rounded-full {{ $backlog->type_wo == 'PM' ? 'bg-blue-100 text-blue-600' : 'bg-yellow-100 text-yellow-600' }}">
                                                {{ $backlog->type_wo }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 border border-gray-200 text-center">
                                            <span class="px-2 py-1 rounded-full {{ $backlog->priority == 'Low' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                                {{ $backlog->priority }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 border border-gray-200 text-center">{{ $backlog->schedule_start }}</td>
                                        <td class="px-4 py-2 border border-gray-200 text-center">{{ $backlog->schedule_finish }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="10" class="text-center text-gray-500 py-4">Tidak ada data WO Backlog</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <script>
                    function showTab(tab) {
                        const srTab = document.getElementById('tab-sr');
                        const woTab = document.getElementById('tab-wo');
                        const backlogTab = document.getElementById('tab-backlog');
                        const srContent = document.getElementById('content-sr');
                        const woContent = document.getElementById('content-wo');
                        const backlogContent = document.getElementById('content-backlog');
                        if (tab === 'sr') {
                            srTab.classList.add('text-blue-700', 'border-blue-600');
                            srTab.classList.remove('text-gray-600', 'border-transparent');
                            woTab.classList.remove('text-blue-700', 'border-blue-600');
                            woTab.classList.add('text-gray-600', 'border-transparent');
                            backlogTab.classList.remove('text-blue-700', 'border-blue-600');
                            backlogTab.classList.add('text-gray-600', 'border-transparent');
                            srContent.style.display = '';
                            woContent.style.display = 'none';
                            backlogContent.style.display = 'none';
                        } else if (tab === 'wo') {
                            woTab.classList.add('text-blue-700', 'border-blue-600');
                            woTab.classList.remove('text-gray-600', 'border-transparent');
                            srTab.classList.remove('text-blue-700', 'border-blue-600');
                            srTab.classList.add('text-gray-600', 'border-transparent');
                            backlogTab.classList.remove('text-blue-700', 'border-blue-600');
                            backlogTab.classList.add('text-gray-600', 'border-transparent');
                            srContent.style.display = 'none';
                            woContent.style.display = '';
                            backlogContent.style.display = 'none';
                        } else {
                            backlogTab.classList.add('text-blue-700', 'border-blue-600');
                            backlogTab.classList.remove('text-gray-600', 'border-transparent');
                            srTab.classList.remove('text-blue-700', 'border-blue-600');
                            srTab.classList.add('text-gray-600', 'border-transparent');
                            woTab.classList.remove('text-blue-700', 'border-blue-600');
                            woTab.classList.add('text-gray-600', 'border-transparent');
                            srContent.style.display = 'none';
                            woContent.style.display = 'none';
                            backlogContent.style.display = '';
                        }
                    }
                </script>
            </main>
        </div>
    </div>
    <script src="{{ asset('js/toggle.js') }}"></script>

@endsection