    @extends('layouts.app')

    @section('styles')
        <link rel="stylesheet" href="{{ asset('css/homepage.css') }}">
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
        </style>
    @endsection

    @section('content')
    <style>
        .logo-left {
            height: 50px; 
            margin-right: 15px; 
            
        }

        .navbar {
            display: flex; /* Menggunakan flexbox untuk mengatur posisi */
            align-items: center; /* Menyelaraskan item secara vertikal */
            border-radius: 10px;
            margin-bottom: 30px
        }

        .navbar-brand {
            display: flex; /* Menggunakan flexbox untuk logo */
            align-items: center; /* Menyelaraskan logo secara vertikal */
        }
        .btn-custom {
        background-color: #0095B7; /* Ganti dengan warna yang diinginkan */
        color: white; /* Warna teks */
        border: none; /* Menghilangkan border */
    }
    </style>
    <div class="container my-4">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('logo/navlogo.png') }}" alt="Logo" class="logo-left">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    
                </div>
                <button><a href="{{ route('login') }}" class="btn btn-custom ms-3 d-lg-inline d-block" style="background-color: #A8D600">Login</a></button>
            </div>
            
        </nav>
        



        
        {{-- <h1 class="text-center">Selamat Datang di Sistem Informasi Pembangkit Listrik Sulawesi Tenggara</h1> --}}

        <h3 class="mt-4">Peta Lokasi Unit Pembangkit</h3>
        <div id="map" style="height: 500px; border: 1px solid #ddd; border-radius: 10px;"></div>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.js"></script>
        
            


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
                <tbody>
                    @foreach (array_slice($units, 0, 5) as $unit) <!-- Tampilkan hanya 5 unit -->
                        <tr>
                            <td class="text-center">{{ $unit['power_plant']['name'] ?? 'N/A' }}</td>
                            <td class="text-center">{{ $unit['name'] }}</td>
                            <td class="text-center">{{ $unit['dmn'] ?? 'N/A' }}</td>
                            <td class="text-center">{{ $unit['dmp'] ?? 'N/A' }}</td>
                            <td class="text-center">{{ $unit['load'] ?? 'N/A' }}</td>
                            <td class="text-center {{ $unit['status'] === 'Aktif' ? 'text-success' : 'text-danger' }}">{{ $unit['status'] }}</td>
                            <td class="text-center">{{ $unit['capacity'] }} MW</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button id="show-all" class="btn btn-primary mt-3">Tampilkan Semua Data</button> <!-- Tombol untuk menampilkan semua data -->
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
        // Membuat peta tanpa kontrol zoom dan interaksi dengan zoom out sedikit
        var map = L.map('map', {
                zoomControl: false, // Menonaktifkan tombol zoom
                scrollWheelZoom: false, // Menonaktifkan zoom dengan scroll mouse
                doubleClickZoom: false, // Menonaktifkan zoom dengan klik dua kali
                dragging: false, // Menonaktifkan peta yang bisa digeser
        }).setView([-3.0125, 120.5156], 7); // Mengatur zoom level menjadi 12 dan menggeser ke bawah sedikit
        // Menambahkan layer peta
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        // Menambahkan marker dan popup berdasarkan data dari HomeController
        @foreach($markers as $marker)
            L.marker([{{ $marker['lat'] }}, {{ $marker['lng'] }}]).addTo(map)
                .bindPopup('{{ $marker['name'] }}<br>Kapasitas: {{ $marker['capacity'] }} MW<br>Status: {{ $marker['status'] }}')
                .openPopup();
        @endforeach

        var options = {
                series: [{
                    name: 'Total Kapasitas Listrik',
                    data: [{{ implode(',', $total_capacity_data) }}]
                }, {
                    name: 'Total Unit Pembangkit',
                    data: [{{ implode(',', $total_units_data) }}]
                }, {
                    name: 'Unit Pembangkit Aktif',
                    data: [{{ implode(',', $active_units_data) }}]
                }],
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
                    categories: [{{ implode(',', $dates) }}]
                },
            };
            var chart = new ApexCharts(document.querySelector("#line-chart"), options);
            chart.render();
            
        // Anda dapat menambahkan marker lainnya seperti di atas
    </script>

<script>
    document.getElementById('show-all').addEventListener('click', function() {
        // Mengganti isi tabel dengan semua data
        const tableBody = document.querySelector('#live-data tbody');
        tableBody.innerHTML = ''; // Kosongkan tabel

        @foreach ($units as $unit) // Loop untuk menambahkan semua unit
            const row = `
                <tr>
                    <td class="text-center">{{ $unit['power_plant']['name'] ?? 'N/A' }}</td>
                    <td class="text-center">{{ $unit['name'] }}</td>
                    <td class="text-center">{{ $unit['dmn'] ?? 'N/A' }}</td>
                    <td class="text-center">{{ $unit['dmp'] ?? 'N/A' }}</td>
                    <td class="text-center">{{ $unit['load'] ?? 'N/A' }}</td>
                    <td class="text-center {{ $unit['status'] === 'Aktif' ? 'text-success' : 'text-danger' }}">{{ $unit['status'] }}</td>
                    <td class="text-center">{{ $unit['capacity'] }} MW</td>
                </tr>
            `;
            tableBody.innerHTML += row; // Tambahkan baris ke tabel
        @endforeach
    });
</script>
    @push('scripts')
    @endpush
        


    

    @endsection
