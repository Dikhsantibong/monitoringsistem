    @extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <!-- Sidebar -->
   @include('components.sidebar')

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
        <div class="container mx-auto px-2 sm:px-6 py-4 sm:py-8">
            <!-- Header & Breadcrumb -->
            

            <!-- Machine List Table -->
            <div class="bg-white rounded-lg shadow p-3 sm:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">Daftar Status Mesin</h3>
                    <span class="text-xs sm:text-sm text-gray-500">
                        Last Updated: <span class="font-medium">{{ now()->format('H:i:s') }}</span>
                    </span>
                </div>
                
                <!-- Search and Buttons Section -->
                <div class="flex flex-col sm:flex-row justify-end gap-3 mb-4">
                    <!-- Search Bar -->
                    <div class="flex w-full sm:w-auto order-2 sm:order-1">
                        <input type="text" 
                            id="searchInput" 
                            placeholder="Cari..." 
                            class="w-full px-3 py-2 text-sm border rounded-l-lg focus:outline-none focus:border-blue-500" 
                            onkeyup="searchMachines()">
                        <button onclick="searchMachines()" 
                            class="bg-blue-500 px-3 py-2 rounded-r-lg text-white hover:bg-blue-800 transition-colors">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-2 w-full sm:w-auto order-1 sm:order-2">
                        <button onclick="refreshTable()" 
                            class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-all text-sm">
                            <i class="fas fa-sync-alt"></i>
                            <span class="hidden sm:inline">Refresh</span>
                        </button>
                        <a href="{{ route('admin.machine-monitor') }}" 
                            class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all text-sm">
                            <i class="fas fa-arrow-left"></i>
                            <span class="hidden sm:inline">Kembali</span>
                        </a>
                    </div>
                </div>

                <!-- Wrapper for Table with Shadow -->
                <div class="overflow-x-auto shadow-md rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 border-collapse border border-gray-200">
                        <thead class="bg-blue-500">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">ID Mesin</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Nama Mesin</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Tipe</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">No. Seri</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Unit</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($machines as $machine)
                            <tr class="hover:bg-gray-50 transition-colors border border-gray-200 border-l-0 border-r-0">
                                <td class="px-4 py-1 border-r border-gray-200 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">#{{ str_pad($machine->id, 3, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="px-4 py-1 border-r border-gray-200 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 bg-gray-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-cog text-gray-500"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $machine->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-1 border-r border-gray-200 whitespace-nowrap">
                                    <span class="text-sm text-gray-500">{{ $machine->type ?? 'N/A' }}</span>
                                </td>
                                <td class="px-4 py-1 border-r border-gray-200 whitespace-nowrap">
                                    <span class="text-sm text-gray-500">{{ $machine->serial_number ?? 'N/A' }}</span>
                                </td>
                                <td class="px-4 py-1 border-r border-gray-200 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($machine->updated_at)->format('Y-m-d H:i:s') }}
                                    </div>
                                </td>
                                <td class="px-4 py-1 border-r border-gray-200 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">{{ $machine->powerPlant->name ?? 'N/A' }}</span>
                                </td>
                                <td class="px-2 py-1 whitespace-nowrap">
                                    <div class="flex space-x-2">
                                        <a href="javascript:void(0);" onclick="openPopup({{ $machine->id }})" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg">
                                            <i class="fas fa-edit text-lg"></i>
                                        </a>
                                        <form action="{{ route('admin.machine-monitor.destroy', $machine->id) }}" method="POST" onsubmit="return showDeleteConfirmation(event, '{{ $machine->name }}', this);">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                                                <i class="fas fa-trash-alt text-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada data mesin yang tersedia
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Section -->
                <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm">
                    <div class="flex items-center gap-1 overflow-x-auto w-full sm:w-auto order-1 sm:order-1">
                        <span class="text-xs sm:text-sm text-gray-700">
                            Menampilkan {{ $machines->firstItem() ?? 0 }} - {{ $machines->lastItem() ?? 0 }} dari {{ $machines->total() }} data
                        </span>
                    </div>
                    <div class="flex items-center gap-1 overflow-x-auto w-full sm:w-auto order-2 sm:order-2 justify-end">
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

/* Responsive table styles */
.overflow-x-auto {
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
}

@media (max-width: 640px) {
    .container {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    table {
        display: block;
        width: 100%;
    }
    
    .pagination {
        flex-wrap: wrap;
        justify-content: center;
    }
}

/* Sidebar transition */
.sidebar {
    transition: transform 0.3s ease-in-out;
}

@media (max-width: 768px) {
    .sidebar {
        position: fixed;
        z-index: 50;
        top: 0;
        left: 0;
        height: 100vh;
        transform: translateX(-100%);
    }

    .sidebar.show {
        transform: translateX(0);
    }
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .container {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    /* Table adjustments */
    .overflow-x-auto {
        margin: 0 -1rem;
        padding: 0 1rem;
    }
    
    table {
        font-size: 0.875rem;
    }
    
    th, td {
        padding: 0.5rem 0.75rem;
        white-space: nowrap;
    }
    
    /* Button and input adjustments */
    input, button, .btn {
        font-size: 0.875rem;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }
    
    /* Card padding adjustments */
    .bg-white {
        padding: 1rem;
    }
}

/* Better touch targets for mobile */
@media (max-width: 768px) {
    button, 
    a[href], 
    input, 
    .btn {
        min-height: 2.5rem;
    }
    
    .pagination button,
    .pagination a {
        padding: 0.5rem 0.75rem;
    }
}

/* Improved table scrolling */
.overflow-x-auto {
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    position: relative;
}

/* Shadow indicators for table scroll */
.overflow-x-auto::after,
.overflow-x-auto::before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 15px;
    z-index: 2;
    pointer-events: none;
}

.overflow-x-auto::before {
    left: 0;
    background: linear-gradient(to right, rgba(255,255,255,0.9), rgba(255,255,255,0));
}

.overflow-x-auto::after {
    right: 0;
    background: linear-gradient(to left, rgba(255,255,255,0.9), rgba(255,255,255,0));
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

document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const desktopMenuToggle = document.getElementById('desktop-menu-toggle');
    const sidebar = document.querySelector('.sidebar'); // Pastikan sidebar memiliki class 'sidebar'
    const mainContent = document.getElementById('main-content');

    function toggleSidebar() {
        sidebar.classList.toggle('hidden');
        mainContent.classList.toggle('md:ml-64'); // Sesuaikan dengan lebar sidebar
    }

    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', toggleSidebar);
    }

    if (desktopMenuToggle) {
        desktopMenuToggle.addEventListener('click', toggleSidebar);
    }

    // Tutup sidebar secara otomatis pada layar mobile ketika mengklik di luar
    document.addEventListener('click', function(event) {
        const isMobile = window.innerWidth < 768;
        if (isMobile && !sidebar.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
            sidebar.classList.add('hidden');
        }
    });

    // Tambahkan event listener untuk resize window
    window.addEventListener('resize', function() {
        const isMobile = window.innerWidth < 768;
        if (!isMobile) {
            sidebar.classList.remove('hidden');
        }
    });
});
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
