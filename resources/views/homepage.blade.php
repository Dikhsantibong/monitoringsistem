@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/homepage.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .navbar.hidden {
            transform: translateY(-100%);
        }

        .navbar-brand img {
            height: 50px;
        }

        .nav-link {
            color: white !important;
        }

        .nav-link:hover {
            color: #A8D600 !important;
        }

        /* Desktop menu styles */
        .desktop-menu {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .desktop-menu a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        /* Mobile menu styles */
        .mobile-menu {
            display: none;
            position: fixed;
            top: 60px; /* Sesuaikan dengan tinggi navbar */
            left: 0;
            right: 0;
            background-color: #1a1a1a;
            padding: 1rem;
            z-index: 50;
            transition: transform 0.3s ease-in-out;
            transform: translateY(-100%);
        }

        .mobile-menu.show {
            transform: translateY(0);
            display: block;
        }

        .mobile-menu a {
            display: block;
            color: white;
            padding: 0.75rem 1rem;
            text-decoration: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .mobile-menu a:last-child {
            border-bottom: none;
        }

        .mobile-menu a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #A8D600;
        }

        @media (max-width: 768px) {
            .navbar-toggler {
                display: block;
            }
            
            .desktop-menu {
                display: none;
            }
        }

        /* Responsive breakpoints */
        @media (max-width: 991px) {
            .desktop-menu {
                display: none;
            }

            .navbar-toggler {
                display: block;
            }

            .mobile-menu.show {
                display: block;
            }
        }

        /* Adjust content padding to prevent overlap with fixed navbar */
        h3 {
            font-size: 1.25rem;
            margin: 5px 0;
            text-align: center;
            color: #0095B7;
        }

        /* Hexagon styles */
        /* Background for the hexagon section */
        .hexagon-background {
            background-image: url('{{ asset('background/backgorund.jpg') }}');
            background-size: cover;
            background-position: center;
        }
        .hexagon {
            position: relative;
            margin: 28.87px 0;
            clip-path: polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            transform-style: preserve-3d;
            text-shadow: 1px 1px 2px #000000;
            
        }

        .hexagon:hover {
            transform: translateY(-10px) rotateX(10deg) rotateY(10deg);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .hexagon:focus {
            transform: translateY(-10px) rotateX(10deg) rotateY(10deg);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .nav-link {
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: #0095B7;
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .nav-link.active {
            color: #0095B7 !important;
        }

        .nav-link.active::after {
            width: 100%;
        }

        /* Dark mode adjustments */
        .dark .nav-link {
            color: white !important;
        }

        /* Ensure content doesn't hide behind navbar */
        main {
            margin-top: 120px; /* Sesuaikan dengan total tinggi kedua navbar */
        }

        /* Loader styles */
        .loader-hidden {
            opacity: 0;
            visibility: hidden;
        }
        
        .page-transition {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        
        .page-visible {
            opacity: 1;
        }

        /* Navbar Styles */
        .nav-link {
            color: #1a202c;
            text-decoration: none;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-link:hover {
            color: #4299e1;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: #4299e1;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .nav-link.active {
            color: #4299e1;
        }

        .nav-link.active::after {
            width: 100%;
        }

        /* Mobile Menu Styles */
        .nav-link-mobile {
            display: block;
            padding: 0.75rem 1rem;
            color: #1a202c;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .nav-link-mobile:hover {
            background-color: #f7fafc;
            color: #4299e1;
        }

        @media (prefers-color-scheme: dark) {
            .nav-link {
                color: #fff;
            }
            
            .nav-link-mobile {
                color: #fff;
            }

            .nav-link-mobile:hover {
                background-color: #2d3748;
            }
        }

        /* Navbar Background */
        .nav-background {
            background-color: #1a1a1a; /* Dark background */
            background-image: linear-gradient(to right, #1a1a1a, #2d3748);
        }

        /* Navbar Styles */
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
            color: #4299e1;
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
            color: #4299e1;
            border-left-color: #4299e1;
        }

        /* Dark mode is now default */
        .nav-link, .nav-link-mobile {
            color: #ffffff;
        }

        .nav-link-mobile:hover {
            background-color: #2d3748;
        }

        /* Glassmorphism effect */
        .nav-background {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* Shadow effect */
        .shadow-lg {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 
                        0 2px 4px -1px rgba(0, 0, 0, 0.06);
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
            margin-top: 80px; /* Sesuaikan dengan tinggi navbar */
        }

        /* Hero section adjustments */
        .hexagon-background {
            position: relative;
            margin-top: -80px;
            background-image: url('{{ asset('background/backgorund.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        /* Tambahkan overlay gelap untuk meningkatkan kontras */
        .hexagon-background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            /* background: rgba(0, 0, 0, 0.5); Sesuaikan opacity sesuai kebutuhan */
            z-index: 1;
        }

        /* Pastikan konten hero berada di atas overlay */
        .hexagon-background > * {
            position: relative;
            z-index: 2;
        }

        /* Pastikan section di bawah hero memiliki background putih */
        #map, #grafik, #live-data {
            background-color: white;
            position: relative;
            z-index: 1;
        }

        /* Adjust content positioning */
        main {
            margin-top: 0;
        }

        /* Remove spacer */
        .h-[80px] {
            display: none;
        }

        /* Ensure map section starts after hero */
        #map {
            margin-top: 0;
            position: relative;
            z-index: 1;
        }

        /* Additional responsive adjustments */
        @media (max-height: 768px) {
            .hexagon-background {
                padding: 120px 0;
            }
        }

        /* Navbar container styles */
        .nav-background .container {
            max-width: 1200px; /* Sesuaikan dengan kebutuhan */
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Logo and menu container */
        .flex.justify-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem; /* Tambahkan gap antara logo dan menu */
        }

        /* Menu items container */
        .hidden.md\:flex.items-center {
            flex: 1; /* Tambahkan ini */
            justify-content: center; /* Tambahkan ini */
        }

        .hidden.md\:flex.items-center ul {
            display: flex;
            justify-content: center; /* Tambahkan ini */
            align-items: center;
            width: 100%; /* Tambahkan ini */
        }
    </style>
@endsection

@section('content')
    <!-- Include loader component -->
    @include('components.loader')

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
                                    {{-- <li><a href="#grafik" class="nav-link">Grafik Kinerja</a></li> --}}
                                    <li><a href="#live-data" class="nav-link">Live Data Unit Operasional</a></li>
                                    <li><a href="{{ route('dashboard.pemantauan') }}" class="nav-link">Dashboard Pemantauan</a></li>
                                    <!-- Tambah Menu Login -->
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
                                {{-- <li><a href="#grafik" class="nav-link-mobile">Grafik Kinerja</a></li> --}}
                                <li><a href="#live-data" class="nav-link-mobile">Live Data Unit Operasional</a></li>
                                <li><a href="{{ route('dashboard.pemantauan') }}" class="nav-link-mobile">Dashboard Pemantauan</a></li>
                                <!-- Tambah Menu Login di Mobile -->
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
            <div class="w-full">
                {{-- Hero section --}}
                <div class="min-h-screen flex flex-col justify-center items-center hexagon-background">
                    <!-- Content wrapper -->
                    <div class="relative z-10 mt-16">
                        <!-- Header -->
                        <h2 class="text-9xl font-bold mb-8 text-center" style="color: #FFCC00; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); line-height: 1;">
                            M<span style="display: inline-flex; align-items: center;"><i class="fas fa-cog fa-spin" style="color: #333333;"></i></span>NDAY
                            <span class="text-6xl block mt-2" style="color: #FFCC00; font-weight: 600; letter-spacing: 2px;">
                                MONITORING DAILY
                                <span class="block text-3xl mt-1" style="color: #FFFFFF; letter-spacing: 1px;">UP KENDARI</span>
                            </span>
                        </h2>
                        <div class="flex gap-2 lg:gap-0 lg:grid grid-cols-2 lg:grid-cols-3">
                            <div>
                                <a href="{{ route('login', ['unit' => 'mysql_wua_wua']) }}" class="block">
                                    <div class="hexagon bg-[#0A749B] bg-opacity-55 flex flex-col items-center justify-center hover:bg-opacity-100 h-36 w-40 md:w-56 md:h-44">
                                        <h5 class="text-sm lg:text-2xl md:text-xl font-bold text-gray-50 text-center">ULPLTD <br> WUA-WUA</h5>
                                    </div>
                                </a>
                                <a href="{{ route('login', ['unit' => 'mysql_poasia']) }}" class="block">
                                    <div class="hexagon bg-[#0A749B] bg-opacity-55 flex flex-col items-center justify-center hover:bg-opacity-100 h-36 w-40 md:w-56 md:h-44">
                                        <h5 class="text-sm lg:text-2xl md:text-xl font-bold text-gray-50 text-center">ULPLTD <br> POASIA</h5>
                                    </div>
                                </a>
                            </div>
                            <div class="flex items-center justify-center">
                                <div class="hidden lg:block md:block">
                                    <a href="{{ route('login', ['unit' => 'mysql']) }}" class="block">
                                        <div class="hexagon bg-[#0A749B] flex flex-col items-center justify-center h-36 w-40 md:w-56 md:h-44">
                                            <h5 class="text-sm lg:text-2xl md:text-xl font-bold text-gray-50 text-center">UP <br> KENDARI</h5>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('login', ['unit' => 'mysql_kolaka']) }}" class="block">
                                    <div class="hexagon bg-[#0A749B] bg-opacity-55 flex flex-col items-center justify-center hover:bg-opacity-100 h-36 w-40 md:w-56 md:h-44 border">
                                        <h5 class="text-sm lg:text-2xl md:text-xl font-bold text-gray-50 text-center">ULPLTD <br> KOLAKA</h5>
                                    </div>
                                </a>
                                <a href="{{ route('login', ['unit' => 'mysql_bau_bau']) }}" class="block">
                                    <div class="hexagon bg-[#0A749B] bg-opacity-55 flex flex-col items-center justify-center hover:bg-opacity-100 h-36 w-40 md:w-56 md:h-44 border">
                                        <h5 class="text-sm lg:text-2xl md:text-xl font-bold text-gray-50 text-center">ULPLTD <br> BAU-BAU</h5>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Map --}}
            <h1 class="text-2xl font-semibold text-center mt-10  text-[#0A749B]">Peta Pembangkit</h1>
            <div id="map"
                style="height: 500px; border-radius: 20px; position: relative; margin: 30px 30px 0; padding: 0; "
                class="z-0">
            </div>

            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.js"></script>

            <div class="container mx-auto px-4 py-6">
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 px-2">Monitoring Beban Tak Tersalur & Kesiapan Mesin</h2>
                    
                    <!-- Grid untuk diagram circle berdampingan -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <!-- Ring Progress Kesiapan Mesin -->
                        <div class="bg-gray-50 rounded-xl p-5 shadow-sm border border-gray-100">
                            <div class="text-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-700">Kesiapan Mesin</h3>
                                <p class="text-sm text-gray-500">Status operasional unit pembangkit</p>
                            </div>
                            <div id="machineReadinessChart" class="mx-auto" style="height: 200px;"></div>
                        </div>
                        
                        <!-- Ring Progress Beban Tersalur -->
                        <div class="bg-gray-50 rounded-xl p-5 shadow-sm border border-gray-100">
                            <div class="text-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-700">Beban Tersalur</h3>
                                <p class="text-sm text-gray-500">Kapasitas daya yang tersedia</p>
                            </div>
                            <div id="powerDeliveryChart" class="mx-auto" style="height: 200px;"></div>
                        </div>
                    </div>
                    
                    <!-- Grafik Bar memanjang horizontal -->
                    <div class="bg-gray-50 rounded-xl p-5 shadow-sm border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Beban Tak Tersalur per Unit</h3>
                        <div id="unservedLoadChart" style="height: 450px;"></div>
                    </div>
                </div>
            </div>
            <!-- Live Data Unit Operasional -->
            <div class="flex justify-center items-center mt-10 mb-4 gap-4">
                <h3 class="text-xl font-semibold">Live Data Unit Operasional</h3>
                <div class="flex items-center text-gray-600">
                    <i class="far fa-clock mr-2"></i>
                    <span id="liveTime"></span>
                </div>
            </div>

            <div class="w-full flex justify-center flex-col items-center mb-5">
                <div id="live-data" class="bg-white border border-gray-300 rounded-lg p-4 w-4/5">
                    <div class="overflow-auto">
                        <table class="table table-striped table-bordered min-w-full">
                            <thead>
                                <tr>
                                    <th class="text-center">Nama Unit</th>
                                    <th class="text-center">Mesin</th>
                                    <th class="text-center">DMN</th>
                                    <th class="text-center">DMP</th>
                                    <th class="text-center">Beban</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Waktu Update</th>
                                </tr>
                            </thead>
                            <tbody id="unit-table-body">
                                @php
                                    $nonOperationalStatuses = ['Gangguan', 'Pemeliharaan', 'Mothballed', 'Overhaul'];
                                @endphp
                                
                                @foreach($powerPlants as $plant)
                                    @foreach($plant->machines as $machine)
                                        @php
                                            $latestStatus = $machine->statusLogs->first();
                                            // Skip jika tidak ada status atau status bukan non-operational
                                            if (!$latestStatus || !in_array($latestStatus->status, $nonOperationalStatuses)) {
                                                continue;
                                            }

                                            // Set style berdasarkan status
                                            $statusStyle = match($latestStatus->status) {
                                                'Gangguan' => [
                                                    'bg' => '#FEE2E2',
                                                    'text' => '#DC2626',
                                                    'border' => '#FCA5A5',
                                                    'icon' => '⚠️'
                                                ],
                                                'Pemeliharaan' => [
                                                    'bg' => '#FEF3C7',
                                                    'text' => '#D97706',
                                                    'border' => '#FCD34D',
                                                    'icon' => '🔧'
                                                ],
                                                'Mothballed' => [
                                                    'bg' => '#FEF3C7',
                                                    'text' => '#D97706',
                                                    'border' => '#FCD34D',
                                                    'icon' => '🔒'
                                                ],
                                                'Overhaul' => [
                                                    'bg' => '#FFE4B5',
                                                    'text' => '#FF8C00',
                                                    'border' => '#FFB74D',
                                                    'icon' => '🔧'
                                                ],
                                                default => [
                                                    'bg' => '#F3F4F6',
                                                    'text' => '#6B7280',
                                                    'border' => '#D1D5DB',
                                                    'icon' => '❔'
                                                ]
                                            };
                                        @endphp
                                        <tr class="table-row">
                                            <td class="text-center">{{ $plant->name }}</td>
                                            <td class="text-center">{{ $machine->name }}</td>
                                            <td class="text-center">{{ number_format($latestStatus->dmn, 1) }}</td>
                                            <td class="text-center">{{ number_format($latestStatus->dmp, 1) }}</td>
                                            <td class="text-center">{{ number_format($latestStatus->load_value, 1) }} MW</td>
                                            <td class="text-center">
                                                <span style="
                                                    background: {{ $statusStyle['bg'] }}; 
                                                    color: {{ $statusStyle['text'] }}; 
                                                    padding: 4px 12px;
                                                    border-radius: 12px;
                                                    font-size: 0.85em;
                                                    border: 1px solid {{ $statusStyle['border'] }};
                                                ">
                                                    {{ $statusStyle['icon'] }} {{ $latestStatus->status }}
                                                </span>
                                            </td>
                                            <td class="text-center text-sm text-gray-500">
                                                {{ $latestStatus->created_at->format('d/m/Y H:i:s') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- </div> --}}

            <!-- Setelah section peta pembangkit -->
          

            <!-- Footer -->
            <footer class="footer w-full">
                <div class="content">
                    <div class="column">
                        <img src="{{ asset('logo/navlogo.png') }}" alt="Logo" style="height: 40px; margin-bottom: 10px">
                        <p>PLN Nusantara Power terdepan dan terpercaya dalam bisnis energi berkelanjutan di Asia Tenggara.</p>
                        <p>The foremost and reliable sustainable energy business on SEA.</p>
                        <div class="social-icons">
                            <!-- Tambahkan ikon media sosial -->
                            <a href="#"><i class="fab fa-youtube"></i></a>
                            <a href="#"><i class="fab fa-facebook"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    <div class="column">
                        <h4>Kontak/Contact</h4>
                        <p>Jl. Chairil Anwar No. 01, Kendari, Sulawesi tenggara, Indonesia</p>
                        <p>Email: info@plnnusantarapower.co.id</p>
                        <p>Telepon: +62 31 8283180</p>
                    </div>
                    {{-- <div class="column">
                        <h4>Strategic Office</h4>
                        <p>18 Office Park, Lt.2 ABCD</p>
                        <p>Jl. TB Simatupang No.18, Jakarta Selatan, Indonesia</p>
                    </div> --}}
                    <div class="column">
                        <h4>Newsletter</h4>
                        <form>
                            <input type="email" placeholder="Email" required />
                            <button type="submit">Subscribe</button>
                        </form>
                    </div>
                </div>
                <div class="copyright">
                    Copyright © 2023 <a href="#">PT PLN Nusantara Power</a>. All Rights Reserved.
                </div>
            </footer>

            <script>
                var options = {
                    series: [{
                            name: 'Total Kapasitas Listrik',
                            data: [{{ implode(',', $total_capacity_data) }}]
                        },
                        {
                            name: 'Total Unit Pembangkit',
                            data: [{{ implode(',', $total_units_data) }}]
                        },
                        {
                            name: 'Unit Pembangkit Aktif',
                            data: [{{ implode(',', $active_units_data) }}]
                        },
                        {
                            name: 'DMN',
                            data: [{{ implode(',', $dmn_data) }}]
                        },
                        {
                            name: 'DMP',
                            data: [{{ implode(',', $dmp_data) }}]
                        },
                        {
                            name: 'Beban',
                            data: [{{ implode(',', $load_value_data) }}]
                        },
                        {
                            name: 'Kapasitas Unit',
                            data: [{{ implode(',', $capacity_data) }}]
                        }
                    ],
                    chart: {
                        type: 'line',
                        height: 350
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: [3, 3, 3, 2, 2, 2, 2],
                        dashArray: [0, 0, 0, 0, 0, 0, 0]
                    },
                    xaxis: {
                        categories: ["{{ implode('","', $dates) }}"]
                    },
                    yaxis: [{
                        title: {
                            text: 'Nilai'
                        }
                    }],
                    title: {
                        text: 'Grafik Kinerja Pembangkit',
                        align: 'center'
                    },
                    colors: [
                        '#FF1E1E', // Merah untuk Total Kapasitas Listrik
                        '#00B050', // Hijau untuk Total Unit Pembangkit
                        '#0070C0', // Biru untuk Unit Pembangkit Aktif
                        '#7030A0', // Ungu untuk DMN
                        '#FFC000', // Kuning untuk DMP
                        '#ED7D31', // Oranye untuk Beban
                        '#4472C4' // Biru Tua untuk Kapasitas Unit
                    ],
                    tooltip: {
                        enabled: true,
                        y: {
                            formatter: function(value, {
                                seriesIndex
                            }) {
                                if (seriesIndex <= 2) return value + ' Unit';
                                if (seriesIndex === 6) return value + ' MW';
                                return value;
                            }
                        }
                    },
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center',
                        markers: {
                            width: 12,
                            height: 12,
                            strokeWidth: 0,
                            radius: 12,
                            offsetX: 0,
                            offsetY: 0
                        },
                        itemMargin: {
                            horizontal: 15,
                            vertical: 8
                        }
                    }
                };

                var chart = new ApexCharts(document.querySelector("#line-chart"), options);
                chart.render();

                var map = L.map('map', {
                    zoomControl: true,
                    scrollWheelZoom: true,
                    doubleClickZoom: true,
                    dragging: true,
                }).setView([-4.0435, 122.4972], 8);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Tambahkan custom info control
                var info = L.control({position: 'bottomleft'});

                info.onAdd = function (map) {
                    this._div = L.DomUtil.create('div', 'map-info');
                    this._div.innerHTML = `
                        <div style="
                            background: rgba(255, 255, 255, 0.9);
                            padding: 10px;
                            border-radius: 5px;
                            box-shadow: 0 1px 5px rgba(0,0,0,0.2);
                            font-size: 13px;
                            max-width: 200px;
                        ">
                            <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                <span style="color: #0095B7; margin-right: 5px;">ℹ️</span>
                                <span style="font-weight: bold; color: #0095B7;">Petunjuk Peta</span>
                            </div>
                            <div style="color: #4B5563; line-height: 1.4;">
                                Klik marker <img src="{{ asset('images/marker-icon.png') }}" alt="marker" style="height: 16px; display: inline; vertical-align: middle;"> untuk melihat informasi detail unit pembangkit
                            </div>
                            <div style="margin-top: 8px; font-size: 12px; color: #6B7280;">
                                Status Unit:
                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 4px; margin-top: 4px;">
                                    <div style="display: flex; align-items: center;">
                                        <span style="
                                            display: inline-block;
                                            width: 8px;
                                            height: 8px;
                                            border-radius: 50%;
                                            background: #059669;
                                            margin-right: 4px;
                                        "></span>
                                        <span>Operasi</span>
                                    </div>
                                    <div style="display: flex; align-items: center;">
                                        <span style="
                                            display: inline-block;
                                            width: 8px;
                                            height: 8px;
                                            border-radius: 50%;
                                            background: #6B7280;
                                            margin-right: 4px;
                                        "></span>
                                        <span>Standby</span>
                                    </div>
                                    <div style="display: flex; align-items: center;">
                                        <span style="
                                            display: inline-block;
                                            width: 8px;
                                            height: 8px;
                                            border-radius: 50%;
                                            background: #DC2626;
                                            margin-right: 4px;
                                        "></span>
                                        <span>Gangguan</span>
                                    </div>
                                    <div style="display: flex; align-items: center;">
                                        <span style="
                                            display: inline-block;
                                            width: 8px;
                                            height: 8px;
                                            border-radius: 50%;
                                            background: #0369A1;
                                            margin-right: 4px;
                                        "></span>
                                        <span>Pemeliharaan</span>
                                    </div>
                                    <div style="display: flex; align-items: center;">
                                        <span style="
                                            display: inline-block;
                                            width: 8px;
                                            height: 8px;
                                            border-radius: 50%;
                                            background: #D97706;
                                            margin-right: 4px;
                                        "></span>
                                        <span>Mothballed</span>
                                    </div>
                                    <div style="display: flex; align-items: center;">
                                        <span style="
                                            display: inline-block;
                                            width: 8px;
                                            height: 8px;
                                            border-radius: 50%;
                                            background: #FF8C00;
                                            margin-right: 4px;
                                        "></span>
                                        <span>Overhaul</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    return this._div;
                };

                info.addTo(map);

                // Buat array untuk menyimpan semua koordinat marker
                var markers = [];
                var bounds = L.latLngBounds();

                @php
                    use App\Models\MachineStatusLog;
                    use Carbon\Carbon;
                @endphp

                @foreach ($powerPlants as $plant)
                    var markerLatLng = [{{ $plant->latitude }}, {{ $plant->longitude }}];
                    var marker = L.marker(markerLatLng)
                        .addTo(map)
                        .bindPopup(`
                            <div style="min-width: 350px;">
                                <h3 style="margin: 0 0 10px 0; text-align: center; color: #0095B7;">{{ $plant->name }}</h3>
                                
                                <!-- Info Derating dan HOP -->
                                <div style="display: flex; justify-content: center; gap: 15px; margin-bottom: 10px; font-size: 0.9em;">
                                    @php
                                        $totalDMN = $plant->machines->sum(function($machine) {
                                            return $machine->statusLogs->first()->dmn ?? 0;
                                        });
                                        $totalDMP = $plant->machines->sum(function($machine) {
                                            return $machine->statusLogs->first()->dmp ?? 0;
                                        });
                                        $totalDerating = $totalDMN + $totalDMP;
                                        $totalCapacity = $plant->machines->sum('capacity');
                                        $deratingPercentage = $totalCapacity > 0 ? round(($totalDerating / $totalCapacity) * 100, 2) : 0;
                                    @endphp
                                    <div style="
                                        background: #FEF3C7; 
                                        color: #D97706; 
                                        padding: 4px 10px; 
                                        border-radius: 8px; 
                                        border: 1px solid #FCD34D;
                                        font-weight: 600;
                                        display: flex;
                                        align-items: center;
                                        gap: 5px;
                                    ">
                                        <span>Derating:</span>
                                        <span>{{ $totalDerating }} MW</span>
                                        <span>({{ $deratingPercentage }}%)</span>
                                    </div>
                                    <div style="
                                        background: #E0F2FE; 
                                        color: #0369A1; 
                                        padding: 4px 10px; 
                                        border-radius: 8px; 
                                        border: 1px solid #7DD3FC;
                                        font-weight: 600;
                                    ">
                                        HOP: {{ $plant->hop ?? 0 }}
                                    </div>
                                </div>

                                <!-- Status Unit Summary -->
                                @php
                                    $unitOperasi = $plant->machines->filter(function($machine) {
                                        $latestStatus = $machine->statusLogs->first();
                                        return $latestStatus && $latestStatus->status === 'Operasi';
                                    })->count();

                                    $unitStandby = $plant->machines->filter(function($machine) {
                                        $latestStatus = $machine->statusLogs->first();
                                        return $latestStatus && $latestStatus->status === 'Standby';
                                    })->count();

                                    $unitAktif = $unitOperasi + $unitStandby; // Gabungan Operasi dan Standby

                                    $unitTidakSiap = $plant->machines->filter(function($machine) {
                                        $latestStatus = $machine->statusLogs->first();
                                        return $latestStatus && in_array($latestStatus->status, [
                                            'Gangguan', 
                                            'Pemeliharaan', 
                                            'Mothballed', 
                                            'Overhaul'
                                        ]);
                                    })->count();
                                @endphp
                                
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 15px;">
                                    <div style="background: #D1FAE5; padding: 8px; border-radius: 8px; text-align: center; border: 1px solid #6EE7B7;">
                                        <div style="font-size: 0.8em; color: #059669;">Unit Aktif</div>
                                        <div style="font-weight: bold; color: #059669; font-size: 1.2em;">{{ $unitAktif }}</div>
                                        <div style="font-size: 0.7em; color: #059669;">
                                            ({{ $unitOperasi }} Operasi, {{ $unitStandby }} Standby)
                                        </div>
                                    </div>
                                    <div style="background: #FEE2E2; padding: 8px; border-radius: 8px; text-align: center; border: 1px solid #FCA5A5;">
                                        <div style="font-size: 0.8em; color: #DC2626;">Tidak Siap Operasi</div>
                                        <div style="font-weight: bold; color: #DC2626; font-size: 1.2em;">{{ $unitTidakSiap }}</div>
                                    </div>
                                    <div style="background: #F3F4F6; padding: 8px; border-radius: 8px; text-align: center; border: 1px solid #D1D5DB;">
                                        <div style="font-size: 0.8em; color: #6B7280;">Total Unit</div>
                                        <div style="font-weight: bold; color: #6B7280; font-size: 1.2em;">{{ $plant->machines->count() }}</div>
                                    </div>
                                </div>

                                <!-- Info Grid Utama -->
                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 15px;">
                                    <div style="background: #f8f9fa; padding: 8px; border-radius: 5px; text-align: center;">
                                        <div style="font-weight: bold; color: #0095B7;">Total Mesin</div>
                                        <div>{{ $plant->machines->count() }}</div>
                                    </div>
                                    <div style="background: #f8f9fa; padding: 8px; border-radius: 5px; text-align: center;">
                                        <div style="font-weight: bold; color: #0095B7;">Mesin Aktif</div>
                                        <div>{{ $plant->machines->where('status', 'Aktif')->count() }}</div>
                                    </div>
                                </div>

                                <!-- Status Mesin Grid -->
                                <div style="margin: 10px 0;">
                                    <h4 style="color: #0095B7; margin: 5px 0;">Status Mesin</h4>
                                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; max-height: 200px; overflow-y: auto;">
                                        @foreach($plant->machines as $machine)
                                            @php
                                                $latestStatus = $machine->statusLogs->first();
                                                // Tentukan warna background dan teks berdasarkan status
                                                $statusStyle = match($latestStatus->status ?? '') {
                                                    'Gangguan' => [
                                                        'bg' => '#FEE2E2',
                                                        'text' => '#DC2626',
                                                        'border' => '#FCA5A5',
                                                        'icon' => '⚠️'
                                                    ],
                                                    'Mothballed' => [
                                                        'bg' => '#FEF3C7',
                                                        'text' => '#D97706',
                                                        'border' => '#FCD34D',
                                                        'icon' => '🔒'
                                                    ],
                                                    'Overhaul' => [
                                                        'bg' => '#FFE4B5',
                                                        'text' => '#FF8C00',
                                                        'border' => '#FFB74D',
                                                        'icon' => '🔧'
                                                    ],
                                                    'Pemeliharaan' => [
                                                        'bg' => '#E0F2FE',
                                                        'text' => '#0369A1',
                                                        'border' => '#7DD3FC',
                                                        'icon' => '🔨'
                                                    ],
                                                    'Standby' => [
                                                        'bg' => '#F3F4F6',
                                                        'text' => '#6B7280',
                                                        'border' => '#D1D5DB',
                                                        'icon' => '⏸️'
                                                    ],
                                                    'Operasi' => [
                                                        'bg' => '#D1FAE5',
                                                        'text' => '#059669',
                                                        'border' => '#6EE7B7',
                                                        'icon' => '✅'
                                                    ],
                                                    default => [
                                                        'bg' => '#F3F4F6',
                                                        'text' => '#6B7280',
                                                        'border' => '#D1D5DB',
                                                        'icon' => '❔'
                                                    ]
                                                };
                                            @endphp
                                            <div style="background: #fff; padding: 8px; border-radius: 5px; border: 1px solid {{ $statusStyle['border'] }};">
                                                <div style="font-weight: bold; color: {{ $statusStyle['text'] }};">{{ $machine->name }}</div>
                                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 5px; font-size: 0.9em;">
                                                    <div>
                                                        <span style="color: #666;">Status:</span>
                                                        <span style="
                                                            background: {{ $statusStyle['bg'] }}; 
                                                            color: {{ $statusStyle['text'] }}; 
                                                            padding: 2px 8px;
                                                            border-radius: 12px;
                                                            font-size: 0.85em;
                                                            border: 1px solid {{ $statusStyle['border'] }};
                                                            display: inline-block;
                                                            margin-top: 2px;
                                                        ">
                                                            {{ $statusStyle['icon'] }} {{ $latestStatus ? $latestStatus->status : 'N/A' }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span style="color: #666;">Beban:</span>
                                                        <span style="color: {{ $statusStyle['text'] }}; font-weight: 600;">
                                                            {{ $latestStatus ? $latestStatus->load_value : 0 }} MW
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span style="color: #666;">DMN:</span>
                                                        <span style="color: {{ $statusStyle['text'] }}; font-weight: 600;">
                                                            {{ $latestStatus ? $latestStatus->dmn : 0 }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span style="color: #666;">DMP:</span>
                                                        <span style="color: {{ $statusStyle['text'] }}; font-weight: 600;">
                                                            {{ $latestStatus ? $latestStatus->dmp : 0 }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Grafik Trend -->
                                <div style="margin: 15px 0;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                        <div style="font-weight: bold; color: #0095B7;">Trend 7 Hari Terakhir</div>
                                        <div class="chart-legend" style="font-size: 12px;">
                                            <span style="color: #0095B7;">● Beban</span>
                                            <span style="color: #FF4560; margin-left: 10px;">● Kapasitas</span>
                                        </div>
                                    </div>
                                    <div id="chart-{{ $plant->id }}" style="height: 200px;"></div>
                                </div>

                                <button onclick="showAccumulationData({{ $plant->id }})" 
                                        style="background: #0095B7; color: white; border: none; 
                                               padding: 8px 15px; border-radius: 5px; 
                                               cursor: pointer; width: 100%; margin-top: 10px;">
                                    Lihat Detail Lengkap
                                </button>
                            </div>
                        `, {
                            maxWidth: 600,
                            maxHeight: 400
                        });

                    // Event listener saat popup dibuka
                    marker.on('popupopen', function() {
                        setTimeout(() => {
                            createLineChart({{ $plant->id }});
                        }, 100);
                    });
                @endforeach

                function createLineChart(plantId) {
                    const chartContainer = document.querySelector("#chart-" + plantId);
                    
                    // Tampilkan loading state
                    chartContainer.innerHTML = '<div style="text-align: center; padding: 20px;">Loading chart data...</div>';

                    // Gunakan route() helper dari Laravel untuk membuat URL yang benar
                    const url = "{{ route('plant.chart.data', ['plantId' => ':plantId']) }}".replace(':plantId', plantId);

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(text || 'Network response was not ok');
                                });
                            }
                            return response.json();
                        })
                        .then(chartData => {
                            console.log('Chart Data:', chartData); // Debug log

                            if (!chartData.dates || !chartData.beban || !chartData.kapasitas ||
                                chartData.dates.length === 0) {
                                throw new Error('No data available for this period');
                            }

                            var options = {
                                series: [{
                                    name: 'Beban',
                                    data: chartData.beban
                                }, {
                                    name: 'Kapasitas',
                                    data: chartData.kapasitas
                                }],
                                chart: {
                                    type: 'line',
                                    height: 200,
                                    toolbar: {
                                        show: false
                                    },
                                    animations: {
                                        enabled: true,
                                        easing: 'easeinout',
                                        speed: 800
                                    }
                                },
                                stroke: {
                                    curve: 'smooth',
                                    width: [3, 2]
                                },
                                colors: ['#0095B7', '#FF4560'],
                                xaxis: {
                                    categories: chartData.dates,
                                    labels: {
                                        style: {
                                            fontSize: '10px'
                                        },
                                        rotate: -45,
                                        rotateAlways: false
                                    }
                                },
                                yaxis: {
                                    title: {
                                        text: 'MW'
                                    },
                                    labels: {
                                        formatter: function(val) {
                                            return val.toFixed(1);
                                        }
                                    }
                                },
                                tooltip: {
                                    shared: true,
                                    intersect: false,
                                    y: {
                                        formatter: function(val) {
                                            return val.toFixed(2) + ' MW';
                                        }
                                    }
                                },
                                legend: {
                                    position: 'top',
                                    horizontalAlign: 'right',
                                    markers: {
                                        width: 8,
                                        height: 8,
                                        radius: 12
                                    }
                                },
                                grid: {
                                    borderColor: '#f1f1f1',
                                    padding: {
                                        top: 10,
                                        right: 10,
                                        bottom: 10,
                                        left: 10
                                    }
                                },
                                noData: {
                                    text: 'No data available',
                                    align: 'center',
                                    verticalAlign: 'middle'
                                }
                            };

                            // Hapus loading state
                            chartContainer.innerHTML = '';
                            
                            var chart = new ApexCharts(chartContainer, options);
                            chart.render();
                        })
                        .catch(error => {
                            console.error('Error details:', error);
                            chartContainer.innerHTML = `
                                <div style="text-align: center; padding: 20px; color: #DC2626;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>Error loading chart data</div>
                                    <div style="font-size: 0.8em; margin-top: 5px;">${error.message}</div>
                                </div>
                            `;
                        });
                }

                function createDonutChart(type, markerId, value) {
                    var options = {
                        series: [value],
                        chart: {
                            type: 'radialBar',
                            height: 120,
                            width: 120
                        },
                        plotOptions: {
                            radialBar: {
                                hollow: {
                                    size: '70%'
                                },
                                dataLabels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '14px',
                                        offsetY: -10
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '16px',
                                        offsetY: 2,
                                        formatter: function(val) {
                                            return val + '%';
                                        }
                                    }
                                }
                            }
                        },
                        labels: [type.toUpperCase()],
                        colors: [type === 'dmn' ? '#0095B7' : '#FF4560']
                    };

                    var chart = new ApexCharts(document.querySelector(`#${type}-chart-${markerId}`), options);
                    chart.render();
                }

                document.addEventListener('DOMContentLoaded', function() {
                    const navLinks = document.querySelectorAll('.nav-link');
                    const sections = {
                        'home': 0, // Untuk home, gunakan posisi 0
                        'map': document.querySelector('#map'),
                        'grafik': document.querySelector('#line-chart'),
                        'live-data': document.querySelector('#live-data')
                    };

                    
                    // Fungsi untuk smooth scroll
                    navLinks.forEach(link => {
                        link.addEventListener('click', function(e) {
                            if (this.getAttribute('href').startsWith('#')) {
                                e.preventDefault();
                                const targetId = this.getAttribute('href').substring(1); // Hapus karakter '#'
                                const targetElement = sections[targetId];
                                
                                if (targetElement) {
                                    // Hapus kelas active dari semua link
                                    navLinks.forEach(link => link.classList.remove('active'));
                                    
                                    // Tambah kelas active ke link yang diklik
                                    this.classList.add('active');

                                    // Jika home, scroll ke atas
                                    if (targetId === 'home') {
                                        window.scrollTo({
                                            top: 0,
                                            behavior: 'smooth'
                                        });
                                    } else {
                                        // Smooth scroll ke target dengan offset
                                        const headerOffset = 100; // Sesuaikan dengan tinggi navbar
                                        const elementPosition = targetElement.getBoundingClientRect().top;
                                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                                        window.scrollTo({
                                            top: offsetPosition,
                                            behavior: 'smooth'
                                        });
                                    }
                                }
                            }
                        });
                    });

                    // Update active state berdasarkan posisi scroll
                    function updateActiveLink() {
                        const scrollPosition = window.scrollY;
                        
                        // Cek untuk home section
                        if (scrollPosition < 100) { // Sesuaikan dengan kebutuhan
                            navLinks.forEach(link => link.classList.remove('active'));
                            document.querySelector('a[href="#"]').classList.add('active');
                            return;
                        }

                        // Cek untuk section lainnya
                        Object.entries(sections).forEach(([id, element]) => {
                            if (element && id !== 'home') {
                                const rect = element.getBoundingClientRect();
                                const elementTop = rect.top + window.pageYOffset;
                                const elementBottom = elementTop + rect.height;
                                
                                if (scrollPosition >= elementTop - 200 && scrollPosition < elementBottom) {
                                    navLinks.forEach(link => link.classList.remove('active'));
                                    document.querySelector(`a[href="#${id}"]`).classList.add('active');
                                }
                            }
                        });
                    }

                    // Tambahkan event listener untuk scroll
                    window.addEventListener('scroll', updateActiveLink);
                    
                    // Panggil updateActiveLink saat halaman dimuat
                    updateActiveLink();
                });

                // Tambahkan fungsi untuk update waktu
                function updateLiveTime() {
                    const now = new Date();
                    const options = { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: false
                    };
                    document.getElementById('liveTime').textContent = now.toLocaleDateString('id-ID', options);
                }

                // Update waktu setiap detik
                setInterval(updateLiveTime, 1000);
                // Panggil sekali saat halaman dimuat
                updateLiveTime();
            </script>
            @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const chartData = @json($chartData);
                
                // Update konfigurasi bar chart untuk orientasi horizontal
                const barOptions = {
                    series: chartData.datasets,
                    chart: {
                        type: 'bar',
                        height: 450,
                        stacked: true,
                        toolbar: {
                            show: true,
                            tools: {
                                download: true,
                                selection: false,
                                zoom: false,
                                zoomin: false,
                                zoomout: false,
                                pan: false,
                            }
                        },
                        background: 'transparent'
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true, // Ubah ke orientasi horizontal
                            borderRadius: 4,
                            barHeight: '70%',
                            dataLabels: {
                                total: {
                                    enabled: true,
                                    offsetX: 10,
                                    style: {
                                        fontSize: '13px',
                                        fontWeight: 600
                                    }
                                }
                            }
                        },
                    },
                    colors: ['#0284c7', '#0891b2', '#0d9488', '#059669', '#65a30d', '#92400e'],
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val.toFixed(1) + ' MW';
                        },
                        textAnchor: 'start',
                        style: {
                            fontSize: '12px'
                        }
                    },
                    xaxis: {
                        categories: chartData.dates,
                        labels: {
                            style: {
                                fontSize: '12px',
                                fontWeight: 500
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Tanggal',
                            style: {
                                fontSize: '13px',
                                fontWeight: 500
                            }
                        }
                    },
                    tooltip: {
                        // ... konfigurasi tooltip yang sudah ada ...
                    },
                    legend: {
                        position: 'right',
                        offsetY: 40
                    }
                };
                
                // Konfigurasi ring progress tetap sama
                const readinessOptions = {
                    series: [chartData.machineReadiness],
                    chart: {
                        height: 180,
                        type: 'radialBar',
                        background: 'transparent',
                        offsetY: -10 // Sesuaikan offset untuk posisi yang lebih baik
                    },
                    plotOptions: {
                        radialBar: {
                            hollow: {
                                size: '75%',
                                margin: 15, // Tambahkan margin
                                background: '#fff',
                            },
                            track: {
                                background: '#e2e8f0',
                                strokeWidth: '97%',
                            },
                            dataLabels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '14px',
                                    fontWeight: 600,
                                    color: '#475569',
                                    offsetY: 30, // Sesuaikan posisi label nama
                                },
                                value: {
                                    show: true,
                                    fontSize: '24px',
                                    fontWeight: 700,
                                    color: '#0284c7',
                                    offsetY: -10, // Sesuaikan posisi label nilai
                                    formatter: function(val) {
                                        return val + '%';
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '14px',
                                    fontWeight: 600,
                                    color: '#475569',
                                    formatter: function(w) {
                                        return chartData.machineReadiness + '%';
                                    }
                                }
                            }
                        }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'dark',
                            type: 'horizontal',
                            shadeIntensity: 0.5,
                            gradientToColors: ['#0284c7'],
                            stops: [0, 100]
                        }
                    },
                    stroke: {
                        lineCap: 'round',
                        dashArray: 0
                    },
                    labels: ['Kesiapan'],
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                height: 150
                            }
                        }
                    }]
                };
                
                const powerDeliveryOptions = {
                    ...readinessOptions,
                    series: [chartData.powerDeliveryPercentage],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            gradientToColors: ['#059669']
                        }
                    },
                    plotOptions: {
                        ...readinessOptions.plotOptions,
                        radialBar: {
                            ...readinessOptions.plotOptions.radialBar,
                            dataLabels: {
                                ...readinessOptions.plotOptions.radialBar.dataLabels,
                                value: {
                                    ...readinessOptions.plotOptions.radialBar.dataLabels.value,
                                    color: '#059669',
                                    formatter: function(val) {
                                        return val + '%';
                                    }
                                },
                                total: {
                                    ...readinessOptions.plotOptions.radialBar.dataLabels.total,
                                    formatter: function(w) {
                                        return chartData.powerDeliveryPercentage + '%';
                                    }
                                }
                            }
                        }
                    },
                    labels: ['Tersalur']
                };
                
                // Render charts
                const charts = {
                    bar: new ApexCharts(document.querySelector("#unservedLoadChart"), barOptions),
                    readiness: new ApexCharts(document.querySelector("#machineReadinessChart"), readinessOptions),
                    power: new ApexCharts(document.querySelector("#powerDeliveryChart"), powerDeliveryOptions)
                };
                
                Object.values(charts).forEach(chart => chart.render());
            });
            </script>
            @endpush
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loader = document.getElementById('loader');
        const pageContent = document.getElementById('page-content');
        
        // Show loader initially
        loader.classList.remove('loader-hidden');
        
        // Hide loader and show content when page is fully loaded
        window.addEventListener('load', function() {
            setTimeout(() => {
                loader.classList.add('loader-hidden');
                pageContent.classList.add('page-visible');
            }, 500);
        });

        // Add loader for navigation to dashboard
        document.querySelectorAll('a[href*="dashboard"]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                
                // Show loader
                loader.classList.remove('loader-hidden');
                
                // Navigate after small delay
                setTimeout(() => {
                    window.location.href = href;
                }, 300);
            });
        });
    });
</script>
@endsection