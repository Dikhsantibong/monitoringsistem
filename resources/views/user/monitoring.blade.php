@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <style>
        /* Sidebar */
        aside {
            background-color: #0A749B; /* Warna biru kehijauan */
            color: white;
        }
    
        /* Link di Sidebar */
        aside nav a {
            color: white; /* Teks default putih */
            display: flex;
            align-items: center;
            padding: 12px 16px;
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s; /* Animasi transisi */
        }
    
        /* Link di Sidebar saat Hover */
        aside nav a:hover {
            background-color: white; /* Latar belakang putih */
            color: black; /* Teks berubah menjadi hitam */
        }
    
        /* Aktif Link */
        aside nav a.bg-yellow-500 {
            background-color: white;
            color: #000102;
        }
    </style>
    <!-- Sidebar -->
    <aside id="mobile-menu"
    class="fixed z-20 overflow-hidden transform transition-transform duration-300 md:relative md:translate-x-0 h-screen w-64 bg-[#0A749B] shadow-md text-white hidden md:block md:shadow-lg">
        <div class="p-4 flex items-center gap-3">
            <img src="{{ asset('logo/navlogo.png') }}" alt="Logo Aplikasi" class="w-40 h-15">
            <!-- Mobile Menu Toggle -->
            <button id="menu-toggle-close"
            class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#009BB9] hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
            aria-controls="mobile-menu" aria-expanded="false">
            <span class="sr-only">Open main menu</span>
            <i class="fa-solid fa-xmark"></i>
        </button>
        </div>
        <nav class="mt-4">
            <a href="{{ route('user.dashboard') }}" >
                <i class="fas fa-home mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('user.machine.monitor') }}">
                <i class="fas fa-cogs mr-3"></i>
                <span>Machine Monitor</span>
            </a>
            <a href="{{ route('daily.meeting') }}">
                <i class="fas fa-users mr-3"></i>
                <span>Daily Meeting</span>
            </a>
            <a href="{{ route('monitoring') }}" class="bg-yellow-500">
                <i class="fas fa-chart-line mr-3"></i>
                <span>Monitoring</span>
            </a>
            <a href="{{ route('documentation') }}">
                <i class="fas fa-book mr-3"></i>
                <span>Documentation</span>
            </a>
            <a href="{{ route('support') }}">
                <i class="fas fa-headset mr-3"></i>
                <span>Support</span>
            </a>
        </nav>
    </aside>

     <!-- Main Content -->
     <div id="main-content" class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0">
            <div class="flex justify-between items-center px-6 py-3">
                <!-- Mobile Menu Toggle -->
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
                <h1 class="text-xl font-semibold text-gray-800">Monitoring</h1>
                <div class="relative">
                    <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                        <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}" 
                             class="w-8 h-8 rounded-full mr-2">
                        <span class="text-gray-700">{{ Auth::user()->name }}</span>
                        <i class="fas fa-caret-down ml-2"></i>
                    </button>
                    <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                        <a href="{{ route('user.profile') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
                        <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </header>

    <!-- Main Content -->
    <div class="flex-1 px-6">
        @include('layouts.breadcrumbs', ['breadcrumbs' => [
                    ['title' => 'Dashboard']
                ]])
        <h1 class="text-2xl font-semibold">Monitoring Proyek</h1>
        <p>Berikut adalah status proyek yang sedang berjalan:</p>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="text-lg font-semibold">Grafik Status Proyek</h2>
                <canvas id="projectStatusChart"></canvas>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="text-lg font-semibold">Detail Proyek</h2>
                <div class="space-y-4">
                    <div class="bg-white rounded-lg shadow p-4">
                        <h3 class="text-lg font-semibold">Proyek A</h3>
                        <p>Status: <span class="text-green-500">Aktif</span></p>
                        <p>Progress: 75%</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <h3 class="text-lg font-semibold">Proyek B</h3>
                        <p>Status: <span class="text-red-500">Tertunda</span></p>
                        <p>Progress: 50%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/toggle.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx2 = document.getElementById('projectStatusChart').getContext('2d');
    const projectStatusChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: ['Proyek A', 'Proyek B'],
            datasets: [{
                label: 'Status Proyek',
                data: [75, 50],
                backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(255, 99, 132, 0.2)'],
                borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
            }
        }
    });
</script>
@push('scripts')
@endpush
@endsection