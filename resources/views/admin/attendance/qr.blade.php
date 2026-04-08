@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-gray-50 overflow-auto">
        <!-- Sidebar -->
        @include('components.sidebar')
        <!-- Main Content -->
        <div id="main-content" class="flex-1 overflow-auto">
            <header class="bg-white shadow-sm sticky z-10">
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
                            class="hidden md:block relative items-center p-2 justify-center rounded-md text-gray-400 hover:bg-[#009BB9] hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                            aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true" data-slot="icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800">QR Code Daftar Hadir</h1>
                    </div>
                    @include('components.timer')
                    <!-- User Dropdown -->
                    <div class="relative">
                        <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                            <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                                class="w-7 h-7 rounded-full mr-2">
                            <span class="text-gray-700 text-sm">{{ Auth::user()->name }}</span>
                            <i class="fas fa-caret-down ml-2 text-gray-600"></i>
                        </button>
                        <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </header>
            <div class="flex items-center pt-2">
                <x-admin-breadcrumb :breadcrumbs="[['name' => 'QR Code Daftar Hadir', 'url' => null]]" />
            </div>
            <main class="px-6">
                <!-- Auto-Sync Status Banner -->
                <div id="sync-banner" class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i id="sync-icon" class="fas fa-sync-alt text-blue-500 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    <span class="font-semibold">Auto-Sync Aktif:</span> Data kehadiran diperbarui otomatis setiap <span class="font-bold">10 detik</span>.
                                    <span id="sync-status" class="ml-2 text-xs text-blue-500"></span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span id="last-sync-time" class="text-xs text-gray-500"></span>
                            <button onclick="manualSync()" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center gap-1" title="Sinkronkan sekarang">
                                <i class="fas fa-redo-alt"></i> Sync
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 mb-3">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Daftar Kehadiran</h2>

                    <!-- Menampilkan Tanggal di bawah judul -->
                    <p class="text-gray-700 mb-4">
                        Tanggal: {{ \Carbon\Carbon::now()->setTimezone('Asia/Makassar')->isoFormat('dddd, D MMMM Y') }}
                    </p>

                    <!-- Input Pencarian -->
                    <!-- Tabs Navigation (Only for MySQL) -->
                    @if(session('unit') === 'mysql')
                    <div class="mb-6 border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <button onclick="switchTab('daily')" id="tab-daily" class="border-b-2 border-blue-500 py-4 px-1 text-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200 active-tab">
                                <i class="fas fa-users mr-2"></i>Absensi Daily
                            </button>
                            <button onclick="switchTab('weekly')" id="tab-weekly" class="border-b-2 border-transparent py-4 px-1 text-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-colors duration-200">
                                <i class="fas fa-calendar-week mr-2"></i>Absensi Weekly
                            </button>
                        </nav>
                    </div>
                    @endif

                    <!-- DAILY SECTION -->
                    <div id="daily-section">
                        <!-- Toolbar Daily -->
                        <div class="mb-4 flex flex-col lg:flex-row gap-x-4 gap-y-3 justify-between items-center">
                            <div class="flex items-center gap-x-4">
                                <!-- Tombol Generate QR Code (Daily) -->
                                <button id="generateQrBtn" onclick="generateQR()" class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center hover:bg-green-700 transition-colors">
                                    <i class="fas fa-qrcode mr-2"></i>
                                    Generate QR Daily
                                </button>

                                <!-- Auto-Sync Indicator (Daily) -->
                                <div id="dailySyncIndicator" class="bg-purple-100 text-purple-700 px-4 py-2 rounded-lg flex items-center border border-purple-200">
                                    <i class="fas fa-check-circle mr-2 text-green-500" id="daily-sync-check"></i>
                                    <span class="text-sm font-medium">Auto-Sync Aktif</span>
                                </div>

                                <!-- Tombol Manage Kehadiran -->
                                <a href="{{ route('admin.daftar_hadir.rekapitulasi') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-tasks mr-2"></i>
                                    Manage Kehadiran
                                </a>
                            </div>
                        </div>

                        <!-- Tabel Absensi Daily -->
                        <div class="overflow-x-auto">
                            <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center">
                                <i class="fas fa-users mr-2 text-blue-600"></i>
                                Absensi Daily (<span id="daily-count">{{ $attendances->count() }}</span> orang)
                            </h3>
                            <table id="attendance-table" class="min-w-full bg-white border border-gray-300 rounded-lg">
                                <thead class="bg-gray-100">
                                    <tr style="background-color: #0A749B; color: white;">
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">Nama</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">Divisi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">Jabatan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">Waktu Absensi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">Tanda Tangan</th>
                                    </tr>
                                </thead>
                                <tbody id="attendance-body" class="divide-y divide-gray-300 border border-gray-300">
                                    @forelse ($attendances as $index => $attendance)
                                        <tr class="hover:bg-gray-100 border-b border-gray-300">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">{{ $attendance->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">{{ $attendance->division }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">{{ $attendance->position }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">{{ \Carbon\Carbon::parse($attendance->time)->setTimezone('Asia/Makassar')->format('d M Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">{{ \Carbon\Carbon::parse($attendance->time)->setTimezone('Asia/Makassar')->format('H:i:s') }} WITA</td>
                                            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-300">
                                                @if($attendance->signature)
                                                    <img src="{{ $attendance->signature }}" alt="Tanda tangan {{ $attendance->name }}" class="h-16 object-contain cursor-pointer" onclick="showSignatureModal(this.src, '{{ $attendance->name }}')">
                                                @else
                                                    <span class="text-gray-400">Tidak ada tanda tangan</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada data absensi daily untuk hari ini</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- WEEKLY SECTION (Hidden by Default) -->
                    @if(session('unit') === 'mysql')
                    <div id="weekly-section" style="display: none;">
                        <!-- Toolbar Weekly -->
                        <div class="mb-4 flex flex-col lg:flex-row gap-x-4 gap-y-3 justify-between items-center">
                            <div class="flex items-center gap-x-4">
                                <!-- Tombol Generate QR Code Weekly -->
                                <button id="generateWeeklyQrBtn" onclick="generateWeeklyQR()" class="bg-teal-600 text-white px-4 py-2 rounded-lg flex items-center hover:bg-teal-700 transition-colors">
                                    <i class="fas fa-calendar-week mr-2"></i>
                                    Generate QR Weekly
                                </button>

                                <!-- Auto-Sync Indicator (Weekly) -->
                                <div id="weeklySyncIndicator" class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-lg flex items-center border border-indigo-200">
                                    <i class="fas fa-check-circle mr-2 text-green-500" id="weekly-sync-check"></i>
                                    <span class="text-sm font-medium">Auto-Sync Aktif</span>
                                </div>
                            </div>
                        </div>

                        <!-- Tabel Absensi Weekly -->
                        <div class="overflow-x-auto">
                            <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center">
                                <i class="fas fa-calendar-week mr-2 text-teal-600"></i>
                                Absensi Weekly Meeting (<span id="weekly-count">{{ $weeklyAttendances->count() }}</span> orang)
                            </h3>
                            <table id="weekly-attendance-table" class="min-w-full bg-white border border-gray-300 rounded-lg">
                                <thead class="bg-gray-100">
                                    <tr style="background-color: #0D9488; color: white;">
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">Nama</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">Divisi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">Jabatan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">Waktu Absensi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">Tanda Tangan</th>
                                    </tr>
                                </thead>
                                <tbody id="weekly-attendance-body" class="divide-y divide-gray-300 border border-gray-300">
                                    @forelse ($weeklyAttendances as $index => $attendance)
                                        <tr class="hover:bg-teal-50 border-b border-gray-300">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">{{ $attendance->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">{{ $attendance->division }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">{{ $attendance->position }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">{{ \Carbon\Carbon::parse($attendance->time)->setTimezone('Asia/Makassar')->format('d M Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">{{ \Carbon\Carbon::parse($attendance->time)->setTimezone('Asia/Makassar')->format('H:i:s') }} WITA</td>
                                            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-300">
                                                @if($attendance->signature)
                                                    <img src="{{ $attendance->signature }}" alt="Tanda tangan {{ $attendance->name }}" class="h-16 object-contain cursor-pointer" onclick="showSignatureModal(this.src, '{{ $attendance->name }}')">
                                                @else
                                                    <span class="text-gray-400">Tidak ada tanda tangan</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada data absensi weekly untuk hari ini</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </main>
        </div>

        <script src="{{ asset('js/toggle.js') }}"></script>

        <!-- Shared QR Modal (Moved outside sections) -->
        <div id="qrModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold flex items-center" id="qrModalTitle">
                        <i class="fas fa-qrcode mr-2"></i>QR Code Absensi
                    </h3>
                    <button onclick="closeQRModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="qrcode-container" class="flex justify-center min-h-[256px] min-w-[256px]"></div>
                <div id="qr-error" class="mt-4 text-red-600 text-center hidden"></div>
                <p class="mt-4 text-sm text-gray-600 text-center">QR Code ini hanya berlaku untuk hari ini</p>
                <div class="mt-3 bg-green-50 border border-green-200 rounded p-3">
                    <p class="text-sm text-green-800 text-center">
                        <i class="fas fa-check-circle mr-1"></i>
                        <span class="font-semibold">Auto-Sync:</span> Data akan otomatis tersinkronisasi setiap 10 detik
                    </p>
                </div>
            </div>
        </div>

        <!-- Modal untuk menampilkan tanda tangan -->
        <div id="signatureModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
            <div class="bg-white p-4 rounded-lg max-w-2xl w-full mx-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold" id="modalTitle">Tanda Tangan</h3>
                    <button onclick="closeSignatureModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <img id="modalSignature" src="" alt="Tanda tangan" class="w-full">
            </div>
        </div>

        <!-- Modal Error Detail -->
        <div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg max-w-3xl w-full mx-4 max-h-[80vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-red-600">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Error Details
                    </h3>
                    <button onclick="closeErrorModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="errorContent" class="bg-gray-100 p-4 rounded text-sm font-mono whitespace-pre-wrap"></div>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

        <script>
            // =============================================
            // AUTO-SYNC CONFIGURATION
            // =============================================
            const PULL_INTERVAL   = 10000; // Pull data from external API every 10 seconds
            const REFRESH_INTERVAL = 5000; // Refresh table from local DB every 5 seconds
            let pullTimer   = null;
            let refreshTimer = null;
            let isSyncing   = false;
            let lastSyncTime = null;

            // =============================================
            // TAB SWITCHING
            // =============================================
            function switchTab(tab) {
                const dailySection = document.getElementById('daily-section');
                const weeklySection = document.getElementById('weekly-section');
                const tabDaily = document.getElementById('tab-daily');
                const tabWeekly = document.getElementById('tab-weekly');

                if (tab === 'daily') {
                    dailySection.style.display = 'block';
                    weeklySection.style.display = 'none';
                    tabDaily.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                    tabDaily.classList.add('border-blue-500', 'text-blue-600', 'hover:text-blue-800');
                    tabWeekly.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                    tabWeekly.classList.remove('border-teal-500', 'text-teal-600', 'hover:text-teal-800');
                } else {
                    dailySection.style.display = 'none';
                    weeklySection.style.display = 'block';
                    tabDaily.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                    tabDaily.classList.remove('border-blue-500', 'text-blue-600', 'hover:text-blue-800');
                    tabWeekly.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                    tabWeekly.classList.add('border-teal-500', 'text-teal-600', 'hover:text-teal-800');
                }
            }

            // =============================================
            // QR CODE GENERATION
            // =============================================
            function generateQR() {
                const container = document.getElementById('qrcode-container');
                const errorContainer = document.getElementById('qr-error');
                const modalTitle = document.getElementById('qrModalTitle');
                container.innerHTML = '<div class="text-center">Generating QR Code...</div>';
                errorContainer.classList.add('hidden');
                errorContainer.textContent = '';
                modalTitle.innerHTML = '<i class="fas fa-qrcode mr-2"></i>QR Code Absensi';
                document.getElementById('qrModal').classList.remove('hidden');

                fetch('{{ route("admin.attendance.qr.generate") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) return response.json().then(d => { throw new Error(d.message || 'Gagal generate QR Code'); });
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.qr_url) {
                        container.innerHTML = '';
                        new QRCode(container, {
                            text: data.qr_url, width: 256, height: 256,
                            colorDark: "#000000", colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                        });
                    } else {
                        throw new Error(data.message || 'QR URL tidak tersedia');
                    }
                })
                .catch(error => {
                    console.error('QR Generation Error:', error);
                    container.innerHTML = '<div class="text-red-500 text-center">Gagal membuat QR Code</div>';
                    errorContainer.classList.remove('hidden');
                    errorContainer.textContent = 'Error: ' + error.message;
                    setTimeout(closeQRModal, 3000);
                });
            }

            function generateWeeklyQR() {
                const container = document.getElementById('qrcode-container');
                const errorContainer = document.getElementById('qr-error');
                const modalTitle = document.getElementById('qrModalTitle');
                container.innerHTML = '<div class="text-center">Generating Weekly QR Code...</div>';
                errorContainer.classList.add('hidden');
                errorContainer.textContent = '';
                modalTitle.innerHTML = '<i class="fas fa-calendar-week mr-2"></i>QR Code Absensi Weekly';
                document.getElementById('qrModal').classList.remove('hidden');

                fetch('{{ route("admin.attendance.qr.generate-weekly") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) return response.json().then(d => { throw new Error(d.message || 'Gagal generate QR Code Weekly'); });
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.qr_url) {
                        container.innerHTML = '';
                        new QRCode(container, {
                            text: data.qr_url, width: 256, height: 256,
                            colorDark: "#000000", colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                        });
                    } else {
                        throw new Error(data.message || 'QR URL tidak tersedia');
                    }
                })
                .catch(error => {
                    console.error('Weekly QR Generation Error:', error);
                    container.innerHTML = '<div class="text-red-500 text-center">Gagal membuat QR Code Weekly</div>';
                    errorContainer.classList.remove('hidden');
                    errorContainer.textContent = 'Error: ' + error.message;
                    setTimeout(closeQRModal, 3000);
                });
            }

            function closeQRModal() {
                document.getElementById('qrModal').classList.add('hidden');
            }

            // =============================================
            // ERROR MODAL
            // =============================================
            function showErrorModal(title, content) {
                document.getElementById('errorContent').textContent = content;
                document.getElementById('errorModal').classList.remove('hidden');
            }

            function closeErrorModal() {
                document.getElementById('errorModal').classList.add('hidden');
            }

            // =============================================
            // SIGNATURE MODAL
            // =============================================
            function showSignatureModal(src, name) {
                document.getElementById('signatureModal').classList.remove('hidden');
                document.getElementById('modalTitle').textContent = `Tanda Tangan - ${name}`;
                document.getElementById('modalSignature').src = src;
            }

            function closeSignatureModal() {
                document.getElementById('signatureModal').classList.add('hidden');
            }

            // =============================================
            // AUTO PULL DATA FROM EXTERNAL API
            // =============================================
            function setSyncStatus(msg, type) {
                const statusEl = document.getElementById('sync-status');
                const iconEl   = document.getElementById('sync-icon');
                const bannerEl = document.getElementById('sync-banner');
                if (statusEl) statusEl.textContent = msg;
                if (type === 'syncing') {
                    iconEl.classList.add('fa-spin');
                    bannerEl.className = 'bg-blue-50 border-l-4 border-blue-400 p-4 mb-4 transition-all duration-300';
                } else if (type === 'success') {
                    iconEl.classList.remove('fa-spin');
                    bannerEl.className = 'bg-green-50 border-l-4 border-green-400 p-4 mb-4 transition-all duration-300';
                } else if (type === 'error') {
                    iconEl.classList.remove('fa-spin');
                    bannerEl.className = 'bg-red-50 border-l-4 border-red-400 p-4 mb-4 transition-all duration-300';
                }
            }

            function updateLastSyncTime() {
                lastSyncTime = new Date();
                const el = document.getElementById('last-sync-time');
                if (el) {
                    el.textContent = 'Terakhir: ' + lastSyncTime.toLocaleTimeString('id-ID');
                }
            }

            async function autoPullData() {
                if (isSyncing) return;
                isSyncing = true;
                setSyncStatus('Menarik data...', 'syncing');

                try {
                    // Pull daily data
                    const dailyRes = await fetch('{{ route("admin.attendance.qr.pull-data") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });
                    const dailyData = await dailyRes.json();

                    @if(session('unit') === 'mysql')
                    // Pull weekly data (only for mysql unit)
                    const weeklyRes = await fetch('{{ route("admin.attendance.qr.pull-weekly-data") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });
                    const weeklyData = await weeklyRes.json();
                    @endif

                    updateLastSyncTime();

                    let importedTotal = (dailyData.attendance_imported || 0);
                    @if(session('unit') === 'mysql')
                    importedTotal += (weeklyData.attendance_imported || 0);
                    @endif

                    if (importedTotal > 0) {
                        setSyncStatus(`✓ ${importedTotal} data baru diimport`, 'success');
                        // Immediately refresh the table from local DB
                        await refreshDailyTable();
                        @if(session('unit') === 'mysql')
                        await refreshWeeklyTable();
                        @endif
                    } else {
                        setSyncStatus('✓ Data sudah up-to-date', 'success');
                    }
                } catch (error) {
                    console.error('Auto-pull error:', error);
                    setSyncStatus('✗ Gagal sinkronisasi', 'error');
                } finally {
                    isSyncing = false;
                }
            }

            function manualSync() {
                autoPullData();
            }

            // =============================================
            // REFRESH TABLE FROM LOCAL DATABASE (NO RELOAD)
            // =============================================
            function buildAttendanceRow(item, hoverClass) {
                const sigCell = item.signature
                    ? `<img src="${item.signature}" alt="Tanda tangan ${item.name}" class="h-16 object-contain cursor-pointer" onclick="showSignatureModal(this.src, '${item.name.replace(/'/g, "\\'")}')">` 
                    : '<span class="text-gray-400">Tidak ada tanda tangan</span>';

                return `<tr class="${hoverClass} border-b border-gray-300">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">${item.no}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">${item.name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">${item.division}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">${item.position}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">${item.date}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">${item.time}</td>
                    <td class="px-6 py-4 whitespace-nowrap border-r border-gray-300">${sigCell}</td>
                </tr>`;
            }

            async function refreshDailyTable() {
                try {
                    const res = await fetch('{{ route("admin.attendance.qr.fetch-daily-data") }}', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const json = await res.json();
                    if (!json.success) return;

                    const tbody = document.getElementById('attendance-body');
                    const countEl = document.getElementById('daily-count');
                    if (countEl) countEl.textContent = json.count;

                    if (json.data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada data absensi daily untuk hari ini</td></tr>';
                    } else {
                        tbody.innerHTML = json.data.map(item => buildAttendanceRow(item, 'hover:bg-gray-100')).join('');
                    }
                } catch (e) {
                    console.error('Refresh daily table error:', e);
                }
            }

            async function refreshWeeklyTable() {
                try {
                    const res = await fetch('{{ route("admin.attendance.qr.fetch-weekly-data") }}', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const json = await res.json();
                    if (!json.success) return;

                    const tbody = document.getElementById('weekly-attendance-body');
                    const countEl = document.getElementById('weekly-count');
                    if (tbody) {
                        if (countEl) countEl.textContent = json.count;

                        if (json.data.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada data absensi weekly untuk hari ini</td></tr>';
                        } else {
                            tbody.innerHTML = json.data.map(item => buildAttendanceRow(item, 'hover:bg-teal-50')).join('');
                        }
                    }
                } catch (e) {
                    console.error('Refresh weekly table error:', e);
                }
            }

            // =============================================
            // INITIALIZATION
            // =============================================
            document.addEventListener('DOMContentLoaded', function() {
                // Close modals on backdrop click
                ['signatureModal', 'qrModal', 'errorModal'].forEach(id => {
                    const modal = document.getElementById(id);
                    if (modal) {
                        modal.addEventListener('click', function(e) {
                            if (e.target === this) {
                                if (id === 'signatureModal') closeSignatureModal();
                                else if (id === 'qrModal') closeQRModal();
                                else if (id === 'errorModal') closeErrorModal();
                            }
                        });
                    }
                });

                // Initial auto-pull on page load (with slight delay)
                setTimeout(() => autoPullData(), 1500);

                // Start auto-pull timer (pull from external API every 10s)
                pullTimer = setInterval(() => autoPullData(), PULL_INTERVAL);

                // Start table refresh timer (refresh from local DB every 5s)
                refreshTimer = setInterval(() => {
                    if (!isSyncing) {
                        refreshDailyTable();
                        @if(session('unit') === 'mysql')
                        refreshWeeklyTable();
                        @endif
                    }
                }, REFRESH_INTERVAL);

                console.log('✓ Auto-sync initialized: Pull every ' + (PULL_INTERVAL/1000) + 's, Refresh every ' + (REFRESH_INTERVAL/1000) + 's');
            });

            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                if (pullTimer) clearInterval(pullTimer);
                if (refreshTimer) clearInterval(refreshTimer);
            });
        </script>
    @push('scripts')
    @endpush
@endsection