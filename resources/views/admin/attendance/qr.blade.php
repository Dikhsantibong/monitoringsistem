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
                <!-- Notification Banner -->
                <div id="notificationBanner" class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-4 rounded-lg shadow-lg mb-4 hidden animate-bounce-slow">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="bg-white rounded-full p-2 animate-pulse">
                                <i class="fas fa-exclamation-circle text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-lg">Pengingat Penting!</p>
                                <p class="text-sm">Setelah selesai absensi, jangan lupa tekan tombol <strong>"Tarik Data"</strong> untuk memperbarui data kehadiran</p>
                            </div>
                        </div>
                        <button onclick="closeNotification()" class="text-white hover:text-gray-200 ml-4">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

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
                            <button id="generateQrBtn" onclick="generateQR()" class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center hover:bg-green-700 transition-all">
                                <i class="fas fa-qrcode mr-2"></i>
                                Generate QR Code
                            </button>

                            <!-- Tombol Tarik Data dengan Animasi -->
                            <div class="relative">
                                <button id="pullDataBtn" onclick="pullData()" class="bg-purple-600 text-white px-4 py-2 rounded-lg flex items-center hover:bg-purple-700 transition-all relative overflow-hidden">
                                    <i class="fas fa-download mr-2"></i>
                                    Tarik Data
                                    <!-- Ping Animation -->
                                    <span id="pullDataPing" class="absolute top-0 right-0 -mt-1 -mr-1 flex h-3 w-3 hidden">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                                    </span>
                                </button>
                                <!-- Tooltip -->
                                <div id="pullDataTooltip" class="absolute -top-12 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-3 py-2 rounded-lg whitespace-nowrap hidden">
                                    Klik untuk memperbarui data!
                                    <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2 rotate-45 w-2 h-2 bg-gray-800"></div>
                                </div>
                            </div>

                            <!-- Tombol Manage Kehadiran -->
                            <a href="{{ route('admin.daftar_hadir.rekapitulasi') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center hover:bg-blue-700 transition-all">
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
                                <p class="mt-2 text-sm text-purple-600 font-semibold text-center animate-pulse">
                                    ⚠️ Jangan lupa tekan "Tarik Data" setelah absensi selesai!
                                </p>
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

        <style>
            @keyframes bounce-slow {
                0%, 100% {
                    transform: translateY(-5%);
                    animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
                }
                50% {
                    transform: translateY(0);
                    animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
                }
            }
            .animate-bounce-slow {
                animation: bounce-slow 2s infinite;
            }
        </style>

        <script>
            // Show notification banner on page load
            window.addEventListener('DOMContentLoaded', function() {
                setTimeout(() => {
                    showNotification();
                    activatePullDataAnimation();
                }, 1000);
            });

            function showNotification() {
                const banner = document.getElementById('notificationBanner');
                banner.classList.remove('hidden');
                
                // Auto hide after 10 seconds
                setTimeout(() => {
                    closeNotification();
                }, 10000);
            }

            function closeNotification() {
                const banner = document.getElementById('notificationBanner');
                banner.classList.add('hidden');
            }

            function activatePullDataAnimation() {
                const ping = document.getElementById('pullDataPing');
                const tooltip = document.getElementById('pullDataTooltip');
                
                ping.classList.remove('hidden');
                tooltip.classList.remove('hidden');
                
                // Hide after 15 seconds
                setTimeout(() => {
                    ping.classList.add('hidden');
                    tooltip.classList.add('hidden');
                }, 15000);
            }

            function generateQR() {
                const container = document.getElementById('qrcode-container');
                const errorContainer = document.getElementById('qr-error');
                
                // Reset tampilan
                container.innerHTML = '<div class="text-center">Generating QR Code...</div>';
                errorContainer.classList.add('hidden');
                errorContainer.textContent = '';
                
                // Tampilkan modal
                document.getElementById('qrModal').classList.remove('hidden');
                
                // Show notification and animation after QR is generated
                setTimeout(() => {
                    showNotification();
                    activatePullDataAnimation();
                }, 2000);
                
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

            function showErrorModal(title, content) {
                document.getElementById('errorContent').textContent = content;
                document.getElementById('errorModal').classList.remove('hidden');
            }

            function closeErrorModal() {
                document.getElementById('errorModal').classList.add('hidden');
            }

            function pullData() {
                const btn = document.getElementById('pullDataBtn');
                const originalText = btn.innerHTML;
                const ping = document.getElementById('pullDataPing');
                const tooltip = document.getElementById('pullDataTooltip');
                
                // Hide animations
                ping.classList.add('hidden');
                tooltip.classList.add('hidden');
                closeNotification();
                
                console.log('=== PULL DATA STARTED ===');
                console.log('Time:', new Date().toISOString());
                
                // Disable button dan show loading
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menarik Data...';
                
                fetch('{{ route("admin.attendance.qr.pull-data") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    console.log('Response Status:', response.status);
                    console.log('Response OK:', response.ok);
                    return response.json().then(data => {
                        console.log('Response Data:', data);
                        return { status: response.status, ok: response.ok, data: data };
                    });
                })
                .then(({ status, ok, data }) => {
                    if (!ok) {
                        // Show error modal with details
                        let errorContent = 'Status: ' + status + '\n\n';
                        errorContent += 'Message: ' + (data.message || 'Unknown error') + '\n\n';
                        
                        if (data.details) {
                            errorContent += 'Details:\n' + JSON.stringify(data.details, null, 2);
                        }
                        
                        showErrorModal('Error', errorContent);
                        console.error('API Error:', data);
                        return;
                    }
                    
                    if (data.success) {
                        console.log('Success! Data imported:', data);
                        
                        let message = data.message;
                        
                        // Tambahkan detail jika ada errors
                        if (data.errors && data.errors.length > 0) {
                            console.warn('Errors during import:', data.errors);
                            message += '\n\n⚠️ Warnings/Errors:\n' + data.errors.slice(0, 5).join('\n');
                            if (data.errors.length > 5) {
                                message += '\n... dan ' + (data.errors.length - 5) + ' error lainnya';
                            }
                        }
                        
                        alert(message);
                        
                        // Reload jika ada data yang diimport
                        if (data.attendance_imported > 0 || data.token_imported > 0) {
                            console.log('Reloading page...');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                    } else {
                        let errorMsg = 'Gagal menarik data: ' + (data.message || 'Unknown error');
                        
                        if (data.details) {
                            showErrorModal('Error', JSON.stringify(data.details, null, 2));
                        }
                        
                        alert(errorMsg);
                        console.error('Pull data failed:', data);
                    }
                })
                .catch(error => {
                    console.error('=== PULL DATA ERROR ===');
                    console.error('Error:', error);
                    console.error('Stack:', error.stack);
                    
                    showErrorModal('Network Error', error.toString() + '\n\n' + error.stack);
                    alert('Gagal menarik data. Silakan cek console untuk detail error.');
                })
                .finally(() => {
                    // Restore button
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    console.log('=== PULL DATA ENDED ===');
                });
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
                const errorModal = document.getElementById('errorModal');
                
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

                if (errorModal) {
                    errorModal.addEventListener('click', function(e) {
                        if (e.target === this) {
                            closeErrorModal();
                        }
                    });
                }
            });
        </script>
    @push('scripts')
    @endpush
@endsection