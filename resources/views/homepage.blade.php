@extends('layouts.app')

@section('content')

<!DOCTYPE html>
<html>
<head>
    <title>Homepage - Peta Mesin</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body, html {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }
        #map {
            height: 100%;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: -1;
        }
        .navbar {
            display: flex;
            justify-content: space-between; /* Memisahkan elemen di navbar */
            align-items: center; /* Vertikal center */
            background-color: rgba(255, 255, 255, 0.8);
            padding: 10px 20px; /* Padding untuk navbar */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
        }
        .navbar .title {
            font-size: 24px; /* Ukuran font untuk judul */
            font-weight: bold; /* Bold untuk judul */
            color: #333; /* Warna teks */
        }
        .navbar a {
            text-decoration: none;
            color: #333;
            margin-left: 20px; /* Jarak antara link */
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        .content-section {
            position: relative;
            z-index: 1;
            margin: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="title">Selamat Datang di Aplikasi Pembangkit Listrik</div>
        <a href="{{ route('login') }}">Login</a>
    </div>
    <div id="map"></div>
    <div class="content-section">
        <h1 class="text-3xl font-bold text-center my-4">Peta Lokasi Unit Pembangkit</h1>
        <!-- Peta lokasi dihapus sesuai permintaan -->
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