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
                                <!-- Filter Unit - hanya tampil untuk session mysql -->
                                @if(session('unit') === 'mysql')
                                <div class="flex items-center">
                                    <label for="unit-source" class="text-gray-700 text-sm font-bold mr-2">Filter Unit:</label>
                                    <select id="unit-source" 
                                        class="border rounded-md px-3 py-2 text-sm w-40 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        onchange="updateData()">
                                        <option value="">Semua Unit</option>
                                        <option value="mysql" {{ request('unit_source') == 'mysql' ? 'selected' : '' }}>UP Kendari</option>
                                        <option value="mysql_wua_wua" {{ request('unit_source') == 'mysql_wua_wua' ? 'selected' : '' }}>Wua Wua</option>
                                        <option value="mysql_poasia" {{ request('unit_source') == 'mysql_poasia' ? 'selected' : '' }}>Poasia</option>
                                        <option value="mysql_kolaka" {{ request('unit_source') == 'mysql_kolaka' ? 'selected' : '' }}>Kolaka</option>
                                        <option value="mysql_bau_bau" {{ request('unit_source') == 'mysql_bau_bau' ? 'selected' : '' }}>Bau Bau</option>
                                    </select>
                                </div>
                                @endif
                                

                                <!-- Filter Tanggal dengan Datepicker -->
                                <div class="flex items-center">
                                    <label for="tanggal-filter" class="text-gray-700 text-sm font-bold mr-2">Pilih Tanggal:</label>
                                    <div class="relative">
                                        <input type="text" 
                                               id="tanggal-filter" 
                                               class="border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-[200px]"
                                               placeholder="mm/dd/yyyy"
                                               value="{{ $selectedDate }}"
                                               data-available-dates='@json($availableDates)'
                                               onfocus="(this.type='date')"
                                               onblur="(this.type='text')">
                                        <div class="absolute right-2 top-1/2 transform -translate-y-1/2">
                                            <!-- <i class="fas fa-calendar text-gray-400"></i> -->
                                        </div>
                                    </div>
                                </div>

                                <!-- Dropdown untuk Tanggal Tersedia -->
                                <div class="relative">
                                    

                                    <!-- Dropdown Content -->
                                    <div id="availableDatesList" class="hidden absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                        <div class="py-1 max-h-60 overflow-auto" role="menu">
                                            <div class="px-4 py-2 text-sm text-gray-700 border-b">
                                                <input type="text" 
                                                       id="dateSearch" 
                                                       class="w-full px-2 py-1 border rounded-md" 
                                                       placeholder="Cari tanggal...">
                                            </div>
                                            <div id="datesList">
                                                @foreach($availableDates as $date)
                                                <a href="#" 
                                                   class="date-option block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                                   data-date="{{ $date }}">
                                                    {{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM Y') }}
                                                </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
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

                    // Langsung buka window print tanpa loading
                    const printUrl = "{{ route('admin.meetings.print') }}?date=" + encodeURIComponent(date);
                    window.open(printUrl, '_blank');
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
                    const searchInput = document.querySelector('#search-input');
                    
                    function showLoader() {
                        const loader = document.getElementById('tableLoader');
                        const content = document.getElementById('tableData');
                        if (loader && content) {
                            loader.classList.remove('hidden');
                            content.classList.add('hidden');
                        }
                    }
                    
                    function hideLoader() {
                        const loader = document.getElementById('tableLoader');
                        const content = document.getElementById('tableData');
                        if (loader && content) {
                            loader.classList.add('hidden');
                            content.classList.remove('hidden');
                        }
                    }
                    
                    if (dateSelect) {
                        dateSelect.addEventListener('change', function() {
                            const selectedDate = this.value;
                            showLoader();
                            
                            // Update URL
                            const newUrl = new URL(window.location.href);
                            newUrl.searchParams.set('tanggal', selectedDate);
                            window.history.pushState({}, '', newUrl);
                            
                            // Fetch data dengan AJAX
                            fetch(`{{ route('admin.meetings') }}?tanggal=${selectedDate}`, {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'text/html'
                                }
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.text();
                            })
                            .then(html => {
                                const dynamicContent = document.querySelector('#dynamic-content');
                                if (dynamicContent) {
                                    dynamicContent.innerHTML = html;
                                    // Reinisialisasi event listeners jika diperlukan
                                    initializeEventListeners();
                                }
                                hideLoader();
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Gagal memuat data. Silakan coba lagi.');
                                hideLoader();
                            });
                        });
                    }

                    // Fungsi untuk menginisialisasi event listeners
                    function initializeEventListeners() {
                        // Tambahkan event listeners tambahan di sini jika diperlukan
                        const searchInput = document.querySelector('#search-input');
                        if (searchInput) {
                            searchInput.addEventListener('input', handleSearch);
                        }
                    }

                    // Fungsi untuk pencarian
                    function handleSearch() {
                        const searchValue = searchInput.value.toLowerCase();
                        const rows = document.querySelectorAll('#tableData tr');
                        
                        rows.forEach(row => {
                            const text = row.textContent.toLowerCase();
                            row.style.display = text.includes(searchValue) ? '' : 'none';
                        });
                    }
                });
                </script>

                <!-- Tambahkan script ini -->
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Inisialisasi Flatpickr
                    const flatpickrInstance = flatpickr("#tanggal-filter", {
                        locale: "id",
                        dateFormat: "Y-m-d",
                        enable: availableDates,
                        disableMobile: "true",
                        onChange: function(selectedDates, dateStr) {
                            updateData(dateStr);
                        }
                    });

                    // Toggle dropdown
                    const dropdown = document.getElementById('availableDatesDropdown');
                    const datesList = document.getElementById('availableDatesList');
                    
                    dropdown.addEventListener('click', function(e) {
                        e.stopPropagation();
                        datesList.classList.toggle('hidden');
                    });

                    // Tutup dropdown ketika klik di luar
                    document.addEventListener('click', function(e) {
                        if (!datesList.contains(e.target) && !dropdown.contains(e.target)) {
                            datesList.classList.add('hidden');
                        }
                    });

                    // Search functionality
                    const searchInput = document.getElementById('dateSearch');
                    const dateOptions = document.querySelectorAll('.date-option');

                    searchInput.addEventListener('input', function(e) {
                        const searchTerm = e.target.value.toLowerCase();
                        
                        dateOptions.forEach(option => {
                            const text = option.textContent.toLowerCase();
                            option.style.display = text.includes(searchTerm) ? '' : 'none';
                        });
                    });

                    // Handle date selection from dropdown
                    dateOptions.forEach(option => {
                        option.addEventListener('click', function(e) {
                            e.preventDefault();
                            const selectedDate = this.dataset.date;
                            flatpickrInstance.setDate(selectedDate);
                            datesList.classList.add('hidden');
                            updateData(selectedDate);
                        });
                    });

                    // Function to update data
                    function updateData(dateStr) {
                        showLoader();
                        
                        const unitSource = document.getElementById('unit-source')?.value || '';
                        const selectedDate = dateStr || document.getElementById('tanggal-filter').value;
                        
                        const params = new URLSearchParams({
                            tanggal: selectedDate,
                            unit_source: unitSource
                        });
                        
                        const newUrl = new URL(window.location.href);
                        params.forEach((value, key) => {
                            newUrl.searchParams.set(key, value);
                        });
                        window.history.pushState({}, '', newUrl);
                        
                        fetch(`{{ route('admin.meetings') }}?${params.toString()}`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html'
                            }
                        })
                        .then(response => response.text())
                        .then(html => {
                            const dynamicContent = document.querySelector('#dynamic-content');
                            if (dynamicContent) {
                                dynamicContent.innerHTML = html;
                            }
                            hideLoader();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Gagal memuat data. Silakan coba lagi.');
                            hideLoader();
                        });
                    }
                });
                </script>
                @push('scripts')
                    
                @endpush

                <style>
                /* Style untuk dropdown */
                .date-option {
                    transition: background-color 0.2s;
                }

                .date-option:hover {
                    background-color: #f3f4f6;
                }

                /* Scrollbar styling */
                #datesList {
                    scrollbar-width: thin;
                    scrollbar-color: #888 #f1f1f1;
                }

                #datesList::-webkit-scrollbar {
                    width: 6px;
                }

                #datesList::-webkit-scrollbar-track {
                    background: #f1f1f1;
                }

                #datesList::-webkit-scrollbar-thumb {
                    background: #888;
                    border-radius: 3px;
                }

                #datesList::-webkit-scrollbar-thumb:hover {
                    background: #555;
                }
                </style>

                <div id="loadingOverlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 flex items-center justify-center">
                    <div class="bg-white p-5 rounded-lg flex flex-col items-center">
                        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-500 mb-3"></div>
                        <p class="text-gray-700" id="loadingText">Memproses...</p>
                    </div>
                </div>

                <script>
                function showLoading(message = 'Memproses...') {
                    document.getElementById('loadingText').textContent = message;
                    document.getElementById('loadingOverlay').classList.remove('hidden');
                }

                function hideLoading() {
                    document.getElementById('loadingOverlay').classList.add('hidden');
                }

                // Update fungsi print
                function printTable() {
                    const dateSelect = document.querySelector('#tanggal-filter');
                    const date = dateSelect.value;
                    
                    if (!date) {
                        alert('Pilih tanggal terlebih dahulu');
                        return;
                    }

                   
                    const printUrl = "{{ route('admin.meetings.print') }}?date=" + encodeURIComponent(date);
                    
                    const printWindow = window.open(printUrl, '_blank');
                    if (printWindow) {
                        printWindow.onload = function() {
                            hideLoading();
                        };
                    } else {
                        hideLoading();
                    }
                }

                // Update fungsi download PDF
                function downloadPDF() {
                    const dateSelect = document.querySelector('#tanggal-filter');
                    const date = dateSelect.value;
                    
                    if (!date) {
                        alert('Pilih tanggal terlebih dahulu');
                        return;
                    }

                    showLoading('Mengunduh PDF...');
                    
                    fetch("{{ route('admin.meetings.download-pdf') }}?tanggal=" + encodeURIComponent(date))
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.blob();
                        })
                        .then(blob => {
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'score_card_' + date + '.pdf';
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);
                            document.body.removeChild(a);
                            hideLoading();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Gagal mengunduh PDF. Silakan coba lagi.');
                            hideLoading();
                        });
                }
                </script>
            @endsection

<style>
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>

