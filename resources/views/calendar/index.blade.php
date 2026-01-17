@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/calender.css') }}">

<style>
    .nav-link {
        color: #ffffff;
        text-decoration: none;
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
        position: relative;
        font-weight: 500;
    }

    .nav-link:hover {
        color: #4299e1;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: -2px;
        left: 50%;
        background-color: #4299e1;
        transition: all 0.3s ease;
        transform: translateX(-50%);
    }

    .nav-link:hover::after {
        width: 100%;
    }

    .nav-link.active {
        color: #A8D600;
    }

    .nav-link.active::after {
        width: 100%;
    }

    /* Mobile Menu Styles */
    .nav-link-mobile {
        display: block;
        padding: 0.75rem 1rem;
        color: #ffffff;
        text-decoration: none;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .nav-link-mobile:hover {
        background-color: #2d3748;
        color: #A8D600;
        border-left-color: #A8D600;
    }

    /* Dark mode is now default */
    .nav-link,
    .nav-link-mobile {
        color: #ffffff;
    }

    .nav-link-mobile:hover {
        background-color: #2d3748;
    }

    .nav-link {
        color: #ffffff !important;
        text-align: center;
    }

    .nav-link-mobile {
        color: #ffffff !important;
    }

    .nav-background {
        background-color: #1a1a1a !important;
    }

    /* Tambahan style untuk tombol login */
    .login-button {
        background-color: #4299e1;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .login-button:hover {
        background-color: #3182ce;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
            0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    /* Style untuk login di mobile menu */
    .login-mobile {
        background-color: #4299e1;
        color: white !important;
        border-radius: 0.375rem;
        margin: 0.5rem 1rem;
    }

    .login-mobile:hover {
        background-color: #3182ce !important;
        border-left-color: transparent !important;
    }

    /* Memastikan icon font-awesome sejajar dengan teks */
    .fas {
        display: inline-flex;
        align-items: center;
    }

    /* Adjust body and main content positioning */
    body {
        margin: 0;
        padding-top: 100px;
        min-height: calc(100vh - 100px);
        overflow-x: hidden;
    }

    main {
        margin-top: 80px;
        /* Sesuaikan dengan tinggi navbar */
    }

    #eventPopup.show {
        opacity: 1 !important;
        pointer-events: auto !important;
    }

    #eventPopup .popup-content {
        transform: scale(1) !important;
    }
</style>
@endsection

