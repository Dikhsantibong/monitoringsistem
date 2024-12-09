@extends('layouts.app')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="flex h-screen bg-gray-50 overflow-auto">
        <!-- Sidebar -->
        <aside id="mobile-menu"
            class="fixed z-20 overflow-hidden transform transition-transform duration-300 md:relative md:translate-x-0 h-screen w-64 bg-[#0A749B] shadow-md text-white hidden md:block md:shadow-lg ">
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
            <header class="bg-white shadow-sm">
                <div class="flex justify-between items-center px-6 py-2">
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
                    <h1 class="text-xl font-semibold text-gray-800">Kesiapan Pembangkit</h1>
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
                <x-admin-breadcrumb :breadcrumbs="[['name' => 'Kesiapan Pembangkit', 'url' => null]]" />
            </div>

            <main class="px-6">
                <!-- Konten Kesiapan Pembangkit -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kesiapan Pembangkit</h2>
                    <div class="mb-4 flex justify-between items-center">
                        <div class="flex space-x-4">
                            <input type="date" 
                                   id="filterDate" 
                                   value="{{ date('Y-m-d') }}"
                                   class="px-4 py-2 border rounded-lg">
                                   
                            <div class="flex items-center">
                                <div class="relative">
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                    <input type="text" 
                                           id="searchInput"
                                           placeholder="Cari mesin..."
                                           class="pl-10 pr-4 py-2 border rounded-lg mr-2">
                                </div>
                                <button onclick="loadData()" 
                                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                    <i class="fas fa-search mr-2"></i>Cari
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex space-x-4">
                            <button onclick="resetForm()" 
                                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                                <i class="fas fa-refresh mr-2"></i>Reset
                            </button>
                                
                            <button onclick="saveData()" 
                                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                                <i class="fas fa-save mr-2"></i>Simpan
                            </button>
                        </div>
                    </div>

                    <!-- Search Bar -->
                    @foreach ($units as $unit)
                        <div class="bg-white rounded-lg shadow p-6 mb-4 unit-table">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ $unit->name }}</h2>

                            <!-- Tabel Status Pembangkit -->
                            <table class="min-w-full divide-y divide-gray-200 border-collapse border border-gray-200">
                                <thead style="background-color: #0A749B; color: white;">
                                    <tr>
                                        <th class="py-2 px-4 font-medium ">Mesin</th>
                                        <th class="py-2 px-4 font-medium ">DMN</th>
                                        <th class="py-2 px-4 font-medium ">DMP</th>
                                        <th class="py-2 px-4 font-medium ">Beban</th>
                                        <th class="py-2 px-4 font-medium ">Status</th>
                                        <th class="py-2 px-4 font-medium ">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($unit->machines as $machine)
                                        <tr class="odd:bg-white even:bg-gray-100 searchable-row">
                                            <td class="py-2 px-4 border-b" data-id="{{ $machine->id }}">{{ $machine->name }}</td>
                                            <td class="py-2 px-4 border-b">
                                                {{ $operations->where('machine_id', $machine->id)->first()->dmn ?? 'N/A' }}
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                {{ $operations->where('machine_id', $machine->id)->first()->dmp ?? 'N/A' }}
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                <input type="number" 
                                                       class="w-full px-2 py-1 border rounded focus:outline-none focus:border-blue-500"
                                                       value=""
                                                       placeholder="Masukkan beban...">
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                <select class="w-full px-2 py-1 border rounded focus:outline-none focus:border-blue-500" 
                                                        onchange="this.style.backgroundColor = this.options[this.selectedIndex].style.backgroundColor">
                                                    <option value="" style="background-color: white">Pilih Status</option>
                                                    <option value="Operasi" style="background-color: #4CAF50">Operasi</option>
                                                    <option value="Standby" style="background-color: #2196F3">Standby</option>
                                                    <option value="Gangguan" style="background-color: #f44336">Gangguan</option>
                                                    <option value="Pemeliharaan" style="background-color: #FF9800">Pemeliharaan</option>
                                                </select>
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                <input type="text"
                                                       class="w-full px-2 py-1 border rounded focus:outline-none focus:border-blue-500"
                                                       value=""
                                                       placeholder="Masukkan keterangan...">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </main>
        </div>
    </div>
    </div>
