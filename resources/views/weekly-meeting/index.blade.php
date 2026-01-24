@extends('layouts.app')

@section('styles')
 <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
@endsection

@section('content')

@include('components.navbar')

<div class="container mx-auto py-8 mt-24 fade-in">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Daily and Weekly Scheduling</h1>
        <p class="text-gray-600 mt-1">Maintenance Planning & Scheduling (Rendalhar)</p>
        
        <div class="mt-4 flex flex-col md:flex-row gap-4">
            <div class="bg-blue-50 border-l-4 border-blue-600 p-4 rounded shadow-sm flex-1">
                <div class="text-xs font-bold text-blue-600 uppercase">Review Period (Minggu Lalu)</div>
                <div class="text-lg font-bold text-gray-800">{{ $lastWeekStart->format('d M Y') }} - {{ $lastWeekEnd->format('d M Y') }}</div>
            </div>
             <div class="bg-green-50 border-l-4 border-green-600 p-4 rounded shadow-sm flex-1">
                <div class="text-xs font-bold text-green-600 uppercase">Planning Period (Minggu Depan)</div>
                <div class="text-lg font-bold text-gray-800">{{ $nextWeekStart->format('d M Y') }} - {{ $nextWeekEnd->format('d M Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Review Section -->
    <div class="mb-10">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-history text-blue-600 mr-2"></i> Evaluasi Minggu Lalu
        </h2>
        
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-3 flex gap-4">
                <button class="tab-review active font-semibold text-blue-700 border-b-2 border-blue-600 pb-2" data-target="review-completed">
                    Completed WOs ({{ count($reviewCompletedWOs) }})
                </button>
                <button class="tab-review font-semibold text-gray-500 hover:text-gray-700 pb-2" data-target="review-created">
                    New Generated WOs/SRs ({{ count($reviewCreatedWOs) }})
                </button>
            </div>
            
            <div class="p-6">
                <!-- Competed WOs Table -->
                <div id="review-completed" class="tab-content-review">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-600 uppercase font-bold text-xs">
                                <tr>
                                    <th class="px-4 py-3">WONUM</th>
                                    <th class="px-4 py-3">Deskripsi</th>
                                    <th class="px-4 py-3">Tipe</th>
                                    <th class="px-4 py-3">Unit/Lokasi</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Tgl Selesai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($reviewCompletedWOs as $wo)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-blue-600">{{ $wo->wonum }}</td>
                                    <td class="px-4 py-3">{{ $wo->description }}</td>
                                    <td class="px-4 py-3">
                                        <span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded text-xs font-bold">{{ $wo->worktype }}</span>
                                    </td>
                                    <td class="px-4 py-3">{{ $wo->location ?? $wo->assetnum }}</td>
                                    <td class="px-4 py-3">
                                        <span class="text-green-600 font-bold text-xs border border-green-200 bg-green-50 px-2 py-1 rounded">{{ $wo->status }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($wo->statusdate)->format('d/m/Y H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 italic">Tidak ada pekerjaan selesai di periode ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Created WOs Table -->
                <div id="review-created" class="tab-content-review hidden">
                     <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-600 uppercase font-bold text-xs">
                                <tr>
                                    <th class="px-4 py-3">WONUM</th>
                                    <th class="px-4 py-3">Deskripsi</th>
                                    <th class="px-4 py-3">Tipe</th>
                                    <th class="px-4 py-3">Prioritas</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Tgl Terbit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($reviewCreatedWOs as $wo)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-blue-600">{{ $wo->wonum }}</td>
                                    <td class="px-4 py-3">{{ $wo->description }}</td>
                                    <td class="px-4 py-3">{{ $wo->worktype }}</td>
                                    <td class="px-4 py-3 text-center">{{ $wo->wopriority }}</td>
                                    <td class="px-4 py-3">{{ $wo->status }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($wo->reportdate)->format('d/m/Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 italic">Tidak ada WO/SR terbit di periode ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Planning Section -->
    <div class="mb-10">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-calendar-alt text-green-600 mr-2"></i> Rencana Minggu Depan
        </h2>

        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
             <div class="bg-gray-50 border-b border-gray-200 px-6 py-3 flex gap-4 overflow-x-auto">
                <button class="tab-plan active font-semibold text-green-700 border-b-2 border-green-600 pb-2 whitespace-nowrap" data-target="plan-pm">
                    <i class="fas fa-sync-alt mr-1"></i> Routine PM ({{ count($planPMs) }})
                </button>
                <button class="tab-plan font-semibold text-gray-500 hover:text-gray-700 pb-2 whitespace-nowrap" data-target="plan-backlog">
                    <i class="fas fa-clock mr-1"></i> Backlog / Carry Over ({{ count($planBacklog) }})
                </button>
                <button class="tab-plan font-semibold text-gray-500 hover:text-gray-700 pb-2 whitespace-nowrap" data-target="plan-urgent">
                    <i class="fas fa-exclamation-circle mr-1 text-red-500"></i> Urgent / Daily ({{ count($urgentWork) }})
                </button>
            </div>
            
             <div class="p-6">
                <!-- PM Routine -->
                <div id="plan-pm" class="tab-content-plan">
                    <div class="mb-2 text-sm text-gray-500 italic">*Pekerjaan rutin yang otomatis ditampilkan EAM (Worktype PM)</div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead class="bg-green-50 text-gray-700 uppercase font-bold text-xs">
                                <tr>
                                    <th class="px-4 py-3">WONUM</th>
                                    <th class="px-4 py-3">Deskripsi Pekerjaan</th>
                                    <th class="px-4 py-3">Unit</th>
                                    <th class="px-4 py-3">Jadwal Mulai</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                             <tbody class="divide-y divide-gray-100">
                                @forelse($planPMs as $wo)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-blue-600">{{ $wo->wonum }}</td>
                                    <td class="px-4 py-3">{{ $wo->description }}</td>
                                    <td class="px-4 py-3">{{ $wo->location ?? $wo->assetnum }}</td>
                                    <td class="px-4 py-3 font-semibold text-green-700">{{ \Carbon\Carbon::parse($wo->schedstart)->format('D, d M Y') }}</td>
                                    <td class="px-4 py-3">
                                         <span class="text-xs font-bold border border-gray-300 bg-gray-100 px-2 py-1 rounded text-gray-600">{{ $wo->status }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-400 italic">Tidak ada jadwal PM minggu depan.</td>
                                </tr>
                                @endforelse
                             </tbody>
                        </table>
                    </div>
                </div>

                 <!-- Backlog -->
                <div id="plan-backlog" class="tab-content-plan hidden">
                    <div class="mb-2 text-sm text-gray-500 italic">*Daftar WO Backlog & Non-Routine (Carry Over)</div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead class="bg-yellow-50 text-gray-700 uppercase font-bold text-xs">
                                <tr>
                                    <th class="px-4 py-3">WONUM</th>
                                    <th class="px-4 py-3">Deskripsi</th>
                                    <th class="px-4 py-3">Tipe</th>
                                    <th class="px-4 py-3">Priority</th>
                                    <th class="px-4 py-3">Age (Days)</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                             <tbody class="divide-y divide-gray-100">
                                @forelse($planBacklog as $wo)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-blue-600">{{ $wo->wonum }}</td>
                                    <td class="px-4 py-3">{{ $wo->description }}</td>
                                    <td class="px-4 py-3">{{ $wo->worktype }}</td>
                                    <td class="px-4 py-3 text-center">{{ $wo->wopriority }}</td>
                                    <td class="px-4 py-3 text-center text-gray-500">
                                        @if(isset($wo->reportdate))
                                            {{ \Carbon\Carbon::parse($wo->reportdate)->diffInDays(now()) }}
                                        @else - @endif
                                    </td>
                                    <td class="px-4 py-3">
                                         <span class="text-xs font-bold border border-yellow-200 bg-yellow-50 px-2 py-1 rounded text-yellow-700">{{ $wo->status }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 italic">Tidak ada backlog.</td>
                                </tr>
                                @endforelse
                             </tbody>
                        </table>
                    </div>
                </div>

                 <!-- Urgent -->
                <div id="plan-urgent" class="tab-content-plan hidden">
                    <div class="mb-2 text-sm text-gray-500 italic">*Pekerjaan Urgent / Daily Planning (Priority 1)</div>
                     <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead class="bg-red-50 text-gray-700 uppercase font-bold text-xs">
                                <tr>
                                    <th class="px-4 py-3">WONUM</th>
                                    <th class="px-4 py-3">Deskripsi</th>
                                    <th class="px-4 py-3">Tipe</th>
                                    <th class="px-4 py-3">Created</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-center">Action</th>
                                </tr>
                            </thead>
                             <tbody class="divide-y divide-gray-100">
                                @forelse($urgentWork as $wo)
                                <tr class="hover:bg-gray-50 bg-red-50">
                                    <td class="px-4 py-3 font-bold text-red-600">{{ $wo->wonum }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $wo->description }}</td>
                                    <td class="px-4 py-3">{{ $wo->worktype }}</td>
                                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($wo->reportdate)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3">
                                         <span class="text-xs font-bold bg-white border border-red-300 px-2 py-1 rounded text-red-600">{{ $wo->status }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">Detail</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 italic">Tidak ada pekerjaan urgent saat ini.</td>
                                </tr>
                                @endforelse
                             </tbody>
                        </table>
                    </div>
                </div>
             </div>
        </div>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Review Tabs
        const btnReview = document.querySelectorAll('.tab-review');
        const contentReview = document.querySelectorAll('.tab-content-review');

        btnReview.forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active classes
                btnReview.forEach(b => {
                    b.classList.remove('active', 'text-blue-700', 'border-b-2', 'border-blue-600');
                    b.classList.add('text-gray-500');
                });
                contentReview.forEach(c => c.classList.add('hidden'));

                // Add active to clicked
                btn.classList.add('active', 'text-blue-700', 'border-b-2', 'border-blue-600');
                btn.classList.remove('text-gray-500');
                
                // Show content
                document.getElementById(btn.dataset.target).classList.remove('hidden');
            });
        });

        // Planning Tabs
        const btnPlan = document.querySelectorAll('.tab-plan');
        const contentPlan = document.querySelectorAll('.tab-content-plan');

        btnPlan.forEach(btn => {
            btn.addEventListener('click', () => {
                 // Remove active classes
                btnPlan.forEach(b => {
                    b.classList.remove('active', 'text-green-700', 'border-b-2', 'border-green-600');
                    b.classList.add('text-gray-500');
                });
                 contentPlan.forEach(c => c.classList.add('hidden'));

                  // Add active to clicked
                btn.classList.add('active', 'text-green-700', 'border-b-2', 'border-green-600');
                btn.classList.remove('text-gray-500');

                 // Show content
                document.getElementById(btn.dataset.target).classList.remove('hidden');
            });
        });
    });
</script>
@endsection
@section('styles')
 <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection

@section('content')

@include('components.navbar')
<div class="container mx-auto py-8 mt-24">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-blue-800">Weekly Meeting</h1>
            <div class="text-gray-600">Periode: {{ $startOfWeek->format('d M Y') }} - {{ $endOfWeek->format('d M Y') }}</div>
        </div>
        <div class="mt-4 md:mt-0 flex gap-2">
            <button id="tab-this-week" class="tab-btn bg-blue-600 text-white px-4 py-2 rounded-l focus:outline-none flex items-center">
                <i class="fas fa-calendar-week mr-2"></i> Minggu Ini
            </button>
            <button id="tab-next-week" class="tab-btn bg-blue-100 text-blue-700 px-4 py-2 rounded-r focus:outline-none flex items-center">
                <i class="fas fa-calendar-plus mr-2"></i> Minggu Depan
            </button>
        </div>
    </div>
    <!-- Ringkasan Card -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 rounded shadow p-4 text-center">
            <div class="text-xs text-blue-700 font-semibold uppercase tracking-wide">WO Backlog</div>
            <div class="text-3xl font-bold text-blue-900">{{ $woBacklogsThisWeek->count() }}</div>
        </div>
        <div class="bg-green-50 rounded shadow p-4 text-center">
            <div class="text-xs text-green-700 font-semibold uppercase tracking-wide">Work Order</div>
            <div class="text-3xl font-bold text-green-900">{{ $workOrdersThisWeek->count() }}</div>
        </div>
        <div class="bg-yellow-50 rounded shadow p-4 text-center">
            <div class="text-xs text-yellow-700 font-semibold uppercase tracking-wide">Service Request</div>
            <div class="text-3xl font-bold text-yellow-900">{{ $serviceRequestsThisWeek->count() }}</div>
        </div>
        <div class="bg-red-50 rounded shadow p-4 text-center">
            <div class="text-xs text-red-700 font-semibold uppercase tracking-wide">Kesiapan Pembangkit</div>
            <div class="text-3xl font-bold text-red-900">{{ $powerPlantMaintenancesThisWeek->count() }}</div>
        </div>
    </div>
    <hr class="my-6 border-blue-200">
    <div id="rekap-this-week">
        <h2 class="text-lg font-bold mb-2 text-blue-800 flex items-center"><i class="fas fa-list-alt mr-2"></i> Rekapan Pekerjaan Minggu Ini</h2>
        <!-- Tab Navigation -->
        <div class="flex gap-2 mb-4">
            <button class="tab-tipe px-4 py-2 rounded bg-blue-600 text-white font-semibold" data-tipe="wo-backlog-this">WO Backlog ({{ $woBacklogsThisWeek->count() }})</button>
            <button class="tab-tipe px-4 py-2 rounded bg-gray-200 text-gray-700 font-semibold" data-tipe="wo-this">Work Order ({{ $workOrdersThisWeek->count() }})</button>
            <button class="tab-tipe px-4 py-2 rounded bg-yellow-100 text-yellow-700 font-semibold" data-tipe="sr-this">Service Request ({{ $serviceRequestsThisWeek->count() }})</button>
            <button class="tab-tipe px-4 py-2 rounded bg-red-100 text-red-700 font-semibold" data-tipe="readiness-this">Kesiapan Pembangkit ({{ $powerPlantMaintenancesThisWeek->count() }})</button>
        </div>
        <!-- Tabel WO Backlog -->
        <div class="tab-pane-tipe" id="wo-backlog-this">
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Mulai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $no = 1; @endphp
                        @foreach($woBacklogsThisWeek as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center border border-gray-200">{{ $no++ }}</td>
                                <td class="px-6 py-4 text-blue-700 font-semibold border border-gray-200">Backlog</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $item->deskripsi }}</td>
                                <td class="px-6 py-4 border border-gray-200">
                                    {{ $unitMap[$item->unit_source] ?? $item->unit_source }}
                                </td>

                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->schedule_start)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->schedule_finish)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 border border-gray-200">@include('weekly-meeting.status-badge', ['status'=>$item->status])</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Tabel Work Order -->
        <div class="tab-pane-tipe hidden" id="wo-this">
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Mulai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $no = 1; @endphp
                        @foreach($workOrdersThisWeek as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center border border-gray-200">{{ $no++ }}</td>
                                <td class="px-6 py-4 text-green-700 font-semibold border border-gray-200">WO</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $item->description }}</td>
                                <td class="px-6 py-4 border border-gray-200">
                                    {{ $unitMap[$item->unit_source] ?? $item->unit_source }}
                                </td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->schedule_start)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->schedule_finish)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 border border-gray-200">@include('weekly-meeting.status-badge', ['status'=>$item->status])</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Tabel Service Request -->
        <div class="tab-pane-tipe hidden" id="sr-this">
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Mulai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $no = 1; @endphp
                        @foreach($serviceRequestsThisWeek as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center border border-gray-200">{{ $no++ }}</td>
                                <td class="px-6 py-4 text-yellow-700 font-semibold border border-gray-200">SR</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $item->description }}</td>
                                <td class="px-6 py-4 border border-gray-200">
                                    {{ $unitMap[$item->unit_source] ?? $item->unit_source }}
                                </td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->created_at)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->updated_at)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 border border-gray-200">@include('weekly-meeting.status-badge', ['status'=>$item->status])</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Tabel Kesiapan Pembangkit -->
        <div class="tab-pane-tipe hidden" id="readiness-this">
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Target Selesai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $no = 1; @endphp
                        @foreach($powerPlantMaintenancesThisWeek as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center border border-gray-200">{{ $no++ }}</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $unitMap[$item->unit_source] ?? $item->unit_source }}</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $item->deskripsi ?? '-' }}</td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->tanggal_mulai)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->target_selesai)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 border border-gray-200">@include('weekly-meeting.status-badge', ['status'=>$item->status])</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Tab Minggu Depan: sama, ganti id dan variabel _NextWeek -->
    <div id="rekap-next-week" class="hidden mt-10">
        <h2 class="text-lg font-bold mb-2 text-blue-800 flex items-center"><i class="fas fa-calendar-plus mr-2"></i> Rencana Pekerjaan Minggu Depan</h2>
        <div class="flex gap-2 mb-4">
            <button class="tab-tipe-next px-4 py-2 rounded bg-blue-600 text-white font-semibold" data-tipe="wo-backlog-next">WO Backlog ({{ $woBacklogsNextWeek->count() }})</button>
            <button class="tab-tipe-next px-4 py-2 rounded bg-gray-200 text-gray-700 font-semibold" data-tipe="wo-next">Work Order ({{ $workOrdersNextWeek->count() }})</button>
            <button class="tab-tipe-next px-4 py-2 rounded bg-yellow-100 text-yellow-700 font-semibold" data-tipe="sr-next">Service Request ({{ $serviceRequestsNextWeek->count() }})</button>
            <button class="tab-tipe-next px-4 py-2 rounded bg-red-100 text-red-700 font-semibold" data-tipe="readiness-next">Kesiapan Pembangkit ({{ $powerPlantMaintenancesNextWeek->count() }})</button>
        </div>
        <div class="tab-pane-tipe-next" id="wo-backlog-next">
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Mulai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $no = 1; @endphp
                        @foreach($woBacklogsNextWeek as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center border border-gray-200">{{ $no++ }}</td>
                                <td class="px-6 py-4 text-blue-700 font-semibold border border-gray-200">Backlog</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $item->deskripsi }}</td>
                                <td class="px-6 py-4 border border-gray-200">
                                    {{ $unitMap[$item->unit_source] ?? $item->unit_source }}
                                </td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->schedule_start)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->schedule_finish)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 border border-gray-200">@include('weekly-meeting.status-badge', ['status'=>$item->status])</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane-tipe-next hidden" id="wo-next">
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Mulai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $no = 1; @endphp
                        @foreach($workOrdersNextWeek as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center border border-gray-200">{{ $no++ }}</td>
                                <td class="px-6 py-4 text-green-700 font-semibold border border-gray-200">WO</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $item->description }}</td>
                                <td class="px-6 py-4 border border-gray-200">
                                    {{ $unitMap[$item->unit_source] ?? $item->unit_source }}
                                </td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->schedule_start)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->schedule_finish)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 border border-gray-200">@include('weekly-meeting.status-badge', ['status'=>$item->status])</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane-tipe-next hidden" id="sr-next">
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Mulai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $no = 1; @endphp
                        @foreach($serviceRequestsNextWeek as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center border border-gray-200">{{ $no++ }}</td>
                                <td class="px-6 py-4 text-yellow-700 font-semibold border border-gray-200">SR</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $item->description }}</td>
                                <td class="px-6 py-4 border border-gray-200">
                                    {{ $unitMap[$item->unit_source] ?? $item->unit_source }}
                                </td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->created_at)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->updated_at)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 border border-gray-200">@include('weekly-meeting.status-badge', ['status'=>$item->status])</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Tabel Kesiapan Pembangkit Minggu Depan -->
        <div class="tab-pane-tipe-next hidden" id="readiness-next">
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Target Selesai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $no = 1; @endphp
                        @foreach($powerPlantMaintenancesNextWeek as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center border border-gray-200">{{ $no++ }}</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $unitMap[$item->unit_source] ?? $item->unit_source }}</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $item->deskripsi ?? '-' }}</td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->tanggal_mulai)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->target_selesai)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 border border-gray-200">@include('weekly-meeting.status-badge', ['status'=>$item->status])</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<hr class="my-6 border-blue-200">
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab Minggu Ini/Minggu Depan
        const tabThisWeek = document.getElementById('tab-this-week');
        const tabNextWeek = document.getElementById('tab-next-week');
        const rekapThisWeek = document.getElementById('rekap-this-week');
        const rekapNextWeek = document.getElementById('rekap-next-week');
        tabThisWeek.addEventListener('click', function() {
            tabThisWeek.classList.add('bg-blue-600', 'text-white');
            tabThisWeek.classList.remove('bg-blue-100', 'text-blue-700');
            tabNextWeek.classList.remove('bg-blue-600', 'text-white');
            tabNextWeek.classList.add('bg-blue-100', 'text-blue-700');
            rekapThisWeek.classList.remove('hidden');
            rekapNextWeek.classList.add('hidden');
        });
        tabNextWeek.addEventListener('click', function() {
            tabNextWeek.classList.add('bg-blue-600', 'text-white');
            tabNextWeek.classList.remove('bg-blue-100', 'text-blue-700');
            tabThisWeek.classList.remove('bg-blue-600', 'text-white');
            tabThisWeek.classList.add('bg-blue-100', 'text-blue-700');
            rekapThisWeek.classList.add('hidden');
            rekapNextWeek.classList.remove('hidden');
        });
        // Tab Minggu Ini per tipe
        const tipeTabs = document.querySelectorAll('.tab-tipe');
        const tipePanes = document.querySelectorAll('.tab-pane-tipe');
        tipeTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                tipeTabs.forEach(t => t.classList.remove('bg-blue-600', 'text-white'));
                tipeTabs.forEach(t => t.classList.add('bg-gray-200', 'text-gray-700'));
                this.classList.remove('bg-gray-200', 'text-gray-700');
                this.classList.add('bg-blue-600', 'text-white');
                tipePanes.forEach(pane => pane.classList.add('hidden'));
                document.getElementById(this.dataset.tipe).classList.remove('hidden');
            });
        });
        // Tab Minggu Depan per tipe
        const tipeTabsNext = document.querySelectorAll('.tab-tipe-next');
        const tipePanesNext = document.querySelectorAll('.tab-pane-tipe-next');
        tipeTabsNext.forEach(tab => {
            tab.addEventListener('click', function() {
                tipeTabsNext.forEach(t => t.classList.remove('bg-blue-600', 'text-white'));
                tipeTabsNext.forEach(t => t.classList.add('bg-gray-200', 'text-gray-700'));
                this.classList.remove('bg-gray-200', 'text-gray-700');
                this.classList.add('bg-blue-600', 'text-white');
                tipePanesNext.forEach(pane => pane.classList.add('hidden'));
                document.getElementById(this.dataset.tipe).classList.remove('hidden');
            });
        });
    });
</script>
@endsection
