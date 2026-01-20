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
            {{-- Success Message (generate hanya simpan ke server, tidak auto buka editor) --}}
            @if(session('success'))
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
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Detail
                                                </a>
                                                @if(!empty($wo['jobcard_exists']) && $wo['jobcard_exists'] === true && !empty($wo['jobcard_path']))
                                                    <a href="{{ route('admin.maximo.jobcard.download', ['path' => $wo['jobcard_path']]) }}"
                                                       class="inline-flex items-center px-3 py-1 bg-gray-700 text-white rounded hover:bg-gray-800 text-xs">
                                                        <i class="fas fa-download mr-1"></i>
                                                        Download
                                                    </a>
                                                @endif
                                                @if(strtoupper($wo['status']) === 'APPR')
                                                    <form method="POST" action="{{ route('admin.maximo.jobcard.generate') }}" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="wonum" value="{{ $wo['wonum'] }}">
                                                        <button type="submit" 
                                                                class="inline-flex items-center px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs"
                                                                onclick="return confirm('Generate jobcard untuk WO {{ $wo['wonum'] }}?')">
                                                            <i class="fas fa-file-alt mr-1"></i>
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
</style>

<!-- Modal PDF Editor -->
<div id="pdfEditorModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-[90vw] h-[90vh] flex flex-col">
        <div class="flex justify-between items-center p-2 border-b">
            <span class="font-bold">Edit Jobcard PDF</span>
            <button onclick="closePdfEditor()" class="text-gray-500 hover:text-red-600 text-xl">&times;</button>
        </div>
        <div class="flex-1 w-full h-full overflow-auto flex items-center justify-center">
            <iframe id="pdfjs-viewer" src="" style="width:100%;height:100%;border:none;"></iframe>
        </div>
        <div class="flex justify-end gap-2 p-4 border-t">
            <a id="downloadLink"
               href="#"
               class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 inline-flex items-center">
                Download
            </a>
            <button id="savePdfBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan Perubahan</button>
        </div>
    </div>
</div>

<script>
let pdfSaved = false;
let currentPdfPath = '';

function openPdfEditor(pdfUrl, pdfPath) {
    console.log('[Jobcard] openPdfEditor called', { pdfUrl, pdfPath });
    pdfSaved = false;
    currentPdfPath = pdfPath;
    const downloadLink = document.getElementById('downloadLink');
    if (downloadLink) {
        downloadLink.href = "{{ route('admin.maximo.jobcard.download') }}" + "?path=" + encodeURIComponent(pdfPath || '');
    }

    const modal = document.getElementById('pdfEditorModal');
    const iframe = document.getElementById('pdfjs-viewer');

    if (!modal || !iframe) {
        console.error('[Jobcard] Modal atau iframe PDF.js tidak ditemukan di DOM');
        return;
    }

    // Cek apakah file PDF bisa diakses
    fetch(pdfUrl, { method: 'HEAD' })
        .then(res => {
            console.log('[Jobcard] HEAD request jobcard', { status: res.status, ok: res.ok, url: pdfUrl });
            if (!res.ok) {
                console.error('[Jobcard] PDF tidak bisa diakses, status:', res.status);
            }
        })
        .catch(err => {
            console.error('[Jobcard] Gagal melakukan HEAD request ke PDF', err);
        });

    modal.classList.remove('hidden');
    const viewerUrl = '{{ asset('pdf.js/web/viewer.html') }}?file=' + encodeURIComponent(pdfUrl);
    console.log('[Jobcard] set iframe src ke viewerUrl', viewerUrl);

    iframe.onload = function () {
        try {
            console.log('[Jobcard] iframe viewer.html onload');
            const win = iframe.contentWindow;
            if (!win) {
                console.error('[Jobcard] contentWindow iframe null');
                return;
            }
            const app = win.PDFViewerApplication;
            if (!app) {
                console.error('[Jobcard] PDFViewerApplication tidak tersedia di viewer (cek MIME type .mjs dan konfigurasi server)');
            } else {
                console.log('[Jobcard] PDFViewerApplication terdeteksi', {
                    initialized: app.initialized,
                    url: app.url
                });
            }
        } catch (err) {
            console.error('[Jobcard] Error saat inspeksi iframe PDF.js', err);
        }
    };

    iframe.onerror = function (e) {
        console.error('[Jobcard] iframe viewer.html onerror', e);
    };

    iframe.src = viewerUrl;
    document.getElementById('downloadPath').value = pdfPath;
    document.body.style.overflow = 'hidden';
}

function closePdfEditor(force = false) {
    if (!pdfSaved && !force) {
        if (!confirm('Anda belum menyimpan perubahan PDF ke server. Yakin ingin keluar tanpa menyimpan?')) {
            return;
        }
    }
    document.getElementById('pdfEditorModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Cegah klik di luar modal menutup modal tanpa konfirmasi
const pdfEditorModal = document.getElementById('pdfEditorModal');
if (pdfEditorModal) {
    pdfEditorModal.addEventListener('mousedown', function(e) {
        if (e.target === pdfEditorModal) {
            closePdfEditor();
        }
    });
}

// Cegah ESC menutup modal tanpa konfirmasi
window.addEventListener('keydown', function(e) {
    if (pdfEditorModal && !pdfEditorModal.classList.contains('hidden') && e.key === 'Escape') {
        closePdfEditor();
    }
});

function saveEditedPdf(blob) {
    const formData = new FormData();
    formData.append('document', blob, currentPdfPath.split('/').pop());
    formData.append('path', currentPdfPath);
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch("{{ route('admin.maximo.jobcard.update') }}", {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            pdfSaved = true;
            alert('Jobcard berhasil diupdate di server!');
            closePdfEditor(true);
        } else {
            alert('Gagal upload PDF ke server: ' + (data.message || 'Unknown error'));
        }
    })
    .catch((err) => {
        console.error('Upload error:', err);
        alert('Gagal upload PDF ke server. Silakan cek koneksi atau ulangi.');
    });
}

window.addEventListener('message', function(event) {
    console.log('[Jobcard] window message event', event);
    if (event.data && event.data.type === 'save-pdf' && event.data.data) {
        let blob = null;
        if (event.data.data instanceof ArrayBuffer) {
            blob = new Blob([event.data.data], { type: 'application/pdf' });
        } else if (event.data.data instanceof Object) {
            const arr = new Uint8Array(Object.values(event.data.data));
            blob = new Blob([arr], { type: 'application/pdf' });
        }
        if (blob) {
            console.log('[Jobcard] menerima blob dari viewer, ukuran (bytes):', blob.size);
            saveEditedPdf(blob);
        } else {
            console.error('[Jobcard] gagal membentuk Blob dari data yang diterima');
            alert('Gagal membaca data PDF hasil edit.');
        }
    }
});

document.getElementById('savePdfBtn').addEventListener('click', function() {
    const iframe = document.getElementById('pdfjs-viewer').contentWindow;
    iframe.postMessage({ type: 'request-save-pdf' }, '*');
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