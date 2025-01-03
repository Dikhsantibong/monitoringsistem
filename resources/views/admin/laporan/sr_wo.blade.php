@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-gray-50 overflow-auto">
        <!-- Sidebar -->
       @include('components.sidebar')

        <!-- Main Content -->
        <div id="main-content" class="flex-1 main-content">
            <header class="bg-white shadow-sm sticky top-0 z-20">
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
                    <h1 class="text-xl font-semibold text-gray-800">Laporan SR/WO
                        
                    </h1>
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
            <div class="pt-2">
                <x-admin-breadcrumb :breadcrumbs="[['name' => 'Laporan SR/WO', 'url' => null]]" />
            </div>

            <main class="px-6">
                <!-- Konten Laporan SR/WO -->
                <div class="bg-white rounded-lg shadow p-6 sm:p-3">
                    <h2 class="text-lg font-semibold text-gray-800">Detail Laporan</h2>
                    <div class="p-4 md:p-0">
                        <!-- Filter dan Search Section -->
                        <div class="mb-4 flex flex-col lg:flex-row justify-end space-x-4 gap-y-3">
                            <form id="filterForm" class="flex flex-col md:flex-row gap-y-3 items-center space-x-2">
                                <div class="flex items-center space-x-2">
                                    <label for="tanggal_mulai" class="text-gray-600">Dari:</label>
                                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" 
                                        value="{{ request('tanggal_mulai', date('Y-m-d', strtotime('-7 days'))) }}"
                                        class="px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                </div>
                                <div class="flex items-center space-x-2">
                                    <label for="tanggal_akhir" class="text-gray-600">Sampai:</label>
                                    <input type="date" id="tanggal_akhir" name="tanggal_akhir" 
                                        value="{{ request('tanggal_akhir', date('Y-m-d')) }}"
                                        class="px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                </div>
                                <button type="button" onclick="updateFilter()" 
                                    class="bg-[#0A749B] text-white px-4 py-2 rounded-lg hover:bg-[#0A649B] transition-colors flex items-center" 
                                    style="height: 42px;">
                                    <i class="fas fa-filter mr-2"></i> Filter
                                </button>
                                <button type="button" onclick="showAllData()" 
                                    class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors flex items-center" 
                                    style="height: 42px;">
                                    <i class="fas fa-list mr-2"></i> Tampilkan Semua
                                </button>
                            </form>

                            <!-- Search Input -->
                            {{-- <div class="flex">
                                <input type="text" id="searchInput" placeholder="Cari..."
                                    class="w-full px-2 py-1 border rounded-l-lg focus:outline-none focus:border-blue-500" style="height: 42px;">
                                <button onclick="searchTables()"
                                    class="bg-blue-500 px-4 py-1 rounded-tr-lg rounded-br-lg text-white font-semibold hover:bg-blue-800 transition-colors" style="height: 42px;">
                                    Search
                                </button>
                            </div> --}}
                        </div>

                        <!-- Card SR -->
                        <div class="bg-white rounded-lg shadow p-6 mb-4">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-md font-semibold">Daftar Service Request (SR)</h3>
                                <a href="{{ route('admin.laporan.create-sr') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 flex items-center">
                                    <i class="fas fa-plus-circle mr-2"></i> Tambah SR
                                </a>
                            </div>
                            <!-- Kolom search untuk SR -->
                            <div class="mb-4">
                                <div class="flex">
                                    <input type="text" id="searchSR" placeholder="Cari SR..."
                                        class="w-full px-2 py-1 border rounded-l-lg focus:outline-none focus:border-blue-500" style="height: 42px;">
                                    <button onclick="searchSRTable()"
                                        class="bg-blue-500 px-4 py-1 rounded-tr-lg rounded-br-lg text-white font-semibold hover:bg-blue-800 transition-colors" style="height: 42px;">
                                        Search
                                    </button>
                                </div>
                            </div>
                            <div class="overflow-auto max-h-96">
                                <table id="srTable"
                                    class="min-w-full divide-y divide-gray-200 border-collapse border border-gray-200">
                                    <thead class="sticky top-0 z-10">
                                        <tr style="background-color: #0A749B; color: white;">
                                            <th class="py-2 px-4 border-b">No</th>
                                            <th class="py-2 px-4 border-b">ID SR</th>
                                            <th class="py-2 px-4 border-b">Deskripsi</th>
                                            <th class="py-2 px-4 border-b">Status</th>
                                            <th class="py-2 px-4 border-b">Tanggal</th>
                                            <th class="py-2 px-4 border-b">Downtime</th>
                                            <th class="py-2 px-4 border-b">Tipe SR</th>
                                            <th class="py-2 px-4 border-b">Priority</th>
                                            <th class="py-2 px-4 border-b">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($serviceRequests as $index => $sr)
                                            <tr class="odd:bg-white even:bg-gray-100">
                                                <td class="py-2 px-4 border border-gray-200">{{ $index + 1 }}</td>
                                                <td class="py-2 px-4 border border-gray-200">{{ $sr->id }}</td>
                                                <td class="py-2 px-4 border border-gray-200">{{ $sr->description }}</td>
                                                <td class="py-2 px-4 border border-gray-200">
                                                    <span class="px-2 py-1 rounded-full {{ $sr->status == 'Open' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                                        {{ $sr->status }}
                                                    </span>
                                                </td>
                                                <td class="py-2 px-4 border border-gray-200">{{ $sr->created_at }}</td>
                                                <td class="py-2 px-4 border border-gray-200">
                                                    {{ $sr->downtime }}
                                                </td>
                                                <td class="py-2 px-4 border border-gray-200">
                                                    {{ $sr->tipe_sr }}
                                                </td>
                                                <td class="py-2 px-4 border border-gray-200">
                                                    {{ $sr->priority }}
                                                </td>
                                                <td class="py-2 px-4 border border-gray-200">
                                                    <button onclick="updateStatus('sr', {{ $sr->id }}, '{{ $sr->status }}')"
                                                        class="px-3 py-1 text-sm rounded-full {{ $sr->status == 'Open' ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600' }} text-white">
                                                        {{ $sr->status == 'Open' ? 'Tutup' : 'Buka' }}
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 text-sm text-gray-600">
                                Menampilkan <span id="srVisibleCount">{{ count($serviceRequests) }}</span> dari total <span id="srTotalCount">{{ count($serviceRequests) }}</span> SR
                            </div>
                        </div>

                        <!-- Card WO -->
                        <div class="bg-white rounded-lg shadow p-6 mb-4">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-md font-semibold">Daftar Work Order (WO)</h3>
                                <a href="{{ route('admin.laporan.create-wo') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 flex items-center">
                                    <i class="fas fa-plus-circle mr-2"></i> Tambah WO
                                </a>
                            </div>
                            <!-- Kolom search untuk WO -->
                            <div class="mb-4">
                                <div class="flex">
                                    <input type="text" id="searchWO" placeholder="Cari WO..."
                                        class="w-full px-2 py-1 border rounded-l-lg focus:outline-none focus:border-blue-500" style="height: 42px;">
                                    <button onclick="searchWOTable()"
                                        class="bg-blue-500 px-4 py-1 rounded-tr-lg rounded-br-lg text-white font-semibold hover:bg-blue-800 transition-colors" style="height: 42px;">
                                        Search
                                    </button>
                                </div>
                            </div>
                            <div class="overflow-auto max-h-96">
                                @if(session('backlog_notification'))
                                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                                    <p class="font-bold">Perhatian!</p>
                                    <p>{{ session('backlog_notification') }}</p>
                                </div>
                                @endif
                                <table id="woTable" class="min-w-full bg-white border border-gray-300">
                                    <thead class="sticky top-0 z-10">
                                        <tr style="background-color: #0A749B; color: white;">
                                            <th class="py-2 px-4 border-b">No</th>
                                            <th class="py-2 px-4 border-b">ID WO</th>
                                            <th class="py-2 px-4 border-b">Deskripsi</th>
                                            <th class="py-2 px-4 border-b">Status</th>
                                            <th class="py-2 px-4 border-b">Tanggal</th>
                                            <th class="py-2 px-4 border-b">Priority</th>
                                            <th class="py-2 px-4 border-b">Schedule Start</th>
                                            <th class="py-2 px-4 border-b">Schedule Finish</th>
                                            <th class="py-2 px-4 border-b">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($workOrders as $index => $wo)
                                            <tr class="odd:bg-white even:bg-gray-100">
                                                <td class="py-2 px-4 border border-gray-200">{{ $index + 1 }}</td>
                                                <td class="py-2 px-4 border border-gray-200">{{ $wo->id }}</td>
                                                <td class="py-2 px-4 border border-gray-200">{{ $wo->description }}</td>
                                                <td class="py-2 px-4 border border-gray-200">
                                                    <span class="bg-{{ $wo->status == 'Open' ? 'red-500' : ($wo->status == 'Closed' ? 'green-500' : ($wo->status == 'Comp' ? 'blue-500' : ($wo->status == 'APPR' ? 'yellow-500' : ($wo->status == 'WAPPR' ? 'purple-500' : 'gray-500')))) }} text-white rounded-full px-2 py-1">
                                                            {{ $wo->status }}
                                                    </span>
                                                </td>
                                                <td class="py-2 px-4 border border-gray-200">{{ $wo->created_at }}</td>
                                                <td class="py-2 px-4 border border-gray-200">
                                                    {{ $wo->priority }}
                                                </td>
                                                <td class="py-2 px-4 border border-gray-200">
                                                    {{ $wo->schedule_start }}
                                                </td>
                                                <td class="py-2 px-4 border border-gray-200">
                                                    {{ $wo->schedule_finish }}
                                                </td>
                                                <td class="py-2 px-4 border border-gray-200">
                                                    @if ($wo->status != 'Closed')
                                                        <button onclick="showStatusOptions({{ $wo->id }}, '{{ $wo->status }}')"
                                                            class="px-3 py-1 text-sm rounded-full bg-blue-500 hover:bg-blue-600 text-white flex items-center">
                                                            <i class="fas fa-edit mr-2"></i> Ubah
                                                        </button>
                                                    @else
                                                        <button disabled class="px-3 py-1 text-sm rounded-full bg-gray-400 text-white">
                                                            Closed
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 text-sm text-gray-600">
                                Menampilkan <span id="woVisibleCount">{{ count($workOrders) }}</span> dari total <span id="woTotalCount">{{ count($workOrders) }}</span> WO
                            </div>
                        </div>

                        <!-- Tabel Backlog -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-md font-semibold">Daftar WO Backlog</h3>
                                <a href="{{ route('admin.laporan.create-wo-backlog') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                    <i class="fas fa-plus-circle mr-2"></i> Tambah WO Backlog
                                </a>
                            </div>
                            <!-- Kolom search untuk Backlog -->
                            <div class="mb-4">
                                <div class="flex">
                                    <input type="text" id="searchBacklog" placeholder="Cari Backlog..."
                                        class="w-full px-2 py-1 border rounded-l-lg focus:outline-none focus:border-blue-500" style="height: 42px;">
                                    <button onclick="searchBacklogTable()"
                                        class="bg-blue-500 px-4 py-1 rounded-tr-lg rounded-br-lg text-white font-semibold hover:bg-blue-800 transition-colors" style="height: 42px;">
                                        Search
                                    </button>
                                </div>
                            </div>
                            <div class="overflow-auto max-h-96">
                                <table id="backlogTable" class="min-w-full divide-y divide-gray-200 border-collapse border border-gray-200">
                                    <thead class="sticky top-0 z-10 " style="height: 60px;">
                                        <tr style="background-color: #0A749B; color: white;">
                                            <th class="py-2 px-4 border-b">No</th>
                                            <th class="py-2 px-4 border-b">No WO</th>
                                            <th class="py-2 px-4 border-b">Deskripsi</th>
                                            <th class="py-2 px-4 border-b">Tanggal Backlog</th>
                                            <th class="py-2 px-4 border-b">Keterangan</th>
                                            <th class="py-2 px-4 border-b">Status</th>
                                            <th class="py-2 px-4 border-b">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($woBacklogs as $index => $backlog)
                                            <tr class="odd:bg-white even:bg-gray-100">
                                                <td class="py-2 px-4 border border-gray-200">{{ $index + 1 }}</td>
                                                <td class="py-2 px-4 border border-gray-200">{{ $backlog->no_wo }}</td>
                                                <td class="py-2 px-4 border border-gray-200">{{ $backlog->deskripsi }}</td>
                                                <td class="py-2 px-4 border border-gray-200">{{ $backlog->created_at }}</td>
                                                <td class="py-2 px-4 border border-gray-200">{{ $backlog->keterangan ?? 'N/A' }}</td>
                                                <td class="py-2 px-4 border border-gray-200">
                                                    <span class="px-2 py-1 rounded-full {{ $backlog->status == 'Open' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                                        {{ $backlog->status }}
                                                    </span>
                                                </td>
                                                <td class="py-2 px-4 border border-gray-200">
                                                    <div class="flex space-x-2">
                                                        {{-- @if($backlog->status == 'Open')
                                                            <button 
                                                                onclick="updateBacklogStatus({{ $backlog->id }})"
                                                                class="px-3 py-1 text-sm rounded-full bg-green-500 hover:bg-green-600 text-white">
                                                                Tutup
                                                            </button>
                                                        @endif --}}
                                                        <a href="{{ route('admin.laporan.edit-wo-backlog', $backlog->id) }}"
                                                            class="px-3 py-1 text-sm rounded-full bg-blue-500 hover:bg-blue-600 text-white flex items-center">
                                                            <i class="fas fa-edit mr-2"></i> Edit
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 text-sm text-gray-600">
                                Menampilkan <span id="backlogVisibleCount">{{ count($woBacklogs) }}</span> dari total <span id="backlogTotalCount">{{ count($woBacklogs) }}</span> WO Backlog
                            </div>
                        </div>
                    </div>
            </main>
        </div>
    </div>

    <!-- Modal SR -->
   
