@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-gray-50 overflow-auto">
        <!-- Sidebar -->
        <aside id="mobile-menu"
            class="fixed z-20 overflow-hidden transform transition-transform duration-300 md:relative md:translate-x-0 h-screen w-64 bg-[#0A749B] shadow-md text-white hidden md:block md:shadow-lg">
            <div class="p-4 flex items-center gap-3">
                <img src="{{ asset('logo/navlogo.png') }}" alt="Logo Aplikasi Rapat Harian" class="w-40 h-15">
                <!-- Mobile Menu Toggle -->
                <button id="menu-toggle-close"
                    class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#009BB9] hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                    aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <nav class="mt-4">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center px-4 py-3  {{ request()->routeIs('admin.dashboard') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-home mr-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.pembangkit.ready') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.pembangkit.ready') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-check mr-3"></i>
                    <span>Kesiapan Pembangkit</span>
                </a>
                <a href="{{ route('admin.laporan.sr_wo') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.laporan.sr_wo') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-file-alt mr-3"></i>
                    <span>Laporan SR/WO</span>
                </a>
                <a href="{{ route('admin.machine-monitor') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.machine-monitor') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-cogs mr-3"></i>
                    <span>Monitor Mesin</span>
                </a>
                <a href="{{ route('admin.daftar_hadir.index') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.daftar_hadir.index') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-list mr-3"></i>
                    <span>Daftar Hadir</span>
                </a>
                <a href="{{ route('admin.score-card.index') }}"
                    class="flex items-center px-4 py-3  {{ request()->routeIs('admin.score-card.*') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-clipboard-list mr-3"></i>
                    <span>Score Card Daily</span>
                </a>
                <a href="{{ route('admin.users') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.users') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-users mr-3"></i>
                    <span>Manajemen Pengguna</span>
                </a>
                <a href="{{ route('admin.meetings') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.meetings') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-chart-bar mr-3"></i>
                    <span>Laporan Rapat</span>
                </a>
                <a href="{{ route('admin.settings') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.settings') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-cog mr-3"></i>
                    <span>Pengaturan</span>
                </a>
            </nav>
        </aside>



        <!-- Main Content -->
        <div id="main-content" class="flex-1 overflow-auto">
            <header class="bg-white shadow-sm sticky z-10">
                <div class="flex justify-between items-center px-6 py-3">
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
                    <h1 class="text-xl font-semibold text-gray-800">Daftar Hadir</h1>
                    @include('components.timer')
                    <!-- User Dropdown -->
                    <div class="relative">
                        <button id="user-menu-button" class="flex items-center gap-2 hover:text-gray-600"
                            onclick="toggleUserDropdown()">
                            <img src="{{ asset('foto_profile/admin1.png' . Auth::user()->avatar) }}" alt="User Avatar"
                                class="w-8 h-8 rounded-full">
                            <span>{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-sm"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="user-dropdown"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 hidden">
                            <a href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
            <div class="flex items-center pt-2">
                <x-admin-breadcrumb :breadcrumbs="[['name' => 'Daftar Hadir', 'url' => null]]" />
            </div>
            <main class="px-6">
                <div class="bg-white rounded-lg shadow p-6 mb-3">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Daftar Kehadiran</h2>

                    <!-- Input Pencarian -->
                    <div class="mb-4 flex flex-col lg:flex-row gap-y-3 justify-between items-center">
                        <!-- Tombol Generate QR Code -->
                        <div class="flex items-center">
                            <button onclick="generateQRCode()"
                                class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded flex items-center mr-2">
                                <i class="fas fa-qrcode mr-2"></i> Generate QR Code
                            </button>

                            <!-- Modal QR Code -->
                            <div id="qrModal"
                                class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
                                <div class="bg-white p-8 rounded-lg shadow-lg">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-xl font-bold flex items-center">
                                            <i class="fas fa-qrcode mr-2"></i>QR Code Absensi
                                        </h3>
                                        <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div id="qrcode-container" class="flex justify-center min-h-[256px] min-w-[256px]">
                                    </div>
                                    <p class="mt-4 text-sm text-gray-600 text-center">QR Code ini hanya berlaku
                                        untuk hari ini</p>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Tanggal and Search Input -->
                        <div class="flex flex-col lg:flex-row items-center space-x-2 gap-y-3">
                            <div class="flex gap-x-2 items-center">
                                <label class="text-gray-600">Tanggal:</label>
                                <input type="date" id="date-filter" value="{{ date('Y-m-d') }}"
                                    onchange="filterByDate(this.value)"
                                    class="px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500 mr-2">
                            </div>

                            <div class="flex">
                                <input type="text" id="search" placeholder="Cari..."
                                    class="w-full px-4 py-2 border rounded-l-lg focus:outline-none focus:border-blue-500">
                                <button
                                    class="bg-blue-500 px-4 py-2 rounded-tr-lg rounded-br-lg text-white font-semibold hover:bg-blue-800 transition-colors">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>

                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table id="attendance-table" class="min-w-full bg-white border border-gray-300 rounded-lg">
                            <thead class="bg-gray-100">
                                <tr style="background-color: #0A749B; color: white;">
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">
                                        Nama
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider border-b border-gray-300">
                                        Divisi
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider border-b border-gray-300">
                                        Tanggal
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">
                                        Waktu Kehadiran
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="attendance-body" class="divide-y divide-gray-300">
                                @foreach ($attendances as $attendance)
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            {{ $attendance->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            {{ $attendance->division }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            {{ \Carbon\Carbon::parse($attendance->time)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            {{ \Carbon\Carbon::parse($attendance->time)->format('H:i:s') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>

        <script src="{{ asset('js/toggle.js') }}"></script>

        <script>
            // Fungsi untuk pencarian
            document.getElementById('search').addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('#attendance-body tr');

                rows.forEach(row => {
                    const name = row.querySelector('td').textContent.toLowerCase();
                    if (name.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            function filterByDate(date) {
                fetch(`{{ route('admin.daftar_hadir.index') }}?date=${date}`)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('attendance-body').innerHTML = html;
                    });
            }

            function toggleUserDropdown() {
                const dropdown = document.getElementById('user-dropdown');
                dropdown.classList.toggle('hidden');
            }

            // Close dropdown if clicked outside
            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('user-dropdown');
                const button = document.getElementById('user-menu-button');
                if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        </script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Pastikan event listener terpasang setelah DOM siap
                const generateButton = document.querySelector('button[onclick="generateQRCode()"]');
                if (generateButton) {
                    generateButton.addEventListener('click', generateQRCode);
                }
            });

            function generateQRCode() {
                console.log('Generate QR Code clicked');

                const today = new Date().toISOString().split('T')[0];
                const token = `attendance_${today}_${Math.random().toString(36).substr(2, 9)}`;

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(`/admin/daftar-hadir/store-token`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            token: token,
                            _token: csrfToken
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Success response:', data);

                        const modal = document.getElementById('qrModal');
                        const modalContent = modal.querySelector('.bg-white');

                        const container = document.getElementById('qrcode-container');
                        container.innerHTML = '';

                        modal.classList.remove('hidden');
                        modalContent.classList.remove('scale-0');
                        modalContent.classList.add('scale-100');

                        const qrData = `${window.location.origin}/attendance/scan/${token}`;
                        console.log('QR Data URL:', qrData);

                        new QRCode(container, {
                            text: qrData,
                            width: 256,
                            height: 256,
                            colorDark: "#000000",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                        });

                        localStorage.setItem('attendance_qr_token', token);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: `Gagal membuat QR Code: ${error.message}`,
                            footer: 'Silakan coba lagi atau hubungi administrator'
                        });
                    });
            }

            // Fungsi untuk menutup modal
            function closeModal() {
                const modal = document.getElementById('qrModal');
                const modalContent = modal.querySelector('.bg-white');

                modalContent.classList.remove('scale-100');
                modalContent.classList.add('scale-0');

                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            // Cek apakah QR Code masih valid
            function checkQRValidity() {
                const token = localStorage.getItem('attendance_qr_token');
                if (token) {
                    const [, date] = token.split('_');
                    const today = new Date().toISOString().split('T')[0];

                    if (date !== today) {
                        localStorage.removeItem('attendance_qr_token');
                    }
                }
            }

            // Jalankan pengecekan saat halaman dimuat
            checkQRValidity();
        </script>
        @push('scripts')
        @endpush
    @endsection
