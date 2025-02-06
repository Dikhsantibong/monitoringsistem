@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        @include('components.user-sidebar')

        <!-- Main Content -->
        <div id="main-content" class="flex-1 overflow-auto">
            <!-- Header -->
            <header class="bg-white shadow-sm sticky top-0">
                <div class="flex justify-between items-center px-6 py-3">
                    <div class="flex items-center gap-x-3">
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
                        <!--  Menu Toggle Sidebar-->
                        <button id="desktop-menu-toggle"
                            class="hidden md:block relative items-center justify-center rounded-md text-gray-400 hover:bg-[#009BB9] p-2 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                            aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true" data-slot="icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800">Daily Meeting</h1>
                    </div>
                    <div class="relative">
                        <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                            <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                                class="w-8 h-8 rounded-full mr-2">
                            <span class="text-gray-700">{{ Auth::user()->name }}</span>
                            <i class="fas fa-caret-down ml-2"></i>
                        </button>
                        <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                            <a href="{{ route('user.profile') }}"
                                class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
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
            <div class="flex-1 px-6">
                @include('layouts.breadcrumbs', ['breadcrumbs' => [['title' => 'Daily Meeting']]])
                <h1 class="text-2xl font-semibold mb-1">Jadwal Pertemuan Harian</h1>
                <p class="text-gray-600 mb-6">Berikut adalah jadwal pertemuan harian Anda:</p>

                <!-- Tampilkan link Zoom jika ada -->
                @if(session('zoom_link'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mt-4">
                        <span class="font-semibold">Link Zoom:</span>
                        <a href="{{ session('zoom_link') }}" class="text-blue-600 underline ml-2" target="_blank">Klik di sini untuk bergabung</a>
                    </div>
                @else
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-4">
                        <span class="font-semibold">Link Zoom tidak tersedia.</span>
                    </div>
                @endif

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Grafik Skor -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-xl font-semibold mb-4">Grafik Skor</h2>
                        <div class="relative" style="height: 300px;">
                            <canvas id="scoreChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-xl font-semibold mb-4">Jadwal Pertemuan</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Waktu</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Agenda</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Peserta</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($meetings as $meeting)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $meeting->scheduled_at }}</td>
                                            <td class="px-6 py-4">{{ $meeting->title }}</td>
                                            <td class="px-6 py-4">
                                                {{ $meeting->participants->pluck('name')->implode(', ') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- QR Code Section -->
                <div class="bg-white rounded-lg shadow p-6 mt-6">
                    <h2 class="text-lg font-semibold">QR Code Absensi</h2>
                    <div id="qrcode" class="mt-4 flex justify-center"></div>
                    <p class="mt-2 text-center">QR Code ini hanya berlaku untuk hari ini: {{ now()->format('d M Y') }}</p>
                    <button id="downloadQRCode" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out">Download QR Code</button>
                </div>
                <script>
                    function generateQR() {
                        fetch('{{ route("attendance.generate-qr") }}')
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Bersihkan container QR sebelum membuat yang baru
                                    document.getElementById('qrcode').innerHTML = '';

                                    // Buat QR code baru
                                    new QRCode(document.getElementById('qrcode'), {
                                        text: data.qr_url,
                                        width: 128,
                                        height: 128,
                                    });
                                } else {
                                    alert('Gagal generate QR Code');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Terjadi kesalahan saat generate QR Code');
                            });
                    }

                    document.getElementById("downloadQRCode").addEventListener("click", function() {
                        const canvas = document.querySelector("#qrcode canvas");
                        if (canvas) {
                            const link = document.createElement("a");
                            link.href = canvas.toDataURL("image/png");
                            link.download = "qrcode.png";
                            link.click();
                        } else {
                            alert("QR Code belum tersedia untuk diunduh.");
                        }
                    });

                    // Generate QR code on page load
                    generateQR();
                </script>


                <!-- QR Code Scanner Section -->
               
            </div>
        </div>
    </div>

    <script src="{{ asset('js/toggle.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>
        // Inisialisasi Grafik Skor
        const ctx = document.getElementById('scoreChart').getContext('2d');
        new Chart(ctx, {
            type: 'line', // Ubah tipe grafik sesuai kebutuhan
            data: {
                labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'], // Label untuk sumbu X
                datasets: [{
                    label: 'Skor',
                    data: [85, 90, 75, 88, 92], // Data skor yang ingin ditampilkan
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    fill: true // Mengisi area di bawah grafik
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

        function generateQR() {
            fetch('{{ route("attendance.generate-qr") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Bersihkan container QR sebelum membuat yang baru
                        document.getElementById('qrcode').innerHTML = '';

                        // Buat QR code baru
                        new QRCode(document.getElementById('qrcode'), {
                            text: data.qr_url,
                            width: 256,
                            height: 256
                        });
                    } else {
                        alert('Gagal generate QR Code');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat generate QR Code');
                });
        }

        // Generate QR code saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            generateQR();
        });

        let html5QrcodeScanner = null;

        document.getElementById('startButton').addEventListener('click', function() {
            if (html5QrcodeScanner === null) {
                html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader", {
                        fps: 10,
                        qrbox: 250
                    }
                );

                html5QrcodeScanner.render(onScanSuccess, onScanError);
            }
        });

        function onScanSuccess(qrCodeMessage) {
            // Kirim data QR ke server
            fetch('{{ route('record.attendance') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        qr_code: qrCodeMessage
                    })
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
