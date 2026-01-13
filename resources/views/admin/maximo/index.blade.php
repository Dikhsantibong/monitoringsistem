@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.sidebar')

    <div id="main-content" class="flex-1 overflow-auto">
        <header class="bg-white shadow-sm sticky z-10">
            <div class="flex justify-between items-center px-6 py-3">
                <h1 class="text-xl font-semibold text-gray-800">
                    Maximo Akses (SITEID: KD)
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
                                    <tr class="border-b hover:bg-gray-100">
                                        <td class="px-3 py-2">{{ $i+1 }}</td>
                                        <td class="px-3 py-2">{{ $wo['wonum'] }}</td>
                                        <td class="px-3 py-2">{{ $wo['parent'] }}</td>
                                        <td class="px-3 py-2">{{ $wo['status'] }}</td>
                                        <td class="px-3 py-2">{{ $wo['statusdate'] }}</td>
                                        <td class="px-3 py-2">{{ $wo['worktype'] }}</td>
                                        <td class="px-3 py-2 truncate max-w-md">{{ $wo['description'] }}</td>
                                        <td class="px-3 py-2">{{ $wo['assetnum'] }}</td>
                                        <td class="px-3 py-2">{{ $wo['location'] }}</td>
                                        <td class="px-3 py-2">{{ $wo['siteid'] }}</td>
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
                                    <tr class="border-b hover:bg-gray-100">
                                        <td class="px-3 py-2">{{ $i+1 }}</td>
                                        <td class="px-3 py-2">{{ $sr['ticketid'] }}</td>
                                        <td class="px-3 py-2">{{ $sr['status'] }}</td>
                                        <td class="px-3 py-2">{{ $sr['statusdate'] }}</td>
                                        <td class="px-3 py-2 truncate max-w-md">{{ $sr['description'] }}</td>
                                        <td class="px-3 py-2">{{ $sr['assetnum'] }}</td>
                                        <td class="px-3 py-2">{{ $sr['location'] }}</td>
                                        <td class="px-3 py-2">{{ $sr['reportedby'] }}</td>
                                        <td class="px-3 py-2">{{ $sr['reportdate'] }}</td>
                                        <td class="px-3 py-2">{{ $sr['siteid'] }}</td>
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
                    </div>

                </div>
            </div>
        </main>
    </div>
</div>
@endsection
