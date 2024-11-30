@extends('layouts.app')

@section('content')
<div class="container my-4" style="background-color: #f5f5f5; color: #333;">
    <h1 class="text-center" style="color: #007bff;">Selamat Datang di Sistem Informasi Pembangkit Listrik Sulawesi Tenggara</h1>

    <!-- Peta Interaktif -->
    <h3 class="mt-4" style="color: #007bff;">Peta Lokasi Unit Pembangkit</h3>
    <div id="map" style="height: 500px; border: 1px solid #ddd; border-radius: 10px;"></div>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    

    <!-- Highlight Kinerja -->
    <h3 class="mt-4" style="color: #007bff;">Highlight Kinerja</h3>
    <div class="row">
        <div class="col-md-4">
            <div class="card text-center" style="background-color: #fff; border: 1px solid #ddd; border-radius: 10px;">
                <div class="card-body">
                    <h5 class="card-title">Total Kapasitas Listrik</h5>
                    <p class="card-text"><strong>{{ $total_capacity }}</strong> MW</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center" style="background-color: #fff; border: 1px solid #ddd; border-radius: 10px;">
                <div class="card-body">
                    <h5 class="card-title">Jumlah Unit Operasional</h5>
                    <p class="card-text"><strong>{{ $total_units }}</strong> Unit</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center" style="background-color: #fff; border: 1px solid #ddd; border-radius: 10px;">
                <div class="card-body">
                    <h5 class="card-title">Kontribusi Energi Hijau</h5>
                    <p class="card-text"><strong>{{ $green_energy_contribution }}</strong></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Berita Terkini -->
    <h3 class="mt-4" style="color: #007bff;">Berita Terkini</h3>
    <div class="list-group">
        @foreach ($news as $item)
            <a href="{{ route('news.show', $item->id) }}" class="list-group-item list-group-item-action mb-2" style="background-color: #fff; border: 1px solid #ddd; border-radius: 10px;">
                <h5 class="mb-1">{{ $item->title }}</h5>
                <img src="{{ $item->thumbnail }}" alt="Thumbnail" class="img-fluid mb-1">
                <p class="mb-1">{{ $item->summary }}</p>
                <small>{{ $item->created_at->format('d M Y') }}</small>
                <button class="btn btn-primary">Baca Selengkapnya</button>
            </a>
        @endforeach
    </div>

    <!-- Informasi Cuaca -->
    <h3 class="mt-4" style="color: #007bff;">Informasi Cuaca</h3>
    <div id="weather-widget" style="background-color: #fff; border: 1px solid #ddd; border-radius: 10px;">
        <!-- Widget cuaca akan diisi di sini -->
    </div>

    {{-- <!-- Video Profil Perusahaan -->
    <h3 class="mt-4" style="color: #007bff;">Video Profil Perusahaan</h3>
    <div class="embed-responsive embed-responsive-16by9" style="background-color: #fff; border: 1px solid #ddd; border-radius: 10px;">
        <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/gpZ5CqSLbh0" allowfullscreen></iframe>
    </div>
    <script>
        $(document).ready(function(){
            $('.embed-responsive').click(function(){
                $(this).toggleClass('embed-responsive-16by9').toggleClass('embed-responsive-4by3');
            });
        });
    </script> --}}

    <!-- Pengumuman Penting -->
    <h3 class="mt-4" style="color: #007bff;">Pengumuman Penting</h3>
    <div class="alert alert-warning" style="background-color: #fff; border: 1px solid #ddd; border-radius: 10px;">
        <strong>Pemberitahuan:</strong> Pemeliharaan Unit Kendari II, 1 Desember 2024.
    </div>

    <!-- FAQ -->
    <h3 class="mt-4" style="color: #007bff;">FAQ</h3>
    <div class="accordion" id="faqAccordion">
        <div class="card" style="background-color: #fff; border: 1px solid #ddd; border-radius: 10px;">
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
    </div>

    <!-- Galeri Foto -->
    <h3 class="mt-4" style="color: #007bff;">Galeri Foto</h3>
    <div class="row">
        @foreach ($photos as $photo)
            <div class="col-md-3">
                <img src="{{ $photo->url }}" class="img-fluid" alt="Gallery Image" style="border-radius: 10px;">
            </div>
        @endforeach
    </div>

    <!-- Live Data Unit Operasional -->
    <h3 class="mt-4" style="color: #007bff;">Live Data Unit Operasional</h3>
    <div id="live-data" style="background-color: #fff; border: 1px solid #ddd; border-radius: 10px;">
        <!-- Data real-time akan ditampilkan di sini -->
    </div>

    <!-- Formulir Kontak -->
    <h3 class="mt-4" style="color: #007bff;">Formulir Kontak</h3>
    <form action="{{ route('contact.submit') }}" method="POST" style="background-color: #fff; border: 1px solid #ddd; border-radius: 10px;">
        @csrf
        <div class="form-group">
            <label for="name">Nama</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="message">Pesan</label>
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
    <h3 class="mt-4" style="color: #007bff;">Blog Edukasi</h3>
    <div class="row">
        @foreach ($educationalBlogs as $blog)
            <div class="col-md-4">
                <div class="card" style="background-color: #fff; border: 1px solid #ddd; border-radius: 10px;">
                    <div class="card-body">
                        <h5 class="card-title">{{ $blog->title }}</h5>
                        <p class="card-text">{{ $blog->summary }}</p>
                        <a href="{{ route('blog.show', $blog->id) }}" class="btn btn-primary">Baca Selengkapnya</a>
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
@endsection