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
                <div class="bg-white rounded-lg shadow p-6 mb-3">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Daftar Kehadiran</h2>

                    <!-- Menampilkan Tanggal di bawah judul -->
                    <p class="text-gray-700 mb-4">
                        Tanggal: {{ \Carbon\Carbon::now()->setTimezone('Asia/Makassar')->isoFormat('dddd, D MMMM Y') }}
                    </p>

                    <!-- Input Pencarian -->
                    <div class="mb-4 flex flex-col lg:flex-row gap-x-4 gap-y-3 justify-between items-center">
                        <div class="flex items-center gap-x-4">
                            <!-- Tombol Generate QR Code -->
                            <button id="generateQrBtn" onclick="generateQR()" class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center hover:bg-green-700">
                                <i class="fas fa-qrcode mr-2"></i>
                                Generate QR Code
                            </button>

                            <!-- Tombol Manage Kehadiran -->
                            <a href="{{ route('admin.daftar_hadir.rekapitulasi') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center hover:bg-blue-700">
                                <i class="fas fa-tasks mr-2"></i>
                                Manage Kehadiran
                            </a>
                        </div>

                        <!-- Modal QR Code -->
                        <div id="qrModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
                            <div class="bg-white p-8 rounded-lg shadow-lg">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-xl font-bold flex items-center">
                                        <i class="fas fa-qrcode mr-2"></i>QR Code Absensi
                                    </h3>
                                    <button onclick="closeQRModal()" class="text-gray-500 hover:text-gray-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div id="qrcode-container" class="flex justify-center min-h-[256px] min-w-[256px]"></div>
                                <div id="qr-error" class="mt-4 text-red-600 text-center hidden"></div>
                                <p class="mt-4 text-sm text-gray-600 text-center">QR Code ini hanya berlaku untuk hari ini</p>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table id="attendance-table" class="min-w-full bg-white border border-gray-300 rounded-lg">
                            <thead class="bg-gray-100">
                                <tr style="background-color: #0A749B; color: white;">
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">
                                        No
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">
                                        Nama
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">
                                        Divisi
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">
                                        Jabatan
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">
                                        Tanggal
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">
                                        Waktu Absensi
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider border-b border-gray-300">
                                        Tanda Tangan
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="attendance-body" class="divide-y divide-gray-300 border border-gray-300">
                                @forelse ($attendances as $index => $attendance)
                                    <tr class="hover:bg-gray-100 border-b border-gray-300">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">
                                            {{ $attendance->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">
                                            {{ $attendance->division }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">
                                            {{ $attendance->position }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">
                                            {{ \Carbon\Carbon::parse($attendance->time)->setTimezone('Asia/Makassar')->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 border-r border-gray-300">
                                            {{ \Carbon\Carbon::parse($attendance->time)->setTimezone('Asia/Makassar')->format('H:i:s') }} WITA
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-300">
                                            @if($attendance->signature)
                                                <img src="{{ $attendance->signature }}" 
                                                     alt="Tanda tangan {{ $attendance->name }}"
                                                     class="h-16 object-contain cursor-pointer"
                                                     onclick="showSignatureModal(this.src, '{{ $attendance->name }}')"
                                                >
                                            @else
                                                <span class="text-gray-400">Tidak ada tanda tangan</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                            Belum ada data absensi untuk hari ini
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>

        <script src="{{ asset('js/toggle.js') }}"></script>

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

        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

        <script>
            function generateQR() {
                const container = document.getElementById('qrcode-container');
                const errorContainer = document.getElementById('qr-error');
                
                // Reset tampilan
                container.innerHTML = '<div class="text-center">Generating QR Code...</div>';
                errorContainer.classList.add('hidden');
                errorContainer.textContent = '';
                
                // Tampilkan modal
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
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Gagal generate QR Code');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.qr_url) {
                        container.innerHTML = '';
                        new QRCode(container, {
                            text: data.qr_url,
                            width: 256,
                            height: 256,
                            colorDark: "#000000",
                            colorLight: "#ffffff",
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

            function closeQRModal() {
                document.getElementById('qrModal').classList.add('hidden');
            }

            // Fungsi untuk menampilkan modal tanda tangan
            function showSignatureModal(src, name) {
                document.getElementById('signatureModal').classList.remove('hidden');
                document.getElementById('modalTitle').textContent = `Tanda Tangan - ${name}`;
                document.getElementById('modalSignature').src = src;
            }

            // Fungsi untuk menutup modal tanda tangan
            function closeSignatureModal() {
                document.getElementById('signatureModal').classList.add('hidden');
            }

            // Tutup modal jika mengklik di luar modal
            document.addEventListener('DOMContentLoaded', function() {
                const signatureModal = document.getElementById('signatureModal');
                const qrModal = document.getElementById('qrModal');
                
                if (signatureModal) {
                    signatureModal.addEventListener('click', function(e) {
                        if (e.target === this) {
                            closeSignatureModal();
                        }
                    });
                }

                if (qrModal) {
                    qrModal.addEventListener('click', function(e) {
                        if (e.target === this) {
                            closeQRModal();
                        }
                    });
                }
            });
        </script>
    @push('scripts')
    @endpush
@endsection