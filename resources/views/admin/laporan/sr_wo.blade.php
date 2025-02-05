@extends('layouts.app')

@section('content')

    <div class="flex h-screen bg-gray-50 overflow-auto">
        <!-- Sidebar -->
       @include('components.sidebar')

        <!-- Main Content -->
        <div id="main-content" class="flex-1 main-content">
            <header class="bg-white shadow-sm sticky top-0 z-20">
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
                    <h1 class="text-xl font-semibold text-gray-800">Laporan SR/WO
                        
                    </h1>
                    </div>
                    
                    @include('components.timer')
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
            <div class="pt-2">
                <x-admin-breadcrumb :breadcrumbs="[['name' => 'Laporan SR/WO', 'url' => null]]" />
            </div>

            <main class="px-6">
                <!-- Tab Navigation -->
                <div class="mb-4 border-b border-gray-200">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                        <li class="mr-2">
                            <a href="#" onclick="switchTab('sr'); return false;" 
                               class="inline-block p-4 border-b-2 rounded-t-lg tab-btn active" 
                               data-tab="sr">
                                Service Request (SR)
                                <span class="ml-2 bg-green-400 text-gray-700 px-2 py-1 rounded-full text-xs">
                                    {{ $serviceRequests->count() }}
                                </span>
                            </a>
                        </li>
                        <li class="mr-2">
                            <a href="#" onclick="switchTab('wo'); return false;" 
                               class="inline-block p-4 border-b-2 rounded-t-lg tab-btn" 
                               data-tab="wo">
                                Work Order (WO)
                                <span class="ml-2 bg-blue-400 text-gray-700 px-2 py-1 rounded-full text-xs">
                                    {{ $workOrders->count() }}
                                </span>
                            </a>
                        </li>
                        <li class="mr-2">
                            <a href="#" onclick="switchTab('backlog'); return false;" 
                               class="inline-block p-4 border-b-2 rounded-t-lg tab-btn" 
                               data-tab="backlog">
                                WO Backlog
                                <span class="ml-2 bg-red-400 text-gray-700 px-2 py-1 rounded-full text-xs">
                                    {{ $woBacklogs->count() }}
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- SR Table -->
                <div id="sr-tab" class="tab-content active">
                    <div class="bg-white rounded-lg shadow p-6 mb-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-md font-semibold">Daftar Service Request (SR)</h3>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.laporan.manage', ['tab' => 'sr']) }}" 
                                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 flex items-center">
                                    <i class="fas fa-cog mr-2"></i> Manage SR
                                </a>
                                <a href="{{ route('admin.laporan.create-sr') }}" 
                                   class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 flex items-center">
                                    <i class="fas fa-plus-circle mr-2"></i> Tambah SR
                                </a>
                            </div>
                        </div>
                        
                        <!-- Header section dengan search dan counter -->
                        <div class="flex justify-between items-center mb-4">
                            <!-- Search dengan style baru -->
                            <div class="w-1/3">
                                <div class="relative">
                                    <input type="text" 
                                           id="searchSR" 
                                           placeholder="Cari SR, unit, atau status..."
                                           onkeyup="if(event.key === 'Enter') searchSRTable()"
                                           class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- Counter -->
                            <div class="text-gray-600">
                                Menampilkan <span id="srVisibleCount">0</span> dari <span id="srTotalCount">0</span> data
                            </div>
                        </div>

                        <!-- Tabel SR -->
                        <div class="overflow-auto max-h-96">
                            <table id="srTable"
                                class="min-w-full divide-y divide-gray-200 border-collapse border border-gray-200">
                                <thead class="sticky top-0 z-10">
                                    <tr style="background-color: #0A749B; color: white;">
                                        <th class="py-2 px-4 border-b min-w-[120px]">No</th>
                                        <th class="py-2 px-4 border-b min-w-[120px]">ID SR</th>
                                        <th class="py-2 px-4 border-b">
                                            <div class="flex items-center justify-between">
                                                <span>Unit</span>
                                                <div class="relative">
                                                    <select id="srUnitFilter" onchange="filterSRTable()" 
                                                            class="appearance-none bg-transparent text-white cursor-pointer pl-2 pr-6 py-0 text-sm focus:outline-none">
                                                        <option value="" class="text-gray-700">Semua</option>
                                                        @foreach($powerPlants as $unit)
                                                            <option value="{{ $unit->name }}" class="text-gray-700">{{ $unit->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
                                                        <svg class="h-4 w-4 fill-current text-white" viewBox="0 0 20 20">
                                                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th class="py-2 px-4 border-b">Deskripsi</th>
                                        <th class="py-2 px-4 border-b">
                                            <div class="flex items-center justify-between">
                                                <span>Status</span>
                                                <div class="relative">
                                                    <select id="srStatusFilter" onchange="filterSRTable()" 
                                                            class="appearance-none bg-transparent text-white cursor-pointer pl-2 pr-6 py-0 text-sm focus:outline-none">
                                                        <option value="" class="text-gray-700">Semua</option>
                                                        <option value="Open" class="text-gray-700">Open</option>
                                                        <option value="Closed" class="text-gray-700">Closed</option>
                                                    </select>
                                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
                                                        <svg class="h-4 w-4 fill-current text-white" viewBox="0 0 20 20">
                                                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th class="py-2 px-4 border-b">Tanggal</th>
                                        <th class="py-2 px-4 border-b">
                                            <div class="flex items-center justify-between">
                                                <span>Downtime</span>
                                                <div class="relative">
                                                    <select id="srDowntimeFilter" onchange="filterSRTable()" 
                                                            class="appearance-none bg-transparent text-white cursor-pointer pl-2 pr-6 py-0 text-sm focus:outline-none">
                                                        <option value="" class="text-gray-700">Semua</option>
                                                        <option value="Yes" class="text-gray-700">Ya</option>
                                                        <option value="No" class="text-gray-700">Tidak</option>
                                                    </select>
                                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
                                                        <svg class="h-4 w-4 fill-current text-white" viewBox="0 0 20 20">
                                                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th class="py-2 px-4 border-b">Tipe SR</th>
                                        <th class="py-2 px-4 border-b">Priority</th>
                                        <th class="py-2 px-4 border-b">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($serviceRequests as $index => $sr)
                                        <tr data-sr-id="SR-{{ str_pad($sr->id, 4, '0', STR_PAD_LEFT) }}" class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-4 py-2 text-center border border-gray-200">{{ $index + 1 }}</td>
                                            <td class="px-4 py-2 border border-gray-200 min-w-[120px] whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    SR-{{ str_pad($sr->id, 5, '0', STR_PAD_LEFT) }}
                                                    @if($sr->created_at->diffInHours(now()) < 24)
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5 animate-pulse"></span>
                                                                New
                                                            </span>
                                                            <span class="text-xs text-gray-500">
                                                                {{ $sr->created_at->diffForHumans(['parts' => 1, 'short' => true]) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-2 px-4 border border-gray-200">
                                                @if($sr->powerPlant)
                                                    {{ $sr->powerPlant->name }}
                                                @elseif($sr->unit_source)
                                                    @php
                                                        $unitName = str_replace('mysql_', '', $sr->unit_source);
                                                        $unitName = ucfirst($unitName);
                                                    @endphp
                                                    {{ $unitName }}
                                                @else
                                                    Unit tidak tersedia
                                                @endif
                                            </td>
                                            <td class="py-2 px-4 border border-gray-200" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $sr->description }}</td>
                                            <td data-column="status" class="py-2 px-4 border border-gray-200">
                                                <span class="px-2 py-1 rounded-full {{ $sr->status == 'Open' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                                    {{ $sr->status }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border border-gray-200">{{ $sr->created_at }}</td>
                                            <td class="py-2 px-4 border border-gray-200">
                                                {{ $sr->downtime }}
                                            </td>
                                            <td class="py-2 px-4 border border-gray-200">
                                                {{ $sr->tipe_sr }}
                                            </td>
                                            <td class="py-2 px-4 border border-gray-200">
                                                {{ $sr->priority }}
                                            </td>
                                            <td data-column="action" class="py-2 px-4 border border-gray-200">
                                                <button onclick="updateStatus('sr', {{ $sr->id }}, '{{ $sr->status }}')"
                                                    class="px-3 py-1 text-sm rounded-full {{ $sr->status == 'Open' ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600' }} text-white">
                                                    {{ $sr->status == 'Open' ? 'Tutup' : 'Buka' }}
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- WO Table -->
                <div id="wo-tab" class="tab-content hidden">
                    <div class="bg-white rounded-lg shadow p-6 mb-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-md font-semibold">Daftar Work Order (WO)</h3>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.laporan.manage', ['tab' => 'wo']) }}" 
                                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 flex items-center">
                                    <i class="fas fa-cog mr-2"></i> Manage WO
                                </a>
                                <a href="{{ route('admin.laporan.create-wo') }}" 
                                   class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 flex items-center">
                                    <i class="fas fa-plus-circle mr-2"></i> Tambah WO
                                </a>
                            </div>
                        </div>
                        
                        <!-- Header section dengan search dan counter -->
                        <div class="flex justify-between items-center mb-4">
                            <!-- Search dengan style baru -->
                            <div class="w-1/3">
                                <div class="relative">
                                    <input type="text" 
                                           id="searchWO" 
                                           placeholder="Cari WO, unit, atau status..."
                                           onkeyup="if(event.key === 'Enter') searchWOTable()"
                                           class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- Counter -->
                            <div class="text-gray-600">
                                Menampilkan <span id="woVisibleCount">0</span> dari <span id="woTotalCount">0</span> data
                            </div>
                        </div>

                        <!-- Tabel WO -->
                        <div class="overflow-auto max-h-96">
                            @if(session('backlog_notification'))
                            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                                <p class="font-bold">Perhatian!</p>
                                <p>{{ session('backlog_notification') }}</p>
                            </div>
                            @endif
                            <table id="woTable" class="min-w-full bg-white border border-gray-300">
                                <thead class="sticky top-0 z-10">
                                    <tr style="background-color: #0A749B; color: white;">
                                        <th class="py-2 px-4 border-b min-w-[120px]">No</th>
                                        <th class="py-2 px-4 border-b min-w-[120px]">ID WO</th>
                                        <th class="py-2 px-4 border-b">
                                            <div class="flex items-center justify-between">
                                                <span>Unit</span>
                                                <div class="relative">
                                                    <select id="woUnitFilter" onchange="filterWOTable()" 
                                                            class="appearance-none bg-transparent text-white cursor-pointer pl-2 pr-6 py-0 text-sm focus:outline-none">
                                                        <option value="" class="text-gray-700">Semua</option>
                                                        @foreach($powerPlants as $unit)
                                                            <option value="{{ $unit->name }}" class="text-gray-700">{{ $unit->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
                                                        <svg class="h-4 w-4 fill-current text-white" viewBox="0 0 20 20">
                                                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th class="py-2 px-4 border-b">Type</th>
                                        <th class="py-2 px-4 border-b" style="max-width: 300px;">Deskripsi</th>
                                        <th class="py-2 px-4 border-b">Kendala</th>
                                        <th class="py-2 px-4 border-b">Tindak Lanjut</th>
                                        <th class="py-2 px-4 border-b">
                                            <div class="flex items-center justify-between">
                                                <span>Status</span>
                                                <div class="relative">
                                                    <select id="woStatusFilter" onchange="filterWOTable()" 
                                                            class="appearance-none bg-transparent text-white cursor-pointer pl-2 pr-6 py-0 text-sm focus:outline-none">
                                                        <option value="" class="text-gray-700">Semua</option>
                                                        <option value="Open" class="text-gray-700">Open</option>
                                                        <option value="Closed" class="text-gray-700">Closed</option>
                                                        <option value="Comp" class="text-gray-700">Comp</option>
                                                        <option value="APPR" class="text-gray-700">APPR</option>
                                                        <option value="WAPPR" class="text-gray-700">WAPPR</option>
                                                        <option value="WMATL" class="text-gray-700">WMATL</option>
                                                    </select>
                                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
                                                        <svg class="h-4 w-4 fill-current text-white" viewBox="0 0 20 20">
                                                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                                        </svg>  
                                                    </div>                  
                                                </div>
                                            </div>
                                        </th>
                                        <th class="py-2 px-4 border-b">Tanggal</th>
                                        <th class="py-2 px-4 border-b">Priority</th>
                                        <th class="py-2 px-4 border-b">Schedule Start</th>
                                        <th class="py-2 px-4 border-b">Schedule Finish</th>
                                        <th class="py-2 px-4 border-b">Dokumen</th>
                                        <th class="py-2 px-4 border-b">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($workOrders as $index => $wo)
                                        <tr data-wo-id="WO-{{ str_pad($wo->id, 4, '0', STR_PAD_LEFT) }}" class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-4 py-2 text-center border border-gray-200">{{ $index + 1 }}</td>
                                            <td class="px-4 py-2 border border-gray-200 min-w-[120px] whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    WO-{{ str_pad($wo->id, 5, '0', STR_PAD_LEFT) }}
                                                    @if($wo->created_at->diffInHours(now()) < 24)
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5 animate-pulse"></span>
                                                                New
                                                            </span>
                                                            <span class="text-xs text-gray-500">
                                                                {{ $wo->created_at->diffForHumans(['parts' => 1, 'short' => true]) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-2 px-4 border border-gray-200">
                                                @if($wo->powerPlant)
                                                    {{ $wo->powerPlant->name }}
                                                @else
                                                    Unit tidak tersedia
                                                @endif
                                            </td>
                                            <td class="py-2 px-4 border border-gray-200">
                                                <span class="px-2 py-1 rounded-full text-xs
                                                    {{ $wo->type == 'CM' ? 'bg-blue-100 text-blue-600' : 
                                                       ($wo->type == 'PM' ? 'bg-green-100 text-green-600' : 
                                                       ($wo->type == 'PDM' ? 'bg-yellow-100 text-yellow-600' : 
                                                       ($wo->type == 'PAM' ? 'bg-purple-100 text-purple-600' : 
                                                       ($wo->type == 'OH' ? 'bg-red-100 text-red-600' : 
                                                       ($wo->type == 'EJ' ? 'bg-indigo-100 text-indigo-600' : 
                                                       ($wo->type == 'EM' ? 'bg-gray-100 text-gray-600' : 
                                                       'bg-gray-100 text-gray-600')))))) }}">
                                                    {{ $wo->type }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border border-gray-200" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $wo->description }}</td>
                                            <td class="py-2 px-4 border border-gray-200" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $wo->kendala ?? '-' }}</td>
                                            <td class="py-2 px-4 border border-gray-200" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $wo->tindak_lanjut ?? '-' }}</td>
                                            <td data-column="status" class="py-2 px-4 border border-gray-200">
                                                <span class="bg-{{ $wo->status == 'Open' ? 'red-500' : ($wo->status == 'Closed' ? 'green-500' : ($wo->status == 'WAPPR' ? 'yellow-500' : 'gray-500')) }} text-white rounded-full px-2 py-1">
                                                    {{ $wo->status }}
                                                </span>
                                                @if($wo->is_backlogged)
                                                    <span class="ml-2 text-xs bg-gray-200 text-gray-700 rounded-full px-2 py-1">
                                                        In Backlog
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-2 px-4 border border-gray-200">{{ $wo->created_at }}</td>
                                            <td class="py-2 px-4 border border-gray-200">
                                                {{ $wo->priority }}
                                            </td>   
                                            <td class="py-2 px-4 border border-gray-200">
                                                {{ $wo->schedule_start }}
                                            </td>
                                            <td class="py-2 px-4 border border-gray-200">
                                                {{ $wo->schedule_finish }}
                                            </td>
                                            <td class="py-2 px-4 border border-gray-200">
                                                @if($wo->document_path)
                                                    <a href="{{ route('admin.laporan.download-document', $wo->id) }}" 
                                                       class="text-blue-600 hover:text-blue-800 flex items-center"
                                                       target="_blank">
                                                        <i class="fas fa-file-alt mr-2"></i>
                                                        {{ $wo->document_description ?? 'Lihat Dokumen' }}
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td data-column="action" class="py-2 px-4 border border-gray-200">
                                                <div class="flex space-x-2">
                                                    @if ($wo->status != 'Closed')
                                                        <!-- Tombol Status -->
                                                        <button onclick="showStatusOptions('{{ $wo->id }}', '{{ $wo->status }}')"
                                                            class="p-2 flex items-center text-blue-600 hover:text-blue-800 hover:bg-blue-100 rounded-lg transition-colors group"
                                                            title="Ubah Status">
                                                            <i class="fas fa-exchange-alt mr-2"></i>
                                                            <span class="opacity-100">
                                                                Update Status
                                                            </span>
                                                        </button>
                                                        
                                                        <!-- Tombol Edit -->
                                                        <a href="{{ route('admin.laporan.edit-wo', $wo->id) }}"
                                                            class="p-2 flex items-center text-green-600 hover:text-green-800 hover:bg-green-100 rounded-lg transition-colors group"
                                                            title="Edit WO">
                                                            <i class="fas fa-edit mr-2 text-green-600"></i>
                                                            <span class="opacity-100">
                                                                Edit WO
                                                            </span>
                                                        </a>
                                                    @else
                                                        <!-- Status Closed -->
                                                        <span class="p-2 flex items-center text-gray-400" title="WO Closed">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                            </svg>
                                                            <span class="ml-2">Closed</span>
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Backlog Table -->
                <div id="backlog-tab" class="tab-content hidden">
                    <div class="bg-white rounded-lg shadow p-6 mb-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-md font-semibold">Daftar WO Backlog</h3>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.laporan.manage', ['tab' => 'backlog']) }}" 
                                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 flex items-center">
                                    <i class="fas fa-cog mr-2"></i> Manage Backlog
                                </a>
                                
                            </div>
                        </div>
                        
                        <!-- Header section dengan search dan counter -->
                        <div class="flex justify-between items-center mb-4">
                            <!-- Search dengan style baru -->
                            <div class="w-1/3">
                                <div class="relative">
                                    <input type="text" 
                                           id="searchBacklog" 
                                           placeholder="Cari backlog, unit, atau status..."
                                           onkeyup="if(event.key === 'Enter') searchBacklogTable()"
                                           class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- Counter -->
                            <div class="text-gray-600">
                                Menampilkan <span id="backlogVisibleCount">0</span> dari <span id="backlogTotalCount">0</span> data
                            </div>
                        </div>

                        <!-- Tabel Backlog -->
                        <div class="overflow-auto max-h-96">
                            <table id="backlogTable" class="min-w-full divide-y divide-gray-200 border-collapse border border-gray-200">
                                <thead class="sticky top-0 z-10">
                                    <tr style="background-color: #0A749B; color: white;">
                                        <th class="py-2 px-4 border-b min-w-[120px]">No</th>
                                        <th class="py-2 px-4 border-b min-w-[120px]">No WO</th>
                                        <th class="py-2 px-4 border-b">
                                            <div class="flex items-center justify-between">
                                                <span>Unit</span>
                                                <div class="relative">
                                                    <select id="backlogUnitFilter" onchange="filterBacklogTable()" 
                                                            class="appearance-none bg-transparent text-white cursor-pointer pl-2 pr-6 py-0 text-sm focus:outline-none">
                                                        <option value="" class="text-gray-700">Semua</option>
                                                        @foreach($powerPlants as $unit)
                                                            <option value="{{ $unit->name }}" class="text-gray-700">{{ $unit->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
                                                        <svg class="h-4 w-4 fill-current text-white" viewBox="0 0 20 20">
                                                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th class="py-2 px-4 border-b">Deskripsi</th>
                                        <th class="py-2 px-4 border-b">Tanggal Backlog</th>
                                        <th class="py-2 px-4 border-b">Keterangan</th>
                                        <th class="py-2 px-4 border-b">
                                            <div class="flex items-center justify-between">
                                                <span>Status</span>
                                                <div class="relative">
                                                    <select id="backlogStatusFilter" onchange="filterBacklogTable()" 
                                                            class="appearance-none bg-transparent text-white cursor-pointer pl-2 pr-6 py-0 text-sm focus:outline-none">
                                                        <option value="" class="text-gray-700">Semua</option>
                                                        <option value="Open" class="text-gray-700">Open</option>
                                                        <option value="Closed" class="text-gray-700">Closed</option>
                                                    </select>
                                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
                                                        <svg class="h-4 w-4 fill-current text-white" viewBox="0 0 20 20">
                                                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th class="py-2 px-4 border-b">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($woBacklogs as $index => $backlog)
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-4 py-2 text-center border border-gray-200">{{ $index + 1 }}</td>
                                            <td class="px-4 py-2 border border-gray-200 min-w-[120px] whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    WO-{{ str_pad($backlog->no_wo, 5, '0', STR_PAD_LEFT) }}
                                                    @if($backlog->created_at->diffInHours(now()) < 24)
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5 animate-pulse"></span>
                                                                New
                                                            </span>
                                                            <span class="text-xs text-gray-500">
                                                                {{ $backlog->created_at->diffForHumans(['parts' => 1, 'short' => true]) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-2 px-4 border border-gray-200">
                                                @if($backlog->powerPlant)
                                                    {{ $backlog->powerPlant->name }}
                                                @elseif($backlog->unit_source)
                                                    @php
                                                        $unitName = str_replace('mysql_', '', $backlog->unit_source);
                                                        $unitName = ucfirst($unitName);
                                                    @endphp
                                                    {{ $unitName }}
                                                @else
                                                    Unit tidak tersedia
                                                @endif
                                            </td>
                                            <td class="py-2 px-4 border border-gray-200" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $backlog->deskripsi }}</td>
                                            <td class="py-2 px-4 border border-gray-200">{{ $backlog->created_at }}</td>
                                            <td class="py-2 px-4 border border-gray-200">{{ $backlog->keterangan ?? 'N/A' }}</td>
                                            <td class="py-2 px-4 border border-gray-200">
                                                <span class="px-2 py-1 rounded-full {{ $backlog->status == 'Open' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                                    {{ $backlog->status }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border border-gray-200">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('admin.laporan.edit-wo-backlog', $backlog->id) }}"
                                                        class="px-3 py-1 text-sm rounded-full bg-blue-500 hover:bg-blue-600 text-white flex items-center">
                                                        <i class="fas fa-edit mr-2"></i> Edit
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    

    <!-- Modal SR -->
   
@endsection
<script src="{{ asset('js/toggle.js') }}"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Helper Functions
    function getStatusColor(status) {
        const colors = {
            'Open': 'red-500',
            'Closed': 'green-500',
            'Comp': 'blue-500',
            'APPR': 'yellow-500',
            'WAPPR': 'purple-500',
            'WMATL': 'gray-500'
        };
        return colors[status] || 'gray-500';
    }

    function getStatusBadge(status) {
        const color = getStatusColor(status);
        return `<span class="px-2 py-1 rounded-full bg-${color} text-white">
            ${status}
        </span>`;
    }

    function getActionButton(id, status) {
        if (status === 'Closed') {
            return `<button disabled class="px-3 py-1 text-sm rounded-full bg-gray-400 text-white">
                Closed
            </button>`;
        }
        return `<button onclick="showStatusOptions('${id}', '${status}')"
            class="px-3 py-1 text-sm rounded-full bg-blue-500 hover:bg-blue-600 text-white flex items-center">
            <i class="fas fa-edit mr-2"></i> Ubah
        </button>`;
    }

    // Main Functions
    function showStatusOptions(woId, currentStatus) {
        if (currentStatus === 'Closed') {
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: 'WO sudah ditutup dan tidak dapat diubah lagi.'
            });
            return;
        }

        // Cek apakah dokumen sudah diupload sebelum mengubah status ke Closed
        const row = document.querySelector(`tr[data-wo-id="WO-${String(woId).padStart(4, '0')}"]`);
        const documentCell = row.querySelector('td:nth-last-child(2)'); // Kolom dokumen
        const hasDocument = documentCell.textContent.trim() !== '-';

        Swal.fire({
            title: 'Pilih Status',
            input: 'select',
            inputOptions: {
                'Open': 'Open',
                'Closed': 'Closed',
                'Comp': 'Comp',
                'APPR': 'APPR',
                'WAPPR': 'WAPPR',
                'WMATL': 'WMATL'
            },
            inputValue: currentStatus,
            showCancelButton: true,
            cancelButtonText: 'Batal',
            confirmButtonText: 'Simpan',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            inputValidator: (value) => {
                if (!value) {
                    return 'Anda harus memilih status!';
                }
                // Cek jika status Closed dipilih tapi belum ada dokumen
                if (value === 'Closed' && !hasDocument) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Dokumen Diperlukan',
                        text: 'Anda harus mengupload dokumen terlebih dahulu sebelum menutup WO.',
                        showCancelButton: true,
                        confirmButtonText: 'Upload Dokumen',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#3085d6'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect ke halaman edit WO
                            window.location.href = `/admin/laporan/edit-wo/${woId}#document`;
                        }
                    });
                    return false;
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                processStatusUpdate(woId, result.value);
            }
        });
    }

    function processStatusUpdate(id, newStatus) {
        const url = `{{ url('/admin/laporan/update-wo-status') }}/${id}`;
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Play success sound
                playSound('success');
                
                // Update tampilan status secara real-time
                const row = document.querySelector(`tr[data-wo-id="WO-${String(id).padStart(4, '0')}"]`);
                const statusCell = row.querySelector('td[data-column="status"]');
                if (statusCell) {
                    statusCell.innerHTML = `
                        <span class="px-2 py-1 rounded-full ${getStatusColorClass(newStatus)}">
                            ${newStatus}
                        </span>
                    `;
                }

                // Update action button
                const actionCell = row.querySelector('td[data-column="action"]');
                if (actionCell) {
                    if (newStatus === 'Closed') {
                        actionCell.innerHTML = `
                            <button disabled class="px-3 py-1 text-sm rounded-full bg-gray-400 text-white">
                                Closed
                            </button>
                        `;
                    } else {
                        actionCell.innerHTML = `
                            <button onclick="showStatusOptions('${id}', '${newStatus}')"
                                class="px-3 py-1 text-sm rounded-full ${newStatus === 'Open' ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600'} text-white">
                                ${newStatus === 'Open' ? 'Tutup' : 'Buka'}
                            </button>
                        `;
                    }
                }

                // Tampilkan alert sukses
                Swal.fire({
                    icon: 'success',
                    title: 'Status Berhasil Diubah!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500,
                    toast: true,
                    position: 'top-end'
                });
            } else {
                throw new Error(data.message || 'Terjadi kesalahan saat mengubah status');
            }
        })
        .catch(error => {
            // Play error sound
            playSound('error');
            
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: error.message || 'Terjadi kesalahan saat mengubah status',
                toast: true,
                position: 'top-end',
                timer: 3000
            });
        });
    }

    // Helper function untuk mendapatkan class warna status
    function getStatusColorClass(status) {
        const colorClasses = {
            'Open': 'bg-red-100 text-red-600',
            'Closed': 'bg-green-100 text-green-600',
            'Comp': 'bg-blue-100 text-blue-600',
            'APPR': 'bg-yellow-100 text-yellow-600',
            'WAPPR': 'bg-purple-100 text-purple-600',
            'WMATL': 'bg-gray-100 text-gray-600'
        };
        return colorClasses[status] || 'bg-gray-100 text-gray-600';
    }

    // Event Listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi event listeners jika diperlukan
    });

    // Fungsi untuk membuka modal SR
    function openSRModal() {
        const modal = document.getElementById('srModal');
        const modalContent = modal.querySelector('.bg-white');
        modal.classList.remove('hidden');
        modal.classList.remove('scale-0');
        modal.classList.add('scale-100');
        setTimeout(() => {
            modalContent.classList.remove('scale-0');
            modalContent.classList.add('scale-100');
        }, 100);
    }

    // Fungsi untuk menutup modal SR 
    function closeSRModal() {
        const modal = document.getElementById('srModal');
        const modalContent = modal.querySelector('.bg-white');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-0');
        setTimeout(() => {
            modal.classList.remove('scale-100');
            modal.classList.add('scale-0');
            modal.classList.add('hidden');
        }, 300);
    }

    // Fungsi untuk membuka modal WO
    function openWOModal() {
        const modal = document.getElementById('woModal');
        const modalContent = modal.querySelector('.bg-white');
        modal.classList.remove('hidden');
        modal.classList.remove('scale-0');
        modal.classList.add('scale-100');
        setTimeout(() => {
            modalContent.classList.remove('scale-0');
            modalContent.classList.add('scale-100');
        }, 100);
    }

    // Fungsi untuk menutup modal WO
    function closeWOModal() {
        const modal = document.getElementById('woModal');
        const modalContent = modal.querySelector('.bg-white');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-0');
        setTimeout(() => {
            modal.classList.remove('scale-100');
            modal.classList.add('scale-0');
            modal.classList.add('hidden');
        }, 300);
    }

    // Fungsi untuk menampilkan alert sukses
    function showSuccessAlert(event, type) {
        event.preventDefault(); // Prevent form submission
        const form = event.target;
        const formData = new FormData(form);
        
        // Simulate form submission (you can replace this with actual AJAX call)
        setTimeout(() => {
            Swal.fire({
                icon: 'success',
                title: `${type} berhasil ditambahkan!`,
                showConfirmButton: false,
                timer: 1500
            });
            form.submit(); // Submit the form after showing the alert
        }, 500);
    }

    // Filter tanggal
    function updateFilter() {
        let baseUrl = window.location.pathname;
        let tanggalMulai = document.getElementById('tanggal_mulai').value;
        let tanggalAkhir = document.getElementById('tanggal_akhir').value;
        let searchValue = document.getElementById('searchInput').value;
        
        // Validasi tanggal
        if (!tanggalMulai || !tanggalAkhir) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Mohon isi kedua tanggal'
            });
            return;
        }

        if (tanggalMulai > tanggalAkhir) {
            
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir'
            });
            return;
        }
        
        // Redirect dengan parameter tanggal dan search jika ada
        let url = `${baseUrl}?tanggal_mulai=${tanggalMulai}&tanggal_akhir=${tanggalAkhir}`;
        if (searchValue) {
            url += `&search=${encodeURIComponent(searchValue)}`;
        }
        window.location.href = url;
    }

    // Set tanggal default dan search value dari URL parameters
    window.addEventListener('load', function() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Set tanggal default hanya jika bukan dari tombol "Tampilkan Semua"
        if (!urlParams.has('show_all') && !urlParams.has('tanggal_mulai') && !urlParams.has('tanggal_akhir')) {
            const today = new Date();
            const sevenDaysAgo = new Date(today);
            sevenDaysAgo.setDate(today.getDate() - 7);
            
            document.getElementById('tanggal_mulai').value = sevenDaysAgo.toISOString().split('T')[0];
            document.getElementById('tanggal_akhir').value = today.toISOString().split('T')[0];
        }
        
        // Set search value dari URL jika ada
        if (urlParams.has('search')) {
            document.getElementById('searchInput').value = urlParams.get('search');
            searchTables();
        }
    });

    // Fungsi search
    function searchTables() {
        const searchValue = document.getElementById('searchInput').value.toLowerCase();
        
        // Search di tabel SR
        searchTable('srTable', searchValue);
        
        // Search di tabel WO
        searchTable('woTable', searchValue);
    }

    function searchTable(tableId, searchValue) {
        const table = document.getElementById(tableId);
        const rows = table.getElementsByTagName('tr');
        let visibleCount = 0;

        // Loop melalui semua baris, mulai dari index 1 untuk melewati header
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;

            // Cek setiap sel dalam baris
            for (let j = 0; j < cells.length; j++) {
                const cellText = cells[j].textContent.toLowerCase();
                if (cellText.includes(searchValue)) {
                    found = true;
                    break;
                }
            }

            // Tampilkan/sembunyikan baris berdasarkan hasil pencarian
            row.style.display = found ? '' : 'none';
            if (found) visibleCount++;
        }

        // Update counter
        const counter = document.getElementById(`${tableId}VisibleCount`);
        if (counter) {
            counter.textContent = visibleCount;
        }
    }

    // Event listener untuk search input
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            searchTables();
        }
    });

    // Debounce function untuk search
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

    // Aplikasikan debounce pada search
    const debouncedSearch = debounce(() => searchTables(), 300);
    document.getElementById('searchInput').addEventListener('input', debouncedSearch);

    function updateStatus(type, id, currentStatus) {
        // Cek jika status sudah Closed
        if (currentStatus === 'Closed') {
            // Play error sound
            playSound('error');
            
            Swal.fire({
                icon: 'info',
                title: 'Tidak dapat diubah!',
                text: type.toUpperCase() + ' sudah ditutup dan tidak dapat diubah lagi.',
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false
            });
            return;
        }

        const newStatus = type === 'sr' ? (currentStatus === 'Open' ? 'Closed' : 'Open') : currentStatus;
        
        if (type === 'sr') {
            processSRStatusUpdate(id, newStatus);
        } else {
            processStatusUpdate(id, newStatus);
        }
    }

    function processSRStatusUpdate(id, newStatus) {
        const url = `{{ url('/admin/laporan/update-sr-status') }}/${id}`;
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Play success sound
                playSound('success');
                
                // Update tampilan status secara real-time
                const row = document.querySelector(`tr[data-sr-id="SR-${String(id).padStart(4, '0')}"]`);
                const statusCell = row.querySelector('td[data-column="status"]');
                if (statusCell) {
                    statusCell.innerHTML = `
                        <span class="px-2 py-1 rounded-full ${getStatusColorClass(newStatus)}">
                            ${newStatus}
                        </span>
                    `;
                }

                // Update action button
                const actionCell = row.querySelector('td[data-column="action"]');
                if (actionCell) {
                    if (newStatus === 'Closed') {
                        actionCell.innerHTML = `
                            <button disabled class="px-3 py-1 text-sm rounded-full bg-gray-400 text-white">
                                Closed
                            </button>
                        `;
                    } else {
                        actionCell.innerHTML = `
                            <button onclick="updateStatus('sr', ${id}, '${newStatus}')"
                                class="px-3 py-1 text-sm rounded-full ${newStatus === 'Open' ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600'} text-white">
                                ${newStatus === 'Open' ? 'Tutup' : 'Buka'}
                            </button>
                        `;
                    }
                }

                // Tampilkan alert sukses
                Swal.fire({
                    icon: 'success',
                    title: 'Status Berhasil Diubah!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500,
                    toast: true,
                    position: 'top-end'
                });
            } else {
                throw new Error(data.message || 'Terjadi kesalahan saat mengubah status');
            }
        })
        .catch(error => {
            // Play error sound
            playSound('error');
            
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: error.message || 'Terjadi kesalahan saat mengubah status',
                toast: true,
                position: 'top-end',
                timer: 3000
            });
        });
    }

    function showAllData() {
        // Redirect ke halaman yang sama tanpa parameter tanggal
        window.location.href = window.location.pathname;
    }

    // Fungsi pencarian untuk tabel SR
    function searchSRTable() {
        const searchValue = document.getElementById('searchSR').value.toLowerCase();
        const table = document.getElementById('srTable');
        const rows = table.getElementsByTagName('tr');
        let visibleCount = 0;

        // Simpan filter yang sedang aktif
        const status = document.getElementById('srStatusFilter').value;
        const unit = document.getElementById('srUnitFilter').value;
        const downtime = document.getElementById('srDowntimeFilter').value;

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;
            let matchesFilter = true;

            // Cek apakah baris memenuhi kriteria filter
            if (status) {
                const statusCell = cells[4].textContent.trim();
                if (!statusCell.includes(status)) matchesFilter = false;
            }
            if (unit) {
                const unitCell = cells[2].textContent.trim();
                if (!unitCell.includes(unit)) matchesFilter = false;
            }
            if (downtime) {
                const downtimeCell = cells[6].textContent.trim();
                if (downtime === 'Yes' && downtimeCell === '0') matchesFilter = false;
                if (downtime === 'No' && downtimeCell !== '0') matchesFilter = false;
            }

            // Cek apakah baris mengandung kata yang dicari
            for (let j = 0; j < cells.length; j++) {
                const cellText = cells[j].textContent.toLowerCase();
                if (cellText.includes(searchValue)) {
                    found = true;
                    break;
                }
            }

            // Tampilkan baris jika memenuhi kriteria pencarian dan filter
            if (found && matchesFilter) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        }

        // Update counter
        document.getElementById('srVisibleCount').textContent = visibleCount;
    }

    // Fungsi pencarian untuk tabel WO
    function searchWOTable() {
        const searchValue = document.getElementById('searchWO').value.toLowerCase();
        const table = document.getElementById('woTable');
        const rows = table.getElementsByTagName('tr');
        let visibleCount = 0;

        // Simpan filter yang sedang aktif
        const status = document.getElementById('woStatusFilter').value;
        const unit = document.getElementById('woUnitFilter').value;

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;
            let matchesFilter = true;

            // Cek apakah baris memenuhi kriteria filter
            if (status) {
                const statusCell = row.querySelector('td[data-column="status"]').textContent.trim();
                if (!statusCell.includes(status)) matchesFilter = false;
            }
            if (unit) {
                const unitCell = cells[2].textContent.trim();
                if (!unitCell.includes(unit)) matchesFilter = false;
            }

            // Cek apakah baris mengandung kata yang dicari
            for (let j = 0; j < cells.length; j++) {
                const cellText = cells[j].textContent.toLowerCase();
                if (cellText.includes(searchValue)) {
                    found = true;
                    break;
                }
            }

            // Tampilkan baris jika memenuhi kriteria pencarian dan filter
            if (found && matchesFilter) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        }

        // Update counter
        document.getElementById('woVisibleCount').textContent = visibleCount;
    }

    // Fungsi pencarian untuk tabel Backlog
    function searchBacklogTable() {
        const searchValue = document.getElementById('searchBacklog').value.toLowerCase();
        const table = document.getElementById('backlogTable');
        const rows = table.getElementsByTagName('tr');
        let visibleCount = 0;

        // Simpan filter yang sedang aktif
        const status = document.getElementById('backlogStatusFilter').value;
        const unit = document.getElementById('backlogUnitFilter').value;

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;
            let matchesFilter = true;

            // Cek apakah baris memenuhi kriteria filter
            if (status) {
                const statusCell = cells[5].textContent.trim();
                if (!statusCell.includes(status)) matchesFilter = false;
            }
            if (unit) {
                const unitCell = cells[2].textContent.trim();
                if (!unitCell.includes(unit)) matchesFilter = false;
            }

            // Cek apakah baris mengandung kata yang dicari
            for (let j = 0; j < cells.length; j++) {
                const cellText = cells[j].textContent.toLowerCase();
                if (cellText.includes(searchValue)) {
                    found = true;
                    break;
                }
            }

            // Tampilkan baris jika memenuhi kriteria pencarian dan filter
            if (found && matchesFilter) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        }

        // Update counter
        document.getElementById('backlogVisibleCount').textContent = visibleCount;
    }

    // Event listeners untuk pencarian real-time
    document.addEventListener('DOMContentLoaded', function() {
        const searchInputs = {
            'searchSR': searchSRTable,
            'searchWO': searchWOTable,  
            'searchBacklog': searchBacklogTable
        };

        Object.entries(searchInputs).forEach(([inputId, searchFunction]) => {
            const input = document.getElementById(inputId);
            if (input) {
                const debouncedSearch = debounce(searchFunction, 300);
                
                // Event listener untuk input
                input.addEventListener('input', debouncedSearch);
                
                // Event listener untuk tombol Enter
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        searchFunction();
                    }
                });

                // Event listener untuk tombol search
                const searchButton = input.nextElementSibling;
                if (searchButton) {
                    searchButton.addEventListener('click', searchFunction);
                }
            }
        });
    });

    // Tambahkan debounce untuk pencarian real-time
    const debouncedSRSearch = debounce(() => searchSRTable(), 300);
    const debouncedWOSearch = debounce(() => searchWOTable(), 300);
    const debouncedBacklogSearch = debounce(() => searchBacklogTable(), 300);

    document.getElementById('searchSR').addEventListener('input', debouncedSRSearch);
    document.getElementById('searchWO').addEventListener('input', debouncedWOSearch);
    document.getElementById('searchBacklog').addEventListener('input', debouncedBacklogSearch);

    // Fungsi untuk memperbarui jumlah data yang ditampilkan
    function updateTableCounts(tableId, visibleCountId, totalCountId) {
        const table = document.getElementById(tableId);
        const rows = table.getElementsByTagName('tr');
        let visibleCount = 0;
        let totalCount = 0;

        // Hitung jumlah baris yang terlihat dan total (skip header)
        for (let i = 1; i < rows.length; i++) {
            totalCount++;
            if (rows[i].style.display !== 'none') {
                visibleCount++;
            }
        }

        // Update tampilan jumlah
        document.getElementById(visibleCountId).textContent = visibleCount;
        document.getElementById(totalCountId).textContent = totalCount;
    }

    // Perbaikan fungsi pencarian
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi event listeners untuk search
        const searchInputs = {
            'searchSR': { tableId: 'srTable', counterId: 'srVisibleCount' },
            'searchWO': { tableId: 'woTable', counterId: 'woVisibleCount' },
            'searchBacklog': { tableId: 'backlogTable', counterId: 'backlogVisibleCount' }
        };

        Object.entries(searchInputs).forEach(([inputId, config]) => {
            const searchInput = document.getElementById(inputId);
            if (searchInput) {
                // Fungsi pencarian untuk setiap tabel
                const searchTable = () => {
                    const searchValue = searchInput.value.toLowerCase();
                    const table = document.getElementById(config.tableId);
                    const rows = table.getElementsByTagName('tr');
                    let visibleCount = 0;

                    // Mulai dari index 1 untuk melewati header
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

                        row.style.display = found ? '' : 'none';
                        if (found) visibleCount++;
                    }

                    // Update counter
                    const counter = document.getElementById(config.counterId);
                    if (counter) {
                        counter.textContent = visibleCount;
                    }
                };

                // Tambahkan event listeners
                const debouncedSearch = debounce(searchTable, 300);
                searchInput.addEventListener('input', debouncedSearch);
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        searchTable();
                    }
                });

                // Tambahkan event listener untuk tombol search jika ada
                const searchButton = searchInput.nextElementSibling;
                if (searchButton && searchButton.tagName === 'BUTTON') {
                    searchButton.addEventListener('click', searchTable);
                }
            }
        });
    });

    // Fungsi untuk reset pencarian
    function resetSearch(inputId, tableId) {
        const searchInput = document.getElementById(inputId);
        if (searchInput) {
            searchInput.value = '';
            const table = document.getElementById(tableId);
            const rows = table.getElementsByTagName('tr');
            for (let i = 1; i < rows.length; i++) {
                rows[i].style.display = '';
            }
            // Reset counter
            const counterId = inputId === 'searchSR' ? 'srVisibleCount' :
                             inputId === 'searchWO' ? 'woVisibleCount' : 'backlogVisibleCount';
            const counter = document.getElementById(counterId);
            if (counter) {
                counter.textContent = rows.length - 1; // -1 untuk header
            }
        }
    }

    // Fungsi untuk memperbarui jumlah data yang ditampilkan
    function updateTableCounts() {
        const tables = [
            { tableId: 'srTable', visibleId: 'srVisibleCount', totalId: 'srTotalCount' },
            { tableId: 'woTable', visibleId: 'woVisibleCount', totalId: 'woTotalCount' },
            { tableId: 'backlogTable', visibleId: 'backlogVisibleCount', totalId: 'backlogTotalCount' }
        ];

        tables.forEach(({ tableId, visibleId, totalId }) => {
            const table = document.getElementById(tableId);
            if (table) {
                const rows = table.getElementsByTagName('tr');
                const totalCount = rows.length - 1; // -1 untuk header
                let visibleCount = 0;

                for (let i = 1; i < rows.length; i++) {
                    if (rows[i].style.display !== 'none') {
                        visibleCount++;
                    }
                }

                const visibleElement = document.getElementById(visibleId);
                const totalElement = document.getElementById(totalId);
                
                if (visibleElement) visibleElement.textContent = visibleCount;
                if (totalElement) totalElement.textContent = totalCount;
            }
        });
    }

    // Panggil updateTableCounts saat halaman dimuat
    document.addEventListener('DOMContentLoaded', updateTableCounts);

    function filterSRTable() {
        const status = document.getElementById('srStatusFilter').value;
        const unit = document.getElementById('srUnitFilter').value;
        const downtime = document.getElementById('srDowntimeFilter').value;
        const rows = document.querySelectorAll('#srTable tbody tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const statusCell = row.querySelector('td:nth-child(5)');
            const unitCell = row.querySelector('td:nth-child(3)');
            const downtimeCell = row.querySelector('td:nth-child(7)');
            
            const statusMatch = !status || statusCell.textContent.trim().includes(status);
            const unitMatch = !unit || unitCell.textContent.trim().includes(unit);
            const downtimeMatch = !downtime || 
                (downtime === 'Yes' && downtimeCell.textContent.trim() !== '0') ||
                (downtime === 'No' && downtimeCell.textContent.trim() === '0');
            
            if (statusMatch && unitMatch && downtimeMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        document.getElementById('srVisibleCount').textContent = visibleCount;
    }

    function filterWOTable() {
        const status = document.getElementById('woStatusFilter').value;
        const unit = document.getElementById('woUnitFilter').value;
        const rows = document.querySelectorAll('#woTable tbody tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const statusCell = row.querySelector('td[data-column="status"]');
            const unitCell = row.querySelector('td:nth-child(3)');
            
            const statusMatch = !status || statusCell.textContent.trim().includes(status);
            const unitMatch = !unit || unitCell.textContent.trim().includes(unit);
            
            if (statusMatch && unitMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        document.getElementById('woVisibleCount').textContent = visibleCount;
    }

    function filterBacklogTable() {
        const status = document.getElementById('backlogStatusFilter').value;
        const unit = document.getElementById('backlogUnitFilter').value;
        const rows = document.querySelectorAll('#backlogTable tbody tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const statusCell = row.querySelector('td:nth-child(6)');
            const unitCell = row.querySelector('td:nth-child(3)');
            
            const statusMatch = !status || statusCell.textContent.trim().includes(status);
            const unitMatch = !unit || unitCell.textContent.trim().includes(unit);
            
            if (statusMatch && unitMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        document.getElementById('backlogVisibleCount').textContent = visibleCount;
    }

    // Tambahkan fungsi untuk memutar audio
    function playSound(type) {
        const audio = new Audio(`{{ asset('audio/${type}.MP3') }}`);
        audio.play();
    }

    // Tab switching functionality
    function switchTab(tabId) {
        // Hapus kelas active dari semua tab
        document.querySelectorAll('.tab-btn').forEach(tab => {
            tab.classList.remove('active', 'border-blue-500');
        });
        
        // Tambah kelas active ke tab yang diklik
        const selectedTab = document.querySelector(`.tab-btn[data-tab="${tabId}"]`);
        if (selectedTab) {
            selectedTab.classList.add('active', 'border-blue-500');
        }
        
        // Sembunyikan semua konten tab
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Tampilkan konten tab yang dipilih
        const selectedContent = document.getElementById(`${tabId}-tab`);
        if (selectedContent) {
            selectedContent.classList.remove('hidden');
            
            // Update table counts dan jalankan filter yang sesuai
            updateTableCounts();
            if (tabId === 'sr') {
                filterSRTable();
            } else if (tabId === 'wo') {
                filterWOTable();
            } else if (tabId === 'backlog') {
                filterBacklogTable();
            }
        }
    }

    // Tambahkan event listener saat dokumen dimuat
    document.addEventListener('DOMContentLoaded', function() {
        // Set tab pertama sebagai active
        const firstTab = document.querySelector('.tab-btn');
        if (firstTab) {
            const tabId = firstTab.getAttribute('data-tab');
            switchTab(tabId);
        }
    });

    function checkExpiredWorkOrders() {
        const rows = document.querySelectorAll('#woTable tbody tr');
        const now = new Date();
        
        rows.forEach(row => {
            const statusCell = row.querySelector('td[data-column="status"]');
            const scheduleFinishCell = row.querySelector('td:nth-child(10)'); // sesuaikan dengan index kolom schedule_finish
            
            if (statusCell && scheduleFinishCell && !statusCell.textContent.includes('Closed')) {
                const finishDate = new Date(scheduleFinishCell.textContent);
                
                if (finishDate < now) {
                    // Kirim request ke backend untuk memindahkan WO ke backlog
                    const woId = row.getAttribute('data-wo-id').replace('WO-', '');
                    moveToBacklog(woId);
                }
            }
        });
    }

    async function moveToBacklog(woId) {
        try {
            const response = await fetch(`/admin/laporan/move-to-backlog/${woId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            if (response.ok) {
                location.reload(); // Refresh halaman setelah pemindahan berhasil
            }
        } catch (error) {
            console.error('Error moving WO to backlog:', error);
        }
    }

    // Jalankan pengecekan setiap 1 menit
    setInterval(checkExpiredWorkOrders, 60000);
    // Jalankan pengecekan saat halaman dimuat
    document.addEventListener('DOMContentLoaded', checkExpiredWorkOrders);

    function verifyAndOpenDocument(url, fileName) {
        console.log('Verifying document access:', url);
        
        fetch(url, { 
            method: 'HEAD',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (response.ok) {
                console.log('Document found, opening...');
                window.open(url, '_blank');
            } else {
                console.error('Document not found:', response.status);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Dokumen tidak ditemukan atau tidak dapat diakses'
                });
            }
        })
        .catch(error => {
            console.error('Error accessing document:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat mengakses dokumen'
            });
        });
    }
</script>

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
    