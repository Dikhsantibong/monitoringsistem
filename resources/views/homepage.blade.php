@extends('layouts.app')

@section('content')

<!DOCTYPE html>
<html>
<head>
    <title>Homepage - Peta Mesin</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            height: 400px; /* Tinggi peta */
            border-radius: 10px; /* Sudut bulat pada peta */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Bayangan untuk efek 3D */
            width: 100%; /* Lebar peta */
        }
        .content-section {
            margin: 20px 0;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .container {
            display: flex;
            justify-content: space-between;
        }
        .info-table {
            width: 30%; /* Lebar tabel informasi */
            margin-left: 20px; /* Jarak antara peta dan tabel */
        }
    </style>
</head>
<body>
    <div class="container mx-auto">
        <div>
            <h1 class="text-3xl font-bold text-center my-4">Selamat Datang di Aplikasi Pembangkit Listrik</h1>
            
            <div class="content-section">
                <h2 class="text-xl font-semibold">Peta Lokasi Unit Pembangkit</h2>
                <div id="map"></div>
            </div>
        </div>
        <div class="info-table">
            <div class="content-section">
                <h2 class="text-xl font-semibold">Deskripsi</h2>
                <p>Aplikasi ini memberikan informasi terkini mengenai unit pembangkit listrik yang tersebar di Sulawesi Tenggara. Anda dapat melihat lokasi, status, dan kategori dari setiap unit pembangkit.</p>
            </div>

            <div class="content-section">
                <h2 class="text-xl font-semibold">Statistik Unit Pembangkit</h2>
                <ul>
                    <li>Total Unit Pembangkit: {{ $units->count() }}</li>
                    <li>Total Kapasitas: {{ $units->sum('capacity') }} MW</li>
                    <li>Status Operasional: {{ $units->where('status', 'START')->count() }} Unit Aktif, {{ $units->where('status', 'STOP')->count() }} Unit Tidak Aktif</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Inisialisasi peta
        var map = L.map('map').setView([-4.1449, 122.1746], 8); // Koordinat awal peta di Sulawesi Tenggara

        // Tambahkan tile layer dari OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            minZoom: 8,
            maxZoom: 18
        }).addTo(map);

        // Data mesin dari variabel PHP
        var machines = @json($units);

        // Tambahkan marker ke peta
        machines.forEach(machine => {
            L.marker([machine.latitude, machine.longitude], {
                icon: L.icon({
                    iconUrl: 'path/to/your/icon.png', // Ganti dengan path ke icon Anda
                    iconSize: [50, 50],
                    iconAnchor: [25, 50],
                    popupAnchor: [-3, -76]
                })
            })
            .addTo(map)
            .bindPopup(`<b>${machine.name}</b><br>Status: ${machine.status}<br>Kategori: ${machine.category}`);
        });
    </script>
</body>
</html>

@endsection
