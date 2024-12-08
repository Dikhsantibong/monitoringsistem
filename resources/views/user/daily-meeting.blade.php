@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <style>
        /* Sidebar */
        aside {
            background-color: #0A749B; /* Warna biru kehijauan */
            color: white;
        }
    
        /* Link di Sidebar */
        aside nav a {
            color: white; /* Teks default putih */
            display: flex;
            align-items: center;
            padding: 12px 16px;
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s; /* Animasi transisi */
        }
    
        /* Link di Sidebar saat Hover */
        aside nav a:hover {
            background-color: white; /* Latar belakang putih */
            color: black; /* Teks berubah menjadi hitam */
        }
    
        /* Aktif Link */
        aside nav a.bg-yellow-500 {
            background-color: white;
            color: #000102;
        }
        
    </style>
    <!-- Sidebar -->
    <aside id="mobile-menu"
    class="fixed z-20 overflow-hidden transform transition-transform duration-300 md:relative md:translate-x-0 h-screen w-64 bg-[#0A749B] shadow-md text-white hidden md:block md:shadow-lg">
        <div class="p-4 flex items-center gap-3">
            <img src="{{ asset('logo/navlogo.png') }}" alt="Logo Aplikasi" class="w-40 h-15">
            <!-- Mobile Menu Toggle -->
            <button id="menu-toggle-close"
            class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#009BB9] hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
            aria-controls="mobile-menu" aria-expanded="false">
            <span class="sr-only">Open main menu</span>
            <i class="fa-solid fa-xmark"></i>
        </button>
        </div>
        <nav class="mt-4">
            <a href="{{ route('user.dashboard') }}" >
                <i class="fas fa-home mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('user.machine.monitor') }}">
                <i class="fas fa-cogs mr-3"></i>
                <span>Machine Monitor</span>
            </a>
            <a href="{{ route('daily.meeting') }}" class="bg-yellow-500">
                <i class="fas fa-users mr-3"></i>
                <span>Daily Meeting</span>
            </a>
            <a href="{{ route('monitoring') }}">
                <i class="fas fa-chart-line mr-3"></i>
                <span>Monitoring</span>
            </a>
            <a href="{{ route('documentation') }}">
                <i class="fas fa-book mr-3"></i>
                <span>Documentation</span>
            </a>
            <a href="{{ route('support') }}">
                <i class="fas fa-headset mr-3"></i>
                <span>Support</span>
            </a>
        </nav>
    </aside>
    <!-- Main Content -->
    <div id="main-content" class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0">
            <div class="flex justify-between items-center px-6 py-4">
                <!-- Mobile Menu Toggle -->
                <button id="mobile-menu-toggle"
                class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#009BB9] hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                aria-controls="mobile-menu" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" aria-hidden="true" data-slot="icon">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
                <h1 class="text-2xl font-semibold text-gray-800">Daily Meeting</h1>
                <div class="relative">
                    <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                        <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin.png') }}" 
                             class="w-8 h-8 rounded-full mr-2">
                        <span class="text-gray-700">{{ Auth::user()->name }}</span>
                        <i class="fas fa-caret-down ml-2"></i>
                    </button>
                    <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                        <a href="{{ route('user.profile') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
                        <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="flex-1 p-6">
            @include('layouts.breadcrumbs', ['breadcrumbs' => [
                ['title' => 'Daily Meeting']
            ]])
            <h1 class="text-2xl font-bold mb-4">Jadwal Pertemuan Harian</h1>
            <p class="text-gray-600 mb-6">Berikut adalah jadwal pertemuan harian Anda:</p>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Grafik Pertemuan dengan Chart.js -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Grafik Pertemuan</h2>
                    <div class="relative" style="height: 300px;">
                        <canvas id="meetingChart"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Jadwal Pertemuan</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agenda</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peserta</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($meetings as $meeting)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $meeting->scheduled_at }}</td>
                                    <td class="px-6 py-4">{{ $meeting->title }}</td>
                                    <td class="px-6 py-4">{{ $meeting->participants->pluck('name')->implode(', ') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- QR Code Section -->
            <div class="bg-white rounded-lg shadow p-4 mt-6">
                <h2 class="text-lg font-semibold">QR Code Absensi</h2>
                <div id="qrcode" class="mt-4 flex justify-center"></div>
                <p class="mt-2 text-center">QR Code ini hanya berlaku untuk hari ini: {{ now()->format('d M Y') }}</p>
            </div>

            <!-- Input Kehadiran -->
            <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
                <h2 class="text-xl font-semibold mb-4">Input Kehadiran</h2>
                <form id="attendance-form">
                    <div class="flex space-x-4">
                        <input type="text" id="barcode" placeholder="Masukkan Barcode" 
                               class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <button type="submit" 
                                class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition duration-200">
                            Tambah Kehadiran
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Kehadiran -->
            <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
                <h2 class="text-xl font-semibold mb-4">Daftar Kehadiran</h2>
                <div class="overflow-x-auto">
                    <table id="attendance-table" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>        
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody id="attendance-body" class="bg-white divide-y divide-gray-200">
                            <!-- Data kehadiran akan ditambahkan di sini -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- QR Code Scanner Section -->
            <div class="bg-white rounded-lg shadow p-4 mt-6">
                <h2 class="text-lg font-semibold">Scan QR Code</h2>
                <div class="mt-4">
                    <div id="reader" class="mt-4"></div>
                    <div id="result"></div>
                    <button id="startButton" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 mt-4">
                        <i class="fas fa-camera mr-2"></i>Mulai Scan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/toggle.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    // Inisialisasi Chart.js
    const ctx = document.getElementById('meetingChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
            datasets: [{
                label: 'Jumlah Pertemuan',
                data: [12, 19, 3, 5, 2],
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function toggleDailyMeetingDropdown() {
        const dropdown = document.getElementById('daily-meeting-dropdown');
        dropdown.classList.toggle('hidden');
    }

    // Menutup dropdown jika klik di luar
    window.onclick = function(event) {
        if (!event.target.matches('.flex.items-center')) {
            const dropdowns = document.getElementsByClassName("absolute");
            for (let i = 0; i < dropdowns.length; i++) {
                const openDropdown = dropdowns[i];
                if (!openDropdown.classList.contains('hidden')) {
                    openDropdown.classList.add('hidden');
                }
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        function clearQRCode() {
            const qrcodeDiv = document.getElementById("qrcode");
            qrcodeDiv.innerHTML = '';
        }

        function generateQRCode() {
            clearQRCode();
            
            // Generate QR code dengan timestamp untuk memastikan keunikan
            const timestamp = new Date().getTime();
            const qrData = `attendance_${timestamp}_${Math.random().toString(36).substring(7)}`;
            
            new QRCode(document.getElementById("qrcode"), {
                text: qrData,
                width: 200,
                height: 200,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });

            // Kirim data QR ke server
            fetch('{{ route("record.attendance") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ qr_code: qrData })
            });
        }

        // Generate QR code pertama kali
        generateQRCode();

        // Update QR code setiap 5 menit
        setInterval(generateQRCode, 300000);
    });

    let html5QrcodeScanner = null;

    document.getElementById('startButton').addEventListener('click', function() {
        if (html5QrcodeScanner === null) {
            html5QrcodeScanner = new Html5QrcodeScanner(
                "reader", { fps: 10, qrbox: 250 }
            );
            
            html5QrcodeScanner.render(onScanSuccess, onScanError);
        }
    });

    function onScanSuccess(qrCodeMessage) {
        // Kirim data QR ke server
        fetch('{{ route("record.attendance") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ qr_code: qrCodeMessage })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Tampilkan pesan sukses
                document.getElementById('result').innerHTML = `
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mt-4">
                        ${data.message}
                    </div>
                `;
                
                // Hentikan scanner setelah berhasil
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.clear();
                    html5QrcodeScanner = null;
                }
            } else {
                // Tampilkan pesan error
                document.getElementById('result').innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-4">
                        ${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('result').innerHTML = `
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-4">
                    Terjadi kesalahan saat memproses absensi
                </div>
            `;
        });
    }

    function onScanError(error) {
        // Handle scan error
        console.warn(`QR Code scan error: ${error}`);
    }
</script>

@push('scripts')
@endpush

@endsection