@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* Stats Grid Layout */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        @media (min-width: 640px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (min-width: 1024px) { .stats-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (min-width: 1280px) { .stats-grid { grid-template-columns: repeat(5, 1fr); } }

        /* Stat Card Common */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
            overflow: hidden;
            border: 1px solid #f3f4f6;
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%; /* Ensure equal height */
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        /* Color Variants - Border Left & Icon Background */
        .stat-card.blue { border-left: 4px solid #3b82f6; }
        .stat-card.blue .stat-icon-wrapper { background-color: #eff6ff; color: #3b82f6; }
        
        .stat-card.green { border-left: 4px solid #10b981; }
        .stat-card.green .stat-icon-wrapper { background-color: #ecfdf5; color: #10b981; }
        
        .stat-card.purple { border-left: 4px solid #8b5cf6; }
        .stat-card.purple .stat-icon-wrapper { background-color: #f5f3ff; color: #8b5cf6; }
        
        .stat-card.red { border-left: 4px solid #ef4444; }
        .stat-card.red .stat-icon-wrapper { background-color: #fef2f2; color: #ef4444; }
        
        .stat-card.orange { border-left: 4px solid #f97316; }
        .stat-card.orange .stat-icon-wrapper { background-color: #fff7ed; color: #f97316; }

        /* Content Styling */
        .stat-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-bottom: 0.25rem; }
        .stat-value { font-size: 1.875rem; font-weight: 800; color: #111827; line-height: 1; }
        .stat-subtext { font-size: 0.75rem; color: #9ca3af; margin-top: 0.5rem; font-weight: 500; }
        
        .stat-icon-wrapper {
            padding: 0.75rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0; /* Prevent icon squish */
        }
    </style>
@endsection

@section('content')

@include('components.navbar')

<div class="container mx-auto py-8 mt-24 fade-in px-4">
    <!-- Stats Grid -->
    <div class="stats-grid fade-in">
        <!-- Review: Completed -->
        <div class="stat-card blue">
            <div class="stat-content">
                <div class="stat-label">Review Completed</div>
                <div class="stat-value">{{ $reviewCompletedWOs->total() }}</div>
                <div class="stat-subtext">{{ $lastWeekStart->format('d M') }} - {{ $lastWeekEnd->format('d M') }}</div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-check-double"></i>
            </div>
        </div>
        
        <!-- Review: New Work Orders -->
        <div class="stat-card purple">
            <div class="stat-content">
                <div class="stat-label">New Work Orders</div>
                <div class="stat-value">{{ $reviewCreatedWOs->total() }}</div>
                <div class="stat-subtext">Generated Last Week</div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-hammer"></i>
            </div>
        </div>
        
        <!-- Review: New Service Requests -->
        <div class="stat-card orange">
            <div class="stat-content">
                <div class="stat-label">New Service Requests</div>
                <div class="stat-value">{{ $reviewCreatedSRs->total() }}</div>
                <div class="stat-subtext">Reported Last Week</div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-plus-circle"></i>
            </div>
        </div>
        
        <!-- Plan: Routine PM -->
        <div class="stat-card green">
            <div class="stat-content">
                <div class="stat-label">Next Week PM</div>
                <div class="stat-value">{{ $planPMs->total() }}</div>
                <div class="stat-subtext">{{ $nextWeekStart->format('d M') }} - {{ $nextWeekEnd->format('d M') }}</div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-sync-alt"></i>
            </div>
        </div>
        
        <!-- Plan: Urgent -->
        <div class="stat-card red">
            <div class="stat-content">
                <div class="stat-label">Urgent Priority 1</div>
                <div class="stat-value">{{ $urgentWork->total() }}</div>
                <div class="stat-subtext">Need Immediate Action</div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-exclamation-triangle"></i>
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
                    {{ $reviewCompletedWOs->appends(['review_created_page' => $reviewCreatedWOs->currentPage(), 'review_created_sr_page' => $reviewCreatedSRs->currentPage(), 'plan_pm_page' => $planPMs->currentPage(), 'plan_backlog_page' => $planBacklog->currentPage(), 'plan_urgent_page' => $urgentWork->currentPage()])->links() }}
                </div>
            </div>

            <!-- Card: New Work Orders -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700">New Work Orders</h3>
                    <span class="text-xs font-medium bg-purple-100 text-purple-700 px-2 py-1 rounded-full">{{ $reviewCreatedWOs->total() }} Items</span>
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
                                <td class="px-6 py-3 font-medium text-purple-600">{{ $wo->wonum }}</td>
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
                                    <i class="fas fa-hammer text-4xl mb-2 text-gray-300"></i>
                                    <p>Tidak ada Work Order baru.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                    {{ $reviewCreatedWOs->appends(['review_completed_page' => $reviewCompletedWOs->currentPage(), 'review_created_sr_page' => $reviewCreatedSRs->currentPage(), 'plan_pm_page' => $planPMs->currentPage(), 'plan_backlog_page' => $planBacklog->currentPage(), 'plan_urgent_page' => $urgentWork->currentPage()])->links() }}
                </div>
            </div>

            <!-- Card: New Service Requests -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700">New Service Requests</h3>
                    <span class="text-xs font-medium bg-orange-100 text-orange-700 px-2 py-1 rounded-full">{{ $reviewCreatedSRs->total() }} Items</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-white text-gray-500 font-bold border-b">
                            <tr>
                                <th class="px-6 py-3">Ticket ID</th>
                                <th class="px-6 py-3">Deskripsi</th>
                                <th class="px-6 py-3">Reported By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($reviewCreatedSRs as $sr)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 font-medium text-orange-600">{{ $sr->ticketid }}</td>
                                <td class="px-6 py-3">
                                    <div class="font-medium text-gray-800 truncate w-48" title="{{ $sr->description }}">{{ $sr->description }}</div>
                                    <div class="text-xs text-gray-500">{{ $sr->status }}</div>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-600">
                                    {{ $sr->reportedby }}
                                    <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($sr->reportdate)->format('d/m/Y') }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-400">
                                    <i class="fas fa-envelope-open-text text-4xl mb-2 text-gray-300"></i>
                                    <p>Tidak ada Service Request baru.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                     {{ $reviewCreatedSRs->appends(['review_completed_page' => $reviewCompletedWOs->currentPage(), 'review_created_page' => $reviewCreatedWOs->currentPage(), 'plan_pm_page' => $planPMs->currentPage(), 'plan_backlog_page' => $planBacklog->currentPage(), 'plan_urgent_page' => $urgentWork->currentPage()])->links() }}
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
                                <th class="px-6 py-3">Status</th>
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
                                <td class="px-6 py-3">
                                    <span class="text-xs font-bold text-gray-600 border border-gray-200 bg-gray-50 px-2 py-1 rounded">{{ $wo->status }}</span>
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
                                        {{ round(\Carbon\Carbon::parse($wo->reportdate)->diffInDays(now())) }} Hari
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

