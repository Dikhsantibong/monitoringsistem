@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* Stats Grid Layout - Exact copy from Kinerja */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border: 1px solid #e1e4e8;
            border-radius: 6px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: box-shadow 0.2s ease;
            height: 100%;
        }
        
        .stat-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        
        .stat-content {
            flex: 1;
        }
        
        .stat-label {
            font-size: 0.8125rem;
            color: #6c757d;
            font-weight: 500;
            margin-bottom: 0.375rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .stat-value {
            font-size: 1.75rem;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 0.25rem;
            line-height: 1;
        }
        
        .stat-subtext {
            font-size: 0.75rem;
            color: #868e96;
        }
        
        .stat-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .stat-icon-wrapper i {
            font-size: 1.25rem;
        }
        
        /* Color Variants - Hex Codes from Kinerja */
        .stat-card.blue .stat-icon-wrapper {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .stat-card.purple .stat-icon-wrapper {
            background: #f3e5f5;
            color: #7b1fa2;
        }
        
        .stat-card.red .stat-icon-wrapper {
            background: #ffebee;
            color: #d32f2f;
        }
        
        .stat-card.green .stat-icon-wrapper {
            background: #e8f5e9;
            color: #388e3c;
        }
        
        .stat-card.orange .stat-icon-wrapper {
            background: #fff3e0;
            color: #f57c00;
        }

        /* Calendar Styles */
        .calendar-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            border: 1px solid #e5e7eb;
            background-color: #f3f4f6;
            gap: 1px;
            min-width: 1000px; /* Ensure 7 columns stay readable on smaller screens */
        }
        .calendar-header {
            background: #f8fafc;
            padding: 0.75rem 0.5rem;
            text-align: center;
            font-size: 0.75rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e2e8f0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .calendar-day {
            min-height: 250px;
            padding: 0.5rem;
            background: white;
            transition: background-color 0.2s;
            display: flex;
            flex-direction: column;
            min-width: 0; /* Important for truncation */
        }
        .calendar-day:hover {
            background-color: #f8fafc;
        }
        .calendar-day.other-month {
            background: #fdfdfd;
            color: #cbd5e1;
        }
        .calendar-day.today {
            background: #f0f7ff;
        }
        .calendar-day.today .day-number {
            background: #3b82f6;
            color: white;
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        .day-number {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
        }
        .calendar-events {
            flex: 1;
            min-width: 0;
        }
        .event-item {
            font-size: 0.7rem;
            padding: 4px 6px;
            margin-bottom: 3px;
            border-radius: 4px;
            line-height: 1.2;
            font-weight: 500;
            border: 1px solid transparent;
            transition: transform 0.1s;
            overflow: hidden;
        }
        .event-item:hover {
            transform: translateY(-1px);
            filter: brightness(0.95);
        }
        .event-wo { 
            background: #eff6ff; 
            color: #1e40af; 
            border-color: #bfdbfe;
            border-left: 3px solid #3b82f6; 
        }
        .event-sr { 
            background: #fff7ed; 
            color: #9a3412; 
            border-color: #ffedd5;
            border-left: 3px solid #f97316; 
        }
        .calendar-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 0.5rem;
            border-top: 1px solid #f1f5f9;
            margin-top: auto;
        }
        .pag-btn {
            padding: 2px 6px;
            background: #f1f5f9;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            color: #475569;
            cursor: pointer;
            border: 1px solid #e2e8f0;
        }
        .pag-btn:hover { background: #e2e8f0; }
        .pag-btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .pag-info { font-size: 10px; color: #64748b; font-weight: 500; }
        .calendar-nav-btn {
            padding: 0.5rem 0.75rem;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .calendar-nav-btn:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
            color: #111827;
        }
        .view-toggle-btn {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }
        .view-toggle-active {
            background: white;
            color: #2563eb;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .view-toggle-inactive {
            color: #64748b;
        }
        .view-toggle-inactive:hover {
            background: #f1f5f9;
            color: #334155;
        }
    </style>
@endsection

@section('content')

@include('components.navbar')

<div class="container mx-auto py-8 mt-24 fade-in px-4">
    <div class="flex justify-between items-center mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Weekly Meeting</h1>
            <p class="text-sm text-gray-500">Dashboard Evaluasi & Perencanaan Mingguan</p>
        </div>
        <div class="flex bg-slate-100 p-1 rounded-xl">
            <a href="{{ route('weekly-meeting.index', ['mode' => 'list']) }}" 
               class="view-toggle-btn flex items-center {{ $mode === 'list' ? 'view-toggle-active' : 'view-toggle-inactive' }}">
                <i class="fas fa-list-ul mr-2"></i> List View
            </a>
            <a href="{{ route('weekly-meeting.index', ['mode' => 'calendar']) }}" 
               class="view-toggle-btn flex items-center {{ $mode === 'calendar' ? 'view-toggle-active' : 'view-toggle-inactive' }}">
                <i class="fas fa-calendar-alt mr-2"></i> Calendar View
            </a>
        </div>
    </div>

    @if($mode === 'calendar')
        <!-- Calendar Mode View -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden fade-in">
            <div class="p-4 bg-white border-b border-gray-100 flex flex-wrap justify-between items-center gap-4">
                <div class="flex items-center">
                    <a href="{{ route('weekly-meeting.index', ['mode' => 'calendar', 'month' => $month == 1 ? 12 : $month - 1, 'year' => $month == 1 ? $year - 1 : $year]) }}" 
                       class="calendar-nav-btn" title="Bulan Sebelumnya">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <div class="text-xl font-bold text-gray-800 min-w-[180px] text-center">
                        {{ \Carbon\Carbon::create($year, $month, 1)->translatedFormat('F Y') }}
                    </div>
                    <a href="{{ route('weekly-meeting.index', ['mode' => 'calendar', 'month' => $month == 12 ? 1 : $month + 1, 'year' => $month == 12 ? $year + 1 : $year]) }}" 
                       class="calendar-nav-btn" title="Bulan Berikutnya">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                
                <form action="{{ route('weekly-meeting.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="mode" value="calendar">
                    <select name="unit" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <option value="">Semua Unit</option>
                        @foreach($powerPlants as $plant)
                            <option value="{{ $plant->id }}" {{ $unitFilter == $plant->id ? 'selected' : '' }}>{{ $plant->name }}</option>
                        @endforeach
                    </select>
                    <select name="month" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create($year, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                    <select name="year" class="py-2 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        @foreach(range(now()->year - 5, now()->year + 5) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition-all shadow-sm">
                        FILTER
                    </button>
                </form>
            </div>

            <div class="calendar-wrapper">
                <div class="calendar-grid">
                    @php
                        $startOfGrid = $firstDay->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
                        $endOfGrid = $lastDay->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
                        $currentDay = $startOfGrid->copy();
                    @endphp

                    @foreach(['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'MINGGU'] as $dayName)
                        <div class="calendar-header">
                            {{ $dayName }}
                        </div>
                    @endforeach

                    @while($currentDay <= $endOfGrid)
                        @php 
                            $dayKey = $currentDay->format('Y-m-d');
                            $dayEvents = $events->get($dayKey, collect()); 
                            $totalPages = ceil($dayEvents->count() / 10);
                        @endphp
                        <div class="calendar-day {{ $currentDay->month != $month ? 'other-month' : '' }} {{ $currentDay->isToday() ? 'today' : '' }}" 
                             data-day="{{ $dayKey }}">
                            <div class="flex justify-between items-start mb-2">
                                <span class="day-number">
                                    {{ $currentDay->day }}
                                </span>
                                @if($currentDay->isToday())
                                    <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">HARI INI</span>
                                @endif
                            </div>
                            <div class="calendar-events">
                                @forelse($dayEvents->chunk(10) as $pageIndex => $pageEvents)
                                    <div class="day-page {{ $pageIndex > 0 ? 'hidden' : '' }}" data-page="{{ $pageIndex }}">
                                        @foreach($pageEvents as $event)
                                            <div class="event-item {{ $event['type'] == 'WO' ? 'event-wo' : 'event-sr' }}" 
                                                 title="{{ $event['title'] }}">
                                                <div class="flex justify-between items-center mb-0.5">
                                                    <div class="font-bold truncate mr-1">
                                                        {{ $event['id'] }}
                                                    </div>
                                                    <div class="text-[7px] font-bold px-1 rounded bg-black bg-opacity-5 border border-black border-opacity-5 whitespace-nowrap">
                                                        {{ $event['full_data']->status }}
                                                    </div>
                                                </div>
                                                <div class="text-[9px] truncate opacity-80" title="{{ $event['full_data']->description }}">
                                                    {{ $event['full_data']->description }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @empty
                                    <!-- No events -->
                                @endforelse
                            </div>
                            
                            @if($totalPages > 1)
                                <div class="calendar-pagination">
                                    <button class="pag-btn prev-btn" data-dir="prev" disabled><i class="fas fa-chevron-left text-[8px]"></i></button>
                                    <span class="pag-info">1/{{ $totalPages }}</span>
                                    <button class="pag-btn next-btn" data-dir="next"><i class="fas fa-chevron-right text-[8px]"></i></button>
                                </div>
                            @endif
                        </div>
                        @php $currentDay->addDay(); @endphp
                    @endwhile
                </div>
            </div>
        </div>
    @else
        <!-- Original List View -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex justify-between items-center fade-in">
            <h2 class="text-lg font-bold text-gray-700">Filter Unit</h2>
            <form action="{{ route('weekly-meeting.index') }}" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="mode" value="list">
                <select name="unit" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none min-w-[200px]">
                    <option value="">Semua Unit</option>
                    @foreach($powerPlants as $plant)
                        <option value="{{ $plant->id }}" {{ $unitFilter == $plant->id ? 'selected' : '' }}>{{ $plant->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition-all shadow-sm">
                    TERAPKAN
                </button>
                @if($unitFilter)
                    <a href="{{ route('weekly-meeting.index', ['mode' => 'list']) }}" class="text-sm text-gray-500 hover:text-red-500 ml-2">Reset</a>
                @endif
            </form>
        </div>

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
                    {{ $reviewCompletedWOs->appends(['unit' => $unitFilter, 'review_created_page' => $reviewCreatedWOs->currentPage(), 'review_created_sr_page' => $reviewCreatedSRs->currentPage(), 'plan_pm_page' => $planPMs->currentPage(), 'plan_backlog_page' => $planBacklog->currentPage(), 'plan_urgent_page' => $urgentWork->currentPage()])->links() }}
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
                    {{ $reviewCreatedWOs->appends(['unit' => $unitFilter, 'review_completed_page' => $reviewCompletedWOs->currentPage(), 'review_created_sr_page' => $reviewCreatedSRs->currentPage(), 'plan_pm_page' => $planPMs->currentPage(), 'plan_backlog_page' => $planBacklog->currentPage(), 'plan_urgent_page' => $urgentWork->currentPage()])->links() }}
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
                    {{ $planPMs->appends(['unit' => $unitFilter, 'review_completed_page' => $reviewCompletedWOs->currentPage(), 'review_created_page' => $reviewCreatedWOs->currentPage(), 'plan_backlog_page' => $planBacklog->currentPage(), 'plan_urgent_page' => $urgentWork->currentPage()])->links() }}
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
                    {{ $urgentWork->appends(['unit' => $unitFilter, 'review_completed_page' => $reviewCompletedWOs->currentPage(), 'review_created_page' => $reviewCreatedWOs->currentPage(), 'plan_pm_page' => $planPMs->currentPage(), 'plan_backlog_page' => $planBacklog->currentPage()])->links() }}
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
                    {{ $planBacklog->appends(['unit' => $unitFilter, 'review_completed_page' => $reviewCompletedWOs->currentPage(), 'review_created_page' => $reviewCreatedWOs->currentPage(), 'plan_pm_page' => $planPMs->currentPage(), 'plan_urgent_page' => $urgentWork->currentPage()])->links() }}
                </div>
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
                     {{ $reviewCreatedSRs->appends(['unit' => $unitFilter, 'review_completed_page' => $reviewCompletedWOs->currentPage(), 'review_created_page' => $reviewCreatedWOs->currentPage(), 'plan_pm_page' => $planPMs->currentPage(), 'plan_backlog_page' => $planBacklog->currentPage(), 'plan_urgent_page' => $urgentWork->currentPage()])->links() }}
                </div>
            </div>

        </div>
    </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendar = document.querySelector('.calendar-grid');
    if (!calendar) return;

    calendar.addEventListener('click', function(e) {
        const btn = e.target.closest('.pag-btn');
        if (!btn) return;

        const dayCell = btn.closest('.calendar-day');
        const info = dayCell.querySelector('.pag-info');
        const pages = dayCell.querySelectorAll('.day-page');
        const prevBtn = dayCell.querySelector('.prev-btn');
        const nextBtn = dayCell.querySelector('.next-btn');

        let currentIndex = Array.from(pages).findIndex(p => !p.classList.contains('hidden'));
        const total = pages.length;

        if (btn.dataset.dir === 'next' && currentIndex < total - 1) {
            pages[currentIndex].classList.add('hidden');
            currentIndex++;
            pages[currentIndex].classList.remove('hidden');
        } else if (btn.dataset.dir === 'prev' && currentIndex > 0) {
            pages[currentIndex].classList.add('hidden');
            currentIndex--;
            pages[currentIndex].classList.remove('hidden');
        }

        // Update info & buttons
        info.textContent = `${currentIndex + 1}/${total}`;
        prevBtn.disabled = currentIndex === 0;
        nextBtn.disabled = currentIndex === total - 1;
    });
});
</script>
@endsection

