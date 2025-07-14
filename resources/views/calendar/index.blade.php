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
                            <a href="{{ url('/') }}" class="flex items-center">
                                <img src="{{ asset('logo/navlogo.png') }}" alt="Logo" class="h-8">
                            </a>
                        </div>

                        <!-- Menu Desktop -->
                        <div class="hidden md:flex items-center">
                            <ul class="flex space-x-8">
                                <li><a href="{{ url('/') }}" class="nav-link">Home</a></li>
                                <li><a href="{{ url('/#map') }}" class="nav-link">Peta Pembangkit</a></li>
                                <li><a href="{{ url('/#live-data') }}" class="nav-link">Live Data Unit Operasional</a></li>
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
                            <li><a href="{{ url('/') }}" class="nav-link-mobile">Home</a></li>
                            <li><a href="{{ url('/#map') }}" class="nav-link-mobile">Peta Pembangkit</a></li>
                            <li><a href="{{ url('/#live-data') }}" class="nav-link-mobile">Live Data Unit Operasional</a></li>
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
            <div class="calendar-header">
                <h2 class="text-2xl font-bold text-gray-800">Calendar Events</h2>
                <div class="calendar-nav">
                    <button class="calendar-nav-btn">
                        <i class="fas fa-chevron-left"></i> Previous
                    </button>
                    <button class="calendar-nav-btn">
                        Today
                    </button>
                    <button class="calendar-nav-btn">
                        Next <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            @php
                use Carbon\Carbon;
                \Carbon\Carbon::setLocale('id');
            @endphp
            @if($events->isEmpty())
                <div class="no-events">
                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                    <p class="text-lg">No events found</p>
                </div>
            @else
                <div class="calendar-grid">
                    @foreach($events as $date => $dateEvents)
                        <div class="date-card">
                            <div class="date-header">
                                {{ Carbon::parse($date)->translatedFormat('l, d F Y') }}
                            </div>
                            <div class="date-content">
                                @foreach($dateEvents as $event)
                                    <div class="event-item {{ Str::contains($event['type'], 'Work Order') ? 'work-order' : 'service-request' }}">
                                        <div class="event-type">
                                            {{ $event['type'] }}
                                            <span class="event-status status-{{ strtolower($event['status']) }}">
                                                {{ $event['status'] }}
                                            </span>
                                        </div>
                                        <div class="event-description">
                                            {{ $event['description'] }}
                                        </div>
                                        <div class="event-footer">
                                            <span>ID: {{ $event['id'] }}</span>
                                            <span>{{ \Carbon\Carbon::parse($date)->format('H:i') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Include the toggle.js script for mobile menu functionality -->
<script src="{{ asset('js/toggle.js') }}"></script>
@endsection
