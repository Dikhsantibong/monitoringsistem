@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <!-- Sidebar -->
    <aside id="mobile-menu"
        class="fixed z-20 transform overflow-hidden transition-transform duration-300 md:relative md:translate-x-0 h-screen w-64 bg-[#0A749B] shadow-md text-white hidden md:block md:shadow-lg">
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
                    <h1 class="text-xl font-semibold text-gray-800">Detail Monitor Mesin</h1>
                </div>

                @include('components.timer')
                <div class="relative">
                    <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                        <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                            class="w-7 h-7 rounded-full mr-2">
                        <span class="text-gray-700 text-sm">{{ Auth::user()->name }}</span>
                        <i class="fas fa-caret-down ml-2 text-gray-600"></i>
                    </button>
                    <div id=    "dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                        <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>      
            </div>
        </header>

        <!-- Menaikkan posisi konten detail mesin -->
        <div class="mt-3">
            <x-admin-breadcrumb :breadcrumbs="[
                        ['name' => 'Monitor Mesin', 'url' => route('admin.machine-monitor')],
                        ['name' => 'Detail Mesin', 'url' => null]
                    ]" />
        </div>

        <!-- Content -->    
        <div class="container mx-auto px-6 py-8">
            <!-- Header & Breadcrumb -->
            <div class="mb-2">
                <div class="flex justify-between items-center mb-2">
                 
                  
                </div>
                
            </div>

                {{-- <!-- Summary Cards -->
                <div class="grid grid-cols-3 gap-4 mb-6 mt-2">
                    <!-- Total Mesin Card -->
                    <div class="bg-[#4285F4] rounded-lg p-4 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-xs mb-1">Total Mesin</p>
                                <p class="text-2xl font-bold text-white">{{ count($machines) }}</p>
                            </div>
                            <div class="bg-white/20 p-3 rounded-lg">
                                <i class="fas fa-cogs text-xl text-white"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Mesin Aktif Card -->
                    <div class="bg-[#34A853] rounded-lg p-4 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-xs mb-1">Mesin Aktif</p>
                                <p class="text-2xl font-bold text-white">{{ $machines->where('status', 'START')->count() }}</p>
                            </div>
                            <div class="bg-white/20 p-3 rounded-lg">
                                <i class="fas fa-play text-xl text-white"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Mesin Nonaktif Card -->
                    <div class="bg-[#EA4335] rounded-lg p-4 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-xs mb-1">Mesin Nonaktif</p>
                                <p class="text-2xl font-bold text-white">{{ $machines->where('status', 'STOP')->count() }}</p>
                            </div>
                            <div class="bg-white/20 p-3 rounded-lg">
                                <i class="fas fa-stop text-xl text-white"></i>
                            </div>
                        </div>
                    </div>
                </div> --}}

            <!-- Machine List Table -->
            <div class="bg-white rounded-lg shadow p-6 sm:p-3">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Daftar Status Mesin</h3>
                    <span class="text-sm text-gray-500">
                        Last Updated: <span class="font-medium">{{ now()->format('H:i:s') }}</span>
                    </span>
                </div>
                
                <div>
                    <div class="flex items-center gap-3 mb-4 justify-end">
                        <div class="flex">
                            <input type="text" id="searchInput" placeholder="Cari..."
                                class="w-full px-4 py-2 border rounded-l-lg focus:outline-none focus:border-blue-500" onkeyup="searchMachines()">
                            <button onclick="searchMachines()"
                                class="bg-blue-500 px-4 py-2 rounded-tr-lg rounded-br-lg text-white font-semibold hover:bg-blue-800 transition-colors">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <button onclick="refreshTable()" 
                            class="flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-all">
                            <i class="fas fa-sync-alt"></i>
                            <span>Refresh</span>
                        </button>
                        <a href="{{ route('admin.machine-monitor') }}" 
                            class="flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all">
                            <i class="fas fa-arrow-left"></i>
                            <span>Kembali</span>
                        </a>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200 border-collapse border border-gray-200">
                        <thead class="bg-blue-500">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider bg-blue-500">ID Mesin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider bg-blue-500">Nama Mesin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider bg-blue-500">Tanggal Pembaruan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider bg-blue-500">Asal Unit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider bg-blue-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="machineTable" class="bg-white divide-y divide-gray-200 border border-gray-200 border-l-0 border-r-0">
                            @forelse($machines as $machine)
                            <tr class="hover:bg-gray-50 transition-colors border border-gray-200 border-l-0 border-r-0">
                                <td class="px-4 py-3 border-r border-gray-200 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">#{{ str_pad($machine->id, 3, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="px-4 py-3 border-r border-gray-200 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 bg-gray-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-cog text-gray-500"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $machine->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 border-r border-gray-200 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($machine->updated_at)->format('Y-m-d H:i:s') }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 border-r border-gray-200 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">{{ $machine->powerPlant->name ?? 'N/A' }}</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex space-x-2">
                                        <a href="javascript:void(0);" onclick="openPopup({{ $machine->id }})" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.machine-monitor.destroy', $machine->id) }}" method="POST" onsubmit="return showDeleteConfirmation(event, '{{ $machine->name }}', this);">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada data mesin yang tersedia
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="mt-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-700">
                                Menampilkan {{ $machines->firstItem() ?? 0 }} - {{ $machines->lastItem() ?? 0 }} dari {{ $machines->total() }} data
                            </span>
                        </div>
                        <div class="flex items-center gap-1">
                            @if($machines->previousPageUrl())
                            <a href="{{ $machines->previousPageUrl() }}" 
                                class="px-4 py-2 bg-[#FF6B6B] text-white rounded-md text-sm font-medium hover:bg-[#FF5252] transition-colors">
                                Previous
                            </a>
                            @endif

                            @for($i = 1; $i <= $machines->lastPage(); $i++)
                                @if($i == $machines->currentPage())
                                    <span class="px-4 py-2 bg-[#4285F4] text-white rounded-md text-sm font-medium">
                                        {{ $i }}
                                    </span>
                                @else
                                    <a href="{{ $machines->url($i) }}" 
                                        class="px-4 py-2 bg-[#4285F4] bg-opacity-10 text-[#4285F4] rounded-md text-sm font-medium hover:bg-opacity-20 transition-colors">
                                        {{ $i }}
                                    </a>
                                @endif

                                @if($i < $machines->lastPage())
                                    @if($i == 3 && $machines->lastPage() > 6)
                                        <span class="px-2 text-gray-500">...</span>
                                        @break
                                    @endif
                                @endif
                            @endfor

                            @if($machines->lastPage() > 6)
                                <span class="px-4 py-2 bg-[#4285F4] bg-opacity-10 text-[#4285F4] rounded-md text-sm font-medium">
                                    {{ $machines->lastPage() }}
                                </span>
                            @endif

                            @if($machines->nextPageUrl())
                            <a href="{{ $machines->nextPageUrl() }}" 
                                class="px-4 py-2 bg-[#FF6B6B] text-white rounded-md text-sm font-medium hover:bg-[#FF5252] transition-colors">
                                Next
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom scrollbar */
.overflow-x-auto::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #ddd;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #ccc;
}

