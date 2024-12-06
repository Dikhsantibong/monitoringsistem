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
                <h1 class="text-2xl font-semibold text-gray-800">Daftar Hadir</h1>
            </div>
        </header>

        <main class="p-6">
            <div class="bg-white rounded-lg shadow p-6 mb-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Daftar Kehadiran</h2>
                
                <!-- Input Pencarian -->
                <div class="mb-4 flex justify-end space-x-4">
                    <!-- Filter Tanggal -->
                    <div class="flex items-center space-x-2">
                        <label class="text-gray-600">Tanggal:</label>
                        <input type="date" 
                               id="date-filter"
                               value="{{ date('Y-m-d') }}" 
                               onchange="filterByDate(this.value)"
                               class="px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Search Input -->
                    <div class="flex">
                        <input type="text" 
                               id="search" 
                               placeholder="Cari..." 
                               class="w-full px-4 py-2 border rounded-l-lg focus:outline-none focus:border-blue-500">
                        <button class="bg-blue-500 px-4 py-2 rounded-tr-lg rounded-br-lg text-white font-semibold hover:bg-blue-800 transition-colors">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table id="attendance-table" class="min-w-full bg-white border border-gray-300 rounded-lg">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">
                                    Nama
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">
                                    Waktu Kehadiran
                                </th>
                            </tr>
                        </thead>
                        <tbody id="attendance-body" class="divide-y divide-gray-300">
                            @foreach($attendances as $attendance)
                            <tr class="hover:bg-gray-100">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    {{ $attendance->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    {{ \Carbon\Carbon::parse($attendance->time)->format('d M Y H:i:s') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>        
    </div>
</div>

<script>
    // Fungsi untuk pencarian
    document.getElementById('search').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#attendance-body tr');

        rows.forEach(row => {
            const name = row.querySelector('td').textContent.toLowerCase();
            if (name.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    function filterByDate(date) {
        fetch(`{{ route('admin.daftar_hadir.index') }}?date=${date}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('attendance-body').innerHTML = html;
            });
    }
</script>
@push('scripts')
    
@endpush
@endsection 