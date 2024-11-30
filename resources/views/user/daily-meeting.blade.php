@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-yellow-500 shadow-lg">
        <div class="p-4">
            <h2 class="text-xl font-bold text-blue-600">PLN NUSANTARA POWER KENDARI</h2>
        </div>
        <nav class="mt-4">
            <a href="{{ route('user.dashboard') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-yellow-500">
                <i class="fas fa-home mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('user.machine.monitor') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-yellow-500">
                <i class="fas fa-cogs mr-3"></i>
                <span>Machine Monitor</span>
            </a>
            <a href="{{ route('daily.meeting') }}" class="flex items-center px-4 py-3 bg-yellow-500 text-blue-700">
                <i class="fas fa-users mr-3"></i>
                <span>Daily Meeting</span>
            </a>
            <a href="{{ route('monitoring') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-yellow-500">
                <i class="fas fa-chart-line mr-3"></i>
                <span>Monitoring</span>
            </a>
            <a href="{{ route('documentation') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-yellow-500">
                <i class="fas fa-book mr-3"></i>
                <span>Documentation</span>
            </a>
            <a href="{{ route('support') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-yellow-500">
                <i class="fas fa-headset mr-3"></i>
                <span>Support</span>
            </a>
        </nav>
    </aside>
     <!-- Main Content -->
     <div class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="flex justify-between items-center px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">Daily Meeting</h1>
                <div class="relative">
                    <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                        <img src="{{ Auth::user()->avatar ?? asset('images/default-avatar.png') }}" 
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
        <h1 class="text-2xl font-bold">Jadwal Pertemuan Harian</h1>
        <p>Berikut adalah jadwal pertemuan harian Anda:</p>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="text-lg font-semibold">Grafik Pertemuan</h2>
                <canvas id="meetingChart"></canvas>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="text-lg font-semibold">Jadwal Pertemuan</h2>
                <table class="min-w-full mt-2 bg-white border border-gray-300">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Waktu</th>
                            <th class="py-2 px-4 border-b">Agenda</th>
                            <th class="py-2 px-4 border-b">Peserta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-2 px-4 border-b">09:00 - 10:00</td>
                            <td class="py-2 px-4 border-b">Rapat Tim</td>
                            <td class="py-2 px-4 border-b">Tim Pengembangan</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4 border-b">11:00 - 12:00</td>
                            <td class="py-2 px-4 border-b">Review Proyek</td>
                            <td class="py-2 px-4 border-b">Manajer Proyek</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- QR Code Section -->
        <div class="bg-white rounded-lg shadow p-4 mt-6">
            <h2 class="text-lg font-semibold">QR Code Absensi</h2>
            <div id="qrcode" class="mt-4"></div>
            <p>Scan QR Code ini untuk melakukan absensi.</p>
        </div>

        <!-- Input Kehadiran -->
        <div class="bg-white rounded-lg shadow p-4 mt-6">
            <h2 class="text-lg font-semibold">Input Kehadiran</h2>
            <form id="attendance-form">
                <div class="flex space-x-4">
                    <input type="text" id="barcode" placeholder="Masukkan Barcode" class="flex-1 px-4 py-2 border rounded-lg" required>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Tambah Kehadiran</button>
                </div>
            </form>
        </div>

        <!-- Tabel Kehadiran -->
        <div class="bg-white rounded-lg shadow p-4 mt-4">
            <h2 class="text-lg font-semibold">Daftar Kehadiran</h2>
            <table id="attendance-table" class="min-w-full mt-2">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Kehadiran</th>
                    </tr>
                </thead>
                <tbody id="attendance-body">
                    <!-- Data kehadiran akan ditambahkan di sini -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
<script>
    $(document).ready(function() {
        // Generate QR Code
        var qrData = 'http://your-domain/attendance/' + Math.random().toString(36).substring(7); // Ganti dengan URL absensi yang sesuai
        $('#qrcode').qrcode({
            text: qrData,
            width: 128,
            height: 128
        });

        // Handle attendance form submission
        $('#attendance-form').on('submit', function(e) {
            e.preventDefault();
            const barcode = $('#barcode').val();

            // Simpan data ke database
            $.ajax({
                url: '/attendance', // Ganti dengan rute yang sesuai
                method: 'POST',
                data: {
                    name: barcode, // Ganti dengan data yang sesuai
                    time: new Date().toISOString(),
                    _token: '{{ csrf_token() }}' // Token CSRF
                },
                success: function(response) {
                    alert('Kehadiran berhasil ditambahkan!');
                    $('#barcode').val(''); // Reset input
                },
                error: function() {
                    alert('Gagal menambahkan kehadiran.');
                }
            });
        });
    });

    const ctx = document.getElementById('meetingChart').getContext('2d');
    const meetingChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Rapat Tim', 'Review Proyek'],
            datasets: [{
                label: 'Jumlah Peserta',
                data: [5, 3],
                backgroundColor: ['rgba(255, 215, 0, 0.2)', 'rgba(76, 175, 80, 0.2)'],
                borderColor: ['rgba(255, 215, 0, 1)', 'rgba(76, 175, 80, 1)'],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

@push('scripts')

@endpush
@endsection