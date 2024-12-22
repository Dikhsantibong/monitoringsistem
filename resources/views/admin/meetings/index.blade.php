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
                        <h1 class="text-xl font-semibold text-gray-800">Laporan Rapat</h1>
                    </div>

                    @include('components.timer')
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

            <div class="flex flex-col sm:flex-row justify-between items-center pt-2">
                <div class="flex justify-start w-full">
                    <x-admin-breadcrumb :breadcrumbs="[['name' => 'Laporan Rapat', 'url' => null]]" />
                </div>
            </div>
            <main class="p-6">
                <!-- Filter Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Informasi Rapat -->
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Informasi Rapat</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Tanggal:</p>
                                <p class="font-medium">{{ \Carbon\Carbon::parse($selectedDate)->format('d F Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Lokasi:</p>
                                <p class="font-medium">{{ $scoreCards->isNotEmpty() ? $scoreCards->first()['lokasi'] : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Waktu Mulai:</p>
                                <p class="font-medium">{{ $scoreCards->isNotEmpty() ? \Carbon\Carbon::parse($scoreCards->first()['waktu_mulai'])->format('H:i') : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Waktu Selesai:</p>
                                <p class="font-medium">{{ $scoreCards->isNotEmpty() ? \Carbon\Carbon::parse($scoreCards->first()['waktu_selesai'])->format('H:i') : '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Score Summary -->
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Ringkasan Score</h3>
                        <div class="grid grid-cols-2 gap-4">
                            @if($scoreCards->isNotEmpty())
                                <div>
                                    <p class="text-sm text-gray-600">Kesiapan Panitia:</p>
                                    <p class="font-medium text-blue-600">{{ $scoreCards->first()['kesiapan_panitia'] }}%</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Kesiapan Bahan:</p>
                                    <p class="font-medium text-green-600">{{ $scoreCards->first()['kesiapan_bahan'] }}%</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Aktivitas Luar:</p>
                                    <p class="font-medium text-purple-600">{{ $scoreCards->first()['aktivitas_luar'] }}%</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Total Score:</p>
                                    <p class="font-medium text-red-600">
                                        {{ number_format(($scoreCards->first()['kesiapan_panitia'] + 
                                           $scoreCards->first()['kesiapan_bahan'] + 
                                           $scoreCards->first()['aktivitas_luar']) / 3, 2) }}%
                                    </p>
                                </div>
                            @else
                                <div class="col-span-2 text-center text-gray-500">
                                    Tidak ada data score yang tersedia
                                </div>
                            @endif
                        </div>
                    </div>
                </div>


               

                <!-- Tabel Hasil Rapat -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-6">
                        <!-- Header dengan filter -->
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold text-gray-800">Score Card Daily</h2>
                            <div class="flex items-center gap-4">
                                <!-- Filter Tanggal -->
                                            
                                <!-- Search -->
                                <div class="relative">
                                    <input type="text" id="search-input" 
                                           placeholder="Cari peserta..."
                                           class="border rounded-md pl-10 pr-4 py-2 w-64 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        @if($scoreCards->isNotEmpty())
                            <!-- Card Score Rapat (Dipindahkan ke atas) -->
                         

                            <!-- Tombol Print dan Download -->
                            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                <div class="flex justify-end gap-3">
                                    <div class="flex items-center">
                                        <select id="tanggal-filter" 
                                               class="border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-[200px]"
                                               onchange="changeDateFilter(this.value)">
                                            @foreach($availableDates as $date)
                                                <option value="{{ $date }}" {{ $date == $selectedDate ? 'selected' : '' }}>
                                                    {{ \Carbon\Carbon::parse($date)->format('d F Y') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button onclick="printTable()" 
                                            class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-md transition-colors duration-150 ease-in-out">
                                        <i class="fas fa-print mr-2"></i>
                                        Print
                                    </button>
                                    <button onclick="downloadPDF()" 
                                            class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-md transition-colors duration-150 ease-in-out">
                                        <i class="fas fa-file-pdf mr-2"></i>
                                        PDF
                                    </button>
                                    <button onclick="downloadExcel()" 
                                            class="inline-flex items-center px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium rounded-md transition-colors duration-150 ease-in-out">
                                        <i class="fas fa-file-excel mr-2"></i>
                                        Excel
                                    </button>
                                </div>
                            </div>

                            <!-- Tabel dengan Total Score di bagian bawah -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border-collapse border border-gray-200">
                                    <thead>
                                        <tr style="background-color: #0A749B; color: white">
                                            <th class="px-6 py-3 text-center text-sm font-medium uppercase">No</th>
                                            <th class="px-6 py-3 text-center text-sm font-medium uppercase">Peserta</th>
                                            <th class="px-6 py-3 text-center text-sm font-medium uppercase">Awal</th>
                                            <th class="px-6 py-3 text-center text-sm font-medium uppercase">Akhir</th>
                                            <th class="px-6 py-3 text-center text-sm font-medium uppercase">Score</th>
                                            <th class="px-6 py-3 text-center text-sm font-medium uppercase">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="score-card-body">
                                        @forelse($scoreCards as $scoreCard)
                                            @foreach($scoreCard['peserta'] as $index => $peserta)
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="text-center py-2 whitespace-nowrap border border-gray-300">
                                                        {{ $loop->iteration }}
                                                    </td>
                                                    <td class="py-2 whitespace-nowrap border border-gray-300 px-4">
                                                        {{ $peserta['jabatan'] }}
                                                    </td>
                                                    <td class="text-center py-2 whitespace-nowrap border border-gray-300">
                                                        {{ $peserta['awal'] }}
                                                    </td>
                                                    <td class="text-center py-2 whitespace-nowrap border border-gray-300">
                                                        {{ $peserta['akhir'] }}
                                                    </td>
                                                    <td class="text-center py-2 whitespace-nowrap border border-gray-300">
                                                        {{ $peserta['skor'] }}
                                                    </td>
                                                    <td class="py-2 whitespace-nowrap border border-gray-300 px-4">
                                                        <!-- Kolom keterangan -->
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                                    Tidak ada data score card yang tersedia
                                                </td>
                                            </tr>
                                        @endforelse

                                        <!-- Total Score di bagian bawah -->
                                        @if($scoreCards->isNotEmpty())
                                            @php
                                                $totalPesertaScore = collect($scoreCards->first()['peserta'])->sum('skor');
                                            @endphp
                                            
                                            <!-- Garis pemisah -->
                                            <tr class="bg-gray-100 font-semibold">
                                                <td colspan="4" class="py-3 px-4 border border-gray-300 text-right">
                                                    Total Score Peserta:
                                                </td>
                                                <td class="py-3 px-4 border border-gray-300 text-center">
                                                    {{ number_format($totalPesertaScore, 2) }}
                                                </td>
                                                <td class="border border-gray-300"></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500">Tidak ada data score card yang tersedia</p>
                            </div>
                        @endif
                    </div>
                </div>

                <style>
                    .modal-enter {
                        opacity: 0;
                        transform: scale(0.7);
                    }

                    .modal-enter-active {
                        opacity: 1;
                        transform: scale(1);
                        transition: opacity 0.3s, transform 0.3s;
                    }

                    .modal-leave {
                        opacity: 1;
                        transform: scale(1);
                    }

                    .modal-leave-active {
                        opacity: 0;
                        transform: scale(0.7);
                        transition: opacity 0.3s, transform 0.3s;
                    }
                </style>
                <script src="{{ asset('js/toggle.js') }}"></script>
                <script>
                    document.getElementById('upload-form').addEventListener('submit', function(e) {
                        e.preventDefault();
                        const formData = new FormData(this);

                        fetch('{{ route('admin.meetings.upload') }}', {
                                method: 'POST',
                                body: formData,
                            })
                            .then(response => response.json())
                            .then(data => {
                                document.getElementById('upload-message').innerText = data.message;
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                document.getElementById('upload-message').innerText = 'Upload failed.';
                            });
                    });

                    function openModal() {
                        const modal = document.getElementById('createMeetingModal');
                        modal.classList.remove('hidden');
                        modal.classList.add('modal-enter');
                        setTimeout(() => {
                            modal.classList.remove('modal-enter');
                            modal.classList.add('modal-enter-active');
                        }, 10); // Delay untuk memastikan animasi diterapkan
                    }

                    function closeModal() {
                        const modal = document.getElementById('createMeetingModal');
                        modal.classList.remove('modal-enter-active');
                        modal.classList.add('modal-leave');
                        setTimeout(() => {
                            modal.classList.add('hidden');
                            modal.classList.remove('modal-leave');
                        }, 300); // Delay untuk menunggu animasi selesai
                    }
                </script>

                <script>
                function filterData() {
                    const dateFilter = document.getElementById('tanggal-filter').value;
                    const searchInput = document.getElementById('search-input').value.toLowerCase();
                    const rows = document.querySelectorAll('#score-card-body tr');
                    let visibleCount = 0;

                    rows.forEach(row => {
                        const date = row.getAttribute('data-date');
                        const searchText = row.getAttribute('data-search');
                        
                        // Jika tidak ada filter yang aktif, tampilkan semua data
                        if (!dateFilter && !searchInput) {
                            row.style.display = '';
                            visibleCount++;
                            return;
                        }

                        const matchesDate = !dateFilter || date === dateFilter;
                        const matchesSearch = !searchInput || (searchText && searchText.includes(searchInput));

                        if (matchesDate && matchesSearch) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Tampilkan pesan jika tidak ada data yang sesuai
                    const noDataRow = document.querySelector('.no-data-message');
                    if (visibleCount === 0) {
                        if (!noDataRow) {
                            const tbody = document.getElementById('score-card-body');
                            const tr = document.createElement('tr');
                            tr.className = 'no-data-message';
                            tr.innerHTML = `
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada data yang sesuai dengan filter
                                </td>
                            `;
                            tbody.appendChild(tr);
                        }
                    } else if (noDataRow) {
                        noDataRow.remove();
                    }
                }

                // Inisialisasi tanpa filter otomatis
                document.addEventListener('DOMContentLoaded', function() {
                    // Hanya set tanggal tanpa menjalankan filter
                    const today = new Date().toISOString().split('T')[0];
                    const dateFilter = document.getElementById('tanggal-filter');
                    if (dateFilter) {
                        dateFilter.value = today;
                    }
                    
                    // Tambahkan event listeners
                    dateFilter?.addEventListener('change', filterData);
                    document.getElementById('search-input')?.addEventListener('input', debounce(filterData, 300));
                });

                // Fungsi debounce untuk mencegah terlalu banyak pemanggilan fungsi filter
                function debounce(func, wait) {
                    let timeout;
                    return function executedFunction(...args) {
                        const later = () => {
                            clearTimeout(timeout);
                            func(...args);
                        };
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                    };
                }

                // Tambahkan fungsi untuk reset filter
                function resetFilters() {
                    document.getElementById('tanggal-filter').value = '';
                    document.getElementById('search-input').value = '';
                    filterData();
                }
                </script>

                <!-- Tambahkan script untuk handling print dan download -->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

                <script>
                function printTable() {
                    const printContent = document.querySelector('.bg-white.rounded-lg.shadow.mb-6').innerHTML;
                    const originalContent = document.body.innerHTML;

                    document.body.innerHTML = `
                        <div class="p-4">
                            <h2 class="text-center text-xl font-bold mb-4">Score Card Daily</h2>
                            ${printContent}
                        </div>
                    `;

                    window.print();
                    document.body.innerHTML = originalContent;
                    window.location.reload(); // Reload halaman setelah print
                }

                function downloadPDF() {
                    Swal.fire({
                        title: 'Generating PDF...',
                        text: 'Please wait...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const element = document.querySelector('.bg-white.rounded-lg.shadow.mb-6');
                    const opt = {
                        margin: 1,
                        filename: `score_card_${document.querySelector('#tanggal-filter').value}.pdf`,
                        image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: { scale: 2 },
                        jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
                    };

                    html2pdf().set(opt).from(element).save()
                        .then(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'PDF berhasil diunduh',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        })
                        .catch(err => {
                            console.error('PDF Error:', err);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Gagal mengunduh PDF'
                            });
                        });
                }

                function downloadExcel() {
                    try {
                        // Ambil data dari tabel
                        const table = document.querySelector('table');
                        const rows = Array.from(table.querySelectorAll('tr'));
                        
                        // Konversi data tabel ke array
                        const data = rows.map(row => {
                            return Array.from(row.querySelectorAll('th, td')).map(cell => cell.textContent.trim());
                        });

                        // Tambahkan header informasi
                        const tanggal = document.querySelector('#tanggal-filter option:checked').text;
                        const lokasi = document.querySelector('.bg-gray-50 .grid-cols-2 div:last-child').textContent.replace('Lokasi:', '').trim();
                        
                        const header = [
                            ['Score Card Daily'],
                            ['Tanggal:', tanggal],
                            ['Lokasi:', lokasi],
                            [] // Baris kosong
                        ];

                        // Gabungkan header dan data
                        const finalData = [...header, ...data];

                        // Buat workbook baru
                        const wb = XLSX.utils.book_new();
                        const ws = XLSX.utils.aoa_to_sheet(finalData);

                        // Styling untuk header
                        ws['!cols'] = [{ wch: 10 }, { wch: 30 }, { wch: 10 }, { wch: 10 }, { wch: 10 }, { wch: 30 }];

                        // Tambahkan worksheet ke workbook
                        XLSX.utils.book_append_sheet(wb, ws, 'Score Card');

                        // Download file
                        XLSX.writeFile(wb, `score_card_${document.querySelector('#tanggal-filter').value}.xlsx`);

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Excel berhasil diunduh',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } catch (error) {
                        console.error('Excel Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal mengunduh Excel'
                        });
                    }
                }
                </script>

                @push('scripts')
                <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                @endpush

                @push('styles')
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
                <style>
                    @media print {
                        body * {
                            visibility: hidden;
                        }
                        .bg-white.rounded-lg.shadow.mb-6, .bg-white.rounded-lg.shadow.mb-6 * {
                            visibility: visible;
                        }
                        .bg-white.rounded-lg.shadow.mb-6 {
                            position: absolute;
                            left: 0;
                            top: 0;
                        }
                    }
                </style>
                @endpush
            @endsection
