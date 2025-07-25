@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/calender.css') }}">
@endsection

@section('content')
<!-- Wrap content in transition div -->
<div id="page-content" class="page-transition">
    <div class="w-full">
         <!-- Navbar -->
 <nav class="fixed w-full top-0 z-50">
    <div class="nav-background shadow-lg">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
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
                                <option value="{{ $y }}" @if($y == $year) selected @endif>{{ $y }}</option>
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
                        <div></div>
                    @endfor
                    {{-- Render semua tanggal --}}
                    @foreach($events as $date => $dateEvents)
                        <div class="date-card-mini border rounded-md p-1 min-h-[70px] bg-gray-50 flex flex-col">
                            <div class="text-xs font-bold text-blue-700 mb-1 text-right">{{ Carbon::parse($date)->day }}</div>
                            <div class="flex-1 flex flex-col gap-1">
                                @forelse($dateEvents as $event)
                                    @php
                                        $status = strtolower($event['status']);
                                        $border = $status === 'open' ? 'border-2 border-yellow-400' : ($status === 'closed' ? 'border-2 border-green-500' : 'border');
                                        $bg = $status === 'open' ? 'bg-yellow-50' : ($status === 'closed' ? 'bg-green-50' : 'bg-gray-100');
                                    @endphp
                                    <div class="event-item-mini {{ $bg }} {{ $border }} px-1 py-1 mb-1 rounded flex flex-col gap-0.5">
                                        <div class="flex justify-between items-center">
                                            <span class="font-bold text-xs">#{{ $event['id'] }}</span>
                                            <span class="text-[10px] px-1 py-0.5 rounded {{ $status === 'open' ? 'bg-yellow-300 text-yellow-900' : ($status === 'closed' ? 'bg-green-300 text-green-900' : 'bg-gray-300 text-gray-800') }}">{{ ucfirst($event['status']) }}</span>
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
                                        </div>
                                    </div>
                                @empty
                                    <span class="text-gray-300 text-[10px] italic">-</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include the toggle.js script for mobile menu functionality -->
<script src="{{ asset('js/toggle.js') }}"></script>
@endsection
