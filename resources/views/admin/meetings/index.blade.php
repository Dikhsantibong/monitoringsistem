@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-gray-50 overflow-auto">
        <!-- Sidebar -->
       @include('components.sidebar')
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
           

               

                <!-- Tabel Hasil Rapat -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-6">
                        <!-- Header dengan filter -->
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold text-gray-800">Score Card Daily</h2>
                            <div class="flex items-center gap-4">
                                <!-- Filter Tanggal -->
                                <div class="flex items-center">
                                    <label for="tanggal-filter" class="text-gray-700 text-sm font-bold mr-2">Pilih Tanggal:</label>
                                    <select id="tanggal-filter" 
                                            class="border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-[200px]">
                                        @foreach($availableDates as $date)
                                            <option value="{{ $date }}" 
                                                {{ $date == $selectedDate ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::parse($date)->format('d F Y') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Search -->
                                <div class="relative">
                                    <input type="text" id="search-input" 
                                           placeholder="Cari peserta..."
                                           class="border rounded-md pl-10 pr-4 py-2 w-64 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Content Container -->
                        <div id="dynamic-content">
                            @include('admin.meetings._table', ['scoreCards' => $scoreCards])
                        </div>
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
                    const dateSelect = document.querySelector('#tanggal-filter');
                    const date = dateSelect.value;
                    
                    if (!date) {
                        alert('Pilih tanggal terlebih dahulu');
                        return;
                    }

                    console.log('Selected date:', date); // Untuk debugging
                    const printUrl = "{{ route('admin.meetings.print') }}?date=" + encodeURIComponent(date);
                    window.open(printUrl, '_blank');
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

                <!-- JavaScript untuk handling perubahan tanggal -->
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const dateSelect = document.querySelector('#tanggal-filter');
                    if (dateSelect) {
                        dateSelect.value = '{{ $selectedDate }}';
                        
                        // Tambahkan event listener untuk perubahan tanggal
                        dateSelect.addEventListener('change', function() {
                            const selectedDate = this.value;
                            // Update URL tanpa refresh
                            const newUrl = new URL(window.location.href);
                            newUrl.searchParams.set('tanggal', selectedDate);
                            window.history.pushState({}, '', newUrl);
                            
                            // AJAX call untuk memperbarui data
                            fetch(`{{ route('admin.meetings') }}?tanggal=${selectedDate}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.text())
                            .then(html => {
                                // Update hanya konten dinamis
                                const dynamicContent = document.querySelector('#dynamic-content');
                                if (dynamicContent) {
                                    dynamicContent.innerHTML = html;
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Gagal memuat data. Silakan coba lagi.');
                            });
                        });
                    }
                });

                function printTable() {
                    const dateSelect = document.querySelector('#tanggal-filter');
                    const date = dateSelect.value;
                    
                    if (!date) {
                        alert('Pilih tanggal terlebih dahulu');
                        return;
                    }

                    console.log('Selected date for print:', date);
                    const printUrl = "{{ route('admin.meetings.print') }}?date=" + encodeURIComponent(date);
                    window.open(printUrl, '_blank');
                }
                </script>
            @endsection