@endsection
<script src="{{ asset('js/toggle.js') }}"></script>
<script>
    function searchTables() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const unitTables = document.querySelectorAll('.unit-table');
        
        unitTables.forEach(unitTable => {
            // Ambil nama unit dari heading
            const unitName = unitTable.querySelector('h2').textContent.toLowerCase();
            let unitHasMatch = false;
            
            // Cari di dalam rows mesin
            const rows = unitTable.querySelectorAll('.searchable-row');
            rows.forEach(row => {
                const machineName = row.querySelector('td:first-child').textContent.toLowerCase();
                const status = row.querySelector('select').value.toLowerCase();
                const dmn = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const dmp = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                
                // Cek apakah ada yang cocok dengan kriteria pencarian
                if (unitName.includes(searchInput) || 
                    machineName.includes(searchInput) || 
                    status.includes(searchInput) || 
                    dmn.includes(searchInput) || 
                    dmp.includes(searchInput)) {
                    row.style.display = '';
                    unitHasMatch = true;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Tampilkan/sembunyikan unit table berdasarkan hasil pencarian
            unitTable.style.display = unitHasMatch ? '' : 'none';
        });
    }

    // Event listener untuk real-time search
    document.getElementById('searchInput').addEventListener('input', searchTables);
</script>

<script>
function saveData() {
    const data = [];
    const tables = document.querySelectorAll('.unit-table table');
    const tanggal = document.getElementById('filterDate').value;
    
    tables.forEach(table => {
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const machineId = row.querySelector('td[data-id]').getAttribute('data-id');
            const bebanInput = row.querySelector('input[type="number"]');
            const select = row.querySelector('select');
            const keteranganInput = row.querySelector('input[type="text"]');
            const dmn = row.querySelector('td:nth-child(2)').textContent.trim();
            const dmp = row.querySelector('td:nth-child(3)').textContent.trim();
            
            if (select.value || bebanInput.value || keteranganInput.value) {
                data.push({
                    machine_id: machineId,
                    tanggal: tanggal,
                    status: select.value,
                    keterangan: keteranganInput.value.trim(),
                    dmn: dmn === 'N/A' ? 0 : dmn,
                    dmp: dmp === 'N/A' ? 0 : dmp,
                    load_value: bebanInput.value || null
                });
            }
        });
    });

    if (data.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Tidak ada data yang akan disimpan!'
        });
        return;
    }

    fetch('{{ route("admin.pembangkit.save-status") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ logs: data })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data berhasil disimpan!',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                // Muat ulang data setelah simpan berhasil
                const tanggal = document.getElementById('filterDate').value;
                const searchQuery = document.getElementById('searchInput').value;
                
                fetch(`{{ route("admin.pembangkit.get-status") }}?tanggal=${tanggal}&search=${searchQuery}`)
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            updateFormWithData(result.data);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        } else {
            throw new Error(result.message || 'Gagal menyimpan data');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Terjadi kesalahan saat menyimpan data!'
        });
    });
}

function resetForm() {
    Swal.fire({
        title: 'Konfirmasi Reset',
        text: 'Apakah Anda yakin ingin mereset form? Semua data yang belum disimpan akan hilang.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Reset',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const tables = document.querySelectorAll('.unit-table table');
            tables.forEach(table => {
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const select = row.querySelector('select');
                    const input = row.querySelector('input[type="text"]');
                    const bebanInput = row.querySelector('input[type="number"]');
                    
                    select.value = '';
                    select.style.backgroundColor = '';
                    input.value = '';
                    if (bebanInput) bebanInput.value = '';
                });
            });

            Swal.fire({
                icon: 'success',
                title: 'Form Direset',
                text: 'Form berhasil direset!',
                timer: 1500
            });
        }
    });
}

// Fungsi untuk memuat data
function loadData() {
    const tanggal = document.getElementById('filterDate').value;
    const searchQuery = document.getElementById('searchInput').value;
    
    // Tampilkan loading state jika diperlukan
    document.querySelectorAll('.unit-table').forEach(table => {
        table.style.opacity = '0.5';
    });
    
    fetch(`{{ route("admin.pembangkit.get-status") }}?tanggal=${tanggal}&search=${searchQuery}`)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                updateFormWithData(result.data);
                // Reset opacity setelah data dimuat
                document.querySelectorAll('.unit-table').forEach(table => {
                    table.style.opacity = '1';
                });
            } else {
                console.error('Error:', result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat mengambil data!'
            });
        });
}

// Event listener untuk perubahan tanggal
document.getElementById('filterDate').addEventListener('change', function() {
    loadData();
});

// Event listener untuk tombol cari
document.querySelector('button[onclick="loadData()"]').addEventListener('click', function(e) {
    e.preventDefault();
    loadData();
});

// Load data saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Set tanggal default ke hari ini jika belum diset
    const filterDate = document.getElementById('filterDate');
    if (!filterDate.value) {
        filterDate.value = new Date().toISOString().split('T')[0];
    }
    loadData();
});

function updateFormWithData(data) {
    const tables = document.querySelectorAll('.unit-table');
    
    tables.forEach(table => {
        const unitName = table.querySelector('h2').textContent;
        const rows = table.querySelectorAll('tbody tr');
        let unitHasData = false;
        
        rows.forEach(row => {
            const machineId = row.querySelector('td[data-id]').getAttribute('data-id');
            const machineData = data.find(d => 
                d.machine_id == machineId && 
                d.machine.power_plant.name === unitName
            );
            
            if (machineData) {
                const bebanInput = row.querySelector('input[type="number"]');
                const select = row.querySelector('select');
                const keteranganInput = row.querySelector('input[type="text"]');
                
                bebanInput.value = machineData.load_value || '';
                select.value = machineData.status || '';
                if (select.value) {
                    select.style.backgroundColor = select.options[select.selectedIndex].style.backgroundColor;
                }
                keteranganInput.value = machineData.keterangan || '';
                
                row.style.display = '';
                unitHasData = true;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Tampilkan/sembunyikan unit table berdasarkan ada tidaknya data
        table.style.display = unitHasData ? '' : 'none';
    });
}
</script>
@push('scripts')

@endpush