/* Hover animations */
.hover\:shadow-md {
    transition: all 0.3s ease;
}

.transition-all {
    transition: all 0.2s ease;
}
</style>

<script>
function refreshTable() {
    const button = event.currentTarget;
    const icon = button.querySelector('i');
    
    // Add spinning animation
    icon.classList.add('animate-spin');
    button.disabled = true;
    
    // Reload after slight delay
    setTimeout(() => {
        location.reload();
    }, 500);
}

function openPopup(machineId) {
    const editUrl = `{{ url('admin/machine-monitor') }}/${machineId}/edit`;
    fetch(editUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(html => {
            document.querySelector('#editModal .modal-body').innerHTML = html;
            $('#editModal').modal('show'); // Menampilkan modal
        })
        .catch(error => console.error('Error fetching edit form:', error));
}

function closePopup() {
    document.getElementById('editPopup').style.display = 'none';
}

// Tambahkan event listener pada ikon edit
document.querySelectorAll('.edit-icon').forEach(icon => {
    icon.addEventListener('click', openPopup);
});

function showDeleteConfirmation(event, machineName, form) {
    event.preventDefault(); // Mencegah pengiriman form secara langsung
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Anda akan menghapus mesin: " + machineName,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit(); // Mengirim form jika pengguna mengkonfirmasi
        }
    });
}

function searchMachines() {
    const input = document.getElementById('search').value.toLowerCase();
    const rows = document.querySelectorAll('#machineTable tr');

    rows.forEach(row => {
        const machineName = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase();
        if (machineName && machineName.includes(input)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function redirectToShowPage() {
    window.location.href = "{{ route('admin.machine-monitor') }}"; // Ganti dengan rute yang sesuai
}
</script>

<!-- Modal untuk Edit -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Data Mesin</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="redirectToShowPage()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Konten edit akan dimuat di sini -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="redirectToShowPage()">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Tombol untuk menguji modal -->


@push('scripts')
@endpush
@endsection
