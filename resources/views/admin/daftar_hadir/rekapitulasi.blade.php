@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-gray-50 overflow-auto">
        <!-- Sidebar -->
       @include('components.sidebar')


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
                    <!-- User Dropdown -->
                    <div class="relative">
                       

                        <!-- Dropdown Menu -->
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
                </div>
            </header>
            <div class="flex items-center pt-2">
                <x-admin-breadcrumb :breadcrumbs="[['name' => 'Daftar Hadir', 'url' => null]]" />
            </div>
<div class="container mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Rekapitulasi Kehadiran Rapat</h1>
        
        <!-- Tombol Download, Print, dan Absen Mundur -->
        <div class="flex gap-3">
            <!-- Tombol Absen Mundur -->
            <button onclick="openBackdateModal()" 
                    class="bg-yellow-500 text-white px-4 py-2 rounded-lg flex items-center hover:bg-yellow-600">
                <i class="fas fa-history mr-2"></i>
                Absen Mundur
            </button>
            
            <!-- Tombol Download Excel -->
            <a href="{{ route('admin.daftar_hadir.export-excel', [
                'tanggal_awal' => request('tanggal_awal', now()->startOfMonth()->format('Y-m-d')),
                'tanggal_akhir' => request('tanggal_akhir', now()->endOfMonth()->format('Y-m-d'))
            ]) }}" 
            class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center hover:bg-green-700">
                <i class="fas fa-file-excel mr-2"></i>
                Download Excel
            </a>

            <!-- Tombol Download PDF -->
            <a href="{{ route('admin.daftar_hadir.export-pdf', [
                'tanggal_awal' => request('tanggal_awal', now()->startOfMonth()->format('Y-m-d')),
                'tanggal_akhir' => request('tanggal_akhir', now()->endOfMonth()->format('Y-m-d'))
            ]) }}" 
            class="bg-red-600 text-white px-4 py-2 rounded-lg flex items-center hover:bg-red-700">
                <i class="fas fa-file-pdf mr-2"></i>
                Download PDF
            </a>

            <!-- Tombol Print diubah menjadi link yang membuka halaman print di tab baru -->
            <a href="{{ route('admin.daftar_hadir.print', [
                'tanggal_awal' => request('tanggal_awal', now()->startOfMonth()->format('Y-m-d')),
                'tanggal_akhir' => request('tanggal_akhir', now()->endOfMonth()->format('Y-m-d'))
            ]) }}" 
            target="_blank"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center hover:bg-blue-700">
                <i class="fas fa-print mr-2"></i>
                Print
            </a>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!-- Total Kehadiran -->
        <div class="bg-blue-500 rounded-lg shadow p-6 flex items-center">
            <i class="fas fa-users text-white text-4xl mr-4"></i>
            <div class="flex flex-col text-white">
                <div class="font-bold text-3xl">{{ $statistik['total'] }}</div>
                <div class="text-sm">Total Kehadiran</div>
            </div>
        </div>

        <!-- Tepat Waktu -->
        <div class="bg-green-500 rounded-lg shadow p-6 flex items-center">
            <i class="fas fa-clock text-white text-4xl mr-4"></i>
            <div class="flex flex-col text-white">
                <div class="font-bold text-3xl">{{ $statistik['tepat_waktu'] }}</div>
                <div class="text-sm">Tepat Waktu</div>
            </div>
        </div>

        <!-- Terlambat -->
        <div class="bg-red-500 rounded-lg shadow p-6 flex items-center">
            <i class="fas fa-exclamation-circle text-white text-4xl mr-4"></i>
            <div class="flex flex-col text-white">
                <div class="font-bold text-3xl">{{ $statistik['terlambat'] }}</div>
                <div class="text-sm">Terlambat</div>
            </div>
        </div>

        <!-- Persentase Ketepatan -->
        <div class="bg-purple-500 rounded-lg shadow p-6 flex items-center">
            <i class="fas fa-calculator text-white text-4xl mr-4"></i>
            <div class="flex flex-col text-white">
                <div class="font-bold text-3xl">{{ $statistik['persentase_tepat'] }}%</div>
                <div class="text-sm">Persentase Tepat Waktu</div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form action="{{ route('admin.daftar_hadir.rekapitulasi') }}" method="GET" class="flex gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Awal</label>
                <input type="date" name="tanggal_awal" 
                       value="{{ request('tanggal_awal', now()->startOfMonth()->format('Y-m-d')) }}" 
                       class="w-full rounded-md border-gray-300">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" 
                       value="{{ request('tanggal_akhir', now()->endOfMonth()->format('Y-m-d')) }}"
                       class="w-full rounded-md border-gray-300">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Rekapitulasi -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>

                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Divisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jabatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Hadir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($attendances as $hadir)
                    <tr>
                        <td class="px-6 py-4 border-r border-gray-200">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 border-r border-gray-200">{{ $hadir->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            {{ Carbon\Carbon::parse($hadir->time)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 border-r border-gray-200">{{ $hadir->division }}</td>
                        <td class="px-6 py-4 border-r border-gray-200">{{ $hadir->position }}</td>
                        <td class="px-6 py-4 border-r border-gray-200">{{ Carbon\Carbon::parse($hadir->time)->format('H:i:s') }}</td>
                        <td class="px-6 py-4 border-r border-gray-200">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ Carbon\Carbon::parse($hadir->time)->format('H:i:s') <= '09:00:00' 
                                   ? 'bg-green-100 text-green-800' 
                                   : 'bg-red-100 text-red-800' }}">
                                {{ Carbon\Carbon::parse($hadir->time)->format('H:i:s') <= '09:00:00' 
                                   ? 'Tepat Waktu' 
                                   : 'Terlambat' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data kehadiran untuk periode yang dipilih
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Absen Mundur -->
<div id="backdateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Absen Mundur</h3>
            <form id="backdateForm">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Absen</label>
                    <input type="date" name="tanggal_absen" 
                           max="{{ date('Y-m-d') }}"
                           class="w-full rounded-md border-gray-300" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Absen</label>
                    <input type="time" name="waktu_absen" 
                           class="w-full rounded-md border-gray-300" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan</label>
                    <textarea name="alasan" rows="3" 
                              class="w-full rounded-md border-gray-300" 
                              placeholder="Berikan alasan absen mundur" required></textarea>
                </div>
                
                <!-- Form Controls -->
                <div class="flex justify-end gap-3" id="formControls">
                    <button type="button" onclick="closeBackdateModal()" 
                            class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">
                        Tutup
                    </button>
                    <button type="button" onclick="generateBackdateQR()" 
                            class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        Generate QR
                    </button>
                </div>

                <!-- QR Code Container -->
                <div class="mb-4 mt-6" id="qrCodeContainer" style="display: none;">
                    <div class="text-center">
                        <div id="qrCode" class="mx-auto flex justify-center mb-4"></div>
                        <p class="text-sm text-gray-600 mb-4">Scan QR Code untuk melakukan absen mundur</p>
                        <button type="button" onclick="closeBackdateModal()" 
                                class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 w-full">
                            Selesai
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script untuk Modal dan QR Code -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    function openBackdateModal() {
        const modal = document.getElementById('backdateModal');
        const form = document.getElementById('backdateForm');
        const qrContainer = document.getElementById('qrCodeContainer');
        const formControls = document.getElementById('formControls');
        
        // Reset form dan tampilan
        form.reset();
        qrContainer.style.display = 'none';
        formControls.style.display = 'flex';
        
        // Tampilkan modal
        modal.classList.remove('hidden');
    }

    function closeBackdateModal() {
        const modal = document.getElementById('backdateModal');
        const form = document.getElementById('backdateForm');
        const qrContainer = document.getElementById('qrCodeContainer');
        const formControls = document.getElementById('formControls');
        const qrElement = document.getElementById('qrCode');
        
        // Reset form
        form.reset();
        
        // Reset QR
        qrElement.innerHTML = '';
        qrContainer.style.display = 'none';
        
        // Tampilkan kembali form controls
        formControls.style.display = 'flex';
        
        // Tutup modal
        modal.classList.add('hidden');
    }

    function generateBackdateQR() {
        const form = document.getElementById('backdateForm');
        const formData = new FormData(form);
        const generateButton = form.querySelector('button[type="button"]');

        // Validasi form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Tampilkan loading state
        generateButton.disabled = true;
        generateButton.textContent = 'Generating...';

        // Persiapkan data yang akan dikirim
        const requestData = {
            tanggal_absen: formData.get('tanggal_absen'),
            waktu_absen: formData.get('waktu_absen'),
            alasan: formData.get('alasan')
        };

        // Kirim request untuk generate token backdate
        fetch('{{ route("admin.daftar_hadir.generate-backdate-token") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Tampilkan container QR
                const qrContainer = document.getElementById('qrCodeContainer');
                const qrElement = document.getElementById('qrCode');
                const formControls = document.getElementById('formControls');
                
                // Reset QR container
                qrElement.innerHTML = '';
                
                // Sembunyikan tombol form controls
                formControls.style.display = 'none';
                
                // Tampilkan QR container
                qrContainer.style.display = 'block';
                
                // Generate QR Code
                new QRCode(qrElement, {
                    text: data.qr_url,
                    width: 200,
                    height: 200,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            } else {
                throw new Error(data.message || 'Gagal generate QR Code');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan: ' + error.message);
        })
        .finally(() => {
            generateButton.disabled = false;
            generateButton.textContent = 'Generate QR';
        });
    }
</script>
@endsection

@section('styles')
<style>
    #qrCode img {
        margin: 0 auto;
        display: block;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
@endsection