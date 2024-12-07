@extends('layouts.app')

@section('content')
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
                <div class="flex justify-between items-center px-6 py-4">
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
                    <h1 class="text-2xl font-semibold text-gray-800">Kesiapan Pembangkit</h1>
                </div>
                <x-admin-breadcrumb :breadcrumbs="[['name' => 'Kesiapan Pembangkit', 'url' => null]]" />
            </header>

            <main class="p-6">
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
                                                {{ $operations->where('machine_id', $machine->id)->first()->load_value ?? 'N/A' }}
                                            </td>
                                                <td class="py-2 px-4 border-b">
                                                <select class="w-full px-2 py-1 border rounded focus:outline-none focus:border-blue-500" onchange="this.style.backgroundColor = this.options[this.selectedIndex].style.backgroundColor">
                                                    <option value="Operasi" style="background-color: #4CAF50" {{ ($operations->where('machine_id', $machine->id)->first()->status ?? '') == 'Operasi' ? 'selected' : '' }}>Operasi</option>
                                                    <option value="Standby" style="background-color: #2196F3" {{ ($operations->where('machine_id', $machine->id)->first()->status ?? '') == 'Standby' ? 'selected' : '' }}>Standby</option>
                                                    <option value="Gangguan" style="background-color: #f44336" {{ ($operations->where('machine_id', $machine->id)->first()->status ?? '') == 'Gangguan' ? 'selected' : '' }}>Gangguan</option>
                                                    <option value="Pemeliharaan" style="background-color: #FF9800" {{ ($operations->where('machine_id', $machine->id)->first()->status ?? '') == 'Pemeliharaan' ? 'selected' : '' }}>Pemeliharaan</option>
                                                </select>
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                <input type="text"
                                                    class="w-full px-2 py-1 border rounded focus:outline-none focus:border-blue-500"
                                                    value="{{ $operations->where('machine_id', $machine->id)->first()->keterangan ?? '' }}"
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
        const searchInput = document.getElementById('searchInput');
        const filter = searchInput.value.toLowerCase();
        const unitTables = document.getElementsByClassName('unit-table');

        Array.from(unitTables).forEach(unitTable => {
            // Ambil nama unit dari h2
            const unitName = unitTable.querySelector('h2').textContent.toLowerCase();

            // Tampilkan/sembunyikan berdasarkan nama unit
            if (unitName.includes(filter)) {
                unitTable.style.display = '';
            } else {
                unitTable.style.display = 'none';
            }
        });
    }

    // Event listener untuk real-time search
    document.getElementById('searchInput').addEventListener('keyup', searchTables);
</script>

<script>
function saveData() {
    const data = [];
    const tables = document.querySelectorAll('.unit-table table');
    const tanggal = document.getElementById('filterDate').value;
    
    tables.forEach(table => {
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const machineId = row.querySelector('td:first-child').getAttribute('data-id');
            const status = row.querySelector('select').value;
            const keterangan = row.querySelector('input[type="text"]').value;
            
            if (status) {
                data.push({
                    machine_id: machineId,
                    tanggal: tanggal,
                    status: status,
                    keterangan: keterangan
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

    // Konfirmasi sebelum menyimpan
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin menyimpan data?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Tampilkan loading
            Swal.fire({
                title: 'Menyimpan Data',
                text: 'Mohon tunggu...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Kirim data ke server
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
                        timer: 1500
                    });
                    loadData();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal menyimpan data: ' + result.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat menyimpan data!'
                });
            });
        }
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
                    
                    select.value = 'Operasi';
                    select.style.backgroundColor = select.options[select.selectedIndex].style.backgroundColor;
                    input.value = '';
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
    
    fetch(`{{ route("admin.pembangkit.get-status") }}?tanggal=${tanggal}`)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                updateFormWithData(result.data);
            } else {
                resetForm();
                if (result.message) {
                    alert(result.message);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengambil data!');
        });
}

// Fungsi untuk mengupdate form dengan data
function updateFormWithData(data) {
    const tables = document.querySelectorAll('.unit-table table');
    
    // Reset form dulu
    resetForm();
    
    tables.forEach(table => {
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const machineId = row.querySelector('td:first-child').getAttribute('data-id');
            const machineData = data.find(d => d.machine_id == machineId);
            
            if (machineData) {
                const select = row.querySelector('select');
                const input = row.querySelector('input[type="text"]');
                
                select.value = machineData.status;
                select.style.backgroundColor = select.options[select.selectedIndex].style.backgroundColor;
                input.value = machineData.keterangan || '';
            }
        });
    });
}

// Event listener untuk tanggal
document.getElementById('filterDate').addEventListener('change', loadData);

// Load data saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    loadData();
});
</script>
@push('scripts')

@endpush
