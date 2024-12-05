    @extends('layouts.app')

    @section('styles')
        <link rel="stylesheet" href="{{ asset('css/homepage.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <style>
            .navbar {
                background-color: #0095B7; /* Warna navbar */
            }
            .navbar-brand img {
                height: 50px; /* Sesuaikan ukuran logo jika perlu */
            }
            .navbar-nav .nav-link {
                color: white; /* Warna teks menu */
            }
            .navbar-nav .nav-link:hover {
                color: #A8D600; /* Warna saat hover */
            }
            .btn-custom {
                background-color: white; /* Ubah warna tombol login menjadi putih */
                color: #0095B7; /* Ubah warna teks tombol menjadi warna navbar */
                border: 1px solid #0095B7; /* Tambahkan border jika diinginkan */
            }
            .btn-custom:hover {
                background-color: #A8D600; /* Warna saat hover */
                color: white; /* Ubah warna teks saat hover */
            }

            /* Animasi untuk table row */
            .table-row {
                opacity: 0;
                transform: translateY(20px);
                transition: all 0.5s ease-out;
            }

            .table-row.show {
                opacity: 1;
                transform: translateY(0);
            }

            /* Mengatur delay untuk setiap row */
            .table-row:nth-child(1) { transition-delay: 0.1s; }
            .table-row:nth-child(2) { transition-delay: 0.2s; }
            .table-row:nth-child(3) { transition-delay: 0.3s; }
            .table-row:nth-child(4) { transition-delay: 0.4s; }
            .table-row:nth-child(5) { transition-delay: 0.5s; }

            /* Custom navbar styles */
            .navbar-toggler {
                border-color: white;
            }
            
            .navbar-toggler-icon {
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
            }

            .mobile-menu-card {
                background: white;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                margin-top: 10px;
            }

            .mobile-menu-card .nav-link {
                color: #0095B7 !important;
                padding: 10px 15px;
            }

            .mobile-menu-card .nav-link:hover {
                background-color: #f8f9fa;
            }

            @media (max-width: 991px) {
                .desktop-menu {
                    display: none !important;
                }
            }
        </style>
    @endsection

    @section('content')
    <style>
        .logo-left {
            height: 50px; 
            margin-right: 15px; 
        }

        .navbar {
            display: flex;
            align-items: center;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
        }
        
        .btn-custom {
            background-color: #0095B7;
            color: white;
            border: none;
        }

        .nav-links {
            margin-left: 0;
            justify-content: flex-end;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        @media (max-width: 991px) {
            .mobile-menu-card {
                position: absolute;
                right: 0;
                top: 100%;
                width: 200px;
            }
        }
    </style>

    <div class="container my-4">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('logo/navlogo.png') }}" alt="Logo" class="logo-left">
                </a>

                <!-- Desktop Menu -->
                <div class="nav-links desktop-menu ms-auto">
                    <a href="{{ route('login') }}" class="text-white">
                        <i class="fas fa-user"></i> Login
                    </a>
                    <a href="{{ url('/') }}" class="text-white">
                        <i class="fas fa-home"></i> Beranda
                    </a>
                </div>

                <!-- Mobile Menu -->
                <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenu" aria-controls="mobileMenu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mobileMenu">
                    <div class="mobile-menu-card d-lg-none">
                        <a href="{{ route('login') }}" class="nav-link">
                            <i class="fas fa-user me-2"></i> Login
                        </a>
                        <a href="{{ url('/') }}" class="nav-link">
                            <i class="fas fa-home me-2"></i> Beranda
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <h3 class="mt-4">Peta Lokasi Unit Pembangkit</h3>
        <div id="map" style="height: 500px; border: 1px solid #ddd; border-radius: 10px;"></div>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.js"></script>

        <!-- Rest of the content remains exactly the same -->
        <!-- Highlight Kinerja -->
        <h3 class="mt-4 mb-4">Highlight Kinerja</h3>
        <div class="row">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Kapasitas Listrik</h5>
                        <p class="card-text"><strong>{{ $total_capacity }}</strong> MW</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Unit Pembangkit</h5>
                        <p class="card-text"><strong>{{ $total_units }}</strong> Unit</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Unit Pembangkit Aktif</h5>
                        <p class="card-text"><strong>{{ $active_units }}</strong> Unit</p>
                    </div>
                </div>
            </div>
        </div>
        
        <h3 class="mt-4 mb-4">Grafik Line</h3>
        <div id="line-chart" style="height: 500px; border: 1px solid #ddd; border-radius: 10px;"></div>
        
    
        <!-- Live Data Unit Operasional -->
        <h3 class="mt-4">Live Data Unit Operasional</h3>
        <div id="live-data" class="bg-white border border-gray-300 rounded-lg p-4">
            <table class="table table-striped table-bordered">
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
                curve: 'smooth'
            },
            xaxis: {
                categories: ["{{ implode('","', $dates) }}"]
            },
            title: {
                text: 'Grafik Kinerja Pembangkit',
                align: 'center'
            },
            colors: ['#0095B7', '#A8D600', '#FF5733'],
            tooltip: {
                enabled: true
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
