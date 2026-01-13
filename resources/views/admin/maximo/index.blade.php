@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.sidebar')

    <div class="flex-1 overflow-auto">
        <header class="bg-white shadow-sm sticky top-0 z-10">
            <div class="px-6 py-3 flex justify-between items-center">
                <h1 class="text-xl font-semibold text-gray-800">
                    Maximo - Service Request & Work Order
                </h1>
            </div>
        </header>

        <main class="p-6">

            {{-- ================= ERROR ================= --}}
            @if(!empty($error))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                    <strong>Gagal mengambil data dari Maximo</strong>
                    <div class="mt-2 text-sm break-all">
                        {{ $error }}
                    </div>
                </div>
            @endif

            {{-- ================= TAB BUTTON ================= --}}
            <div class="flex gap-3 mb-6">
                <button onclick="openTab('sr')"
                    class="tab-btn px-4 py-2 rounded bg-blue-600 text-white font-semibold">
                    Service Request
                </button>
                <button onclick="openTab('wo')"
                    class="tab-btn px-4 py-2 rounded bg-gray-300 text-gray-800 font-semibold">
                    Work Order
                </button>
            </div>

            {{-- ================= SERVICE REQUEST ================= --}}
            <div id="sr" class="tab-content">
                <h2 class="text-lg font-semibold mb-3">
                    Service Request (5 Data Terakhir)
                </h2>

                <div class="overflow-x-auto bg-white rounded shadow">
                    <table class="min-w-full border text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2">No</th>
                                <th class="border px-3 py-2">Ticket ID</th>
                                <th class="border px-3 py-2">Status</th>
                                <th class="border px-3 py-2">Report Date</th>
                                <th class="border px-3 py-2">Location</th>
                                <th class="border px-3 py-2">Reported By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($srData as $i => $sr)
                                <tr class="hover:bg-gray-50">
                                    <td class="border px-3 py-2">{{ $i + 1 }}</td>
                                    <td class="border px-3 py-2">{{ $sr['ticketid'] }}</td>
                                    <td class="border px-3 py-2">{{ $sr['status'] }}</td>
                                    <td class="border px-3 py-2">
                                        {{ $sr['reportdate']?->format('d M Y H:i') ?? '-' }}
                                    </td>
                                    <td class="border px-3 py-2">{{ $sr['location'] }}</td>
                                    <td class="border px-3 py-2">{{ $sr['reportedby'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-gray-500">
                                        Tidak ada data Service Request
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ================= WORK ORDER ================= --}}
            <div id="wo" class="tab-content hidden">
                <h2 class="text-lg font-semibold mb-3">
                    Work Order (5 Data Terakhir)
                </h2>

                <div class="overflow-x-auto bg-white rounded shadow">
                    <table class="min-w-full border text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2">No</th>
                                <th class="border px-3 py-2">WO Number</th>
                                <th class="border px-3 py-2">Status</th>
                                <th class="border px-3 py-2">Status Date</th>
                                <th class="border px-3 py-2">Location</th>
                                <th class="border px-3 py-2">Asset</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($woData as $i => $wo)
                                <tr class="hover:bg-gray-50">
                                    <td class="border px-3 py-2">{{ $i + 1 }}</td>
                                    <td class="border px-3 py-2">{{ $wo['wonum'] }}</td>
                                    <td class="border px-3 py-2">{{ $wo['status'] }}</td>
                                    <td class="border px-3 py-2">
                                        {{ $wo['statusdate']?->format('d M Y H:i') ?? '-' }}
                                    </td>
                                    <td class="border px-3 py-2">{{ $wo['location'] }}</td>
                                    <td class="border px-3 py-2">{{ $wo['assetnum'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-gray-500">
                                        Tidak ada data Work Order
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
</div>

{{-- ================= TAB SCRIPT ================= --}}
<script>
    function openTab(tab) {
        document.querySelectorAll('.tab-content').forEach(el => {
            el.classList.add('hidden');
        });

        document.getElementById(tab).classList.remove('hidden');

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white');
            btn.classList.add('bg-gray-300', 'text-gray-800');
        });

        event.target.classList.add('bg-blue-600', 'text-white');
        event.target.classList.remove('bg-gray-300', 'text-gray-800');
    }
</script>
@endsection
