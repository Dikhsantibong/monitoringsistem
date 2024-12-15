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
            /* width: 200px;
                height: 170px; */
            margin: 28.87px 0;
            clip-path: polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%);
        }
    </style>
@endsection

@section('content')
    <div class="w-full">
        <nav class="fixed bg-cyan-600 left-0 top-0 right-0 z-50 py-3 px-6">
            <div class="container-fluid flex justify-between w-full items-center">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('logo/navlogo.png') }}" alt="Logo" class="logo-left">
                </a>
                <button class="navbar-toggler" type="button" onclick="toggleMobileMenu()">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="desktop-menu ms-auto">
                    <a href="{{ route('login') }}">
                        <i class="fas fa-user"></i> Login
                    </a>
                    <a href="{{ url('/') }}">
                        <i class="fas fa-home"></i> Beranda
                    </a>
                </div>
                <div class="mobile-menu" id="mobileMenu">
                    <a href="{{ route('login') }}">
                        <i class="fas fa-user"></i> Login
                    </a>
                    <a href="{{ url('/') }}">
                        <i class="fas fa-home"></i> Beranda
                    </a>
                </div>
            </div>
        </nav>

        <script>
            function toggleMobileMenu() {
                document.getElementById('mobileMenu').classList.toggle('show');
            }

            // Menutup dropdown saat mengklik di luar
            document.addEventListener('click', function(event) {
                const mobileMenu = document.getElementById('mobileMenu');
                const navbarToggler = document.querySelector('.navbar-toggler');

                if (!mobileMenu.contains(event.target) && !navbarToggler.contains(event.target)) {
                    mobileMenu.classList.remove('show');
                }
            });

            // Scroll handling untuk navbar
            let lastScrollTop = 0;
            const navbar = document.querySelector('.navbar');

            window.addEventListener('scroll', () => {
                let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                if (scrollTop > lastScrollTop) {
                    // Scroll ke bawah
                    navbar.classList.add('hidden');
                } else {
                    // Scroll ke atas
                    navbar.classList.remove('hidden');
                }

                lastScrollTop = scrollTop;
            });
        </script>

        {{-- Hero section --}}
        <div class="bg-gray-100 h-screen flex justify-center items-center hexagon-background">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-white h-screen bg-opacity-50"></div>
            <div class="flex gap-2 lg:gap-0 lg:grid grid-cols-2 lg:grid-cols-3">
                <div>
                    <div
                        class="hexagon bg-[#0A749B] bg-opacity-55 flex flex-col items-center justify-center hover:bg-opacity-100 h-36 w-40 md:w-56 md:h-44">
                        {{-- h-44 w-56 --}}
                        <h5 class="text-sm lg:text-2xl md:text-xl font-bold text-gray-50 ">ULPLTD <br> WUA-WUA
                        </h5>
                    </div>
                    <div
                        class="hexagon bg-[#0A749B] bg-opacity-55 flex flex-col items-center justify-center hover:bg-opacity-100 h-36 w-40 md:w-56 md:h-44">
                        <h5 class="text-sm lg:text-2xl md:text-xl font-bold text-gray-50">ULPLTD <br>POASIA
                        </h5>
                    </div>
                </div>
                <div class="flex items-center justify-center">
                    <div class="hidden lg:block md:block">
                        <div class="hexagon flex items-center bg-white h-36 w-40 md:w-56 md:h-44">
                            <div>
                                <img alt="PLN logo" src="{{ asset('logo/navlog1.png') }}" />
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div
                        class="hexagon bg-[#0A749B] bg-opacity-55 flex flex-col items-center justify-center hover:bg-opacity-100 h-36 w-40 md:w-56 md:h-44 border">
                        <h5 class="text-sm lg:text-2xl md:text-xl font-bold text-gray-50">ULPLTD <br> KOLAKA
                        </h5>
                    </div>
                    <div
                        class="hexagon bg-[#0A749B] bg-opacity-55 flex flex-col items-center justify-center hover:bg-opacity-100 h-36 w-40 md:w-56 md:h-44 border">
                        <h5 class="text-sm lg:text-2xl md:text-xl    font-bold text-gray-50">ULPLTD <br> BAU-BAU</h5>
                    </div>
                </div>
            </div>
        </div>

        {{-- Map --}}
        <div id="map"
            style="height: 500px; border: 1px solid #ddd; position: relative; margin-top: 100px"
            class="z-0">

            <div class="accumulation-data-container"
                style="
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 9999999999;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            padding: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 240px;
            width: 100%;
            box-sizing: border-box;
        ">
                <h3
                    style="
                color: #0095B7;
                margin-bottom: 10px;
                font-size: 1rem;
                border-bottom: 1px solid #0095B7;
                padding-bottom: 6px;
            ">
                    Data Akumulasi</h3>
                <ul
                    style="
                list-style-type: none;
                padding: 0;
                margin: 0;
            ">
                    @if(isset($machineStatusLogs))
                        @foreach ($machineStatusLogs as $log)
                        <li style="margin: 6px 0; color: #333; display: flex; align-items: center;">
                            <span style="width: 8px; height: 8px; background-color: #0095B7; border-radius: 50%; margin-right: 8px;"></span>
                            Proxy Assistance: Medium (ID: {{ $log->machine_id }}) - Asal Unit: {{ $log->powerPlant->name ?? 'N/A' }}
                        </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.js"></script>

    <!-- Rest of the content remains exactly the same -->
    <!-- Highlight Kinerja -->
    <h3 class="mt-10 mb-4 text-2xl font-semibold">Highlight Kinerja</h3>
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
    </div>

    <div class="w-full flex justify-center flex-col items-center">
        <h3 class="mt-4 mb-4 text-xl font-semibold">Grafik Line</h3>
        <div id="line-chart" style="border: 1px solid #ddd; border-radius: 10px;"
            class="w-4/5 flex justify-center">
        </div>
    </div>


    <!-- Live Data Unit Operasional -->
    <h3 class="mt-10 mb-4 text-xl font-semibold">Live Data Unit Operasional</h3>
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
                            <th class="text-center">Kapasitas</th>
                        </tr>
                    </thead>
                    <tbody id="unit-table-body">
                        @foreach ($units->take(5) as $unit)
                            <tr class="table-row">
                                <td class="text-center">{{ $unit->powerPlant->name ?? 'N/A' }}</td>
                                <td class="text-center">{{ $unit->name }}</td>
                                <td class="text-center">{{ $unit->machineOperations->first()->dmn ?? 'N/A' }}</td>
                                <td class="text-center">{{ $unit->machineOperations->first()->dmp ?? 'N/A' }}</td>
                                <td class="text-center">{{ $unit->machineOperations->first()->load_value ?? 'N/A' }}</td>
                                <td class="text-center {{ $unit->status === 'Aktif' ? 'text-success' : 'text-danger' }}">
                                    {{ $unit->status }}</td>
                                <td class="text-center">{{ $unit->capacity }} MW</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="toggle-data" class="mt-3 text-center">
                <i class="fas fa-arrow-down fa-2x animate-pulse" style="color: #0095B7; cursor: pointer;"></i>
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
            zoomControl: false,
            scrollWheelZoom: false,
            doubleClickZoom: false,
            dragging: false,
        }).setView([-3.0125, 120.5156], 7);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        @foreach ($markers as $marker)
            L.marker([{{ $marker['lat'] }}, {{ $marker['lng'] }}]).addTo(map)
                .bindPopup(
                    '{{ $marker['name'] }}<br>Kapasitas: {{ $marker['capacity'] }} MW<br>Status: {{ $marker['status'] }}'
                )
                .openPopup();
        @endforeach

        document.addEventListener('DOMContentLoaded', function() {
            const tableBody = document.getElementById('unit-table-body');
            const toggleButton = document.getElementById('toggle-data');
            let showingAll = false;

            const units = @json($units);

            function renderTable(showAll) {
                const existingRows = tableBody.querySelectorAll('tr');
                existingRows.forEach(row => {
                    row.style.opacity = '0';
                    row.style.transform = 'translateY(-20px)';
                });

                setTimeout(() => {
                    tableBody.innerHTML = '';
                    const displayUnits = showAll ? units : units.slice(0, 5);

                    displayUnits.forEach((unit, index) => {
                        const row = document.createElement('tr');
                        row.className = 'table-row';
                        row.innerHTML = `
                        <td class="text-center">${unit.power_plant?.name ?? 'N/A'}</td>
                        <td class="text-center">${unit.name}</td>
                        <td class="text-center">${unit.dmn ?? 'N/A'}</td>
                        <td class="text-center">${unit.dmp ?? 'N/A'}</td>
                        <td class="text-center">${unit.load ?? 'N/A'}</td>
                        <td class="text-center ${unit.status === 'Aktif' ? 'text-success' : 'text-danger'}">${unit.status}</td>
                        <td class="text-center">${unit.capacity} MW</td>
                    `;
                        tableBody.appendChild(row);

                        row.offsetHeight;

                        setTimeout(() => {
                            row.classList.add('show');
                        }, 50 * index);
                    });
                }, 300);

                toggleButton.innerHTML = showAll ?
                    '<i class="fas fa-arrow-up fa-2x animate-pulse" style="color: #0095B7; cursor: pointer;"></i>' :
                    '<i class="fas fa-arrow-down fa-2x animate-pulse" style="color: #0095B7; cursor: pointer;"></i>';
            }

            toggleButton.addEventListener('click', function() {
                showingAll = !showingAll;
                renderTable(showingAll);

                if (showingAll) {
                    const tableBottom = tableBody.getBoundingClientRect().bottom;
                    window.scrollTo({
                        top: window.scrollY + tableBottom - window.innerHeight + 100,
                        behavior: 'smooth'
                    });
                }
            });

            document.querySelectorAll('.table-row').forEach((row, index) => {
                setTimeout(() => {
                    row.classList.add('show');
                }, 100 * index);
            });
        });
    </script>
    @push('scripts')
    @endpush
@endsection
