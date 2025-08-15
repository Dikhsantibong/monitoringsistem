@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/calender.css') }}">
<style>
    .calendar-fixed-layout {
        display: flex;
        min-height: 100vh;
        background: #f9fafb;
    }
    .calendar-sidebar-fixed {
        position: sticky;
        top: 0;
        height: 100vh;
        z-index: 30;
        flex-shrink: 0;
    }
    .calendar-header-fixed {
        position: sticky;
        top: 0;
        z-index: 20;
        background: #fff;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    .calendar-main-scroll {
        height: calc(100vh - 64px);
        /* 64px = header height, adjust if needed */
        overflow-y: auto;
        padding: 24px;
        background: #f9fafb;
    }
    @media (max-width: 768px) {
        .calendar-main-scroll {
            height: auto;
            min-height: 100vh;
            padding: 12px;
        }
    }
</style>
@endsection

@section('content')
<div class="calendar-fixed-layout">
    <div class="calendar-sidebar-fixed hidden md:block">
        @include('components.pemeliharaan-sidebar')
    </div>
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Header -->
        <header class="calendar-header-fixed w-full">
            <div class="flex justify-between items-center px-6 py-3">
                <div class="flex items-center gap-x-3">
                    <button id="mobile-menu-toggle"
                        class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#009BB9] hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800">Kalender Pemeliharaan</h1>
                </div>
                <div class="flex items-center gap-x-4 relative">
                    <div class="relative">
                        <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                            <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                                class="w-8 h-8 rounded-full mr-2">
                            <span class="text-gray-700">{{ Auth::user()->name }}</span>
                            <i class="fas fa-caret-down ml-2"></i>
                        </button>
                        <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                            <a href="{{ route('user.profile') }}"
                                class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <main class="calendar-main-scroll flex-1 min-w-0">
            <div class="calendar-container">
                <div class="flex flex-col md:flex-row items-center justify-between mb-4 gap-2">
                    <div class="flex gap-2">
                        <a href="{{ route('pemeliharaan.calendar', ['month' => $month == 1 ? 12 : $month - 1, 'year' => $month == 1 ? $year - 1 : $year]) }}" class="calendar-nav-btn">&laquo; Bulan Sebelumnya</a>
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
                        <a href="{{ route('pemeliharaan.calendar', ['month' => $month == 12 ? 1 : $month + 1, 'year' => $month == 12 ? $year + 1 : $year]) }}" class="calendar-nav-btn">Bulan Berikutnya &raquo;</a>
                    </div>
                </div>
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
                        @for($i = 1; $i < $startDayOfWeek; $i++)
                            <div></div>
                        @endfor
                        @for($d = 1; $d <= $totalDays; $d++)
                            @php
                                $date = Carbon::create($year, $month, $d)->toDateString();
                                $dateEvents = $events[$date] ?? [];
                                $dateBacklogs = $backlogEvents[$date] ?? [];
                            @endphp
                            <div class="date-card-mini border rounded-md p-1 min-h-[70px] bg-gray-50 flex flex-col">
                                <div class="text-xs font-bold text-blue-700 mb-1 text-right">{{ $d }}</div>
                                <div class="flex-1 flex flex-col gap-1">
                                    @foreach($dateEvents as $event)
                                        @php
                                            $status = strtolower($event['status']);
                                            if ($status === 'closed') {
                                                $border = 'border-2 border-green-500';
                                                $bg = 'bg-green-50';
                                                $badge = 'bg-green-300 text-green-900';
                                            } elseif ($status === 'wmatl') {
                                                $border = 'border-2 border-blue-500';
                                                $bg = 'bg-blue-50';
                                                $badge = 'bg-blue-300 text-blue-900';
                                            } else {
                                                $border = 'border-2 border-red-500';
                                                $bg = 'bg-red-50';
                                                $badge = 'bg-red-300 text-red-900';
                                            }
                                        @endphp
                                        <div class="event-item-mini {{ $bg }} {{ $border }} px-1 py-1 mb-1 rounded flex flex-col gap-0.5">
                                            <div class="flex justify-between items-center">
                                                <span class="font-bold text-xs">#{{ $event['id'] }}</span>
                                                <span class="text-[10px] px-1 py-0.5 rounded {{ $badge }}">{{ ucfirst($event['status']) }}</span>
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
                                    @endforeach
                                    @if(count($dateBacklogs) > 0)
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
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<script>
    function toggleDropdown() {
        var dropdown = document.getElementById('dropdown');
        dropdown.classList.toggle('hidden');
    }
    document.addEventListener('click', function(event) {
        var userDropdown = document.getElementById('dropdown');
        var userBtn = document.getElementById('dropdownToggle');
        if (userDropdown && !userDropdown.classList.contains('hidden') && !userBtn.contains(event.target) && !userDropdown.contains(event.target)) {
            userDropdown.classList.add('hidden');
        }
    });
</script>
@endsection
