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
<!-- Wrap content in transition div -->
<div id="page-content" class="page-transition">
    <div class="w-full">
        <!-- Navbar -->
        <nav class="fixed w-full top-0 z-50">
            <div class="nav-background ">
                <div class="container mx-auto px-4">
                    <div class="flex justify-between items-center h-16">
                        <!-- Logo -->
                        <div class="flex items-center">
                            <a href="#" class="flex items-center">
                                <img src="{{ asset('logo/navlogo.png') }}" alt="Logo" class="h-8">
                            </a>
                        </div>

                        <!-- Menu Desktop -->
                        <div class="hidden md:flex items-center ">
                            <ul class="flex space-x-8">
                                <li><a href="#" class="nav-link">Home</a></li>
                                <li><a href="#map" class="nav-link">Peta Pembangkit</a></li>
                                <li><a href="#live-data" class="nav-link">Live Data</a></li>
                                <li><a href="{{ route('dashboard.pemantauan') }}" class="nav-link">Dashboard Pemantauan</a></li>
                                <li><a href="https://sites.google.com/view/pemeliharaan-upkendari" class="nav-link" target="_blank">Bid. Pemeliharaan</a></li>
                                <li><a href="{{ route('notulen.form') }}" class="nav-link">Notulen</a></li>
                                <li><a href="{{ route('calendar.index') }}" class="nav-link">
                                        <i class="fas fa-calendar-alt mr-1"></i> Calendar
                                    </a></li>
                                <li>
                                    <a href="{{ route('kinerja.pemeliharaan') }}" class="nav-link">
                                        <i class="fas fa-chart-line mr-1"></i> Kinerja Pemeliharaan
                                    </a>
                                </li>

                                <!-- Login button -->
                                <li>
                                    <a href="{{ route('login') }}" class="login-button">
                                        <i class="fas fa-user mr-2"></i> Login
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Menu Mobile -->
                        <div class="md:hidden">
                            <button id="mobile-menu-button" class="text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Mobile Menu -->
                    <div id="mobile-menu" class="hidden md:hidden pb-4">
                        <ul class="space-y-4">
                            <li><a href="#" class="nav-link-mobile">Home</a></li>
                            <li><a href="#map" class="nav-link-mobile">Peta Pembangkit</a></li>
                            <li><a href="#live-data" class="nav-link-mobile">Live Data Unit Operasional</a></li>
                            <li><a href="{{ route('dashboard.pemantauan') }}" class="nav-link-mobile">Dashboard Pemantauan</a></li>
                            <li><a href="https://sites.google.com/view/pemeliharaan-upkendari" class="nav-link-mobile" target="_blank">Bid. Pemeliharaan</a></li>
                            <li><a href="{{ route('notulen.form') }}" class="nav-link-mobile">Notulen</a></li>
                            <li><a href="{{ route('calendar.index') }}" class="nav-link-mobile">
                                    <i class="fas fa-calendar-alt mr-1"></i> Calendar
                                </a></li>
                            <li>
                                <a href="{{ route('kinerja.pemeliharaan') }}" class="nav-link">
                                    <i class="fas fa-chart-line mr-1"></i> Kinerja Pemeliharaan
                                </a>
                            </li>
                            <!-- Login button in mobile -->
                            <li>
                                <a href="{{ route('login') }}" class="nav-link-mobile login-mobile">
                                    <i class="fas fa-user mr-2"></i> Login
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="h-[80px]"></div>

        <div class="calendar-container">
            <!-- Navigasi Bulan & Tahun -->
            <div class="flex flex-col md:flex-row items-center justify-between mb-4 gap-2">
                <div class="flex gap-2">
                    <a href="{{ route('calendar.index', ['month' => $month == 1 ? 12 : $month - 1, 'year' => $month == 1 ? $year - 1 : $year]) }}" class="calendar-nav-btn">&laquo; Bulan Sebelumnya</a>
                </div>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-lg">{{ \Carbon\Carbon::create($year, $month, 1)->translatedFormat('F Y') }}</span>
                    <form method="GET" action="" class="inline-block">
                        <select name="year" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm w-20">
                            @for($y = $year-5; $y <= $year+5; $y++)
                                <option value="{{ $y }}" @if($y==$year) selected @endif>{{ $y }}</option>
                                @endfor
                        </select>
                        <input type="hidden" name="month" value="{{ $month }}">
                    </form>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('calendar.index', ['month' => $month == 12 ? 1 : $month + 1, 'year' => $month == 12 ? $year + 1 : $year]) }}" class="calendar-nav-btn">Bulan Berikutnya &raquo;</a>
                </div>
            </div>

            <div class="calendar-header">
                <h2 class="text-2xl font-bold text-gray-800">Calendar SR/WO</h2>
            </div>

            {{-- Hapus form filter tanggal dan ganti dengan navigasi di atas --}}
            @php
            use Carbon\Carbon;
            \Carbon\Carbon::setLocale('id');
            $daysOfWeek = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
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
                        $status = strtolower($event['status']);
                        $border = $status === 'closed' ? 'border-2 border-green-500' : 'border-2 border-red-500';
                        $bg = $status === 'closed' ? 'bg-green-50' : 'bg-red-50';
                        @endphp
                        <div class="event-item-mini {{ $bg }} {{ $border }} px-1 py-1 mb-1 rounded flex flex-col gap-0.5">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-xs">#{{ $event['id'] }}</span>
                                <span class="text-[10px] px-1 py-0.5 rounded {{ $status === 'closed' ? 'bg-green-300 text-green-900' : 'bg-red-300 text-red-900' }}">{{ ucfirst($event['status']) }}</span>
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
                    </div>
                    <!-- Mobile: tampilkan simbol saja, detail di popup -->
                    <div class="flex-1 flex flex-col gap-1 md:hidden">
                        @if(count($dateEvents) > 0)
                        <button
                            type="button"
                            class="w-6 h-6 mx-auto my-2 rounded-full flex items-center justify-center text-white text-xs font-bold focus:outline-none focus:ring-2 focus:ring-blue-400"
                            style="background: #0A749B;"
                            data-events='@json($dateEvents)'
                            data-maintenance='@json(($maintenanceEvents[$date] ?? collect())->values())'
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
        const date = button.getAttribute('data-date');
        showEventPopup(events, date, maint);
    }

    function showEventPopup(events, date, maintenance) {
        const popup = document.getElementById('eventPopup');
        const content = document.getElementById('popupContent');
        const dateTitle = document.getElementById('popupDate');

        dateTitle.textContent = 'Event: ' + date;
        let html = '';

        if (events.length === 0) {
            html = '<div class="text-gray-400 text-sm">Tidak ada event</div>';
        } else {
            events.forEach(function(event, idx) {
                // Highlight open (red) and closed (green)
                let status = (event.status || '').toLowerCase();
                let highlightClass = '';
                let highlightBg = '';
                if (status === 'open') {
                    highlightClass = 'border border-red-400 bg-red-50';
                    highlightBg = 'bg-red-300 text-red-900';
                } else if (status === 'closed') {
                    highlightClass = 'border border-green-400 bg-green-50';
                    highlightBg = 'bg-green-300 text-green-900';
                } else {
                    highlightClass = 'border border-gray-300 bg-gray-50';
                    highlightBg = 'bg-gray-300 text-gray-900';
                }

                html += `<div class="mb-3 border-b pb-2 ${highlightClass} rounded px-2 py-1">
                    <div class="font-bold text-blue-700 text-xs mb-1">#${event.id} - ${event.type}</div>
                    <div class="text-xs text-gray-700 mb-1">${event.description}</div>
                    <div class="text-[11px] text-gray-500 mb-1">
                        Status: <b class="px-1 py-0.5 rounded ${highlightBg}">${event.status}</b>
                    </div>
                    <div class="text-[11px] text-gray-500 mb-1">Unit: <b>${event.power_plant_name}</b></div>
                    <div class="text-[11px] text-gray-500 mb-1">Priority: <b>${event.priority ?? '-'}</b></div>
                    <div class="text-[11px] text-gray-500 mb-1">Start: <b>${event.schedule_start?.substring(0,10) ?? '-'}</b></div>
                    <div class="text-[11px] text-gray-500 mb-1">Finish: <b>${event.schedule_finish?.substring(0,10) ?? '-'}</b></div>
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