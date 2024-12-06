@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#0A749B] shadow-md">
        <div class="p-4">
            <img src="{{ asset('logo/navlogo.png') }}" alt="Logo Aplikasi Rapat Harian" class="w-40 h-15">
        </div>
        <nav class="mt-4">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-home mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.score-card.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.score-card.*') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-clipboard-list mr-3"></i>
                <span>Score Card Daily</span>
            </a>
            <a href="{{ route('admin.daftar_hadir.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.daftar_hadir.index') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-list mr-3"></i>
                <span>Daftar Hadir</span>
            </a>
            <a href="{{ route('admin.pembangkit.ready') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.pembangkit.ready') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-check mr-3"></i>
                <span>Kesiapan Pembangkit</span>
            </a>
            <a href="{{ route('admin.laporan.sr_wo') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.laporan.sr_wo') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-file-alt mr-3"></i>
                <span>Laporan SR/WO</span>
            </a>
            <a href="{{ route('admin.machine-monitor') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.machine-monitor') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-cogs mr-3"></i>
                <span>Monitor Mesin</span>
            </a>
            <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.users') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-users mr-3"></i>
                <span>Manajemen Pengguna</span>
            </a>
            <a href="{{ route('admin.meetings') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.meetings') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-chart-bar mr-3"></i>
                <span>Laporan Rapat</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.settings') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-cog mr-3"></i>
                <span>Pengaturan</span>
            </a>
        </nav>
    </aside>
    

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <header class="bg-white shadow-sm">
            <div class="flex justify-between items-center px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">Kesiapan Pembangkit</h1>
            </div>
        </header>

        <main class="p-6">
            <!-- Konten Kesiapan Pembangkit -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kesiapan Pembangkit</h2>
                <div class="mb-4 flex justify-end space-x-4">
                    <div class="flex space-x-4">
                        <span class="text-gray-600 self-center">Tanggal: {{ \Carbon\Carbon::now()->format('d M Y') }}</span>
                        <button class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                            <i class="fas fa-redo mr-2"></i>Reset
                        </button>
                    </div>  
                    <div class="flex gap-3">
                        <div class="flex">
                            <input type="text" id="searchInput" placeholder="Cari mesin..." class="w-full px-4 py-2 border rounded-l-lg focus:outline-none focus:border-blue-500" onkeyup="searchTables()">
                            <button class="bg-blue-500 px-4 py-2 rounded-tr-lg rounded-br-lg text-white font-semibold hover:bg-blue-800 transition-colors"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>

                <!-- Search Bar -->
                
                    
                

                @foreach($units as $unit)
                <div class="bg-white rounded-lg shadow p-6 mb-4 unit-table">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ $unit->name }}</h2>
                    
                    <!-- Tabel Status Pembangkit -->
                    <table class="min-w-full divide-y divide-gray-200 border-collapse border border-gray-200">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 font-medium text-gray-500">Mesin</th>
                                <th class="py-2 px-4 font-medium text-gray-500">DMN</th>
                                <th class="py-2 px-4 font-medium text-gray-500">DMP</th>
                                <th class="py-2 px-4 font-medium text-gray-500">Beban</th>
                                <th class="py-2 px-4 font-medium text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($unit->machines as $machine)
                            <tr class="odd:bg-white even:bg-gray-100 searchable-row">
                                <td class="py-2 px-4 border-b">{{ $machine->name }}</td>
                                <td class="py-2 px-4 border-b">{{ $operations->where('machine_id', $machine->id)->first()->dmn ?? 'N/A' }}</td>
                                <td class="py-2 px-4 border-b">{{ $operations->where('machine_id', $machine->id)->first()->dmp ?? 'N/A' }}</td>
                                <td class="py-2 px-4 border-b">{{ $operations->where('machine_id', $machine->id)->first()->load_value ?? 'N/A' }}</td>
                                <td class="py-2 px-4 border-b">{{ $operations->where('machine_id', $machine->id)->first()->status ?? 'N/A' }}</td>
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
@push('scripts')
    
@endpush