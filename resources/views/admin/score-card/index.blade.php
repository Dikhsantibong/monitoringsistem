@extends('layouts.app')

@push('styles')
    <!-- Tambahkan Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Pastikan konten utama dapat di-scroll */
        .main-content {
            overflow-y: auto;
            /* Izinkan scroll vertikal */
            height: calc(100vh - 64px);
            /* Sesuaikan tinggi dengan mengurangi tinggi header */
        }

        #timer {
            font-size: 2em;
            font-weight: bold;
            color: #333;
            margin: 10px 0;
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="flex h-screen bg-gray-50 overflow-auto">
        <!-- Sidebar -->
     @include('components.sidebar')
        <!-- Main Content -->
        <div id="main-content" class="flex-1 main-content">
            <!-- Header -->
            <header class="bg-white shadow-sm sticky top-0 z-10">
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
                        <h2 class="text-xl font-semibold text-gray-800">Score Card Daily</h2>
                    </div>

                    <div id="timer" class="text-lg font-bold text-gray-800" style="display: none;">00:00:00</div>
                    <div class="flex items-center">
                        <div class="relative">
                            <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                                <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                                    class="w-7 h-7 rounded-full mr-2">
                                <span class="text-gray-700 text-sm">{{ Auth::user()->name }}</span>
                                <i class="fas fa-caret-down ml-2 text-gray-600"></i>
                            </button>
                            <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden">
                                <a href="{{ route('profile.edit') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                                <a href="#" onclick="showLogoutConfirmation()"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Keluar</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <div class="flex items-center pt-2">
                <x-admin-breadcrumb :breadcrumbs="[['name' => 'Score Card Daily', 'url' => null]]" />
            </div>

            <!-- Main Content -->
            <div class="container mx-auto px-4">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-6">SCORE CARD DAILY</h2>

                    <div class="mb-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p>Daily Meeting Hari / Tanggal: {{ $scoreCard->tanggal ?? now()->format('d F Y') }}</p>
                                <p>Lokasi: {{ $scoreCard->lokasi ?? 'Ruang Rapat Rongi' }}</p>
                            </div>
                            <div class="flex justify-center">
                                <a href="{{ route('admin.score-card.create') }}"
                                    class="bg-blue-500 text-white px-4 py-2 rounded flex items-center ml-2">
                                    <i class="fas fa-book mr-2"></i> Buat Score Card
                                </a>
                                <button id="startMeetingBtn" onclick="startMeeting()"
                                    class="bg-green-500 text-white px-4 py-2 rounded flex items-center ml-2">
                                    <i class="fas fa-play mr-2"></i> Mulai Rapat
                                </button>
                                <a href="#" onclick="createZoomMeeting()"
                                    class="bg-purple-500 text-white px-4 py-2 rounded flex items-center ml-2">
                                    <i class="fas fa-video mr-2"></i> Buat Rapat Zoom
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Timer Display -->
                    <div id="timer" class="text-center"></div>

                    <!-- Elemen untuk menampilkan link Zoom -->
                    <div id="zoom-link-container" class="mt-4">
                        <div id="zoom-link" class="bg-gray-100 border border-gray-300 rounded-lg p-4 shadow-md">
                            <span class="font-semibold">Link Zoom:</span>
                            <a href="#" id="zoom-link-url" class="text-blue-600 underline ml-2"
                                target="_blank"></a>
                            <i class="fas fa-copy cursor-pointer ml-2" onclick="copyToClipboard()"></i>
                        </div>
                    </div>

                    <div class="overflow-auto">
                        <table class="min-w-full bg-white border">
                            <thead>
                                <tr style="background-color: #0A749B; color: white;" class="text-center">
                                    <th class="border p-2">No</th>
                                    <th class="border p-2">Peserta</th>
                                    <th class="border p-2">Awal</th>
                                    <th class="border p-2">Akhir</th>
                                    <th class="border p-2">Skor</th>
                                    <th class="border p-2">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Ambil hanya score card terbaru
                                    $latestScoreCard = $scoreCards->sortByDesc('created_at')->first();
                                    $totalIndex = 0;
                                    $totalScore = 0; // Reset total score
                                @endphp
                                @if($latestScoreCard)
                                    @php
                                        $peserta = json_decode($latestScoreCard->peserta, true);
                                        $ketentuanRapat = json_decode($latestScoreCard->ketentuan_rapat, true);
                                        $pesertaCount = count($peserta);
                                        $currentIndex = 0;
                                        
                                        // Hitung total score hanya dari score card terbaru
                                        foreach ($peserta as $data) {
                                            $totalScore += $data['skor'] ?? 0;
                                        }
                                    @endphp
                                    @foreach ($peserta as $jabatan => $data)
                                        @php
                                            $currentIndex++;
                                        @endphp
                                        <tr>
                                            <td class="border p-2 text-center">{{ $totalIndex + 1 }}</td>
                                            <td class="border p-2">{{ ucfirst(str_replace('_', ' ', $jabatan)) }}</td>
                                            <td class="border p-2 text-center">{{ $data['skor'] == 50 ? 0 : '' }}</td>
                                            <td class="border p-2 text-center">{{ $data['skor'] == 100 ? 1 : '' }}</td>
                                            <td class="border p-2 text-center">{{ $data['skor'] }}</td>
                                            <td class="border p-2"></td>
                                        </tr>
                                        @php
                                            $totalIndex++;
                                        @endphp

                                        @if ($currentIndex === $pesertaCount)
                                            <!-- Menampilkan ketentuan rapat di kolom terpisah -->

                                            <tr>
                                                <td class="border p-2 text-center">{{ $totalIndex + 1 }}</td>
                                                <td class="border p-2">Ketepatan waktu memulai meeting</td>
                                                <td class="border p-2 text-center">START</td>
                                                <td class="border p-2 text-center">
                                                    {{ $ketentuanRapat['aktifitas_meeting'] ?? 'N/A' }}</td>
                                                <td class="border p-2 text-center">-</td>
                                                <td class="border p-2">-</td>
                                            </tr>
                                            <tr>
                                                <td class="border p-2 text-center">{{ $totalIndex + 2 }}</td>
                                                <td class="border p-2">Ketepatan waktu mengakhiri meeting</td>
                                                <td class="border p-2 text-center">FINISH</td>
                                                <td class="border p-2 text-center">
                                                    {{ $ketentuanRapat['gangguan_diskusi'] ?? 'N/A' }}</td>
                                                <td class="border p-2 text-center">-</td>
                                                <td class="border p-2">-</td>
                                            </tr>
                                            <tr>
                                                <td class="border p-2 text-center">{{ $totalIndex + 3 }}</td>
                                                <td class="border p-2">Kesiapan panitia pelaksana meeting</td>
                                                <td class="border p-2 text-center">-</td>
                                                <td class="border p-2 text-center">
                                                    {{ $ketentuanRapat['kesiapan_panitia'] ?? 'N/A' }}</td>
                                                <td class="border p-2 text-center">-</td>
                                                <td class="border p-2">-</td>
                                            </tr>
                                            <tr>
                                                <td class="border p-2 text-center">{{ $totalIndex + 4 }}</td>
                                                <td class="border p-2">Kesiapan bahan oleh peserta meeting</td>
                                                <td class="border p-2 text-center">-</td>
                                                <td class="border p-2 text-center">
                                                    {{ $ketentuanRapat['kesiapan_bahan'] ?? 'N/A' }}</td>
                                                <td class="border p-2 text-center">-</td>
                                                <td class="border p-2">-</td>
                                            </tr>
                                            <tr>
                                                <td class="border p-2 text-center">{{ $totalIndex + 5 }}</td>
                                                <td class="border p-2">Aktifitas di Luar Kegiatan Meeting</td>
                                                <td class="border p-2 text-center">-</td>
                                                <td class="border p-2 text-center">
                                                    {{ $ketentuanRapat['aktifitas_luar'] ?? 'N/A' }}</td>
                                                <td class="border p-2 text-center">-</td>
                                                <td class="border p-2">-</td>
                                            </tr>
                                            <!-- Tambahkan ketentuan lain dengan format serupa -->
                                        @endif
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="border p-2 text-center">Belum ada score card yang dibuat</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="5" class="border p-2 text-right font-bold">Total Score:</td>
                                    <td class="border p-2 text-center font-bold">{{ $totalScore }}</td>
                                </tr>
                            </tbody>
                        </table>



                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/toggle.js') }}"></script>
    <script>
        let timerInterval;
        let startTime;
        let elapsedTime = 0; // Menyimpan waktu yang telah berlalu
        let isRunning = false;

        // Cek apakah timer sedang berjalan saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const storedStartTime = localStorage.getItem('startTime');
            const storedElapsedTime = localStorage.getItem('elapsedTime');
            const storedIsRunning = localStorage.getItem('isRunning');

            if (storedStartTime && storedIsRunning === 'true') {
                startTime = new Date(parseInt(storedStartTime));
                elapsedTime = parseInt(storedElapsedTime) || 0; // Ambil waktu yang telah berlalu
                isRunning = true;
                updateTimerDisplay(); // Perbarui tampilan timer
                timerInterval = setInterval(updateTimer, 1000); // Mulai interval

                // Tampilkan timer
                document.getElementById('timer').style.display = 'block'; // Tampilkan timer

                // Update tombol sesuai status
                const startButton = document.getElementById('startMeetingBtn');
                startButton.innerHTML = '<i class="fas fa-stop mr-2"></i> Stop Rapat';
                startButton.classList.remove('bg-green-500');
                startButton.classList.add('bg-red-500');
            } else {
                // Jika timer tidak berjalan, sembunyikan timer
                document.getElementById('timer').style.display = 'none';
            }
        });

        function startMeeting() {
            if (!isRunning) {
                // Start the timer
                const timerDisplay = document.getElementById('timer');
                timerDisplay.style.display = 'block';
                startTime = new Date();
                localStorage.setItem('startTime', startTime.getTime()); // Simpan waktu mulai

                // Change button text and color
                const startButton = document.getElementById('startMeetingBtn');
                startButton.innerHTML = '<i class="fas fa-stop mr-2"></i> Stop Rapat';
                startButton.classList.remove('bg-green-500');
                startButton.classList.add('bg-red-500');

                timerInterval = setInterval(updateTimer, 1000);
                isRunning = true;
                localStorage.setItem('isRunning', 'true'); // Simpan status timer
            } else {
                stopMeeting();
            }
        }

        function stopMeeting() {
            clearInterval(timerInterval);
            const startButton = document.getElementById('startMeetingBtn');
            startButton.innerHTML = '<i class="fas fa-play mr-2"></i> Mulai Rapat';
            startButton.classList.remove('bg-red-500');
            startButton.classList.add('bg-green-500');
            isRunning = false;
            localStorage.setItem('isRunning', 'false'); // Update status timer

            // Hide timer
            document.getElementById('timer').style.display = 'none';
            localStorage.removeItem('startTime'); // Hapus waktu mulai
            localStorage.removeItem('elapsedTime'); // Hapus waktu yang telah berlalu
        }

        function updateTimer() {
            const now = new Date();
            elapsedTime += 1000; // Tambahkan 1 detik ke waktu yang telah berlalu
            localStorage.setItem('elapsedTime', elapsedTime); // Simpan waktu yang telah berlalu

            updateTimerDisplay(); // Perbarui tampilan timer
        }

        function updateTimerDisplay() {
            const totalElapsedTime = elapsedTime + (isRunning ? new Date() - startTime : 0);

            const hours = Math.floor(totalElapsedTime / (1000 * 60 * 60));
            const minutes = Math.floor((totalElapsedTime % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((totalElapsedTime % (1000 * 60)) / 1000);

            const timerDisplay = document.getElementById('timer');
            timerDisplay.textContent = `${padNumber(hours)}:${padNumber(minutes)}:${padNumber(seconds)}`;
        }

        function padNumber(number) {
            return number.toString().padStart(2, '0');
        }

        async function createZoomMeeting() {
            try {
                const response = await fetch("{{ route('admin.create-zoom-meeting') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });

                // Debug response
                console.log('Response status:', response.status);
                const contentType = response.headers.get('content-type');
                console.log('Content type:', contentType);

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Error response:', errorText);
                    throw new Error('Gagal membuat meeting: ' + response.status);
                }

                const data = await response.json();
                console.log('Meeting data:', data);

                if (data.success && data.data?.join_url) {
                    const zoomLink = document.getElementById('zoom-link-url');
                    zoomLink.innerHTML = 'Klik di sini untuk bergabung ke Zoom Meeting';
                    zoomLink.href = data.data.join_url;
                    document.getElementById('zoom-link-container').style.display = 'block';

                    Swal.fire({
                        icon: 'success',
                        title: 'Meeting Zoom',
                        text: 'Link Zoom berhasil dibuat!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    throw new Error(data.message || 'Tidak ada join URL dalam respons');
                }
            } catch (error) {
                console.error('Error detail:', error);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Membuat Meeting',
                    text: error.message,
                    footer: 'Silakan cek koneksi internet dan konfigurasi Zoom API'
                });
            }
        }

        // Sembunyikan container link zoom saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('zoom-link-container').style.display = 'none';
        });

        function copyToClipboard() {
            const zoomLink = document.getElementById('zoom-link-url');
            if (zoomLink.href) {
                navigator.clipboard.writeText(zoomLink.href).then(() => {
                    // Tambahkan Sweet Alert di sini
                    Swal.fire({
                        icon: 'success',
                        title: 'Link Disalin',
                        text: 'Link Zoom telah disalin ke clipboard!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }).catch(err => {
                    console.error('Gagal menyalin: ', err);
                });
            } else {
                alert('Link Zoom tidak tersedia untuk disalin.');
            }
        }
    </script>


    {{--    
        <script>
            // Sweet Alert untuk memberitahu bahwa score card daily telah di submit
            Swal.fire({
                icon: 'success',
                title: 'Score Card Daily',
                text: 'Score Card Daily telah di submit',
                showConfirmButton: false,
                timer: 1500
            });
        </script> --}}

    @push('scripts')
    @endpush
@endsection
