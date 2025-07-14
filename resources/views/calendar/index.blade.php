@extends('layouts.app')

@section('styles')
<style>
    /* Navbar styles */
    .nav-background {
        background-color: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
    }

    .nav-link {
        color: white;
        font-weight: 500;
        transition: color 0.3s;
        text-decoration: none;
    }

    .nav-link:hover {
        color: #FFCC00;
    }

    .nav-link-mobile {
        display: block;
        padding: 0.5rem;
        color: white;
        font-weight: 500;
        transition: color 0.3s;
        text-decoration: none;
    }

    .nav-link-mobile:hover {
        color: #FFCC00;
    }

    .login-button {
        background-color: #FFCC00;
        color: black;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: background-color 0.3s;
        text-decoration: none;
    }

    .login-button:hover {
        background-color: #e6b800;
    }

    .login-mobile {
        background-color: #FFCC00;
        color: black !important;
        border-radius: 0.375rem;
    }

    /* Calendar styles */
    .calendar-container {
        padding: 20px;
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding: 0 20px;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        padding: 20px;
    }

    .date-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 1px solid #e2e8f0;
    }

    .date-header {
        background: #0095B7;
        color: white;
        padding: 15px;
        font-size: 1.1rem;
        font-weight: 600;
        text-align: center;
    }

    .date-content {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        max-height: 400px;
    }

    .event-item {
        padding: 12px;
        margin-bottom: 10px;
        border-radius: 6px;
        background: #f8fafc;
        border-left: 4px solid;
        transition: transform 0.2s;
    }

    .event-item:hover {
        transform: translateX(5px);
    }

    .event-item:last-child {
        margin-bottom: 0;
    }

    .event-item.service-request {
        border-left-color: #0095B7;
    }

    .event-item.work-order {
        border-left-color: #A8D600;
    }

    .event-type {
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .event-description {
        color: #4a5568;
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .event-status {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-open {
        background: #e0f2fe;
        color: #0369a1;
    }

    .status-closed {
        background: #dcfce7;
        color: #166534;
    }

    .event-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #e2e8f0;
        font-size: 0.875rem;
        color: #64748b;
    }

    .no-events {
        text-align: center;
        padding: 30px;
        color: #64748b;
        background: #f8fafc;
        border-radius: 8px;
        margin: 20px;
    }

    /* Calendar Navigation */
    .calendar-nav {
        display: flex;
        gap: 10px;
    }

    .calendar-nav-btn {
        padding: 8px 16px;
        border-radius: 6px;
        background: #0095B7;
        color: white;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .calendar-nav-btn:hover {
        background: #007a94;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .calendar-container {
            padding: 10px;
        }

        .calendar-grid {
            grid-template-columns: 1fr;
            padding: 10px;
        }

        .date-card {
            margin-bottom: 15px;
        }

        .calendar-header {
            flex-direction: column;
            gap: 15px;
            text-align: center;
            padding: 0 10px;
        }
    }
</style>
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
                                {{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}
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
