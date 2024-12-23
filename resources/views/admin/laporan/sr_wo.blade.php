@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-gray-50 overflow-auto">
        <!-- Sidebar -->
       @include('components.sidebar')

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
                        <!-- Filter dan Search Section -->
                        <div class="mb-4 flex flex-col lg:flex-row justify-end space-x-4 gap-y-3">
                            <form id="filterForm" class="flex flex-col md:flex-row gap-y-3 items-center space-x-2">
                                <div class="flex items-center space-x-2">
                                    <label for="tanggal" class="text-gray-600">Pilih Tanggal:</label>
                                    <input type="date" id="tanggal" name="tanggal" 
                                        value="{{ request('tanggal', date('Y-m-d')) }}"
                                        class="px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                </div>
                            </form>

                            <!-- Search Input -->
                            <div class="flex">
                                <input type="text" id="searchInput" placeholder="Cari..."
                                    class="w-full px-2 py-1 border rounded-l-lg focus:outline-none focus:border-blue-500" style="height: 42px;">
                                <button onclick="searchTables()"
                                    class="bg-blue-500 px-4 py-1 rounded-tr-lg rounded-br-lg text-white font-semibold hover:bg-blue-800 transition-colors" style="height: 42px;">
                                    search
                                </button>
                            </div>
                        </div>

                        <!-- Card SR -->
                        <div class="bg-white rounded-lg shadow p-6 mb-4">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-md font-semibold">Daftar Service Request (SR)</h3>
                                <div class="flex justify-end space-x-4">
                                    <a href="{{ route('admin.laporan.create-sr') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 flex items-center">
                                        <i class="fas fa-plus-circle mr-2"></i> Tambah SR
                                    </a>
                                    {{-- <a href="{{ route('admin.laporan.sr-wo') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                                        Kembali
                                    </a> --}}
                                </div>
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
                                                <td class="py-2 px-4 border border-gray-200 {{ $sr->status == 'Open' ? 'text-red-500' : 'text-green-500' }}">
                                                    {{ $sr->status }}</td>
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
                                                    {{ $wo->status }}
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
                                                    <button onclick="updateStatus('wo', {{ $wo->id }}, '{{ $wo->status }}')"
                                                        class="px-3 py-1 text-sm rounded-full {{ $wo->status == 'Open' ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600' }} text-white">
                                                        {{ $wo->status == 'Open' ? 'Tutup' : 'Buka' }}
                                                    </button>
                                                </td>
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
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="tanggal">
                        Tanggal
                    </label>
                    <input type="date" name="tanggal" id="tanggal" class="w-full px-3 py-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="downtime">
                        Downtime
                    </label>
                    <select name="downtime" id="downtime" class="w-full px-3 py-2 border rounded-lg" required>
                        <option value="ya">Ya</option>
                        <option value="tidak">Tidak</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="tipe_sr">
                        Tipe SR
                    </label>
                    <select name="tipe_sr" id="tipe_sr" class="w-full px-3 py-2 border rounded-lg" required>
                        <option value="CM">CM</option>
                        <option value="EJ">EJ</option>
                        <option value="FLM">FLM</option>
                        <option value="PDM">PDM</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="priority">
                        Priority
                    </label>
                    <select name="priority" id="priority" class="w-full px-3 py-2 border rounded-lg" required>
                        <option value="emergency">Emergency</option>
                        <option value="normal">Normal</option>
                        <option value="outage">Outage</option>
                        <option value="urgent">Urgent</option>
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

    // Filter tanggal
    document.getElementById('tanggal').addEventListener('change', function() {
        let baseUrl = window.location.pathname;
        let tanggal = this.value;
        let searchValue = document.getElementById('searchInput').value;
        
        // Redirect dengan parameter tanggal dan search jika ada
        let url = `${baseUrl}?tanggal=${tanggal}`;
        if (searchValue) {
            url += `&search=${encodeURIComponent(searchValue)}`;
        }
        window.location.href = url;
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

    // Set tanggal default dan search value dari URL parameters
    window.addEventListener('load', function() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Set tanggal default jika tidak ada di URL
        if (!urlParams.has('tanggal')) {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('tanggal').value = today;
            window.location.href = `${window.location.pathname}?tanggal=${today}`;
        }
        
        // Set search value dari URL jika ada
        if (urlParams.has('search')) {
            document.getElementById('searchInput').value = urlParams.get('search');
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
                .then(response => response.json())
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
</script>
@push('scripts')
@endpush