@section('content')
<!-- Navbar -->
@include('components.navbar')
<!-- Wrap content in transition div -->
<div id="page-content" class="page-transition">
    <div class="w-full">
        <div class="h-[80px]"></div>

        <div class="calendar-container">
            <!-- Navigasi Bulan & Tahun dengan Filter -->
            <div class="flex flex-col md:flex-row items-center justify-between mb-4 gap-2 flex-wrap">
                <div class="flex gap-2 items-center flex-wrap">
                    <a href="{{ route('calendar.index', array_merge(['month' => $month == 1 ? 12 : $month - 1, 'year' => $month == 1 ? $year - 1 : $year], array_filter(['status' => $statusFilter, 'worktype' => $workTypeFilter]))) }}" class="calendar-nav-btn">&laquo; Bulan Sebelumnya</a>
                    
                    <!-- Filter Status & Work Type -->
                    <form method="GET" action="{{ route('calendar.index') }}" id="filterForm" class="flex items-center gap-2">
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        
                        <select name="status" onchange="document.getElementById('filterForm').submit()" class="border rounded px-2 py-1 text-sm w-32">
                            <option value="">Semua Status</option>
                            @foreach($statusOptions as $status)
                                <option value="{{ $status }}" @if($statusFilter == $status) selected @endif>{{ $status }}</option>
                            @endforeach
                        </select>
                        
                        <select name="worktype" onchange="document.getElementById('filterForm').submit()" class="border rounded px-2 py-1 text-sm w-40 min-w-[12rem] lg:w-64">
                            <option value="">Semua Work Type</option>
                            @foreach($workTypeOptions as $workType)
                                <option value="{{ $workType }}" @if($workTypeFilter == $workType) selected @endif>{{ $workType }}</option>
                            @endforeach
                        </select>
                        
                        @if($statusFilter || $workTypeFilter)
                            <a href="{{ route('calendar.index', ['month' => $month, 'year' => $year]) }}" class="px-3 py-1 bg-gray-500 text-white rounded text-sm hover:bg-gray-600">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>
                
                <div class="flex items-center gap-2">
                    <span class="font-bold text-lg">{{ \Carbon\Carbon::create($year, $month, 1)->translatedFormat('F Y') }}</span>
                    <form method="GET" action="{{ route('calendar.index') }}" class="inline-block">
                        <select name="year" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm w-20">
                            @for($y = $year-5; $y <= $year+5; $y++)
                                <option value="{{ $y }}" @if($y==$year) selected @endif>{{ $y }}</option>
                            @endfor
                        </select>
                        <input type="hidden" name="month" value="{{ $month }}">
                        @if($statusFilter)
                            <input type="hidden" name="status" value="{{ $statusFilter }}">
                        @endif
                        @if($workTypeFilter)
                            <input type="hidden" name="worktype" value="{{ $workTypeFilter }}">
                        @endif
                    </form>
                </div>
                
                <div class="flex gap-2">
                    <a href="{{ route('calendar.index', array_merge(['month' => $month == 12 ? 1 : $month + 1, 'year' => $month == 12 ? $year + 1 : $year], array_filter(['status' => $statusFilter, 'worktype' => $workTypeFilter]))) }}" class="calendar-nav-btn">Bulan Berikutnya &raquo;</a>
                </div>
            </div>

            <div class="calendar-header flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
                <h2 class="text-2xl font-bold text-gray-800">Calendar SR/WO</h2>
                
                {{-- Presentasi Work Type - Satu Baris Kecil --}}
                @if(isset($workTypeStats) && count($workTypeStats) > 0)
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs text-gray-600 font-semibold">Work Type:</span>
                    <div class="flex items-center gap-2 flex-wrap">
                        @foreach($workTypeStats as $workType => $stat)
                        <div class="flex items-center gap-1 bg-gray-100 border border-gray-300 rounded px-2 py-1">
                            <span class="text-xs font-bold text-blue-600">{{ $stat['percentage'] }}%</span>
                            <span class="text-xs text-gray-700">{{ $workType }}</span>
                            <span class="text-[10px] text-gray-500">({{ $stat['count'] }})</span>
                        </div>
                        @endforeach
                    </div>
                    <span class="text-xs text-gray-500">Total: <strong>{{ $totalWO }}</strong></span>
                </div>
                @endif
            </div>

            {{-- Hapus form filter tanggal dan ganti dengan navigasi di atas --}}
            @php
            use Carbon\Carbon;
            \Carbon\Carbon::setLocale('id');
            $daysOfWeek = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            $firstDayOfMonth = Carbon::create($year, $month, 1);
            $startDayOfWeek = $firstDayOfMonth->dayOfWeekIso; // 1=Senin, 7=Minggu
            $totalDays = $lastDay->day;
            @endphp
            <div class="calendar-grid-month w-full bg-white rounded-lg shadow p-4">
                <div class="grid grid-cols-7 gap-1 mb-2">
                    @foreach($daysOfWeek as $day)
                    <div class="text-center font-semibold text-xs text-gray-600 py-1">{{ $day }}</div>
                    @endforeach
                </div>
                <div class="grid grid-cols-7 gap-1">
                    {{-- Padding awal jika hari pertama bukan Senin --}}
                    @for($i = 1; $i < $startDayOfWeek; $i++)
                        <div>
                </div>
                @endfor
                {{-- Render semua tanggal --}}
                @foreach($events as $date => $dateEvents)
                <div class="date-card-mini border rounded-md p-1 min-h-[70px] bg-gray-50 flex flex-col">
                    <div class="text-xs font-bold text-blue-700 mb-1 text-right">{{ Carbon::parse($date)->day }}</div>
                    <!-- Desktop: tampilkan detail event seperti biasa -->
                    <div class="flex-1 flex flex-col gap-1 hidden md:flex">
                        @forelse($dateEvents as $event)
                        @php
                        $status = strtoupper($event['status'] ?? '');
                        // Warna khusus per status WO dari Maximo: WAPPR, APPR, INPRG, COMP, CLOSE
                        if ($status === 'CLOSE' || $status === 'COMP') {
                            // Completed/Closed -> Hijau
                            $border = 'border-2 border-green-500';
                            $bg = 'bg-green-50';
                            $badge = 'bg-green-300 text-green-900';
                        } elseif ($status === 'INPRG') {
                            // In Progress -> Kuning/Orange
                            $border = 'border-2 border-yellow-500';
                            $bg = 'bg-yellow-50';
                            $badge = 'bg-yellow-300 text-yellow-900';
                        } elseif ($status === 'APPR') {
                            // Approved -> Biru muda
                            $border = 'border-2 border-blue-400';
                            $bg = 'bg-blue-50';
                            $badge = 'bg-blue-200 text-blue-900';
                        } elseif ($status === 'WAPPR') {
                            // Waiting Approval -> Biru
                            $border = 'border-2 border-blue-500';
                            $bg = 'bg-blue-50';
                            $badge = 'bg-blue-300 text-blue-900';
                        } else {
                            // Status lainnya -> Abu-abu
                            $border = 'border-2 border-gray-500';
                            $bg = 'bg-gray-50';
                            $badge = 'bg-gray-300 text-gray-900';
                        }
                        @endphp
                        <div class="event-item-mini {{ $bg }} {{ $border }} px-1 py-1 mb-1 rounded flex flex-col gap-0.5">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-xs">#{{ $event['id'] }}</span>
                                <div class="flex gap-1 items-center">
                                    <span class="text-[10px] px-1 py-0.5 rounded {{ $badge }}">{{ ucfirst($event['status']) }}</span>
                                    @if(isset($event['backlog_status']) && $event['backlog_status'] !== null)
                                        @if($event['backlog_status'] === 'overdue')
                                            <span class="text-[9px] px-1 py-0.5 rounded bg-red-600 text-white font-bold" title="Sudah backlog: {{ (int)$event['backlog_days'] }} hari">
                                                ❌ {{ (int)$event['backlog_days'] }}h
                                            </span>
                                        @elseif($event['backlog_status'] === 'warning')
                                            <span class="text-[9px] px-1 py-0.5 rounded bg-orange-500 text-white font-bold" title="Akan backlog dalam {{ (int)$event['backlog_days'] }} hari">
                                                ⚠️ {{ (int)$event['backlog_days'] }}h
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="font-semibold text-[11px]">{{ $event['type'] }}</div>
                            <div class="text-[10px] text-gray-700 event-desc">{{ $event['description'] }}</div>
                            <div class="flex flex-wrap gap-1 mt-1">
                                <span class="text-[9px] text-gray-500">Unit: <b>{{ $event['power_plant_name'] }} </b></span>
                                <span class="text-[9px] text-gray-500">Type: <b>{{ $event['type'] }}</b></span>
                                <span class="text-[9px] text-gray-500">Priority: <b>{{ $event['priority'] ?? '-' }}</b></span>
                            </div>
                            <div class="flex flex-col text-[9px] text-gray-500">
                                <span>Start: {{ isset($event['schedule_start']) ? \Carbon\Carbon::parse($event['schedule_start'])->format('d/m/Y') : '-' }}</span>
                                <span>Finish: {{ isset($event['schedule_finish']) ? \Carbon\Carbon::parse($event['schedule_finish'])->format('d/m/Y') : '-' }}</span>
                                @if(isset($event['is_backlog']) && $event['is_backlog'])
                                    <span class="text-red-600 font-bold">❌ Sudah backlog: {{ (int)$event['backlog_days'] }} hari</span>
                                @elseif(isset($event['backlog_status']) && $event['backlog_status'] === 'warning')
                                    <span class="text-orange-600 font-bold">⚠️ Akan backlog: {{ (int)$event['backlog_days'] }} hari lagi</span>
                                @endif
                                <span>Labor: <b>{{ $event['labor'] ?? '-' }}</b></span>
                            </div>
                        </div>
                        @empty
                        <span class="text-gray-300 text-[10px] italic">-</span>
                        @endforelse

                        @php $dateMaint = ($maintenanceEvents[$date] ?? collect()); @endphp
                        @if($dateMaint->count() > 0)
                            <div class="mt-1 pt-1 border-t border-dashed">
                                <div class="text-[10px] font-semibold text-orange-600 mb-1">Maintenance</div>
                                @foreach($dateMaint as $m)
                                    <div class="bg-yellow-50 border-2 border-yellow-400 px-1 py-1 mb-1 rounded">
                                        <div class="flex justify-between items-center">
                                            <span class="font-bold text-xs">{{ $m['type'] }}</span>
                                            <span class="text-[10px] px-1 py-0.5 rounded bg-yellow-300 text-yellow-900">Alert</span>
                                        </div>
                                        <div class="text-[10px] text-gray-700">{{ $m['description'] }}</div>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            <span class="text-[9px] text-gray-500">Unit: <b>{{ $m['power_plant_name'] ?: '-' }}</b></span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @php $dateBacklogs = ($backlogEvents[$date] ?? collect()); @endphp
                        @if($dateBacklogs->count() > 0)
                            <div class="mt-1 pt-1 border-t border-dashed">
                                <div class="text-[10px] font-semibold text-purple-600 mb-1">Backlog</div>
                                @foreach($dateBacklogs as $b)
                                    <div class="bg-purple-50 border-2 border-purple-400 px-1 py-1 mb-1 rounded">
                                        <div class="flex justify-between items-center">
                                            <span class="font-bold text-xs">{{ $b['id'] }}</span>
                                            <span class="text-[10px] px-1 py-0.5 rounded bg-purple-300 text-purple-900">Backlog</span>
                                        </div>
                                        <div class="font-semibold text-[11px]">{{ $b['type'] }}</div>
                                        <div class="text-[10px] text-gray-700">{{ $b['description'] }}</div>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            <span class="text-[9px] text-gray-500">Unit: <b>{{ $b['power_plant_name'] ?? '-' }}</b></span>
                                            <span class="text-[9px] text-gray-500">Status: <b>{{ $b['status'] ?? '-' }}</b></span>
                                            <span class="text-[9px] text-gray-500">Labor: <b>{{ $b['labor'] ?? '-' }}</b></span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <!-- Mobile: tampilkan simbol saja, detail di popup -->
                    <div class="flex-1 flex flex-col gap-1 md:hidden">
                        @php $hasMaint = ($maintenanceEvents[$date] ?? collect())->count() > 0; @endphp
                        @php $hasBacklog = ($backlogEvents[$date] ?? collect())->count() > 0; @endphp
                        @if(count($dateEvents) > 0 || $hasMaint || $hasBacklog)
                        <button
                            type="button"
                            class="w-6 h-6 mx-auto my-2 rounded-full flex items-center justify-center text-white text-xs font-bold focus:outline-none focus:ring-2 focus:ring-blue-400"
                            style="background: #0A749B;"
                            data-events='@json($dateEvents)'
                            data-maintenance='@json(($maintenanceEvents[$date] ?? collect())->values())'
                            data-backlogs='@json(($backlogEvents[$date] ?? collect())->values())'
                            data-date="{{ Carbon::parse($date)->format('d/m/Y') }}"
                            onclick="handlePopupClick(this)">
                            <i class="fas fa-calendar-alt"></i>
                        </button>

                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
