@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/homepage.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .navbar {
            background-color: #0095B7;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: background-color 0.3s;
        }
        
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
        .container {
            padding-top: 80px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 80px 15px 15px;
        }

        h3 {
            font-size: 1.25rem;
            margin: 5px 0;
            text-align: center;
            color: #0095B7;
        }

        /* Hexagon styles */
        .hexagon {
            position: relative;
            width: 150px;
            height: 86.6px;
            background-color: rgba(255, 255, 255, 0.75);
            margin: 43.3px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            transform: rotate(90deg); 
        }
        
        .hexagon:before,
        .hexagon:after {
            content: "";
            position: absolute;
            width: 0;
            border-left: 75px solid transparent;
            border-right: 75px solid transparent;
        }
        .hexagon:before {
            bottom: 100%;
            border-bottom: 43.3px solid rgba(255, 255, 255, 0.75);
        }
        .hexagon:after {
            top: 100%;
            width: 0;
            border-top: 43.3px solid rgba(255, 255, 255, 0.75);
        }
        .hexagon-center {
            width: 200px;
            height: 115.47px;
            background-color: #1E3A8A; /* Dark blue glossy background */
        }
        .hexagon-center:before,
        .hexagon-center:after {
            border-left: 100px solid transparent;
            border-right: 100px solid transparent;
        }
        .hexagon-center:before {
            border-bottom: 57.74px solid #1E3A8A;
        }
        .hexagon-center:after {
            border-top: 57.74px solid #1E3A8A;
        }
        .connection-line {
            position: absolute;
            width: 2px;
            background-color: rgba(255, 255, 255, 0.5);
        }

        /* Background for the hexagon section */
        .hexagon-background {
            background-image: url('{{ asset('background/backgorund.jpg') }}'); // Ganti dengan path yang benar
            background-size: cover;
            background-position: center;
            padding: 50px 0;
            margin-top: 80px;
        }
    </style>
@endsection

@section('content')
<div class="container my-4">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
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

    <div class="hexagon-background">
        
        <div class="relative flex flex-col items-center justify-center">
            <div class="relative">
                <!-- Central Hexagon -->
                <div class="hexagon hexagon-center flex flex-col items-center justify-center" style="transform: rotate(90deg);">
                    <img alt="PLN logo" class="h-12 mb-2" height="100" src="{{ asset('logo/navlogo.png') }}" width="200" style="transform: rotate(-90deg);"/>
                </div>
                <!-- Surrounding Hexagons -->
                <div class="absolute top-0 left-0 transform -translate-x-32 -translate-y-24">
                    <div class="hexagon hexagon-center flex flex-col items-center justify-center" style="transform: rotate(90deg); background-color: #FFDDC1;">
                        <i class="fas fa-handshake text-3xl text-orange-500 mb-2"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Vendor Management</h3>
                    </div>
                </div>
                <div class="absolute top-0 right-0 transform translate-x-32 -translate-y-32">
                    <div class="hexagon hexagon-center flex flex-col items-center justify-center" style="transform: rotate(90deg); background-color: #CFE2F3;">
                        <i class="fas fa-chart-line text-3xl text-orange-500 mb-2"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Demand Management</h3>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 transform -translate-x-32 translate-y-32">
                    <div class="hexagon hexagon-center flex flex-col items-center justify-center" style="transform: rotate(90deg); background-color: #D9EAD3;">
                        <i class="fas fa-shopping-cart text-3xl text-orange-500 mb-2"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Procurement Management</h3>
                    </div>
                </div>
                <div class="absolute bottom-0 right-0 transform translate-x-32 translate-y-32">
                    <div class="hexagon hexagon-center flex flex-col items-center justify-center" style="transform: rotate(90deg); background-color: #F9CB9C;">
                        <i class="fas fa-warehouse text-3xl text-orange-500 mb-2"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Inventory & Warehouse Management</h3>
                    </div>
                </div>
                <!-- Connection Lines -->
                <div class="connection-line" style="top: 50%; left: 50%; height: 100px; transform: translate(-50%, -50%) rotate(45deg);"></div>
                <div class="connection-line" style="top: 50%; left: 50%; height: 100px; transform: translate(-50%, -50%) rotate(-45deg);"></div>
                <div class="connection-line" style="top: 50%; left: 50%; height: 100px; transform: translate(-50%, -50%) rotate(135deg);"></div>
                <div class="connection-line" style="top: 50%; left: 50%; height: 100px; transform: translate(-50%, -50%) rotate(-135deg);"></div>
            </div>
        </div>
    </div>
    
    <div id="map" style="height: 500px; border: 1px solid #ddd; border-radius: 10px; position: relative; margin-top: 100px;">
        
        <div class="accumulation-data-container" style="
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1000;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            padding: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 240px;
            width: 100%;
            box-sizing: border-box;
        ">
            <h3 style="
                color: #0095B7;
                margin-bottom: 10px;
                font-size: 1rem;
                border-bottom: 1px solid #0095B7;
                padding-bottom: 6px;
            ">Data Akumulasi</h3>
            <ul style="
                list-style-type: none;
                padding: 0;
                margin: 0;
            ">
                <li style="
                    margin: 6px 0;
                    color: #333;
                    display: flex;
                    align-items: center;
                ">
                    <span style="
                        width: 8px;
                        height: 8px;
                        background-color: #0095B7;
                        border-radius: 50%;
                        margin-right: 8px;
                    "></span>
                    Proxy Assistance: Medium (ID: 172.16.1.40)
                </li>
                <li style="
                    margin: 6px 0;
                    color: #333;
                    display: flex;
                    align-items: center;
                ">
                    <span style="
                        width: 8px;
                        height: 8px;
                        background-color: #0095B7;
                        border-radius: 50%;
                        margin-right: 8px;
                    "></span>
                    Proxy Assistance: Medium (ID: 172.16.1.41)
                </li>
                <li style="
                    margin: 6px 0;
                    color: #333;
                    display: flex;
                    align-items: center;
                ">
                    <span style="
                        width: 8px;
                        height: 8px;
                        background-color: #0095B7;
                        border-radius: 50%;
                        margin-right: 8px;
                    "></span>
                    Proxy Assistance: Medium (ID: 172.16.1.42)
                </li>
            </ul>
        </div>
    </div>
