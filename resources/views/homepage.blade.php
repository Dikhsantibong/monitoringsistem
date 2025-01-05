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
        }

        .desktop-menu a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        /* Mobile menu styles */
        .navbar-toggler {
            display: none;
            border: none;
            padding: 0.25rem 0.75rem;
            font-size: 1.25rem;
            background-color: transparent;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .mobile-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 0;
            min-width: 200px;
            z-index: 1001;
        }

        .mobile-menu a {
            display: block;
            padding: 0.5rem 1rem;
            color: #0095B7;
            text-decoration: none;
        }

        .mobile-menu a:hover {
            background-color: #f8f9fa;
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
                            <div class="hidden md:flex items-center">
                                <ul class="flex space-x-8">
                                    <li><a href="#" class="nav-link">Home</a></li>
                                    <li><a href="#map" class="nav-link">Peta Pembangkit</a></li>
                                    <li><a href="#grafik" class="nav-link">Grafik Kinerja</a></li>
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
                                <li><a href="#grafik" class="nav-link-mobile">Grafik Kinerja</a></li>
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
                        <h2 class="text-6xl font-bold mb-8 text-center" style="color: #FFFF00; text-shadow: 1px 1px 2px #000000;">
                            M<i class="fas fa-helmet-safety"></i>NDAY <br>MONITORING DAILY
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
            <div id="map"
                style="height: 500px; border-radius: 20px; position: relative; margin: 100px 30px 0; padding: 0; "
                class="z-0">
            </div>

            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.js"></script>

            <!-- Rest of the content remains exactly the same -->
            <!-- Highlight Kinerja -->
            {{-- <h3 class="mt-10 mb-4 text-2xl font-semibold">Highlight Kinerja</h3>
            <div class="flex justify-center gap-5">
                <div class="bg-box">
                    <h3 class="text-title">TOTAL KAPASITAS LISTRIK</h3>
                    <p class="text-value">{{ $total_capacity }} MW</p>
                </div>
                <div class="bg-box">
                    <h3 class="text-title">TOTAL UNIT PEMBANGKIT</h3>
                    <p class="text-value">{{ $total_units }} UNIT</p>
                </div>
                <div class="bg-box">
                    <h3 class="text-title">UNIT PEMBANGKIT AKTIF</h3>
                    <p class="text-value">{{ $active_units }} UNIT</p>
                </div>
            </div> --}}

            <div class="w-full flex justify-center flex-col items-center">
                <h3 class="mt-4 mb-4 text-xl font-semibold">Grafik Line</h3>
                <div id="line-chart" style="border: 1px solid #ddd; border-radius: 10px;"
                    class="w-4/5 flex justify-center">
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
                                @foreach ($units as $log)
                                    <tr class="table-row">
                                        <td class="text-center">{{ $log->machine->powerPlant->name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $log->machine->name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $log->dmn ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $log->dmp ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $log->load_value ?? 'N/A' }}</td>
                                        <td class="text-center">
                                            <span class="px-2 py-1 rounded-full bg-red-100 text-red-600">
                                                {{ $log->status }}
                                            </span>
                                        </td>
                                       
                                        <td class="text-center text-sm text-gray-500">
                                            {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- </div> --}}

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
                    <div class="column">
                        <h4>Strategic Office</h4>
                        <p>18 Office Park, Lt.2 ABCD</p>
                        <p>Jl. TB Simatupang No.18, Jakarta Selatan, Indonesia</p>
                    </div>
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
                }).setView([-4.0435, 122.4972], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                @foreach ($markers as $marker)
                    var marker = L.marker([{{ $marker['latitude'] }}, {{ $marker['longitude'] }}])
                        .addTo(map)
                        .bindPopup(`
                            <div style="min-width: 200px;">
                                <h3 style="margin: 0 0 10px 0;">{{ $marker['name'] }}</h3>
                                <p style="margin: 5px 0;">
                                    <strong>Total Mesin:</strong> {{ $marker['total_machines'] }}<br>
                                    <strong>Mesin Aktif:</strong> {{ $marker['active_machines'] }}<br>
                                    <strong>Total Kapasitas:</strong> {{ $marker['total_capacity'] }} MW
                                </p>
                                <button onclick="showAccumulationData({{ $marker['id'] }})" 
                                        style="background: #0095B7; color: white; border: none; 
                                               padding: 5px 10px; border-radius: 4px; 
                                               cursor: pointer; width: 100%;">
                                    Lihat Detail Gangguan
                                </button>
                            </div>
                        `);
                @endforeach

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
