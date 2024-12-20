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
                        <h1 class="text-xl font-semibold text-gray-800">Daftar Hadir</h1>
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

                fetch("{{ route('admin.daftar_hadir.store_token') }}", {
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
                    console.error('Error details:', error);
                    let errorMessage = 'Terjadi kesalahan yang tidak diketahui';
                    
                    if (error.response) {
                        errorMessage = `Server error: ${error.response.status}`;
                    } else if (error.request) {
                        errorMessage = 'Tidak dapat terhubung ke server';
                    } else {
                        errorMessage = error.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: errorMessage,
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
