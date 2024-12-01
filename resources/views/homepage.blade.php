    @extends('layouts.app')

    @section('content')
    


    <div class="container my-4" style="background-color: #fff; color: #333;">
        <h1 class="text-center" style="color: #0288d1;">Selamat Datang di Sistem Informasi Pembangkit Listrik Sulawesi Tenggara</h1>

        <!-- Peta Interaktif -->
        <h3 class="mt-4" style="color: #0288d1; font-family: 'Arial', sans-serif; font-weight: bold;">Peta Lokasi Unit Pembangkit</h3>
        <div id="map" style="height: 500px; border: 1px solid #ddd; border-radius: 10px;"></div>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            var map = L.map('map', {
                zoomControl: false, // Menambahkan tombol zoom
                scrollWheelZoom: false, // Menonaktifkan zoom dengan scroll mouse
                doubleClickZoom: false,
                center: [-6.200000, 106.816666], // Menonaktifkan zoom dengan klik dua kali
                dragging: false, // Menonaktifkan peta yang bisa digeser
            }).setView([-3.9875, 122.5156], 13);
        
            // Menambahkan layer peta
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 8,
            }).addTo(map);
            var markers = [
                {lat: -3.9875, lng: 122.5156, name: 'Sistem Interkoneksi Kendari', capacity: 100, status: 'Aktif'},
                {lat: -4.0272, lng: 122.6025, name: 'PLTD Poasia', capacity: 50, status: 'Tidak Aktif'},
                {lat: -4.0486, lng: 122.4851, name: 'PLTMG Kendari', capacity: 20, status: 'Aktif'},
                {lat: -4.0333, lng: 121.5833, name: 'PLTD Kolaka', capacity: 30, status: 'Aktif'},
                {lat: -3.9972, lng: 122.6214, name: 'PLTD Lanipa Nipa', capacity: 40, status: 'Tidak Aktif'},
                {lat: -4.0222, lng: 122.6167, name: 'PLTD Ladumpi', capacity: 60, status: 'Aktif'},
                {lat: -4.0342, lng: 122.5217, name: 'PLTM Sabilambo', capacity: 10, status: 'Tidak Aktif'},
                {lat: -4.1000, lng: 122.6000, name: 'PLTM Mikuasi', capacity: 25, status: 'Aktif'},
                {lat: -5.4670, lng: 122.6173, name: 'Sistem Interkoneksi Bau Bau', capacity: 150, status: 'Aktif'},
                {lat: -5.4282, lng: 122.6220, name: 'PLTD Pasarwajo', capacity: 75, status: 'Tidak Aktif'},
                {lat: -5.4673, lng: 122.6158, name: 'PLTMG Bau Bau', capacity: 35, status: 'Aktif'},
                {lat: -5.4772, lng: 122.6325, name: 'PLTM Winning', capacity: 45, status: 'Tidak Aktif'},
                {lat: -5.4833, lng: 122.6500, name: 'PLTM Rongi', capacity: 55, status: 'Aktif'},
                {lat: -4.8610, lng: 122.6536, name: 'PLTD Raha', capacity: 65, status: 'Tidak Aktif'},
                {lat: -5.3144, lng: 123.5803, name: 'PLTD Wangi-Wangi', capacity: 80, status: 'Aktif'},
                {lat: -4.0178, lng: 122.9178, name: 'PLTD Langara', capacity: 90, status: 'Tidak Aktif'},
                {lat: -4.4961, lng: 123.1042, name: 'PLTD Ereke', capacity: 95, status: 'Aktif'},
            ];
            markers.forEach(function(marker) {
                L.marker([marker.lat, marker.lng]).addTo(map)
                    .bindPopup(`<strong>${marker.name}</strong><br>Status: ${marker.status}<br>Kapasitas: ${marker.capacity} MW`)
                    .openPopup();
            });
        </script>
        

        <!-- Highlight Kinerja -->
        <h3 class="mt-4" style="color: #0288d1;">Highlight Kinerja</h3>
        <div class="row">
            <div class="col-md-4">
                <div class="card text-center" style="background-color: #e8f5e9; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);">
                    <div class="card-body">
                        <h5 class="card-title" style="color: #0288d1; font-weight: bold;">Total Kapasitas Listrik</h5>
                        <p class="card-text"><strong>{{ $total_capacity }}</strong> MW</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center" style="background-color: #e8f5e9; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);">
                    <div class="card-body">
                        <h5 class="card-title" style="color: #0288d1; font-weight: bold;">Jumlah Unit Operasional</h5>
                        <p class="card-text"><strong>{{ $total_units }}</strong> Unit</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center" style="background-color: #e8f5e9; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);">
                    <div class="card-body">
                        <h5 class="card-title" style="color: #0288d1; font-weight: bold;">Kontribusi Energi Hijau</h5>
                        <p class="card-text"><strong>{{ $green_energy_contribution }}</strong></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- <!-- Berita Terkini -->
        <h3 class="mt-4" style="color: #0288d1;">Berita Terkini</h3>
        <div class="list-group">
            @foreach ($news as $item)
                <a href="{{ route('news.show', $item->id) }}" class="list-group-item list-group-item-action mb-2" style="background-color: #e8f5e9; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);">
                    <h5 class="mb-1" style="color: #0288d1;">{{ $item->title }}</h5>
                    <img src="{{ $item->thumbnail }}" alt="Thumbnail" class="img-fluid mb-1">
                    <p class="mb-1">{{ $item->summary }}</p>
                    <small>{{ $item->created_at->format('d M Y') }}</small>
                    <button class="btn btn-primary">Baca Selengkapnya</button>
                </a>
            @endforeach
        </div> --}}

        {{-- <!-- Informasi Cuaca -->
        <h3 class="mt-4" style="color: #0288d1;">Informasi Cuaca</h3>
        <div id="weather-widget" style="background-color: #e8f5e9; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);">
            <!-- Widget cuaca akan diisi di sini -->
        </div> --}}

        {{-- <!-- Video Profil Perusahaan -->
        <h3 class="mt-4" style="color: #0288d1;">Video Profil Perusahaan</h3>
        <div class="embed-responsive embed-responsive-16by9" style="background-color: #e8f5e9; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);">
            <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/gpZ5CqSLbh0" allowfullscreen></iframe>
        </div>
        <script>
            $(document).ready(function(){
                $('.embed-responsive').click(function(){
                    $(this).toggleClass('embed-responsive-16by9').toggleClass('embed-responsive-4by3');
                });
            });
        </script> --}}

        {{-- <!-- Pengumuman Penting -->
        <h3 class="mt-4" style="color: #0288d1;">Pengumuman Penting</h3>
        <div class="alert alert-warning" style="background-color: #e8f5e9; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);">
            <strong>Pemberitahuan:</strong> Pemeliharaan Unit Kendari II, 1 Desember 2024.
        </div>

        <!-- FAQ -->
        <h3 class="mt-4" style="color: #0288d1;">FAQ</h3>
        <div class="accordion" id="faqAccordion">
            <div class="card" style="background-color: #e8f5e9; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);">
                <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Apa itu pembangkit listrik?
                        </button>
                    </h2>
                </div>
                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#faqAccordion">
                    <div class="card-body">
                        Pembangkit listrik adalah fasilitas yang menghasilkan listrik dari sumber energi.
                    </div>
                </div>
            </div>
            <!-- Tambahkan lebih banyak pertanyaan di sini -->
        </div>

        <!-- Testimoni dan Cerita Inspiratif -->
        <h3 class="mt-4" style="color: #007bff;">Testimoni dan Cerita Inspiratif</h3>
        <div class="row">
            @foreach ($testimonials as $testimonial)
                <div class="col-md-4">
                    <div class="card" style="background-color: #fff; border: 1px solid #ddd; border-radius: 10px;">
                        <div class="card-body">
                            <h5 class="card-title">{{ $testimonial->name }}</h5>
                            <p class="card-text">{{ $testimonial->message }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Program CSR -->
        <h3 class="mt-4" style="color: #007bff;">Program CSR</h3>
        <div class="row">
            @foreach ($csrPrograms as $program)
                <div class="col-md-4">
                    <div class="card" style="background-color: #fff; border: 1px solid #ddd; border-radius: 10px;">
                        <div class="card-body">
                            <h5 class="card-title">{{ $program->title }}</h5>
                            <p class="card-text">{{ $program->description }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div> --}}

        <!-- Galeri Foto -->
        <h3 class="mt-4" style="color: #007bff;">Galeri Foto</h3>
        <div class="row justify-content-center">
            @foreach ($photos as $photo)
                <div class="col-md-3 d-flex justify-content-center mb-4">
                    <img src="{{ $photo->url }}" class="img-fluid" alt="Gallery Image" style="border-radius: 10px;">
                </div>
            @endforeach
        </div>

        <!-- Live Data Unit Operasional -->
        <h3 class="mt-4" style="color: #007bff;">Live Data Unit Operasional</h3>
        <div id="live-data" class="bg-white border border-gray-300 rounded-lg p-4">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">Nama Unit</th>
                        <th class="text-center">Latitude</th>
                        <th class="text-center">Longitude</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Kapasitas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($units as $unit)
                        <tr>
                            <td class="text-center">{{ $unit->name }}</td>
                            <td class="text-center">{{ $unit->latitude }}</td>
                            <td class="text-center">{{ $unit->longitude }}</td>
                            <td class="text-center {{ $unit->status == 'Aktif' ? 'text-success' : 'text-danger' }}">{{ $unit->status }}</td>
                            <td class="text-center">{{ $unit->capacity }} MW</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Formulir Kontak -->
        <h3 class="mt-4" style="color: #007bff;">Formulir Kontak</h3>
        <form action="{{ route('contact.submit') }}" method="POST" style="background-color: #fff; border: 1px solid #ddd; border-radius: 10px; padding: 20px;">
            @csrf
            <div class="form-group mb-3">
                <label for="name" class="form-label">Nama</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group mb-3">
                <label for="message" class="form-label">Pesan</label>
                <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Kirim</button>
        </form>

        {{-- <!-- Informasi Rekrutmen -->
        <h3 class="mt-4" style="color: #007bff;">Informasi Rekrutmen</h3>
        <div class="alert alert-info" style="background-color: #fff; border: 1px solid #ddd; border-radius: 10px;">
            <strong>Lowongan Pekerjaan:</strong> Kami sedang mencari tenaga kerja untuk posisi teknisi.
        </div> --}}
    {{-- 
        <!-- Event Kalender -->
        <h3 class="mt-4" style="color: #007bff;">Event Kalender</h3>
        <div id="calendar" style="background-color: #fff; border: 1px solid #ddd; border-radius: 10px;">
            <!-- Kalender interaktif akan ditampilkan di sini -->
        </div> --}}

        {{-- <!-- Partner dan Kerja Sama -->
        <h3 class="mt-4" style="color: #007bff;">Partner dan Kerja Sama</h3>
        <div class="row">
            @foreach ($partners as $partner)
                <div class="col-md-2">
                    <img src="{{ $partner->logo }}" class="img-fluid" alt="Partner Logo" style="border-radius: 10px;">
                </div>
            @endforeach
        </div>

        <!-- Performa Lingkungan -->
        <h3 class="mt-4" style="color: #007bff;">Performa Lingkungan</h3>
        <div id="environmental-performance" style="background-color: #fff; border: 1px solid #ddd; border-radius: 10px;">
            <!-- Data performa lingkungan akan ditampilkan di sini -->
        </div> --}}

        <!-- Blog Edukasi -->
        <h3 class="mt-4 text-primary">Blog Edukasi</h3>
        <div class="row justify-content-center">
            @foreach (array_slice($educationalBlogs, 0, 3) as $blog)
                <div class="col-md-4 mb-4">
                    <div class="card bg-white border border-gray-300 rounded-lg shadow">
                        <div class="card-body">
                            <h5 class="card-title text-center mb-3">{{ $blog->title }}</h5>
                            <p class="card-text text-center">{{ $blog->summary }}</p>
                            <div class="text-center">
                                <a href="{{ route('blog.show', $blog->id) }}" class="btn btn-primary">Baca Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Statistik Pengunjung -->
        <h3 class="mt-4" style="color: #007bff;">Statistik Pengunjung</h3>
        <div id="visitor-statistics" style="background-color: #fff; border: 1px solid #ddd; border-radius: 10px;">
            <!-- Statistik pengunjung akan ditampilkan di sini -->
        </div>
    </div>
    @section('styles')

    <script>
        // Inisialisasi Peta
        var map = L.map('map').setView([-4.0096, 122.512], 7); // Koordinat tengah Sulawesi Tenggara
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Data Lokasi Pembangkit
        var units = @json($units);

        // Tambahkan Marker ke Peta
        units.forEach(function(unit) {
            var marker = L.marker([unit.latitude, unit.longitude]).addTo(map);
            marker.bindPopup(`<strong>${unit.name}</strong><br>Status: ${unit.status}<br>Kapasitas: ${unit.capacity} MW`);
        });
    </script>
    @push('scripts')
        
    @endpush
    @endsection