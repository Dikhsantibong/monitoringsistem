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
        <div id="main-content" class="flex-1 main-content">
            <header class="bg-white shadow-sm">
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
                    <h1 class="text-xl font-semibold text-gray-800">Laporan SR/WO</h1>
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
                        <div class="mb-4 flex flex-col lg:flex-row justify-end space-x-4 gap-y-3">
                            <!-- Filter Tanggal -->
                            <div class="flex flex-col md:flex-row gap-y-3 items-center space-x-2">
                                <label class="text-gray-600">Dari:</label>
                                <input type="date" id="startDate"
                                    class="px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                <label class="text-gray-600">Sampai:</label>
                                <input type="date" id="endDate"
                                    class="px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                            </div>

                            <!-- Search Input -->
                            <div class="flex">
                                <input type="text" id="searchInput" placeholder="Cari..."
                                    class="w-full px-4 py-2 border rounded-l-lg focus:outline-none focus:border-blue-500">
                                <button onclick="searchTables()"
                                    class="bg-blue-500 px-4 py-2 rounded-tr-lg rounded-br-lg text-white font-semibold hover:bg-blue-800 transition-colors">
                                    search
                                </button>
                            </div>
                        </div>

                        <!-- Card SR -->
                        <div class="bg-white rounded-lg shadow p-6 mb-4">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-md font-semibold">Daftar Service Request (SR)</h3>
                                <button onclick="openSRModal()"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm sm:text-base">
                                    <i class="fas fa-plus mr-2"></i>Tambah SR
                                </button>
                            </div>
                            <div class="overflow-auto max-h-96">
                                <table id="srTable"
                                    class="min-w-full divide-y divide-gray-200 border-collapse border border-gray-200">
                                    <thead>
                                        <tr style="background-color: #0A749B; color: white;">
                                            <th class="py-2 px-4 border-b">No</th>
                                            <th class="py-2 px-4 border-b">ID SR</th>
                                            <th class="py-2 px-4 border-b">Deskripsi</th>
                                            <th class="py-2 px-4 border-b">Status</th>
                                            <th class="py-2 px-4 border-b">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($serviceRequests as $index => $sr)
                                            <tr class="odd:bg-white even:bg-gray-100">
                                                <td class="py-2 px-4 border-b">{{ $index + 1 }}</td>
                                                <td class="py-2 px-4 border-b">{{ $sr->id }}</td>
                                                <td class="py-2 px-4 border-b">{{ $sr->description }}</td>
                                                <td
                                                    class="py-2 px-4 border-b {{ $sr->status == 'Open' ? 'text-red-500' : 'text-green-500' }}">
                                                    {{ $sr->status }}</td>
                                                <td class="py-2 px-4 border-b">{{ $sr->created_at }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Card WO -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-md font-semibold">Daftar Work Order (WO)</h3>
                                <button onclick="openWOModal()"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm sm:text-base">
                                    <i class="fas fa-plus mr-2"></i>Tambah WO
                                </button>
                            </div>
                            <div class="overflow-auto max-h-96">
                                <table id="woTable" class="min-w-full bg-white border border-gray-300">
                                    <thead>
                                        <tr style="background-color: #0A749B; color: white;">
                                            <th class="py-2 px-4 border-b">No</th>
                                            <th class="py-2 px-4 border-b">ID WO</th>
                                            <th class="py-2 px-4 border-b">Deskripsi</th>
                                            <th class="py-2 px-4 border-b">Status</th>
                                            <th class="py-2 px-4 border-b">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($workOrders as $index => $wo)
                                            <tr>
                                                <td class="py-2 px-4 border-b">{{ $index + 1 }}</td>
                                                <td class="py-2 px-4 border-b">{{ $wo->id }}</td>
                                                <td class="py-2 px-4 border-b">{{ $wo->description }}</td>
                                                <td class="py-2 px-4 border-b">{{ $wo->status }}</td>
                                                <td class="py-2 px-4 border-b">{{ $wo->created_at }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
            </main>
        </div>
    </div>

    <!-- Modal SR -->
    <div id="srModal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center transform transition-all duration-300 scale-0">
        <div class="bg-white p-8 rounded-lg w-1/2 transform transition-all duration-300 scale-0">
            <h2 class="text-xl font-bold mb-4">Tambah Service Request (SR)</h2>
            <form id="srForm" action="{{ route('admin.laporan.store-sr') }}" method="POST" onsubmit="showSuccessAlert(event, 'SR')">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="sr_id">
                        ID SR
                    </label>
                    <input type="number" name="sr_id" id="sr_id" class="w-full px-3 py-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                        Deskripsi
                    </label>
                    <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 border rounded-lg" required></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                        Status
                    </label>
                    <select name="status" id="status" class="w-full px-3 py-2 border rounded-lg" required>
                        <option value="Open">Open</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeSRModal()"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal WO -->
    <div id="woModal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center transform transition-all duration-300 scale-0">
        <div class="bg-white p-8 rounded-lg w-1/2 transform transition-all duration-300 scale-0">
            <h2 class="text-xl font-bold mb-4">Tambah Work Order (WO)</h2>
            <form id="woForm" action="{{ route('admin.laporan.store-wo') }}" method="POST" onsubmit="showSuccessAlert(event, 'WO')">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="wo_id">
                        ID WO
                    </label>
                    <input type="number" name="wo_id" id="wo_id" class="w-full px-3 py-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                        Deskripsi
                    </label>
                    <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 border rounded-lg" required></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                        Status
                    </label>
                    <select name="status" id="status" class="w-full px-3 py-2 border rounded-lg" required>
                        <option value="Open">Open</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeWOModal()"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
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

    function searchTables() {
        const searchValue = document.getElementById('searchInput').value.toLowerCase();
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        // Fungsi untuk mengecek apakah tanggal dalam range
        function isDateInRange(dateStr) {
            if (!startDate && !endDate) return true;

            const date = new Date(dateStr);
            const start = startDate ? new Date(startDate) : null;
            const end = endDate ? new Date(endDate) : null;

            if (start && end) {
                return date >= start && date <= end;
            } else if (start) {
                return date >= start;
            } else if (end) {
                return date <= end;
            }
            return true;
        }

        // Cari di tabel SR
        const srTable = document.getElementById('srTable');
        const srRows = srTable.getElementsByTagName('tr');

        for (let i = 1; i < srRows.length; i++) {
            const row = srRows[i];
            const cells = row.getElementsByTagName('td');
            let textFound = false;
            let dateFound = false;

            // Cek text di semua kolom
            for (let cell of cells) {
                if (cell.textContent.toLowerCase().includes(searchValue)) {
                    textFound = true;
                }
            }

            // Cek tanggal (asumsikan tanggal ada di kolom terakhir)
            const dateCell = cells[cells.length - 1];
            if (dateCell) {
                dateFound = isDateInRange(dateCell.textContent);
            }

            row.style.display = (textFound && dateFound) ? '' : 'none';
        }

        // Cari di tabel WO
        const woTable = document.getElementById('woTable');
        const woRows = woTable.getElementsByTagName('tr');

        for (let i = 1; i < woRows.length; i++) {
            const row = woRows[i];
            const cells = row.getElementsByTagName('td');
            let textFound = false;
            let dateFound = false;

            // Cek text di semua kolom
            for (let cell of cells) {
                if (cell.textContent.toLowerCase().includes(searchValue)) {
                    textFound = true;
                }
            }

            // Cek tanggal (asumsikan tanggal ada di kolom terakhir)
            const dateCell = cells[cells.length - 1];
            if (dateCell) {
                dateFound = isDateInRange(dateCell.textContent);
            }

            row.style.display = (textFound && dateFound) ? '' : 'none';
        }
    }

    // Event listener untuk input tanggal
    document.getElementById('startDate').addEventListener('change', searchTables);
    document.getElementById('endDate').addEventListener('change', searchTables);

    // Event listener untuk pencarian teks (kode yang sudah ada)
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            searchTables();
        } else {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                searchTables();
            }, 300);
        }
    });

    // Reset pencarian
    document.getElementById('searchInput').addEventListener('input', function() {
        if (this.value === '') {
            searchTables();
        }
    });

    // Set tanggal default
    window.addEventListener('load', function() {
        // Set tanggal akhir ke hari ini
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('endDate').value = today;

        // Set tanggal awal ke 30 hari yang lalu
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
        document.getElementById('startDate').value = thirtyDaysAgo.toISOString().split('T')[0];

        // Jalankan pencarian awal
        searchTables();
    });
</script>
@push('scripts')
@endpush
