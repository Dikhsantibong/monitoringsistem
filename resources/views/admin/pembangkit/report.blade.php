@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <!-- Sidebar -->
   @include('components.sidebar')

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <header class="bg-white shadow-sm sticky top-0 z-10">
            <div class="flex justify-between items-center px-6 py-3">
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
                <h1 class="text-xl font-semibold text-gray-800">Laporan Kesiapan Pembangkit</h1>
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

        <div class="p-6">
            <!-- Filter dan Tombol -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <input type="date" 
                           id="filterDate" 
                           value="{{ request('date', date('Y-m-d')) }}"
                           class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-4 w-full md:w-auto">
                    <button onclick="window.location.href='{{ route('admin.pembangkit.report.download') }}?date=' + document.getElementById('filterDate').value"
                            class="w-full md:w-auto bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors flex items-center justify-center">
                        <i class="fas fa-download mr-2"></i>Download PDF
                    </button>
                    <button onclick="window.open('{{ route('admin.pembangkit.report.print') }}?date=' + document.getElementById('filterDate').value, '_blank')"
                            class="w-full md:w-auto bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition-colors flex items-center justify-center">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                </div>
            </div>

            <!-- Tabel -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Unit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Mesin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Beban</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">DMN</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">DMP</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Kronologi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Deskripsi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Action Plan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Progres</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Target Selesai</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $index => $log)
                                <tr class="hover:bg-gray-50 border border-gray-200">
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->machine->powerPlant->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->machine->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $log->status === 'Operasi' ? 'bg-green-100 text-green-800' : 
                                               ($log->status === 'Gangguan' ? 'bg-red-100 text-red-800' : 
                                               'bg-yellow-100 text-yellow-800') }}">
                                            {{ $log->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->load_value }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->dmn }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->dmp }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->kronologi }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->deskripsi }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->action_plan }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->progres }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->target_selesai ? $log->target_selesai->format('d/m/Y') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-6 py-4 text-center text-gray-500 border border-gray-200">
                                        Tidak ada data untuk ditampilkan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($logs->hasPages())
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $logs->links() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Menampilkan
                            <span class="font-medium">{{ $logs->firstItem() ?? 0 }}</span>
                            sampai
                            <span class="font-medium">{{ $logs->lastItem() ?? 0 }}</span>
                            dari
                            <span class="font-medium">{{ $logs->total() }}</span>
                            hasil
                        </p>
                    </div>
                    <div>
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>          

<script>
document.getElementById('filterDate').addEventListener('change', function() {
    const selectedDate = this.value;
    
    // Tambahkan loading indicator (opsional)
    const tableBody = document.querySelector('tbody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="10" class="px-6 py-4 text-center">
                <i class="fas fa-spinner fa-spin"></i> Loading...
            </td>
        </tr>
    `;
    
    // Lakukan fetch request
    fetch(`{{ route('admin.pembangkit.report') }}?date=${selectedDate}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update seluruh tabel dengan data baru
            document.querySelector('.overflow-x-auto').innerHTML = data.html;
        } else {
            // Tampilkan pesan error jika ada
            tableBody.innerHTML = `
                <tr></tr>
                    <td colspan="10" class="px-6 py-4 text-center text-red-500">
                        ${data.message || 'Terjadi kesalahan saat memuat data'}
                    </td>
                </tr>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        tableBody.innerHTML = `
            <tr>
                <td colspan="10" class="px-6 py-4 text-center text-red-500">
                    Terjadi kesalahan saat memuat data
                </td>
            </tr>
        `;
    });
});

function toggleDropdown() {
    const dropdown = document.getElementById('dropdown');
    dropdown.classList.toggle('hidden');
}

// Tutup dropdown ketika mengklik di luar
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('dropdown');
    const button = event.target.closest('button');

    if (!button && !dropdown.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});
</script>

@push('scripts')
@endpush
@endsection