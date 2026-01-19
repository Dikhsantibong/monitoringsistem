@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.sidebar')

    <div id="main-content" class="flex-1 overflow-auto">
        <header class="bg-white shadow-sm sticky top-0">
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
                    <h1 class="text-xl font-semibold text-gray-800">Maximo Akses</h1>
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

        <main class="px-6 mt-4">
            {{-- Success Message dengan Link ke PDF.js Viewer --}}
            @if(session('success') && session('jobcard_url'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <div class="mt-2 flex gap-2">
                        <button onclick="openPdfEditor('{{ session('jobcard_url') }}', '{{ session('jobcard_path') }}')" 
                                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm font-semibold">
                            Buka & Edit Jobcard di PDF.js Viewer
                        </button>
                        <form method="GET" action="{{ route('admin.maximo.jobcard.download') }}" class="inline">
                            <input type="hidden" name="path" value="{{ session('jobcard_path') }}">
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-semibold">
                                Download Jobcard
                            </button>
                        </form>
                    </div>
                </div>
            @elseif(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Error Message --}}
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow p-6">

                {{-- ERROR DEBUG --}}
                @if(!empty($errorDetail))
                <div class="mb-4 bg-gray-100 border border-gray-300 p-4 rounded text-sm">
                    <p class="font-semibold mb-2">Detail Error (Debug)</p>
                    <pre class="text-xs break-all">{{ json_encode($errorDetail, JSON_PRETTY_PRINT) }}</pre>
                </div>
                @endif

                {{-- SEARCH --}}
                <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="w-full md:w-1/3">
                        <form method="GET" action="{{ route('admin.maximo.index') }}">
                            <input type="hidden" name="wo_page" value="{{ request('wo_page', 1) }}">
                            <input type="hidden" name="sr_page" value="{{ request('sr_page', 1) }}">
                            <input type="hidden" name="wo_status" value="{{ request('wo_status') }}">
                            <input type="hidden" name="wo_worktype" value="{{ request('wo_worktype') }}">
                            <div class="flex">
                                <input
                                    type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Cari WO, Ticket, Description, Asset, Location..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                >
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-r-lg hover:bg-blue-700 transition-colors"
                                >
                                    Cari
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- TABS --}}
                <div x-data="{ tab: 'wo' }">
                    <div class="border-b mb-4 flex gap-4">
                        <button
                            @click="tab='wo'"
                            :class="tab==='wo' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
                            class="pb-2 font-semibold">
                            Work Order
                        </button>

                        <button
                            @click="tab='sr'"
                            :class="tab==='sr' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
                            class="pb-2 font-semibold">
                            Service Request
                        </button>
                    </div>

                    {{-- ================= WORK ORDER TAB ================= --}}
                    <div x-show="tab==='wo'">
                        <h2 class="text-lg font-semibold mb-3">Data Work Order</h2>

                        <form method="GET" action="{{ route('admin.maximo.index') }}" id="woFilterForm">
                            <input type="hidden" name="wo_page" value="1">
                            <input type="hidden" name="sr_page" value="{{ request('sr_page', 1) }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full table-fixed divide-y divide-gray-200 border border-gray-200 whitespace-nowrap text-sm">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-2 text-center">No</th>
                                            <th class="px-4 py-2 text-center">Aksi</th>
                                            <th class="px-4 py-2 text-center">WO</th>
                                            <th class="px-4 py-2 text-center">Parent</th>
                                            <th class="px-4 py-2 text-center">Description</th>
                                            <th class="px-4 py-2 text-center">Asset</th>
                                            <th class="px-4 py-2 text-center">
                                                <div class="flex items-center justify-between">
                                                    <span>Status</span>
                                                    <div class="relative ml-2">
                                                        <select name="wo_status" onchange="document.getElementById('woFilterForm').submit()" 
                                                                class="border border-gray-300 rounded px-2 py-1 text-xs bg-white text-gray-800 focus:outline-none">
                                                            <option value="" {{ !request('wo_status') ? 'selected' : '' }}>Semua</option>
                                                            <option value="WAPPR" {{ request('wo_status') == 'WAPPR' ? 'selected' : '' }}>WAPPR</option>
                                                            <option value="APPR" {{ request('wo_status') == 'APPR' ? 'selected' : '' }}>APPR</option>
                                                            <option value="INPRG" {{ request('wo_status') == 'INPRG' ? 'selected' : '' }}>INPRG</option>
                                                            <option value="COMP" {{ request('wo_status') == 'COMP' ? 'selected' : '' }}>COMP</option>
                                                            <option value="CLOSE" {{ request('wo_status') == 'CLOSE' ? 'selected' : '' }}>CLOSE</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </th>
                                            <th class="px-4 py-2 text-center">Report Date</th>
                                            <th class="px-4 py-2 text-center">Priority</th>
                                            <th class="px-4 py-2 text-center">
                                                <div class="flex items-center justify-between">
                                                    <span>Work Type</span>
                                                    <div class="relative ml-2">
                                                        <select name="wo_worktype" onchange="document.getElementById('woFilterForm').submit()" 
                                                                class="border border-gray-300 rounded px-2 py-1 text-xs bg-white text-gray-800 focus:outline-none">
                                                            <option value="" {{ !request('wo_worktype') ? 'selected' : '' }}>Semua</option>
                                                            <option value="CH" {{ request('wo_worktype') == 'CH' ? 'selected' : '' }}>CH</option>
                                                            <option value="CM" {{ request('wo_worktype') == 'CM' ? 'selected' : '' }}>CM</option>
                                                            <option value="CP" {{ request('wo_worktype') == 'CP' ? 'selected' : '' }}>CP</option>
                                                            <option value="OH" {{ request('wo_worktype') == 'OH' ? 'selected' : '' }}>OH</option>
                                                            <option value="OP" {{ request('wo_worktype') == 'OP' ? 'selected' : '' }}>OP</option>
                                                            <option value="PAM" {{ request('wo_worktype') == 'PAM' ? 'selected' : '' }}>PAM</option>
                                                            <option value="PDM" {{ request('wo_worktype') == 'PDM' ? 'selected' : '' }}>PDM</option>
                                                            <option value="PM" {{ request('wo_worktype') == 'PM' ? 'selected' : '' }}>PM</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </th>
                                            <th class="px-4 py-2 text-center">Sched Start</th>
                                            <th class="px-4 py-2 text-center">Sched Finish</th>
                                        </tr>
                                    </thead>
                                <tbody>
                                @forelse($workOrders as $i => $wo)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-center border border-gray-200">
                                            @if($workOrdersPaginator)
                                                {{ ($workOrdersPaginator->currentPage() - 1) * $workOrdersPaginator->perPage() + $loop->iteration }}
                                            @else
                                                {{ $i+1 }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-center border border-gray-200 whitespace-nowrap">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('admin.maximo.workorder.show', ['wonum' => $wo['wonum']]) }}"
                                                   class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs">
                                                    Detail
                                                </a>
                                                @if(strtoupper($wo['status']) === 'APPR')
                                                    <form method="POST" action="{{ route('admin.maximo.jobcard.generate') }}" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="wonum" value="{{ $wo['wonum'] }}">
                                                        <button type="submit" 
                                                                class="inline-flex items-center px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs"
                                                                onclick="return confirm('Generate jobcard untuk WO {{ $wo['wonum'] }}?')">
                                                            Generate Jobcard
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 text-center border border-gray-200">{{ $wo['wonum'] }}</td>
                                        <td class="px-4 py-2 text-center border border-gray-200">{{ $wo['parent'] }}</td>
                                        <td class="px-4 py-2 border border-gray-200">
                                            <span class="inline-block w-96 break-words whitespace-normal maximo-description">
                                                {{ $wo['description'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-center border border-gray-200">{{ $wo['assetnum'] }}</td>
                                        <td class="px-4 py-2 text-center border border-gray-200">
                                            @php
                                                $woStatus = strtoupper($wo['status']);
                                            @endphp
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full text-center
                                                @if(in_array($woStatus, ['COMP', 'CLOSE', 'RESOLVED'])) bg-green-100 text-green-800
                                                @elseif(in_array($woStatus, ['WAPPR', 'APPR'])) bg-blue-100 text-blue-800
                                                @elseif(in_array($woStatus, ['INPRG', 'IN PROGRESS'])) bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $wo['status'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-center border border-gray-200">
                                            @if(isset($wo['reportdate']) && $wo['reportdate'] !== '-')
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-50 text-green-800 rounded-md">
                                                    {{ $wo['reportdate'] }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-center border border-gray-200">{{ $wo['wopriority'] }}</td>
                                        <td class="px-4 py-2 text-center border border-gray-200">{{ $wo['worktype'] }}</td>
                                        <td class="px-4 py-2 text-center border border-gray-200">{{ $wo['schedstart'] }}</td>
                                        <td class="px-4 py-2 text-center border border-gray-200">{{ $wo['schedfinish'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4 text-gray-500">
                                            Tidak ada data Work Order
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        </form>

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
                                    <a href="{{ $workOrdersPaginator->appends([
                                        'sr_page' => request('sr_page', 1), 
                                        'search' => request('search'),
                                        'wo_status' => request('wo_status'),
                                        'wo_worktype' => request('wo_worktype')
                                    ])->previousPageUrl() }}" 
                                       class="px-3 py-1 bg-[#0A749B] text-white rounded">Sebelumnya</a>
                                @endif

                                @foreach ($workOrdersPaginator->getUrlRange(1, min($workOrdersPaginator->lastPage(), 10)) as $page => $url)
                                    @if ($page == $workOrdersPaginator->currentPage())
                                        <span class="px-3 py-1 bg-[#0A749B] text-white rounded">{{ $page }}</span>
                                    @else
                                        <a href="{{ $workOrdersPaginator->appends([
                                            'sr_page' => request('sr_page', 1), 
                                            'search' => request('search'),
                                            'wo_status' => request('wo_status'),
                                            'wo_worktype' => request('wo_worktype')
                                        ])->url($page) }}" 
                                           class="px-3 py-1 rounded bg-white text-[#0A749B] border border-[#0A749B]">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach

                                @if ($workOrdersPaginator->hasMorePages())
                                    <a href="{{ $workOrdersPaginator->appends([
                                        'sr_page' => request('sr_page', 1), 
                                        'search' => request('search'),
                                        'wo_status' => request('wo_status'),
                                        'wo_worktype' => request('wo_worktype')
                                    ])->nextPageUrl() }}" 
                                       class="px-3 py-1 bg-[#0A749B] text-white rounded">Selanjutnya</a>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- ================= SERVICE REQUEST TAB ================= --}}
                    <div x-show="tab==='sr'">
                        <h2 class="text-lg font-semibold mb-3">Data Service Request</h2>

                        <div class="overflow-x-auto">
                            <table class="min-w-full table-fixed divide-y divide-gray-200 border border-gray-200 whitespace-nowrap text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-center">No</th>
                                        <th class="px-4 py-2 text-center">Aksi</th>
                                        <th class="px-4 py-2 text-center">Ticket</th>
                                        <th class="px-4 py-2 text-center">Status</th>
                                        <th class="px-4 py-2 text-center">Status Date</th>
                                        <th class="px-4 py-2 text-center">Description</th>
                                        <th class="px-4 py-2 text-center">Asset</th>
                                        <th class="px-4 py-2 text-center">Location</th>
                                        <th class="px-4 py-2 text-center">Reported By</th>
                                        <th class="px-4 py-2 text-center">Report Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($serviceRequests as $i => $sr)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-center border border-gray-200">
                                            @if($serviceRequestsPaginator)
                                                {{ ($serviceRequestsPaginator->currentPage() - 1) * $serviceRequestsPaginator->perPage() + $loop->iteration }}
                                            @else
                                                {{ $i+1 }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-center border border-gray-200 whitespace-nowrap">
                                            <a href="{{ route('admin.maximo.service-request.show', ['ticketid' => $sr['ticketid']]) }}"
                                               class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs">
                                                Detail
                                            </a>
                                        </td>
                                        <td class="px-4 py-2 text-center border border-gray-200">{{ $sr['ticketid'] }}</td>
                                        <td class="px-4 py-2 text-center border border-gray-200">
                                            @php
                                                $srStatus = strtoupper($sr['status']);
                                            @endphp
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                @if(in_array($srStatus, ['COMP', 'CLOSE', 'RESOLVED'])) bg-green-100 text-green-800
                                                @elseif(in_array($srStatus, ['WAPPR', 'APPR'])) bg-blue-100 text-blue-800
                                                @elseif(in_array($srStatus, ['INPRG', 'IN PROGRESS'])) bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $sr['status'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-center border border-gray-200">
                                            @if(isset($sr['statusdate']) && $sr['statusdate'] !== '-')
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-50 text-blue-800 rounded-md">
                                                    {{ $sr['statusdate'] }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 border border-gray-200">
                                            <span class="inline-block w-96 break-words whitespace-normal maximo-description">
                                                {{ $sr['description'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-center border border-gray-200">{{ $sr['assetnum'] }}</td>
                                        <td class="px-4 py-2 text-center border border-gray-200">{{ $sr['location'] }}</td>
                                        <td class="px-4 py-2 text-center border border-gray-200">{{ $sr['reportedby'] }}</td>
                                        <td class="px-4 py-2 text-center border border-gray-200">
                                            @if(isset($sr['reportdate']) && $sr['reportdate'] !== '-')
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-50 text-green-800 rounded-md">
                                                    {{ $sr['reportdate'] }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4 text-gray-500">
                                            Tidak ada data Service Request
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination Service Request --}}
                        @if($serviceRequestsPaginator && $serviceRequestsPaginator->hasPages())
                        <div class="mt-4 flex justify-between items-center">
                            <div class="text-sm text-gray-700">
                                Menampilkan 
                                {{ ($serviceRequestsPaginator->currentPage() - 1) * $serviceRequestsPaginator->perPage() + 1 }} 
                                hingga 
                                {{ min($serviceRequestsPaginator->currentPage() * $serviceRequestsPaginator->perPage(), $serviceRequestsPaginator->total()) }} 
                                dari 
                                {{ $serviceRequestsPaginator->total() }} 
                                entri
                            </div>
                            <div class="flex items-center gap-1">
                                @if (!$serviceRequestsPaginator->onFirstPage())
                                    <a href="{{ $serviceRequestsPaginator->appends(['wo_page' => request('wo_page', 1), 'search' => request('search')])->previousPageUrl() }}" 
                                       class="px-3 py-1 bg-[#0A749B] text-white rounded">Sebelumnya</a>
                                @endif

                                @foreach ($serviceRequestsPaginator->getUrlRange(1, min($serviceRequestsPaginator->lastPage(), 10)) as $page => $url)
                                    @if ($page == $serviceRequestsPaginator->currentPage())
                                        <span class="px-3 py-1 bg-[#0A749B] text-white rounded">{{ $page }}</span>
                                    @else
                                        <a href="{{ $serviceRequestsPaginator->appends(['wo_page' => request('wo_page', 1), 'search' => request('search')])->url($page) }}" 
                                           class="px-3 py-1 rounded bg-white text-[#0A749B] border border-[#0A749B]">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach

                                @if ($serviceRequestsPaginator->hasMorePages())
                                    <a href="{{ $serviceRequestsPaginator->appends(['wo_page' => request('wo_page', 1), 'search' => request('search')])->nextPageUrl() }}" 
                                       class="px-3 py-1 bg-[#0A749B] text-white rounded">Selanjutnya</a>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>

                </div>
            </div>
        </main>
    </div>
</div>

<style>
    .maximo-description {
        white-space: normal;
        word-break: break-word;
    }
    .active-tool {
        opacity: 0.8;
        transform: scale(0.95);
    }
    #pdfViewerContainer {
        scroll-behavior: smooth;
    }
    #pdfPages canvas {
        display: block;
    }
</style>

<!-- Modal PDF Editor Custom -->
<div id="pdfEditorModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-[95vw] h-[95vh] flex flex-col">
        <!-- Header dengan Tools -->
        <div class="flex justify-between items-center p-3 border-b bg-gray-50">
            <div class="flex items-center gap-3">
                <span class="font-bold text-lg">Edit Jobcard PDF</span>
                <div class="flex items-center gap-2 border-l pl-3 ml-3">
                    <button id="toolPen" class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 active-tool" data-tool="pen">
                        ✏️ Menulis
                    </button>
                    <button id="toolEraser" class="px-3 py-1 bg-gray-600 text-white rounded text-sm hover:bg-gray-700" data-tool="eraser">
                        🧹 Hapus
                    </button>
                    <button id="toolSignature" class="px-3 py-1 bg-purple-600 text-white rounded text-sm hover:bg-purple-700" data-tool="signature">
                        ✍️ Tanda Tangan
                    </button>
                    <button id="toolClear" class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700" onclick="clearAllDrawings()">
                        🗑️ Hapus Semua
                    </button>
                </div>
            </div>
            <button onclick="closePdfEditor()" class="text-gray-500 hover:text-red-600 text-2xl font-bold">&times;</button>
        </div>
        
        <!-- PDF Viewer Container dengan iframe -->
        <div id="pdfViewerContainer" class="flex-1 overflow-auto bg-gray-200 relative" style="max-height: calc(95vh - 120px);">
            <div id="pdfWrapper" style="position:relative;width:100%;">
                <iframe id="pdfIframe" src="" style="width:100%;min-height:100%;border:none;pointer-events:auto;"></iframe>
                <canvas id="drawingCanvas" style="position:absolute;top:0;left:0;width:100%;height:100%;cursor:default;z-index:10;pointer-events:none;background:transparent;"></canvas>
            </div>
        </div>
        
        <!-- Footer dengan Actions -->
        <div class="flex justify-between items-center p-3 border-t bg-gray-50">
            <div class="text-sm text-gray-600">
                <span id="pageInfo">PDF Editor</span>
            </div>
            <div class="flex gap-2">
                <form method="GET" action="{{ route('admin.maximo.jobcard.download') }}" id="downloadForm" class="inline">
                    <input type="hidden" name="path" id="downloadPath" value="">
                    <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm">
                        Download
                    </button>
                </form>
                <button id="savePdfBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Signature -->
<div id="signatureModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col items-center">
        <span class="font-bold mb-3 text-lg">Gambar Tanda Tangan</span>
        <canvas id="signature-canvas" width="600" height="200" class="border-2 border-gray-300 mb-3 cursor-crosshair"></canvas>
        <div class="flex gap-2">
            <button onclick="clearSignature()" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Bersihkan</button>
            <button onclick="saveSignature()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Gunakan</button>
            <button onclick="closeSignatureModal()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Batal</button>
        </div>
    </div>
</div>

<script>
// Deklarasi variabel global
let pdfSaved = false;
let currentPdfPath = '';
let currentPdfUrl = '';
let currentTool = 'pen';
let isDrawing = false;
let drawingCanvas = null;
let drawingCtx = null;
let signatureImage = null;
let canvasOffset = { x: 0, y: 0 };
let canvasScale = { x: 1, y: 1 };

// Deklarasi fungsi global di awal (sebelum DOMContentLoaded)
// Pastikan fungsi tersedia untuk dipanggil dari inline onclick
window.openPdfEditor = function(pdfUrl, pdfPath) {
    pdfSaved = false;
    currentPdfPath = pdfPath;
    currentPdfUrl = pdfUrl;

    const modal = document.getElementById('pdfEditorModal');
    const iframe = document.getElementById('pdfIframe');

    if (!modal || !iframe) {
        console.error('[Jobcard] Modal atau iframe tidak ditemukan');
        return;
    }

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    document.getElementById('downloadPath').value = pdfPath;
    
    // Load PDF in iframe (browser native viewer)
    iframe.src = pdfUrl;
    
    // Clear previous drawings
    if (drawingCtx && drawingCanvas) {
        drawingCtx.clearRect(0, 0, drawingCanvas.width, drawingCanvas.height);
    }
    
    // Resize canvas saat PDF editor dibuka
    const container = document.getElementById('pdfViewerContainer');
    const pdfWrapper = document.getElementById('pdfWrapper');
    
    if (container && pdfWrapper && iframe && drawingCanvas) {
        // Tunggu iframe load dulu
        setTimeout(() => {
            const wrapperRect = pdfWrapper.getBoundingClientRect();
            drawingCanvas.width = wrapperRect.width;
            drawingCanvas.height = Math.max(wrapperRect.height, container.clientHeight);
            if (typeof updateDrawingCanvasTool === 'function') {
                updateDrawingCanvasTool();
            }
            if (typeof updateCanvasCursor === 'function') {
                updateCanvasCursor();
            }
        }, 200);
    }
    
    if (typeof updateCanvasCursor === 'function') {
        updateCanvasCursor();
    }
};

window.clearAllDrawings = function() {
    if (!confirm('Hapus semua gambar/tulisan yang sudah dibuat?')) return;
    
    if (drawingCtx && drawingCanvas) {
        drawingCtx.clearRect(0, 0, drawingCanvas.width, drawingCanvas.height);
    }
};

window.closePdfEditor = function(force = false) {
    if (!pdfSaved && !force) {
        if (!confirm('Anda belum menyimpan perubahan PDF ke server. Yakin ingin keluar tanpa menyimpan?')) {
            return;
        }
    }
    const modal = document.getElementById('pdfEditorModal');
    if (modal) {
        modal.classList.add('hidden');
    }
    document.body.style.overflow = '';
    
    // Clear iframe
    const iframe = document.getElementById('pdfIframe');
    if (iframe) {
        iframe.src = '';
    }
    
    // Clear drawings
    if (drawingCtx && drawingCanvas) {
        drawingCtx.clearRect(0, 0, drawingCanvas.width, drawingCanvas.height);
    }
};

window.openSignatureModal = function() {
    const modal = document.getElementById('signatureModal');
    if (!modal) return;
    
    modal.classList.remove('hidden');
    const canvas = document.getElementById('signature-canvas');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    ctx.strokeStyle = '#000000';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    
    let isDrawing = false;
    let lastX = 0;
    let lastY = 0;
    
    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        return {
            x: e.clientX - rect.left,
            y: e.clientY - rect.top
        };
    }
    
    canvas.onmousedown = (e) => {
        isDrawing = true;
        const pos = getPos(e);
        lastX = pos.x;
        lastY = pos.y;
    };
    
    canvas.onmousemove = (e) => {
        if (!isDrawing) return;
        const pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
        lastX = pos.x;
        lastY = pos.y;
    };
    
    canvas.onmouseup = () => { isDrawing = false; };
    canvas.onmouseleave = () => { isDrawing = false; };
};

window.clearSignature = function() {
    const canvas = document.getElementById('signature-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
};

window.saveSignature = function() {
    const canvas = document.getElementById('signature-canvas');
    if (!canvas) return;
    
    signatureImage = new Image();
    signatureImage.src = canvas.toDataURL();
    window.closeSignatureModal();
    currentTool = 'signature';
    
    const toolSignature = document.getElementById('toolSignature');
    const toolPen = document.getElementById('toolPen');
    const toolEraser = document.getElementById('toolEraser');
    
    if (toolSignature) toolSignature.classList.add('active-tool');
    if (toolPen) toolPen.classList.remove('active-tool');
    if (toolEraser) toolEraser.classList.remove('active-tool');
};

window.closeSignatureModal = function() {
    const modal = document.getElementById('signatureModal');
    if (modal) {
        modal.classList.add('hidden');
    }
};

// Tool selection
document.addEventListener('DOMContentLoaded', function() {
    const tools = ['toolPen', 'toolEraser', 'toolSignature'];
    tools.forEach(toolId => {
        const btn = document.getElementById(toolId);
        if (btn) {
            btn.addEventListener('click', function() {
                // Remove active class from all tools
                tools.forEach(t => {
                    const b = document.getElementById(t);
                    if (b) b.classList.remove('active-tool');
                });
                // Add active class to clicked tool
                this.classList.add('active-tool');
                currentTool = this.dataset.tool;
                updateDrawingCanvasTool();
                updateCanvasCursor();
            });
        }
    });
    
    // Setup drawing canvas
    drawingCanvas = document.getElementById('drawingCanvas');
    if (drawingCanvas) {
        drawingCtx = drawingCanvas.getContext('2d');
        setupDrawingCanvas();
    }
});

function updateDrawingCanvasTool() {
    if (!drawingCtx) return;
    
    if (currentTool === 'eraser') {
        // Eraser: gunakan destination-out untuk menghapus hanya drawing, bukan PDF di bawahnya
        drawingCtx.globalCompositeOperation = 'destination-out';
        drawingCtx.strokeStyle = 'rgba(0,0,0,1)'; // Warna tidak penting untuk destination-out
        drawingCtx.lineWidth = 20;
    } else {
        // Pen dan Signature: gunakan source-over untuk menambahkan drawing di atas PDF
        drawingCtx.globalCompositeOperation = 'source-over';
        drawingCtx.strokeStyle = '#000000';
        // Ketebalan seperti pulpen (1.5px)
        drawingCtx.lineWidth = 1.5;
    }
    
    drawingCtx.lineCap = 'round';
    drawingCtx.lineJoin = 'round';
}

function updateCanvasCursor() {
    if (!drawingCanvas) return;
    
    // Ubah cursor berdasarkan tool yang aktif
    // Pointer-events akan diatur dinamis saat mouse down/up
    if (currentTool === 'pen') {
        drawingCanvas.style.cursor = 'crosshair';
    } else if (currentTool === 'eraser') {
        drawingCanvas.style.cursor = 'grab';
    } else if (currentTool === 'signature') {
        drawingCanvas.style.cursor = 'crosshair';
    } else {
        // Default: biarkan scroll PDF (nonaktifkan canvas)
        drawingCanvas.style.cursor = 'default';
        drawingCanvas.style.pointerEvents = 'none';
        // Aktifkan scroll container
        const container = document.getElementById('pdfViewerContainer');
        if (container) {
            container.style.overflow = 'auto';
        }
        return;
    }
    
    // Aktifkan canvas untuk tool drawing, tapi biarkan scroll bekerja
    // Canvas akan menangkap event saat mouse down untuk drawing
    drawingCanvas.style.pointerEvents = 'auto';
    const container = document.getElementById('pdfViewerContainer');
    if (container) {
        container.style.overflow = 'auto'; // Tetap aktifkan scroll
    }
}

function setupDrawingCanvas() {
    if (!drawingCanvas || !drawingCtx) return;
    
    // Resize canvas to match container dan iframe
    const container = document.getElementById('pdfViewerContainer');
    const pdfWrapper = document.getElementById('pdfWrapper');
    const iframe = document.getElementById('pdfIframe');
    
    if (container && pdfWrapper && iframe) {
        const resizeCanvas = () => {
            // Canvas mengikuti ukuran wrapper (yang mengikuti iframe)
            const wrapperRect = pdfWrapper.getBoundingClientRect();
            drawingCanvas.width = wrapperRect.width;
            drawingCanvas.height = Math.max(wrapperRect.height, container.clientHeight);
            
            // Set background transparan untuk canvas
            drawingCtx.clearRect(0, 0, drawingCanvas.width, drawingCanvas.height);
            
            updateDrawingCanvasTool();
            updateCanvasCursor();
        };
        
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);
        
        // Update canvas saat iframe load
        iframe.addEventListener('load', () => {
            setTimeout(resizeCanvas, 100); // Delay untuk memastikan iframe sudah render
        });
    }
    
    // Pastikan canvas memiliki background transparan
    drawingCtx.clearRect(0, 0, drawingCanvas.width, drawingCanvas.height);
    updateDrawingCanvasTool();
    updateCanvasCursor();
    
    let lastX = 0;
    let lastY = 0;
    
    function getMousePos(e) {
        const rect = drawingCanvas.getBoundingClientRect();
        return {
            x: e.clientX - rect.left,
            y: e.clientY - rect.top
        };
    }
    
    function startDrawing(e) {
        // Hanya aktif jika tool drawing dipilih
        if (currentTool === 'pen' || currentTool === 'eraser' || currentTool === 'signature') {
            e.preventDefault(); // Prevent default untuk memungkinkan drawing
            e.stopPropagation(); // Stop propagation agar tidak trigger scroll
            isDrawing = true;
            
            // Nonaktifkan scroll saat mulai drawing
            const container = document.getElementById('pdfViewerContainer');
            if (container) {
                container.style.overflow = 'hidden';
            }
            
            const pos = getMousePos(e);
            lastX = pos.x;
            lastY = pos.y;
            
            // Update tool settings setiap kali mulai drawing
            updateDrawingCanvasTool();
            
            if (currentTool === 'signature' && signatureImage) {
                // Untuk signature, gunakan source-over
                drawingCtx.globalCompositeOperation = 'source-over';
                drawingCtx.drawImage(signatureImage, pos.x - 100, pos.y - 50, 200, 100);
                isDrawing = false;
                // Aktifkan kembali scroll setelah signature
                if (container) {
                    container.style.overflow = 'auto';
                }
            } else {
                drawingCtx.beginPath();
                drawingCtx.moveTo(lastX, lastY);
            }
        }
    }
    
    function draw(e) {
        if (!isDrawing || currentTool === 'signature') return;
        
        e.preventDefault(); // Prevent default untuk memungkinkan drawing
        e.stopPropagation(); // Stop propagation agar tidak trigger scroll
        
        const pos = getMousePos(e);
        drawingCtx.lineTo(pos.x, pos.y);
        drawingCtx.stroke();
        lastX = pos.x;
        lastY = pos.y;
    }
    
    function stopDrawing(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        if (isDrawing) {
            drawingCtx.stroke();
        }
        isDrawing = false;
        
        // Aktifkan kembali scroll setelah selesai drawing
        const container = document.getElementById('pdfViewerContainer');
        if (container) {
            container.style.overflow = 'auto';
        }
    }
    
    drawingCanvas.addEventListener('mousedown', startDrawing);
    drawingCanvas.addEventListener('mousemove', draw);
    drawingCanvas.addEventListener('mouseup', stopDrawing);
    drawingCanvas.addEventListener('mouseleave', stopDrawing);
    
    // Touch support
    drawingCanvas.addEventListener('touchstart', (e) => {
        e.preventDefault();
        const touch = e.touches[0];
        const mouseEvent = new MouseEvent('mousedown', {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        drawingCanvas.dispatchEvent(mouseEvent);
    });
    
    drawingCanvas.addEventListener('touchmove', (e) => {
        e.preventDefault();
        const touch = e.touches[0];
        const mouseEvent = new MouseEvent('mousemove', {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        drawingCanvas.dispatchEvent(mouseEvent);
    });
    
    drawingCanvas.addEventListener('touchend', (e) => {
        e.preventDefault();
        const mouseEvent = new MouseEvent('mouseup', {});
        drawingCanvas.dispatchEvent(mouseEvent);
    });
}

// Update tool button to open signature modal
document.addEventListener('DOMContentLoaded', function() {
    const sigBtn = document.getElementById('toolSignature');
    if (sigBtn) {
        sigBtn.addEventListener('click', function() {
            if (!signatureImage) {
                window.openSignatureModal();
            } else {
                currentTool = 'signature';
            }
        });
    }
});

// Save PDF with drawings
document.getElementById('savePdfBtn').addEventListener('click', async function() {
    if (!drawingCanvas || !currentPdfPath) {
        alert('PDF tidak dimuat');
        return;
    }
    
    // Disable button untuk prevent double click
    const saveBtn = document.getElementById('savePdfBtn');
    saveBtn.disabled = true;
    saveBtn.textContent = 'Menyimpan...';
    
    try {
        // Convert drawing canvas to image dengan alpha channel (PNG)
        // Pastikan hanya drawing yang diambil, bukan PDF di bawahnya
        const drawingImage = drawingCanvas.toDataURL('image/png');
        
        // Validasi apakah ada drawing yang dibuat (cek apakah ada pixel yang tidak transparan)
        const imageData = drawingCtx.getImageData(0, 0, drawingCanvas.width, drawingCanvas.height);
        let hasDrawing = false;
        for (let i = 0; i < imageData.data.length; i += 4) {
            // Cek alpha channel (index 3 dari setiap 4 byte: R, G, B, A)
            const alpha = imageData.data[i + 3];
            if (alpha > 0) {
                // Cek apakah ini bukan hanya background putih/transparan
                const r = imageData.data[i];
                const g = imageData.data[i + 1];
                const b = imageData.data[i + 2];
                // Jika ada warna atau alpha > 0, berarti ada drawing
                if (alpha > 10 || (r > 0 || g > 0 || b > 0)) {
                    hasDrawing = true;
                    break;
                }
            }
        }
        
        if (!hasDrawing) {
            // Biarkan user tetap bisa save meskipun tidak ada drawing (untuk clear drawing)
            console.log('[Jobcard] Tidak ada drawing yang dibuat, tetap lanjutkan save');
        }
        
        // Send to server to merge PDF + drawing
        const formData = new FormData();
        formData.append('path', currentPdfPath);
        formData.append('drawing', drawingImage);
        formData.append('_token', '{{ csrf_token() }}');
        
        const response = await fetch("{{ route('admin.maximo.jobcard.update') }}", {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            pdfSaved = true;
            alert('Jobcard berhasil diupdate di server!');
            window.closePdfEditor(true);
        } else {
            alert('Gagal update jobcard: ' + (data.message || 'Unknown error'));
            saveBtn.disabled = false;
            saveBtn.textContent = 'Simpan Perubahan';
        }
    } catch (error) {
        console.error('[Jobcard] Error saving PDF:', error);
        alert('Gagal menyimpan perubahan. Silakan coba lagi.');
        saveBtn.disabled = false;
        saveBtn.textContent = 'Simpan Perubahan';
    }
});

// Cegah klik di luar modal menutup modal tanpa konfirmasi
const pdfEditorModal = document.getElementById('pdfEditorModal');
if (pdfEditorModal) {
    pdfEditorModal.addEventListener('mousedown', function(e) {
        if (e.target === pdfEditorModal) {
            window.closePdfEditor();
        }
    });
}

// Cegah ESC menutup modal tanpa konfirmasi
window.addEventListener('keydown', function(e) {
    if (pdfEditorModal && !pdfEditorModal.classList.contains('hidden') && e.key === 'Escape') {
        window.closePdfEditor();
    }
});

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
@endsection