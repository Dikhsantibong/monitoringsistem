@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        
        .stat-card { transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-2px); }
    </style>
@endsection

@section('content')

@include('components.navbar')

<div class="container mx-auto py-8 mt-24 fade-in px-4">
    <!-- Header & Period Info -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Operational Scheduling</h1>
            <p class="text-gray-600 mt-1">Maintenance Planning & Review (Rendalhar)</p>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Blue: Review Completed -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-blue-500 p-4 stat-card">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-xs font-bold text-blue-500 uppercase tracking-widest">Completed (Last Week)</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1">{{ $reviewCompletedWOs->total() }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $lastWeekStart->format('d M') }} - {{ $lastWeekEnd->format('d M') }}</div>
                </div>
                <div class="p-2 bg-blue-50 rounded-full text-blue-500">
                    <i class="fas fa-check-double"></i>
                </div>
            </div>
        </div>

        <!-- Indigo: New SRs -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-indigo-500 p-4 stat-card">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-xs font-bold text-indigo-500 uppercase tracking-widest">New Generated (Last Week)</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1">{{ $reviewCreatedWOs->total() }}</div>
                    <div class="text-xs text-gray-500 mt-1">SR/WO Created</div>
                </div>
                <div class="p-2 bg-indigo-50 rounded-full text-indigo-500">
                    <i class="fas fa-plus-circle"></i>
                </div>
            </div>
        </div>

        <!-- Green: Plan PM -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-green-500 p-4 stat-card">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-xs font-bold text-green-500 uppercase tracking-widest">Routine PM (Next Week)</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1">{{ $planPMs->total() }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $nextWeekStart->format('d M') }} - {{ $nextWeekEnd->format('d M') }}</div>
                </div>
                <div class="p-2 bg-green-50 rounded-full text-green-500">
                    <i class="fas fa-sync-alt"></i>
                </div>
            </div>
        </div>

        <!-- Red: Urgent -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-red-500 p-4 stat-card">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-xs font-bold text-red-500 uppercase tracking-widest">Urgent / Priority 1</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1">{{ $urgentWork->total() }}</div>
                    <div class="text-xs text-gray-500 mt-1">Need Immediate Action</div>
                </div>
                <div class="p-2 bg-red-50 rounded-full text-red-500">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Left Column: Review Phase -->
        <div class="flex flex-col gap-6">
            <div class="flex items-center gap-2 mb-2">
                <div class="bg-blue-600 text-white p-2 rounded shadow-sm">
                    <i class="fas fa-history"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Evaluasi Minggu Lalu</h2>
            </div>

            <!-- Card: Completed WOs -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700">Completed Work Orders</h3>
                    <span class="text-xs font-medium bg-blue-100 text-blue-700 px-2 py-1 rounded-full">{{ $reviewCompletedWOs->total() }} Items</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-white text-gray-500 font-bold border-b">
                            <tr>
                                <th class="px-6 py-3">WONUM</th>
                                <th class="px-6 py-3">Deskripsi</th>
                                <th class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($reviewCompletedWOs as $wo)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 font-medium text-blue-600">{{ $wo->wonum }}</td>
                                <td class="px-6 py-3">
                                    <div class="font-medium text-gray-800 truncate w-48" title="{{ $wo->description }}">{{ $wo->description }}</div>
                                    <div class="text-xs text-gray-500">{{ $wo->location ?? $wo->assetnum }}</div>
                                </td>
                                <td class="px-6 py-3">
                                    <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded border border-green-200">{{ $wo->status }}</span>
                                    <div class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($wo->statusdate)->format('d/m') }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-400">
                                    <i class="fas fa-clipboard-check text-4xl mb-2 text-gray-300"></i>
                                    <p>Tidak ada data completed.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                    {{ $reviewCompletedWOs->appends(['review_created_page' => $reviewCreatedWOs->currentPage(), 'plan_pm_page' => $planPMs->currentPage(), 'plan_backlog_page' => $planBacklog->currentPage(), 'plan_urgent_page' => $urgentWork->currentPage()])->links() }}
                </div>
            </div>

            <!-- Card: New Generated -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700">New Generated SR/WO</h3>
                    <span class="text-xs font-medium bg-indigo-100 text-indigo-700 px-2 py-1 rounded-full">{{ $reviewCreatedWOs->total() }} Items</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-white text-gray-500 font-bold border-b">
                            <tr>
                                <th class="px-6 py-3">WONUM</th>
                                <th class="px-6 py-3">Deskripsi</th>
                                <th class="px-6 py-3">Prioritas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($reviewCreatedWOs as $wo)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 font-medium text-indigo-600">{{ $wo->wonum }}</td>
                                <td class="px-6 py-3">
                                    <div class="font-medium text-gray-800 truncate w-48" title="{{ $wo->description }}">{{ $wo->description }}</div>
                                    <div class="text-xs text-gray-500">{{ $wo->worktype }}</div>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <div class="text-sm font-bold text-gray-700">{{ $wo->wopriority }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                                    <p>Tidak ada WO/SR baru.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                    {{ $reviewCreatedWOs->appends(['review_completed_page' => $reviewCompletedWOs->currentPage(), 'plan_pm_page' => $planPMs->currentPage(), 'plan_backlog_page' => $planBacklog->currentPage(), 'plan_urgent_page' => $urgentWork->currentPage()])->links() }}
                </div>
            </div>
        </div>

        <!-- Right Column: Planning Phase -->
        <div class="flex flex-col gap-6">
            <div class="flex items-center gap-2 mb-2">
                <div class="bg-green-600 text-white p-2 rounded shadow-sm">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Rencana Minggu Depan</h2>
            </div>

            <!-- Card: Routine PM -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700">Routine PM</h3>
                    <span class="text-xs font-medium bg-green-100 text-green-700 px-2 py-1 rounded-full">{{ $planPMs->total() }} Items</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-white text-gray-500 font-bold border-b">
                            <tr>
                                <th class="px-6 py-3">WONUM</th>
                                <th class="px-6 py-3">Deskripsi</th>
                                <th class="px-6 py-3">Jadwal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($planPMs as $wo)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 font-medium text-green-600">{{ $wo->wonum }}</td>
                                <td class="px-6 py-3">
                                    <div class="font-medium text-gray-800 truncate w-48" title="{{ $wo->description }}">{{ $wo->description }}</div>
                                    <div class="text-xs text-gray-500">{{ $wo->location ?? $wo->assetnum }}</div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="text-xs font-bold text-gray-600 bg-gray-100 px-2 py-1 rounded text-center">
                                        {{ \Carbon\Carbon::parse($wo->schedstart)->format('d M') }}
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-400">
                                    <i class="fas fa-calendar-check text-4xl mb-2 text-gray-300"></i>
                                    <p>Tidak ada jadwal PM.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                    {{ $planPMs->appends(['review_completed_page' => $reviewCompletedWOs->currentPage(), 'review_created_page' => $reviewCreatedWOs->currentPage(), 'plan_backlog_page' => $planBacklog->currentPage(), 'plan_urgent_page' => $urgentWork->currentPage()])->links() }}
                </div>
            </div>

            <!-- Card: Urgent Work -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex justify-between items-center">
                    <h3 class="font-bold text-red-700"><i class="fas fa-exclamation-circle mr-1"></i> Urgent / Daily Focus</h3>
                    <span class="text-xs font-medium bg-red-200 text-red-800 px-2 py-1 rounded-full">{{ $urgentWork->total() }} Items</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-white text-gray-500 font-bold border-b">
                            <tr>
                                <th class="px-6 py-3">WONUM</th>
                                <th class="px-6 py-3">Deskripsi</th>
                                <th class="px-6 py-3">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($urgentWork as $wo)
                            <tr class="hover:bg-red-50 bg-white">
                                <td class="px-6 py-3 font-bold text-red-600">{{ $wo->wonum }}</td>
                                <td class="px-6 py-3">
                                    <div class="font-medium text-gray-800">{{ $wo->description }}</div>
                                    <div class="text-xs text-red-400 font-semibold">{{ $wo->status }}</div>
                                </td>
                                <td class="px-6 py-3 text-gray-500 text-xs">
                                    {{ \Carbon\Carbon::parse($wo->reportdate)->format('d/m/Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-400">
                                    <i class="fas fa-check-circle text-4xl mb-2 text-gray-300"></i>
                                    <p>Aman! Tidak ada pekerjaan urgent.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                    {{ $urgentWork->appends(['review_completed_page' => $reviewCompletedWOs->currentPage(), 'review_created_page' => $reviewCreatedWOs->currentPage(), 'plan_pm_page' => $planPMs->currentPage(), 'plan_backlog_page' => $planBacklog->currentPage()])->links() }}
                </div>
            </div>

            <!-- Card: Backlog -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700">Backlog / Carry Over</h3>
                    <span class="text-xs font-medium bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full">{{ $planBacklog->total() }} Items</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-white text-gray-500 font-bold border-b">
                            <tr>
                                <th class="px-6 py-3">WONUM</th>
                                <th class="px-6 py-3">Deskripsi</th>
                                <th class="px-6 py-3">Age</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($planBacklog as $wo)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 font-medium text-gray-600">{{ $wo->wonum }}</td>
                                <td class="px-6 py-3">
                                    <div class="font-medium text-gray-800 truncate w-48" title="{{ $wo->description }}">{{ $wo->description }}</div>
                                    <div class="text-xs text-gray-500">
                                        <span class="bg-gray-100 px-1 rounded">{{ $wo->worktype }}</span> 
                                        {{ $wo->status }}
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-gray-500 text-xs">
                                    @if(isset($wo->reportdate))
                                        {{ \Carbon\Carbon::parse($wo->reportdate)->diffInDays(now()) }} Days
                                    @else - @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-400">
                                    <i class="fas fa-clipboard-check text-4xl mb-2 text-gray-300"></i>
                                    <p>Tidak ada backlog.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                    {{ $planBacklog->appends(['review_completed_page' => $reviewCompletedWOs->currentPage(), 'review_created_page' => $reviewCreatedWOs->currentPage(), 'plan_pm_page' => $planPMs->currentPage(), 'plan_urgent_page' => $urgentWork->currentPage()])->links() }}
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

