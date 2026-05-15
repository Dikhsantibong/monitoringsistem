@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.sidebar')

    <div id="main-content" class="flex-1 overflow-auto">
        <header class="bg-white shadow-sm sticky top-0 z-10">
            <div class="flex justify-between items-center px-6 py-3">
                <div class="flex items-center gap-x-3">
                    <h1 class="text-xl font-semibold text-gray-800">Data Lengkap Workorder (Status: APPR)</h1>
                </div>
                <div class="flex items-center gap-x-4">
                    <a href="{{ route('admin.maximo.index') }}" class="px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Maximo Akses
                    </a>
                </div>
            </div>
        </header>

        <main class="px-6 mt-4">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div class="flex-1">
                        <form method="GET" action="{{ route('admin.maximo.workorder-table.index') }}" class="flex gap-2">
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="Cari WONUM, Deskripsi, Asset, atau Lokasi..." 
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                            >
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-search mr-2"></i> Cari
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.maximo.workorder-table.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300 transition-colors flex items-center">
                                    Reset
                                </a>
                            @endif
                        </form>
                    </div>
                    <div class="text-sm text-gray-500">
                        Total Data: <strong>{{ $workOrders->total() }}</strong>
                    </div>
                </div>

                <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-3 text-left font-bold text-gray-700 uppercase tracking-wider border-b">WONUM</th>
                                <th class="px-3 py-3 text-left font-bold text-gray-700 uppercase tracking-wider border-b">Parent</th>
                                <th class="px-3 py-3 text-left font-bold text-gray-700 uppercase tracking-wider border-b">Description</th>
                                <th class="px-3 py-3 text-left font-bold text-gray-700 uppercase tracking-wider border-b">Status</th>
                                <th class="px-3 py-3 text-left font-bold text-gray-700 uppercase tracking-wider border-b">Work Type</th>
                                <th class="px-3 py-3 text-left font-bold text-gray-700 uppercase tracking-wider border-b">Asset</th>
                                <th class="px-3 py-3 text-left font-bold text-gray-700 uppercase tracking-wider border-b">Location</th>
                                <th class="px-3 py-3 text-left font-bold text-gray-700 uppercase tracking-wider border-b">Priority</th>
                                <th class="px-3 py-3 text-left font-bold text-gray-700 uppercase tracking-wider border-b">Report Date</th>
                                <th class="px-3 py-3 text-left font-bold text-gray-700 uppercase tracking-wider border-b">Status Date</th>
                                <th class="px-3 py-3 text-left font-bold text-gray-700 uppercase tracking-wider border-b">Lead</th>
                                <th class="px-3 py-3 text-left font-bold text-gray-700 uppercase tracking-wider border-b">Site ID</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($workOrders as $wo)
                                @php $woArray = array_change_key_case((array)$wo, CASE_LOWER); @endphp
                                <tr class="hover:bg-blue-50 transition-colors">
                                    <td class="px-3 py-2 font-bold text-blue-700 border-r">{{ $woArray['wonum'] }}</td>
                                    <td class="px-3 py-2 text-gray-600 border-r">{{ $woArray['parent'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-gray-800 border-r min-w-[250px] whitespace-normal">{{ $woArray['description'] }}</td>
                                    <td class="px-3 py-2 border-r">
                                        <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-semibold">{{ $woArray['status'] }}</span>
                                    </td>
                                    <td class="px-3 py-2 text-gray-600 border-r">{{ $woArray['worktype'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-gray-600 border-r">{{ $woArray['assetnum'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-gray-600 border-r">{{ $woArray['location'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-center border-r">{{ $woArray['wopriority'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-gray-600 border-r">
                                        {{ $woArray['reportdate'] ? \Carbon\Carbon::parse($woArray['reportdate'])->format('d-m-Y H:i') : '-' }}
                                    </td>
                                    <td class="px-3 py-2 text-gray-600 border-r">
                                        {{ $woArray['statusdate'] ? \Carbon\Carbon::parse($woArray['statusdate'])->format('d-m-Y H:i') : '-' }}
                                    </td>
                                    <td class="px-3 py-2 text-gray-600 border-r">{{ $woArray['lead'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-gray-600">{{ $woArray['siteid'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-3 py-10 text-center text-gray-500 italic">
                                        Tidak ada data workorder dengan status APPR yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $workOrders->appends(['search' => $search])->links() }}
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