@endsection
<script src="{{ asset('js/toggle.js') }}"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Fungsi untuk membuka modal SR
    function openSRModal() {
        const modal = document.getElementById('srModal');
        const modalContent = modal.querySelector('.bg-white');
        modal.classList.remove('hidden');
        modal.classList.remove('scale-0');
        modal.classList.add('scale-100');
        setTimeout(() => {
            modalContent.classList.remove('scale-0');
            modalContent.classList.add('scale-100');
        }, 100);
    }

    // Fungsi untuk menutup modal SR 
    function closeSRModal() {
        const modal = document.getElementById('srModal');
        const modalContent = modal.querySelector('.bg-white');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-0');
        setTimeout(() => {
            modal.classList.remove('scale-100');
            modal.classList.add('scale-0');
            modal.classList.add('hidden');
        }, 300);
    }

    // Fungsi untuk membuka modal WO
    function openWOModal() {
        const modal = document.getElementById('woModal');
        const modalContent = modal.querySelector('.bg-white');
        modal.classList.remove('hidden');
        modal.classList.remove('scale-0');
        modal.classList.add('scale-100');
        setTimeout(() => {
            modalContent.classList.remove('scale-0');
            modalContent.classList.add('scale-100');
        }, 100);
    }

    // Fungsi untuk menutup modal WO
    function closeWOModal() {
        const modal = document.getElementById('woModal');
        const modalContent = modal.querySelector('.bg-white');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-0');
        setTimeout(() => {
            modal.classList.remove('scale-100');
            modal.classList.add('scale-0');
            modal.classList.add('hidden');
        }, 300);
    }

    // Fungsi untuk menampilkan alert sukses
    function showSuccessAlert(event, type) {
        event.preventDefault(); // Prevent form submission
        const form = event.target;
        const formData = new FormData(form);
        
        // Simulate form submission (you can replace this with actual AJAX call)
        setTimeout(() => {
            Swal.fire({
                icon: 'success',
                title: `${type} berhasil ditambahkan!`,
                showConfirmButton: false,
                timer: 1500
            });
            form.submit(); // Submit the form after showing the alert
        }, 500);
    }

    // Filter tanggal
    function updateFilter() {
        let baseUrl = window.location.pathname;
        let tanggalMulai = document.getElementById('tanggal_mulai').value;
        let tanggalAkhir = document.getElementById('tanggal_akhir').value;
        let searchValue = document.getElementById('searchInput').value;
        
        // Validasi tanggal
        if (!tanggalMulai || !tanggalAkhir) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Mohon isi kedua tanggal'
            });
            return;
        }

        if (tanggalMulai > tanggalAkhir) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir'
            });
            return;
        }
        
        // Redirect dengan parameter tanggal dan search jika ada
        let url = `${baseUrl}?tanggal_mulai=${tanggalMulai}&tanggal_akhir=${tanggalAkhir}`;
        if (searchValue) {
            url += `&search=${encodeURIComponent(searchValue)}`;
        }
        window.location.href = url;
    }

    // Set tanggal default dan search value dari URL parameters
    window.addEventListener('load', function() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Set tanggal default hanya jika bukan dari tombol "Tampilkan Semua"
        if (!urlParams.has('show_all') && !urlParams.has('tanggal_mulai') && !urlParams.has('tanggal_akhir')) {
            const today = new Date();
            const sevenDaysAgo = new Date(today);
            sevenDaysAgo.setDate(today.getDate() - 7);
            
            document.getElementById('tanggal_mulai').value = sevenDaysAgo.toISOString().split('T')[0];
            document.getElementById('tanggal_akhir').value = today.toISOString().split('T')[0];
        }
        
        // Set search value dari URL jika ada
        if (urlParams.has('search')) {
            document.getElementById('searchInput').value = urlParams.get('search');
            searchTables();
        }
    });

    // Fungsi search
    function searchTables() {
        const searchValue = document.getElementById('searchInput').value.toLowerCase();
        
        // Search di tabel SR
        searchTable('srTable', searchValue);
        
        // Search di tabel WO
        searchTable('woTable', searchValue);
    }

    function searchTable(tableId, searchValue) {
        const table = document.getElementById(tableId);
        const rows = table.getElementsByTagName('tr');

        // Loop melalui semua baris, mulai dari index 1 untuk melewati header
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;

            // Cek setiap sel dalam baris
            for (let j = 0; j < cells.length; j++) {
                const cellText = cells[j].textContent.toLowerCase();
                if (cellText.includes(searchValue)) {
                    found = true;
                    break;
                }
            }

            // Tampilkan/sembunyikan baris berdasarkan hasil pencarian
            row.style.display = found ? '' : 'none';
        }
    }

    // Event listener untuk search input
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            searchTables();
        }
    });

    // Debounce function untuk search
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

    // Aplikasikan debounce pada search
    const debouncedSearch = debounce(() => searchTables(), 300);
    document.getElementById('searchInput').addEventListener('input', debouncedSearch);

    function updateStatus(type, id, currentStatus) {
        const newStatus = currentStatus === 'Open' ? 'Closed' : 'Open';
        if (currentStatus === 'Closed') {
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: 'WO sudah ditutup dan tidak dapat diubah lagi.',
            });
            return; // Menghentikan eksekusi jika sudah ditutup
        }
        const url = type === 'sr' ? 
            "{{ route('admin.laporan.update-sr-status', ['id' => ':id']) }}".replace(':id', id) :
            "{{ route('admin.laporan.update-wo-status', ['id' => ':id']) }}".replace(':id', id);

        Swal.fire({
            title: 'Konfirmasi',
            text: `Apakah Anda yakin ingin mengubah status menjadi ${newStatus}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: newStatus })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Status berhasil diubah!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi kesalahan!',
                            text: data.message || 'Gagal mengubah status'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan!',
                        text: 'Gagal mengubah status'
                    });
                });
            }
        });
    }

    function updateBacklogStatus(id) {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin menutup WO Backlog ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                fetch(`/admin/laporan/wo-backlog/${id}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ status: 'Closed' })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'WO Backlog berhasil ditutup!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan!',
                        text: 'Gagal mengubah status'
                    });
                });
            }
        });
    }

    function showStatusOptions(id, currentStatus) {
        // Definisikan semua status yang tersedia sesuai enum database
        const allStatuses = {
            'Open': 'Open',
            'WAPPR': 'WAPPR',
            'APPR': 'APPR',
            'Comp': 'Comp',
            'Closed': 'Closed',  // Ubah dari 'Closed' ke 'Close'
            'WMATL': 'WMATL'
        };

        // Jika status saat ini adalah Close
        if (currentStatus === 'Closed') {  // Ubah dari 'Closed' ke 'Close'
            Swal.fire({
                icon: 'info',
                title: 'Status Closed',
                text: 'WO sudah ditutup dan tidak dapat diubah statusnya',
            });
            return;
        }

        // Hapus status saat ini dari pilihan
        const inputOptions = { ...allStatuses };
        delete inputOptions[currentStatus];

        console.log('Available Status Options:', inputOptions); // Debug log

        Swal.fire({
            title: 'Ubah Status',
            text: `Status saat ini: ${currentStatus}`,
            input: 'select',
            inputOptions: inputOptions,
            inputPlaceholder: 'Pilih status baru',
            showCancelButton: true,
            confirmButtonText: 'Ubah',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            inputValidator: (value) => {
                if (!value) {
                    return 'Anda harus memilih status!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                console.log('Selected Status:', result.value); // Debug log
                processStatusUpdate(id, result.value);
            }
        });
    }

    function processStatusUpdate(id, newStatus) {
        const url = "{{ route('admin.laporan.update-wo-status', ['id' => ':id']) }}".replace(':id', id);
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    // Refresh halaman dengan clear cache
                    window.location.href = window.location.href.split('?')[0] + 
                        '?_=' + new Date().getTime();
                });
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: error.message
            });
        });
    }

    function showAllData() {
        // Redirect ke halaman yang sama tanpa parameter tanggal
        window.location.href = window.location.pathname;
    }

    // Fungsi pencarian untuk tabel SR
    function searchSRTable() {
        const searchValue = document.getElementById('searchSR').value.toLowerCase();
        const table = document.getElementById('srTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;

            for (let j = 0; j < cells.length; j++) {
                const cellText = cells[j].textContent.toLowerCase();
                if (cellText.includes(searchValue)) {
                    found = true;
                    break;
                }
            }

            row.style.display = found ? '' : 'none';
        }

        // Update jumlah data SR
        updateTableCounts('srTable', 'srVisibleCount', 'srTotalCount');
    }

    // Fungsi pencarian untuk tabel WO
    function searchWOTable() {
        const searchValue = document.getElementById('searchWO').value.toLowerCase();
        const table = document.getElementById('woTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;

            for (let j = 0; j < cells.length; j++) {
                const cellText = cells[j].textContent.toLowerCase();
                if (cellText.includes(searchValue)) {
                    found = true;
                    break;
                }
            }

            row.style.display = found ? '' : 'none';
        }

        // Update jumlah data WO
        updateTableCounts('woTable', 'woVisibleCount', 'woTotalCount');
    }

    // Fungsi pencarian untuk tabel Backlog
    function searchBacklogTable() {
        const searchValue = document.getElementById('searchBacklog').value.toLowerCase();
        const table = document.getElementById('backlogTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;

            for (let j = 0; j < cells.length; j++) {
                const cellText = cells[j].textContent.toLowerCase();
                if (cellText.includes(searchValue)) {
                    found = true;
                    break;
                }
            }

            row.style.display = found ? '' : 'none';
        }

        // Update jumlah data Backlog
        updateTableCounts('backlogTable', 'backlogVisibleCount', 'backlogTotalCount');
    }

    // Tambahkan event listener untuk pencarian real-time
    document.getElementById('searchSR').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            searchSRTable();
        }
    });

    document.getElementById('searchWO').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            searchWOTable();
        }
    });

    document.getElementById('searchBacklog').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            searchBacklogTable();
        }
    });

    // Tambahkan debounce untuk pencarian real-time
    const debouncedSRSearch = debounce(() => searchSRTable(), 300);
    const debouncedWOSearch = debounce(() => searchWOTable(), 300);
    const debouncedBacklogSearch = debounce(() => searchBacklogTable(), 300);

    document.getElementById('searchSR').addEventListener('input', debouncedSRSearch);
    document.getElementById('searchWO').addEventListener('input', debouncedWOSearch);
    document.getElementById('searchBacklog').addEventListener('input', debouncedBacklogSearch);

    // Fungsi untuk memperbarui jumlah data yang ditampilkan
    function updateTableCounts(tableId, visibleCountId, totalCountId) {
        const table = document.getElementById(tableId);
        const rows = table.getElementsByTagName('tr');
        let visibleCount = 0;
        let totalCount = 0;

        // Hitung jumlah baris yang terlihat dan total (skip header)
        for (let i = 1; i < rows.length; i++) {
            totalCount++;
            if (rows[i].style.display !== 'none') {
                visibleCount++;
            }
        }

        // Update tampilan jumlah
        document.getElementById(visibleCountId).textContent = visibleCount;
        document.getElementById(totalCountId).textContent = totalCount;
    }
</script>
@push('scripts')
@endpush
    