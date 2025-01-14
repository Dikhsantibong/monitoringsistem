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

                    <div id="timer" class="text-2xl font-bold text-gray-800" style="display: none;">00:00:00</div>
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
                                <a href="https://us02web.zoom.us/j/82649015876?pwd=mweLJcKxUWEhifFUNc7XsCpKK6Yuhg.1" style="color:  blue "   >link zoom</a>
                            
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
                                {{-- <a href="#" onclick="ZoomMeeting()"
                                    class="bg-purple-500 text-white px-4 py-2 rounded flex items-center ml-2">
                                    <i class="fas fa-video mr-2"></i> Buat Rapat Zoom
                                </a> --}}
                            </div>
                        </div>
                    </div>

                    <!-- Timer Display -->
                    <div id="timer" class="text-center"></div>

                    <!-- Elemen untuk menampilkan link Zoom -->
                    {{-- <div id="zoom-link-container" class="mt-4">
                        <div id="zoom-link" class="bg-gray-100 border border-gray-300 rounded-lg p-4 shadow-md">
                            <span class="font-semibold">Link Zoom:</span>
                            <a href="#" id="zoom-link-url" class="text-blue-600 underline ml-2"
                                target="_blank"></a>
                            <i class="fas fa-copy cursor-pointer ml-2" onclick="copyToClipboard()"></i>
                        </div>
                    </div> --}}

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
                                    $peserta = []; // Inisialisasi variabel peserta
                                @endphp
                                @if($latestScoreCard)
                                    @php
                                        // Decode data peserta dengan nilai default array kosong
                                        $peserta = json_decode($latestScoreCard->peserta, true) ?? [];
                                        $ketentuanRapat = json_decode($latestScoreCard->ketentuan_rapat, true) ?? [];
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
                                            <td class="border p-2 text-center">{{ $data['skor'] ?? 0 }}</td>
                                            <td class="border p-2">{{ $data['keterangan'] ?? '-' }}</td>
                                        </tr>
                                        @php
                                            $totalIndex++;
                                        @endphp

                                        @if ($currentIndex === $pesertaCount)
                                            <!-- Menampilkan ketentuan rapat -->
                                            <tr>
                                                <td class="border p-2 text-center">{{ $totalIndex + 1 }}</td>
                                                <td class="border p-2">Ketepatan waktu memulai meeting (09:30)</td>
                                                <td class="border p-2 text-center">Start</td>
                                                <td class="border p-2 text-center">{{ \Carbon\Carbon::parse($latestScoreCard->waktu_mulai)->format('H:i') }}</td>
                                                <td class="border p-2 text-center">{{ $latestScoreCard->skor_waktu_mulai ?? 100 }}</td>
                                                <td class="border p-2">100 Jika dimulai tepat waktu.
Setiap 3 menit keterlambatan waktu maka skor dikurangi 10.</td>
                                            </tr>
                                            <tr>
                                                <td class="border p-2 text-center">{{ $totalIndex + 2 }}</td>
                                                <td class="border p-2">Ketepatan waktu mengakhiri meeting (10:00)</td>
                                                <td class="border p-2 text-center">Finish</td> 
                                                <td class="border p-2 text-center">{{ \Carbon\Carbon::parse($latestScoreCard->waktu_selesai)->format('H:i') }}</td>
                                                <td class="border p-2 text-center">{{ $latestScoreCard->skor_waktu_selesai ?? 100 }}</td>
                                                <td class="border p-2">100 Jika di akhiri tepat waktu tepat waktu.
