@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.sidebar')

    <div id="main-content" class="flex-1 overflow-auto">
        <header class="bg-white shadow-sm sticky z-10">
            <div class="flex justify-between items-center px-6 py-3">
                <h1 class="text-xl font-semibold text-gray-800">
                    Maximo Akses (UP KENDARI)
                </h1>
                @include('components.timer')
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

                        <div class="overflow-x-auto">
                            <table class="min-w-full border border-gray-300 text-sm">
                                <thead class="bg-blue-700 text-white">
                                    <tr>
                                        <th class="px-3 py-2">No</th>
                                        <th class="px-3 py-2">WO</th>
                                        <th class="px-3 py-2">Parent</th>
                                        <th class="px-3 py-2">Status</th>
                                        <th class="px-3 py-2">Status Date</th>
                                        <th class="px-3 py-2">Work Type</th>
                                        <th class="px-3 py-2">Description</th>
                                        <th class="px-3 py-2">Asset</th>
                                        <th class="px-3 py-2">Location</th>
                                        <th class="px-3 py-2">Site</th>
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
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $wo['status'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $wo['statusdate'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $wo['worktype'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2 truncate max-w-md">{{ $wo['description'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $wo['assetnum'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $wo['location'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $wo['siteid'] }}</td>
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
                                    <a href="{{ $workOrdersPaginator->appends(['sr_page' => request('sr_page', 1), 'search' => request('search')])->previousPageUrl() }}" 
                                       class="px-3 py-1 bg-[#0A749B] text-white rounded">Sebelumnya</a>
                                @endif

                                @foreach ($workOrdersPaginator->getUrlRange(1, min($workOrdersPaginator->lastPage(), 10)) as $page => $url)
                                    @if ($page == $workOrdersPaginator->currentPage())
                                        <span class="px-3 py-1 bg-[#0A749B] text-white rounded">{{ $page }}</span>
                                    @else
                                        <a href="{{ $workOrdersPaginator->appends(['sr_page' => request('sr_page', 1), 'search' => request('search')])->url($page) }}" 
                                           class="px-3 py-1 rounded bg-white text-[#0A749B] border border-[#0A749B]">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach

                                @if ($workOrdersPaginator->hasMorePages())
                                    <a href="{{ $workOrdersPaginator->appends(['sr_page' => request('sr_page', 1), 'search' => request('search')])->nextPageUrl() }}" 
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
                            <table class="min-w-full border border-gray-300 text-sm">
                                <thead class="bg-green-700 text-white">
                                    <tr>
                                        <th class="px-3 py-2">No</th>
                                        <th class="px-3 py-2">Ticket</th>
                                        <th class="px-3 py-2">Status</th>
                                        <th class="px-3 py-2">Status Date</th>
                                        <th class="px-3 py-2">Description</th>
                                        <th class="px-3 py-2">Asset</th>
                                        <th class="px-3 py-2">Location</th>
                                        <th class="px-3 py-2">Reported By</th>
                                        <th class="px-3 py-2">Report Date</th>
                                        <th class="px-3 py-2">Site</th>
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
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $sr['status'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $sr['statusdate'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2 truncate max-w-md">{{ $sr['description'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $sr['assetnum'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $sr['location'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $sr['reportedby'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $sr['reportdate'] }}</td>
                                        <td class="border-r border-gray-300 px-3 py-2">{{ $sr['siteid'] }}</td>
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
@endsection