</div>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.js"></script>

    <!-- Rest of the content remains exactly the same -->
    <!-- Highlight Kinerja -->
    <h3 class="mt-4 mb-4">Highlight Kinerja</h3>
    <div class="row">
        <div class="col-md-4">
            <div class="bg-box">
                <h3 class="text-title">TOTAL KAPASITAS LISTRIK</h3>
                <p class="text-value">{{ $total_capacity }} MW</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-box">
                <h3 class="text-title">TOTAL UNIT PEMBANGKIT</h3>
                <p class="text-value">{{ $total_units }} UNIT</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-box">
                <h3 class="text-title">UNIT PEMBANGKIT AKTIF</h3>
                <p class="text-value">{{ $active_units }} UNIT</p>
            </div>
        </div>
    </div>
    
    <h3 class="mt-4 mb-4">Grafik Line</h3>
    <div id="line-chart" style="height: 500px; border: 1px solid #ddd; border-radius: 10px;"></div>
    

    <!-- Live Data Unit Operasional -->
    <h3 class="mt-4">Live Data Unit Operasional</h3>
    <div id="live-data" class="bg-white border border-gray-300 rounded-lg p-4">
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
                            <td class="text-center {{ $unit->status === 'Aktif' ? 'text-success' : 'text-danger' }}">{{ $unit->status }}</td>
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

<!-- Footer -->
<footer class="footer w-screen">
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
        series: [
            {
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
        yaxis: [
            {
                title: {
                    text: 'Nilai'
                }
            }
        ],
        title: {
            text: 'Grafik Kinerja Pembangkit',
            align: 'center'
        },
        colors: [
            '#FF1E1E',  // Merah untuk Total Kapasitas Listrik
            '#00B050',  // Hijau untuk Total Unit Pembangkit
            '#0070C0',  // Biru untuk Unit Pembangkit Aktif
            '#7030A0',  // Ungu untuk DMN
            '#FFC000',  // Kuning untuk DMP
            '#ED7D31',  // Oranye untuk Beban
            '#4472C4'   // Biru Tua untuk Kapasitas Unit
        ],
        tooltip: {
            enabled: true,
            y: {
                formatter: function(value, { seriesIndex }) {
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

    @foreach($markers as $marker)
        L.marker([{{ $marker['lat'] }}, {{ $marker['lng'] }}]).addTo(map)
            .bindPopup('{{ $marker['name'] }}<br>Kapasitas: {{ $marker['capacity'] }} MW<br>Status: {{ $marker['status'] }}')
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