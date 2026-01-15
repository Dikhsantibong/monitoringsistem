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
                            <input type="hidden" name="wo_date_from" value="{{ request('wo_date_from') }}">
                            <input type="hidden" name="wo_date_to" value="{{ request('wo_date_to') }}">
                            <input type="hidden" name="sr_status" value="{{ request('sr_status') }}">
                            <input type="hidden" name="sr_date_from" value="{{ request('sr_date_from') }}">
                            <input type="hidden" name="sr_date_to" value="{{ request('sr_date_to') }}">
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
                                <table class="min-w-full border border-gray-300 text-sm">
                                    <thead class="bg-blue-700 text-white">
                                        <tr>
                                            <th class="px-3 py-2">No</th>
                                            <th class="px-3 py-2">WO</th>
                                            <th class="px-3 py-2">Parent</th>
                                            <th class="px-3 py-2">Description</th>
                                            <th class="px-3 py-2">Asset</th>
                                            <th class="px-3 py-2">
                                                <div class="flex items-center justify-between">
                                                    <span>Status</span>
                                                    <div class="relative ml-2">
                                                        <select name="wo_status" onchange="document.getElementById('woFilterForm').submit()" 
                                                                class="appearance-none bg-transparent text-white cursor-pointer pl-1 pr-5 py-0 text-xs focus:outline-none border border-blue-500 rounded">
                                                            <option value="" class="text-gray-700" {{ !request('wo_status') ? 'selected' : '' }}>Semua</option>
                                                            @foreach($woStatuses ?? [] as $status)
                                                                <option value="{{ $status }}" class="text-gray-700" {{ request('wo_status') == $status ? 'selected' : '' }}>
                                                                    {{ $status }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-1">
                                                            <svg class="h-3 w-3 fill-current text-white" viewBox="0 0 20 20">
                                                                <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            </th>
                                            <th class="px-3 py-2">
                                                <div class="flex flex-col gap-1">
                                                    <span>Report Date</span>
                                                    <div class="flex gap-1">
                                                        <input type="date" name="wo_date_from" value="{{ request('wo_date_from') }}" 
                                                               onchange="document.getElementById('woFilterForm').submit()"
                                                               class="text-xs px-1 py-0.5 text-gray-700 rounded border border-blue-500">
                                                        <span class="text-xs">-</span>
                                                        <input type="date" name="wo_date_to" value="{{ request('wo_date_to') }}" 
                                                               onchange="document.getElementById('woFilterForm').submit()"
                                                               class="text-xs px-1 py-0.5 text-gray-700 rounded border border-blue-500">
                                                    </div>
                                                </div>
                                            </th>
                                            <th class="px-3 py-2">Priority</th>
                                            <th class="px-3 py-2">
                                                <div class="flex items-center justify-between">
                                                    <span>Work Type</span>
                                                    <div class="relative ml-2">
                                                        <select name="wo_worktype" onchange="document.getElementById('woFilterForm').submit()" 
                                                                class="appearance-none bg-transparent text-white cursor-pointer pl-1 pr-5 py-0 text-xs focus:outline-none border border-blue-500 rounded">
                                                            <option value="" class="text-gray-700" {{ !request('wo_worktype') ? 'selected' : '' }}>Semua</option>
                                                            @foreach($woWorkTypes ?? [] as $worktype)
                                                                <option value="{{ $worktype }}" class="text-gray-700" {{ request('wo_worktype') == $worktype ? 'selected' : '' }}>
                                                                    {{ $worktype }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-1">
                                                            <svg class="h-3 w-3 fill-current text-white" viewBox="0 0 20 20">
                                                                <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            </th>
                                            <th class="px-3 py-2">Sched Start</th>
                                            <th class="px-3 py-2">Sched Finish</th>
                                        </tr>
                                    </thead>
                                <tbody>
                                @forelse($workOrders as $i => $wo)
                                    <tr class="border-b border-gray-300 hover:bg-gray-100">
                                        <td class="border-r border-gray-300 px-3 py-2">
                                            @if($workOrdersPaginator)
                                                {{ ($workOrdersPaginator->currentPage() - 1) * $workOrdersPaginator->perPage() + $loop->iteration }}
                                            @else
                                                {{ $i+1 }}
                                            @endif
                                        </td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $wo['wonum'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $wo['parent'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2">
                                            <span class="inline-block w-96 break-words whitespace-normal maximo-description">
                                                {{ $wo['description'] }}
                                            </span>
                                        </td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $wo['assetnum'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2">
                                            @php
                                                $woStatus = strtoupper($wo['status']);
                                            @endphp
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                @if(in_array($woStatus, ['COMP', 'CLOSE', 'RESOLVED'])) bg-green-100 text-green-800
                                                @elseif(in_array($woStatus, ['WAPPR', 'APPR'])) bg-blue-100 text-blue-800
                                                @elseif(in_array($woStatus, ['INPRG', 'IN PROGRESS'])) bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $wo['status'] }}
                                            </span>
                                        </td>
                                        <td class="border-r border-gray-300 px-3 py-2">
                                            @if(isset($wo['reportdate']) && $wo['reportdate'] !== '-')
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-50 text-green-800 rounded-md">
                                                    {{ $wo['reportdate'] }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $wo['wopriority'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $wo['worktype'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $wo['schedstart'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $wo['schedfinish'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4 text-gray-500">
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
                                        'wo_worktype' => request('wo_worktype'),
                                        'wo_date_from' => request('wo_date_from'),
                                        'wo_date_to' => request('wo_date_to')
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
                                            'wo_worktype' => request('wo_worktype'),
                                            'wo_date_from' => request('wo_date_from'),
                                            'wo_date_to' => request('wo_date_to')
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
                                        'wo_worktype' => request('wo_worktype'),
                                        'wo_date_from' => request('wo_date_from'),
                                        'wo_date_to' => request('wo_date_to')
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

                        <form method="GET" action="{{ route('admin.maximo.index') }}" id="srFilterForm">
                            <input type="hidden" name="sr_page" value="1">
                            <input type="hidden" name="wo_page" value="{{ request('wo_page', 1) }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full border border-gray-300 text-sm">
                                    <thead class="bg-green-700 text-white">
                                        <tr>
                                            <th class="px-3 py-2">No</th>
                                            <th class="px-3 py-2">Ticket</th>
                                            <th class="px-3 py-2">
                                                <div class="flex items-center justify-between">
                                                    <span>Status</span>
                                                    <div class="relative ml-2">
                                                        <select name="sr_status" onchange="document.getElementById('srFilterForm').submit()" 
                                                                class="appearance-none bg-transparent text-white cursor-pointer pl-1 pr-5 py-0 text-xs focus:outline-none border border-green-500 rounded">
                                                            <option value="" class="text-gray-700" {{ !request('sr_status') ? 'selected' : '' }}>Semua</option>
                                                            @foreach($srStatuses ?? [] as $status)
                                                                <option value="{{ $status }}" class="text-gray-700" {{ request('sr_status') == $status ? 'selected' : '' }}>
                                                                    {{ $status }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-1">
                                                            <svg class="h-3 w-3 fill-current text-white" viewBox="0 0 20 20">
                                                                <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            </th>
                                            <th class="px-3 py-2">Status Date</th>
                                            <th class="px-3 py-2">Description</th>
                                            <th class="px-3 py-2">Asset</th>
                                            <th class="px-3 py-2">Location</th>
                                            <th class="px-3 py-2">Reported By</th>
                                            <th class="px-3 py-2">
                                                <div class="flex flex-col gap-1">
                                                    <span>Report Date</span>
                                                    <div class="flex gap-1">
                                                        <input type="date" name="sr_date_from" value="{{ request('sr_date_from') }}" 
                                                               onchange="document.getElementById('srFilterForm').submit()"
                                                               class="text-xs px-1 py-0.5 text-gray-700 rounded border border-green-500">
                                                        <span class="text-xs">-</span>
                                                        <input type="date" name="sr_date_to" value="{{ request('sr_date_to') }}" 
                                                               onchange="document.getElementById('srFilterForm').submit()"
                                                               class="text-xs px-1 py-0.5 text-gray-700 rounded border border-green-500">
                                                    </div>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                <tbody>
                                @forelse($serviceRequests as $i => $sr)
                                    <tr class="border-b border-gray-300 hover:bg-gray-100">
                                        <td class="border-r border-gray-300 px-3 py-2">
                                            @if($serviceRequestsPaginator)
                                                {{ ($serviceRequestsPaginator->currentPage() - 1) * $serviceRequestsPaginator->perPage() + $loop->iteration }}
                                            @else
                                                {{ $i+1 }}
                                            @endif
                                        </td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $sr['ticketid'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2">
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
                                        <td class="border-r border-gray-300 px-3 py-2">
                                            @if(isset($sr['statusdate']) && $sr['statusdate'] !== '-')
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-50 text-blue-800 rounded-md">
                                                    {{ $sr['statusdate'] }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="border-r border-gray-300 px-3 py-2">
                                            <span class="inline-block w-96 break-words whitespace-normal maximo-description">
                                                {{ $sr['description'] }}
                                            </span>
                                        </td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $sr['assetnum'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $sr['location'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $sr['reportedby'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2">
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
                        </form>

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
                                    <a href="{{ $serviceRequestsPaginator->appends([
                                        'wo_page' => request('wo_page', 1), 
                                        'search' => request('search'),
                                        'sr_status' => request('sr_status'),
                                        'sr_date_from' => request('sr_date_from'),
                                        'sr_date_to' => request('sr_date_to')
                                    ])->previousPageUrl() }}" 
                                       class="px-3 py-1 bg-[#0A749B] text-white rounded">Sebelumnya</a>
                                @endif

                                @foreach ($serviceRequestsPaginator->getUrlRange(1, min($serviceRequestsPaginator->lastPage(), 10)) as $page => $url)
                                    @if ($page == $serviceRequestsPaginator->currentPage())
                                        <span class="px-3 py-1 bg-[#0A749B] text-white rounded">{{ $page }}</span>
                                    @else
                                        <a href="{{ $serviceRequestsPaginator->appends([
                                            'wo_page' => request('wo_page', 1), 
                                            'search' => request('search'),
                                            'sr_status' => request('sr_status'),
                                            'sr_date_from' => request('sr_date_from'),
                                            'sr_date_to' => request('sr_date_to')
                                        ])->url($page) }}" 
                                           class="px-3 py-1 rounded bg-white text-[#0A749B] border border-[#0A749B]">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach

                                @if ($serviceRequestsPaginator->hasMorePages())
                                    <a href="{{ $serviceRequestsPaginator->appends([
                                        'wo_page' => request('wo_page', 1), 
                                        'search' => request('search'),
                                        'sr_status' => request('sr_status'),
                                        'sr_date_from' => request('sr_date_from'),
                                        'sr_date_to' => request('sr_date_to')
                                    ])->nextPageUrl() }}" 
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
</style>
@endsection