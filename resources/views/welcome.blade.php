@extends('layouts.app')

@section('content')

<!DOCTYPE html>
<html>
<head>
    <title>Peta Mesin</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            height: 100vh; /* Peta full tinggi layar */
            border-radius: 10px; /* Tambahkan sudut bulat pada peta */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Tambahkan bayangan untuk efek 3D */
        }
    </style>
</head>
<body>
    <div id="map"></div>
    
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Inisialisasi peta dengan gaya yang lebih keren
        var map = L.map('map').setView([-4.1449, 122.1746], 8); // Koordinat awal peta di Sulawesi Tenggara

        // Tambahkan tile layer dari OpenStreetMap dengan gaya yang lebih keren
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            minZoom: 8, // Batas minimum zoom untuk peta
            maxZoom: 18 // Batas maksimum zoom untuk peta
        }).addTo(map);

        // Data mesin (contoh)
        var machines = [
            { name: "Mesin A", latitude: -4.1449, longitude: 122.1746, status: "START", category: "PLTA" },
            { name: "Mesin B", latitude: -4.1449, longitude: 122.1746, status: "STOP", category: "PLTU" }
        ];

        // Tambahkan marker ke peta dengan gaya yang lebih keren
        machines.forEach(machine => {
            L.marker([machine.latitude, machine.longitude], {
                icon: L.icon({
                    iconUrl: 'path/to/your/icon.png', // Ganti dengan path ke icon Anda
                    iconSize: [50, 50], // Ukuran icon
                    iconAnchor: [25, 50], // Anchor untuk posisi icon
                    popupAnchor: [-3, -76] // Anchor untuk posisi popup
                })
            })
            .addTo(map)
            .bindPopup(`<b>${machine.name}</b><br>Status: ${machine.status}<br>Kategori: ${machine.category}`);
        });
    </script>
</body>
</html>

    