Setiap 3 menit keterlambatan waktu maka skor dikurangi 10.</td>
                                            </tr>
                                            <tr>
                                                <td class="border p-2 text-center">{{ $totalIndex + 3 }}</td>
                                                <td class="border p-2">Kesiapan Panitia Pelaksana Meeting</td>
                                                <td class="border p-2" colspan="2" style="background-color: #f3f4f6"></td>
                                                <td class="border p-2 text-center">{{ $latestScoreCard->kesiapan_panitia }}</td>
                                                <td class="border p-2">100 Jika tidak ada komplain dari peserta.</td>
                                            </tr>
                                            <tr>
                                                <td class="border p-2 text-center">{{ $totalIndex + 4 }}</td>
                                                <td class="border p-2">Kesiapan Bahan Oleh Peserta Meeting</td>
                                                <td class="border p-2" colspan="2" style="background-color: #f3f4f6"></td>
                                                <td class="border p-2 text-center">{{ $latestScoreCard->kesiapan_bahan }}</td>
                                                <td class="border p-2">100 Jika setiap peserta membawa bahan masing-masing.</td>
                                            </tr>
                                            <tr>
                                                <td class="border p-2 text-center">{{ $totalIndex + 5 }}</td>
                                                <td class="border p-2">Aktivitas Luar</td>
                                                <td class="border p-2" colspan="2" style="background-color: #f3f4f6"></td>
                                                <td class="border p-2 text-center">{{ $latestScoreCard->aktivitas_luar }}</td>
                                                <td class="border p-2">100 Jika tidak ada gangguan HP/LAPTOP/Etc</td>
                                            </tr>
                                            <tr>
                                                <td class="border p-2 text-center">{{ $totalIndex + 6 }}</td>
                                                <td class="border p-2">Gangguan Diskusi</td>
                                                <td class="border p-2" colspan="2" style="background-color: #f3f4f6"></td>
                                                <td class="border p-2 text-center">{{ $latestScoreCard->gangguan_diskusi }}</td>
                                                <td class="border p-2">100 Jika semua peserta terfokus pada agenda meeting. Setiap 1 gangguan obrolan (diskusi kecil) dari peserta maka skor dikurangi 20.</td>
                                            </tr>
                                            <tr>
                                                <td class="border p-2 text-center">{{ $totalIndex + 7 }}</td>
                                                <td class="border p-2">Gangguan Keluar Masuk</td>
                                                <td class="border p-2" colspan="2" style="background-color: #f3f4f6"></td>
                                                <td class="border p-2 text-center">{{ $latestScoreCard->gangguan_keluar_masuk }}</td>
                                                <td class="border p-2">100 Jika semua peserta tetap berada di ruangan sampai akhir</td>
                                            </tr>
                                            <tr>
                                                <td class="border p-2 text-center">{{ $totalIndex + 8 }}</td>
                                                <td class="border p-2">Gangguan Interupsi</td>
                                                <td class="border p-2" colspan="2" style="background-color: #f3f4f6"></td>
                                                <td class="border p-2 text-center">{{ $latestScoreCard->gangguan_interupsi }}</td>
                                                <td class="border p-2">100 Jika tidak ada interupsi dari pihak lainnya. Setiap 1 interupsi dari pihak luar maka skor dikurangi 20.</td>
                                            </tr>
                                            <tr>
                                                <td class="border p-2 text-center">{{ $totalIndex + 9 }}</td>
                                                <td class="border p-2">Ketegasan Moderator</td>
                                                <td class="border p-2" colspan="2" style="background-color: #f3f4f6"></td>
                                                <td class="border p-2 text-center">{{ $latestScoreCard->ketegasan_moderator }}</td>
                                                <td class="border p-2">Obyektif</td>
                                            </tr>
                                            <tr>
                                                <td class="border p-2 text-center">{{ $totalIndex + 10 }}</td>
                                                <td class="border p-2">Kelengkapan SR</td>
                                                <td class="border p-2" colspan="2" style="background-color: #f3f4f6"></td>
                                                <td class="border p-2 text-center">{{ $latestScoreCard->kelengkapan_sr }}</td>
                                                <td class="border p-2">Kaidah, Pelaporan Dokumentasi, Upload ke CMMS</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="border p-2 text-center">Belum ada score card yang dibuat</td>
                                    </tr>
                                @endif
                                @php
                                    // Hitung total score peserta dengan pengecekan
                                    $totalScorePeserta = $latestScoreCard->peserta ? collect(json_decode($latestScoreCard->peserta, true))->sum('skor') : 0;
                                    
                                    // Hitung total score ketentuan rapat dengan pengecekan null coalescing
                                    $totalScoreKetentuan = $latestScoreCard ? (
                                        ($latestScoreCard->kesiapan_panitia ?? 100) +
                                        ($latestScoreCard->kesiapan_bahan ?? 100) +
                                        ($latestScoreCard->aktivitas_luar ?? 100) +
                                        ($latestScoreCard->gangguan_diskusi ?? 100) +
                                        ($latestScoreCard->gangguan_keluar_masuk ?? 100) +
                                        ($latestScoreCard->gangguan_interupsi ?? 100) +
                                        ($latestScoreCard->ketegasan_moderator ?? 100) +
                                        ($latestScoreCard->skor_waktu_mulai ?? 100) +
                                        ($latestScoreCard->skor_waktu_selesai ?? 100) +
                                        ($latestScoreCard->kelengkapan_sr ?? 100)
                                    ) : 0;
                                    
                                    // Total keseluruhan
                                    $grandTotal = $totalScorePeserta + $totalScoreKetentuan;

                                    // Hitung total skor maksimum
                                    $maxScorePeserta = $pesertaCount * 100; // Asumsi setiap peserta memiliki skor maksimum 100
                                    $maxScoreKetentuan = 100 * 8; // Asumsi ada 8 ketentuan dengan skor maksimum 100
                                    $maxGrandTotal = $maxScorePeserta + $maxScoreKetentuan;

                                    // Hitung persentase
                                    $scorePercentage = $maxGrandTotal > 0 ? ($grandTotal / $maxGrandTotal) * 100 : 0;
                                @endphp
                                <tr>
                                    <td colspan="4" class="border p-2 text-right font-bold">Total Score:</td>
                                    <td class="border p-2 text-center font-bold">{{ number_format($scorePercentage, 2) }}%</td>
                                    <td class="border p-2">Persentase dari total score maksimum yang mungkin</td>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Definisikan variabel global
        window.timerInterval = null;
        window.startTime = null;
        window.isRunning = false;

        // Definisikan fungsi startMeeting sebagai properti window
        window.startMeeting = function() {
            const timerDisplay = document.getElementById('timer');
            const startButton = document.getElementById('startMeetingBtn');
            
            if (!window.isRunning) {
                // Mulai timer
                window.startTime = new Date();
                window.isRunning = true;
                
                // Tampilkan timer
                timerDisplay.style.display = 'block';
                
                // Update tampilan button
                startButton.innerHTML = '<i class="fas fa-stop mr-2"></i> Stop Rapat';
                startButton.classList.remove('bg-green-500');
                startButton.classList.add('bg-red-500');
                
                // Mulai interval timer
                window.timerInterval = setInterval(updateTimer, 1000);
                
                // Simpan state ke localStorage
                localStorage.setItem('meetingStartTime', window.startTime.getTime());
                localStorage.setItem('isRunning', 'true');
            } else {
                // Stop timer
                clearInterval(window.timerInterval);
                window.isRunning = false;
                
                // Sembunyikan timer
                timerDisplay.style.display = 'none';
                
                // Reset tampilan button
                startButton.innerHTML = '<i class="fas fa-play mr-2"></i> Mulai Rapat';
                startButton.classList.remove('bg-red-500');
                startButton.classList.add('bg-green-500');
                
                // Hapus state dari localStorage
                localStorage.removeItem('meetingStartTime');
                localStorage.removeItem('isRunning');
            }
        }

        function updateTimer() {
            const now = new Date();
            const diff = now - window.startTime;
            
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);
            
            const timerDisplay = document.getElementById('timer');
            timerDisplay.textContent = `${padNumber(hours)}:${padNumber(minutes)}:${padNumber(seconds)}`;
        }

        function padNumber(number) {
            return number.toString().padStart(2, '0');
        }

        // Check timer state saat halaman dimuat
        const savedStartTime = localStorage.getItem('meetingStartTime');
        const savedIsRunning = localStorage.getItem('isRunning');
        
        if (savedStartTime && savedIsRunning === 'true') {
            window.startTime = new Date(parseInt(savedStartTime));
            window.isRunning = true;
            
            const timerDisplay = document.getElementById('timer');
            const startButton = document.getElementById('startMeetingBtn');
            
            // Tampilkan timer
            timerDisplay.style.display = 'block';
            
            // Update tampilan button
            startButton.innerHTML = '<i class="fas fa-stop mr-2"></i> Stop Rapat';
            startButton.classList.remove('bg-green-500');
            startButton.classList.add('bg-red-500');
            
            // Mulai interval timer
            window.timerInterval = setInterval(updateTimer, 1000);
            updateTimer();
        }
    });
    </script>
 
    @push('scripts')
    @endpush
@endsection
