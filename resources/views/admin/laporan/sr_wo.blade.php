@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-gray-50 overflow-auto">
        <!-- Sidebar -->
        <aside class="w-64 bg-[#0A749B] shadow-md">
            <div class="p-4">
                <img src="{{ asset('logo/navlogo.png') }}" alt="Logo Aplikasi Rapat Harian" class="w-40 h-15">
            </div>
            <nav class="mt-4">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center px-4 py-3 text-white {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                    <i class="fas fa-home mr-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.score-card.index') }}"
                    class="flex items-center px-4 py-3 text-white {{ request()->routeIs('admin.score-card.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                    <i class="fas fa-clipboard-list mr-3"></i>
                    <span>Score Card Daily</span>
                </a>
                <a href="{{ route('admin.daftar_hadir.index') }}"
                    class="flex items-center px-4 py-3 text-white {{ request()->routeIs('admin.daftar_hadir.index') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                    <i class="fas fa-list mr-3"></i>
                    <span>Daftar Hadir</span>
                </a>
                <a href="{{ route('admin.pembangkit.ready') }}"
                    class="flex items-center px-4 py-3 text-white {{ request()->routeIs('admin.pembangkit.ready') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                    <i class="fas fa-check mr-3"></i>
                    <span>Kesiapan Pembangkit</span>
                </a>
                <a href="{{ route('admin.laporan.sr_wo') }}"
                    class="flex items-center px-4 py-3 text-black {{ request()->routeIs('admin.laporan.sr_wo') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50 hover:text-black' }}">
                    <i class="fas fa-file-alt mr-3"></i>
                    <span>Laporan SR/WO</span>
                </a>
                <a href="{{ route('admin.machine-monitor') }}"
                    class="flex items-center px-4 py-3 text-white {{ request()->routeIs('admin.machine-monitor') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                    <i class="fas fa-cogs mr-3"></i>
                    <span>Monitor Mesin</span>
                </a>
                <a href="{{ route('admin.users') }}"
                    class="flex items-center px-4 py-3 text-white {{ request()->routeIs('admin.users') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                    <i class="fas fa-users mr-3"></i>
                    <span>Manajemen Pengguna</span>
                </a>
                <a href="{{ route('admin.meetings') }}"
                    class="flex items-center px-4 py-3 text-white {{ request()->routeIs('admin.meetings') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                    <i class="fas fa-chart-bar mr-3"></i>
                    <span>Laporan Rapat</span>
                </a>
                <a href="{{ route('admin.settings') }}"
                    class="flex items-center px-4 py-3 text-white {{ request()->routeIs('admin.settings') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                    <i class="fas fa-cog mr-3"></i>
                    <span>Pengaturan</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <header class="bg-white shadow-sm">
                <div class="flex justify-between items-center px-6 py-4">
                    <h1 class="text-2xl font-semibold text-gray-800">Laporan SR/WO</h1>
                </div>
            </header>

            <main class="p-6">
                <!-- Konten Laporan SR/WO -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Detail Laporan</h2>
                    <div class="p-4">
                    <div class="mb-4 flex justify-end space-x-4">
                        <!-- Filter Tanggal -->
                        <div class="flex items-center space-x-2">
                            <label class="text-gray-600">Dari:</label>
                            <input type="date" 
                                   id="startDate" 
                                   class="px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                            
                            <label class="text-gray-600">Sampai:</label>
                            <input type="date" 
                                   id="endDate" 
                                   class="px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        </div>

                        <!-- Search Input -->
                        <div class="flex">
                            <input type="text" 
                                   id="searchInput" 
                                   placeholder="Cari..." 
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
                            <button onclick="openSRModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                                <i class="fas fa-plus mr-2"></i>Tambah SR
                            </button>
                        </div>
                        <table id="srTable" class="min-w-full divide-y divide-gray-200 border-collapse border border-gray-200">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b">ID SR</th>
                                    <th class="py-2 px-4 border-b">Deskripsi</th>
                                    <th class="py-2 px-4 border-b">Status</th>
                                    <th class="py-2 px-4 border-b">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($serviceRequests as $sr)
                                    <tr class="odd:bg-white even:bg-gray-100">
                                        <td class="py-2 px-4 border-b">{{ $sr->id }}</td>
                                        <td class="py-2 px-4 border-b">{{ $sr->description }}</td>
                                        <td class="py-2 px-4 border-b">{{ $sr->status }}</td>
                                        <td class="py-2 px-4 border-b">{{ $sr->created_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Card WO -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-md font-semibold">Daftar Work Order (WO)</h3>
                            <button onclick="openWOModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                                <i class="fas fa-plus mr-2"></i>Tambah WO
                            </button>
                        </div>
                        <table id="woTable" class="min-w-full bg-white border border-gray-300">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b">ID WO</th>
                                    <th class="py-2 px-4 border-b">Deskripsi</th>
                                    <th class="py-2 px-4 border-b">Status</th>
                                    <th class="py-2 px-4 border-b">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($workOrders as $wo)
                                    <tr>
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
            </main>
        </div>
    </div>

    <!-- Modal SR -->
    <div id="srModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center transform transition-all duration-300 scale-0">
        <div class="bg-white p-8 rounded-lg w-1/2 transform transition-all duration-300 scale-0">
            <h2 class="text-xl font-bold mb-4">Tambah Service Request (SR)</h2>
            <form id="srForm" action="{{ route('admin.laporan.store-sr') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="priority">
                        Prioritas
                    </label>
                    <select name="priority" id="priority" class="w-full px-3 py-2 border rounded-lg">
                        <option value="high">Tinggi</option>
                        <option value="medium">Sedang</option>
                        <option value="low">Rendah</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                        Status
                    </label>
                    <select name="status" id="status" class="w-full px-3 py-2 border rounded-lg">
                        <option value="open">Terbuka</option>
                        <option value="in_progress">Dalam Proses</option>
                        <option value="completed">Selesai</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                        Deskripsi
                    </label>
                    <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 border rounded-lg"></textarea>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeSRModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
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
    <div id="woModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center transform transition-all duration-300 scale-0">
        <div class="bg-white p-8 rounded-lg w-1/2 transform transition-all duration-300 scale-0">
            <h2 class="text-xl font-bold mb-4">Tambah Work Order (WO)</h2>
            <form id="woForm" action="{{ route('admin.laporan.store-wo') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="work_type">
                        Tipe Pekerjaan
                    </label>
                    <select name="work_type" id="work_type" class="w-full px-3 py-2 border rounded-lg">
                        <option value="maintenance">Pemeliharaan</option>
                        <option value="repair">Perbaikan</option>
                        <option value="installation">Instalasi</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                        Status
                    </label>
                    <select name="status" id="status" class="w-full px-3 py-2 border rounded-lg">
                        <option value="pending">Menunggu</option>
                        <option value="in_progress">Dalam Proses</option>
                        <option value="completed">Selesai</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                        Deskripsi
                    </label>
                    <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 border rounded-lg"></textarea>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeWOModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
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

// Fungsi untuk submit form SR
function submitSR() {
    const form = document.getElementById('srForm');
    form.submit();
    closeSRModal();
}

// Fungsi untuk submit form WO 
function submitWO() {
    const form = document.getElementById('woForm');
    form.submit();
    closeWOModal();
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