</div>

<!-- Modal Popup untuk Mobile -->
<div id="eventPopup" class="fixed inset-0 flex items-center justify-center z-50 transition-opacity duration-300 ease-in-out opacity-0 pointer-events-none">
    <div class="bg-white p-6 rounded-lg shadow-lg w-11/12 md:w-1/2 max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300 ease-in-out">
        <div class="flex justify-between items-center mb-4">
            <h2 id="popupDate" class="text-lg font-semibold">Event</h2>
            <button onclick="closeEventPopup()" class="text-gray-600 hover:text-red-600 text-xl font-bold">&times;</button>
        </div>
        <div id="popupContent" class="text-sm text-gray-700">
            <!-- Konten diisi dari JS -->
        </div>
    </div>
</div>


<!-- Include the toggle.js script for mobile menu functionality -->

@push('scripts')
<script src="{{ asset('js/toggle.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        if (menuButton && mobileMenu) {
            menuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
    });

    function handlePopupClick(button) {
        const events = JSON.parse(button.getAttribute('data-events'));
        const maint = JSON.parse(button.getAttribute('data-maintenance') || '[]');
        const backs = JSON.parse(button.getAttribute('data-backlogs') || '[]');
        const date = button.getAttribute('data-date');
        showEventPopup(events, date, maint, backs);
    }

    function showEventPopup(events, date, maintenance, backlogs) {
        const popup = document.getElementById('eventPopup');
        const content = document.getElementById('popupContent');
        const dateTitle = document.getElementById('popupDate');

        dateTitle.textContent = 'Event: ' + date;
        let html = '';

        if (events.length === 0) {
            html = '<div class="text-gray-400 text-sm">Tidak ada event</div>';
        } else {
            events.forEach(function(event, idx) {
                // Warna berdasarkan status Maximo: WAPPR, APPR, INPRG, COMP, CLOSE
                let status = (event.status || '').toUpperCase();
                let highlightClass = '';
                let highlightBg = '';
                if (status === 'CLOSE' || status === 'COMP') {
                    // Completed/Closed -> Hijau
                    highlightClass = 'border border-green-400 bg-green-50';
                    highlightBg = 'bg-green-300 text-green-900';
                } else if (status === 'INPRG') {
                    // In Progress -> Kuning
                    highlightClass = 'border border-yellow-400 bg-yellow-50';
                    highlightBg = 'bg-yellow-300 text-yellow-900';
                } else if (status === 'APPR') {
                    // Approved -> Biru muda
                    highlightClass = 'border border-blue-400 bg-blue-50';
                    highlightBg = 'bg-blue-200 text-blue-900';
                } else if (status === 'WAPPR') {
                    // Waiting Approval -> Biru
                    highlightClass = 'border border-blue-400 bg-blue-50';
                    highlightBg = 'bg-blue-300 text-blue-900';
                } else {
                    // Status lainnya -> Abu-abu
                    highlightClass = 'border border-gray-300 bg-gray-50';
                    highlightBg = 'bg-gray-300 text-gray-900';
                }

                // Backlog badge
                let backlogBadge = '';
                if (event.backlog_status === 'overdue') {
                    const days = Math.floor(event.backlog_days || 0);
                    backlogBadge = `<span class="text-[9px] px-1 py-0.5 rounded bg-red-600 text-white font-bold ml-1" title="Sudah backlog: ${days} hari">❌ ${days}h</span>`;
                } else if (event.backlog_status === 'warning') {
                    const days = Math.floor(event.backlog_days || 0);
                    backlogBadge = `<span class="text-[9px] px-1 py-0.5 rounded bg-orange-500 text-white font-bold ml-1" title="Akan backlog dalam ${days} hari">⚠️ ${days}h</span>`;
                }

                html += `<div class="mb-3 border-b pb-2 ${highlightClass} rounded px-2 py-1">
                    <div class="font-bold text-blue-700 text-xs mb-1">#${event.id} - ${event.type}</div>
                    <div class="text-xs text-gray-700 mb-1">${event.description}</div>
                    <div class="text-[11px] text-gray-500 mb-1 flex items-center">
                        Status: <b class="px-1 py-0.5 rounded ${highlightBg}">${event.status}</b>${backlogBadge}
                    </div>
                    <div class="text-[11px] text-gray-500 mb-1">Unit: <b>${event.power_plant_name}</b></div>
                    <div class="text-[11px] text-gray-500 mb-1">Priority: <b>${event.priority ?? '-'}</b></div>
                    <div class="text-[11px] text-gray-500 mb-1">Start: <b>${event.schedule_start?.substring(0,10) ?? '-'}</b></div>
                    <div class="text-[11px] text-gray-500 mb-1">Finish: <b>${event.schedule_finish?.substring(0,10) ?? '-'}</b></div>
                    ${event.is_backlog ? `<div class="text-[11px] text-red-600 font-bold mb-1">❌ Sudah backlog: ${Math.floor(event.backlog_days || 0)} hari</div>` : ''}
                    ${event.backlog_status === 'warning' ? `<div class="text-[11px] text-orange-600 font-bold mb-1">⚠️ Akan backlog: ${Math.floor(event.backlog_days || 0)} hari lagi</div>` : ''}
                    <div class="text-[11px] text-gray-500 mb-1">Labor: <b>${event.labor ?? '-'}</b></div>
                </div>`;
            });
        }

        if (maintenance && maintenance.length > 0) {
            html += '<div class="mt-3 pt-2 border-t"><div class="text-sm font-semibold text-orange-600 mb-2">Maintenance</div>';
            maintenance.forEach(function(m) {
                html += `
                    <div class="mb-2 border border-yellow-300 bg-yellow-50 rounded px-2 py-1">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-xs">${m.type}</span>
                            <span class="text-[10px] px-1 py-0.5 rounded bg-yellow-300 text-yellow-900">Alert</span>
                        </div>
                        <div class="text-[11px] text-gray-700">${m.description}</div>
                        <div class="text-[10px] text-gray-500">Unit: <b>${m.power_plant_name || '-'}</b></div>
                    </div>
                `;
            });
            html += '</div>';
        }

        if (backlogs && backlogs.length > 0) {
            html += '<div class="mt-3 pt-2 border-t"><div class="text-sm font-semibold text-purple-600 mb-2">Backlog</div>';
            backlogs.forEach(function(b) {
                html += `
                    <div class="mb-2 border border-purple-300 bg-purple-50 rounded px-2 py-1">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-xs">${b.id}</span>
                            <span class="text-[10px] px-1 py-0.5 rounded bg-purple-300 text-purple-900">Backlog</span>
                        </div>
                        <div class="text-[11px] text-gray-700">${b.type}</div>
                        <div class="text-[11px] text-gray-700">${b.description || ''}</div>
                        <div class="text-[10px] text-gray-500">Unit: <b>${b.power_plant_name || '-'}</b> | Status: <b>${b.status || '-'}</b></div>
                    </div>
                `;
            });
            html += '</div>';
        }

        content.innerHTML = html;

        // Tampilkan dengan animasi
        popup.classList.add('show');
    }

    function closeEventPopup() {
        const popup = document.getElementById('eventPopup');
        popup.classList.remove('show');
    }
</script>
@endpush

@endsection