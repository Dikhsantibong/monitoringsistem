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
                <div class="flex flex-col sm:flex-row justify-between gap-3 mb-4">
                    <!-- Search Bar -->
                    <div class="flex w-full sm:w-auto order-2 sm:order-1">
                        <input type="text" 
                            id="searchInput" 
                            placeholder="Cari nama mesin atau unit..." 
                            class="w-full px-3 py-2 text-sm border rounded-l-lg focus:outline-none focus:border-blue-500">
                        <button onclick="searchMachines()" 
                            class="bg-blue-500 px-3 py-2 rounded-r-lg text-white hover:bg-blue-800 transition-colors">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-2 w-full sm:w-auto order-1 sm:order-2">
                        <a href="{{ route('admin.machine-monitor.create') }}" 
                            class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-3 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all text-sm">
                            <i class="fas fa-plus"></i>
                            <span class="hidden sm:inline">Tambah Mesin</span>
                        </a>
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
                        <thead class="bg-[#0A749B]" style="height: 50px;">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Nama Mesin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">No. Seri</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Unit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">DMN</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">DMP</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Beban (MW)</th>
                                @if(session('unit') === 'mysql')
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        
                        <!-- Loader in tbody -->
                        <tbody id="tableLoader">
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="flex justify-center items-center">
                                        <div class="loader-circle"></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>

                        <!-- Data tbody -->
                        <tbody id="machineTable" style="display: none;" class="text-sm">
                            @forelse($machines as $index => $machine)
                            <tr class="hover:bg-gray-50 transition-colors border border-gray-200 border-l-0 border-r-0">
                                <td class="px-4 py-1 border-r border-gray-200 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ ($machines->currentPage() - 1) * $machines->perPage() + $index + 1 }}
                                    </span>
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
                                    <a href="{{ route('admin.power-plants.index') }}?search={{ $machine->powerPlant->name ?? '' }}" 
                                       class="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                        {{ $machine->powerPlant->name ?? 'N/A' }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap border-r border-gray-200">
                                    {{ $machine->operations->first()->dmn ?? '0' }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap border-r border-gray-200">
                                    {{ $machine->operations->first()->dmp ?? '0' }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap border-r border-gray-200">
                                    {{ $machine->operations->first()->load_value ?? '0' }} MW
                                </td>
                                @if(session('unit') === 'mysql')
                                <td class="py-2 whitespace-nowrap flex justify-center gap-2">
                                    <div>
                                        <a href="{{ route('admin.machine-monitor.edit', $machine->id) }}" 
                                           class="text-white btn bg-indigo-500 hover:bg-indigo-600 rounded-lg border px-4 py-2">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                    <div>
                                        <form id="delete-form-{{ $machine->id }}" 
                                              action="{{ route('admin.machine-monitor.destroy', $machine->id) }}" 
                                              method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                    onclick="confirmDelete({{ $machine->id }}, '{{ $machine->name }}')" 
                                                    class="text-white btn bg-red-500 hover:bg-red-600 rounded-lg border px-4 py-2">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada data mesin yang tersedia
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Section -->
                <div class="mt-4 flex justify-between items-center">
                    <div class="text-sm text-gray-700">
                        Menampilkan 
                        {{ ($machines->currentPage() - 1) * $machines->perPage() + 1 }} 
                        hingga 
                        {{ min($machines->currentPage() * $machines->perPage(), $machines->total()) }} 
                        dari 
                        {{ $machines->total() }} 
                        data
                    </div>
                    <div>
                        <ul class="pagination">
                            @if (!$machines->onFirstPage())
                                <li class="page-item">
                                    <a href="{{ $machines->previousPageUrl() }}" class="page-link">Sebelumnya</a>
                                </li>
                            @endif

                            @foreach ($machines->getUrlRange(1, $machines->lastPage()) as $page => $url)
                                <li class="page-item {{ $page == $machines->currentPage() ? 'active' : '' }}">
                                    <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                                </li>
                            @endforeach

                            @if ($machines->hasMorePages())
                                <li class="page-item">
                                    <a href="{{ $machines->nextPageUrl() }}" class="page-link">Selanjutnya</a>
                                </li>
                            @endif
                        </ul>
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

/* .overflow-x-auto::before {
    left: 0;
    background: linear-gradient(to right, rgba(255,255,255,0.9), rgba(255,255,255,0));
} */

/* .overflow-x-auto::after {
    right: 0;
    background: linear-gradient(to left, rgba(255,255,255,0.9), rgba(255,255,255,0));
} */

.pagination {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    gap: 5px;
}

.page-item {
    margin: 0;

}

.page-link {
    display: block;
    padding: 0.5rem 1rem;
    color: #0A749B;
    background-color: #fff;
    border: 1px solid #0A749B;
    border-radius: 0.25rem;
    text-decoration: none;
}

.page-item.active .page-link {
    background-color: #0A749B;
    color: #fff;
    border-color: #0A749B;
}

.page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}

.page-link:hover {
    background-color: #0A749B;
    color: #fff;
    text-decoration: none;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
}

/* Loader styles */
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .5;
    }
}

/* Circle Loader styles */
.loader-circle {
    width: 30px;
    height: 30px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #0A749B;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
function refreshTable() {
    const button = event.currentTarget;
    const icon = button.querySelector('i');
    
    // Add spinning animation
    icon.classList.add('animate-spin');
    button.disabled = true;
    
    // Tampilkan loader dan sembunyikan data
    document.getElementById('tableLoader').style.display = 'table-row-group';
    document.getElementById('machineTable').style.display = 'none';
    
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

function searchMachines() {
    const searchTerm = document.getElementById('searchInput').value;
    const url = new URL(window.location.href);
    
    // Reset halaman ke 1 saat melakukan pencarian
    url.searchParams.delete('page');
    url.searchParams.set('search', searchTerm);

    // Tampilkan loader
    document.getElementById('tableLoader').style.display = 'table-row-group';
    document.getElementById('machineTable').style.display = 'none';

    fetch(url)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Update tbody dengan hasil pencarian
            const newTbody = doc.querySelector('#machineTable');
            if (newTbody) {
                document.getElementById('machineTable').innerHTML = newTbody.innerHTML;
                document.getElementById('machineTable').style.display = 'table-row-group';
            }

            // Update pagination
            const newPagination = doc.querySelector('.mt-4.flex.justify-between.items-center');
            const currentPagination = document.querySelector('.mt-4.flex.justify-between.items-center');
            if (newPagination && currentPagination) {
                currentPagination.innerHTML = newPagination.innerHTML;
            }

            // Sembunyikan loader
            document.getElementById('tableLoader').style.display = 'none';

            // Update URL tanpa reload
            window.history.pushState({}, '', url);
        })
        .catch(error => {
            console.error('Error:', error);
            // Sembunyikan loader jika terjadi error
            document.getElementById('tableLoader').style.display = 'none';
            document.getElementById('machineTable').style.display = 'table-row-group';
        });
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    
    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => func(), wait);
        };
    }

    // Event listener untuk input pencarian
    if (searchInput) {
        // Pencarian otomatis setelah mengetik (dengan debounce)
        searchInput.addEventListener('input', debounce(searchMachines, 500));
        
        // Pencarian saat menekan Enter
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchMachines();
            }
        });
    }
});

function updateDisplayingText() {
    const visibleRows = document.querySelectorAll('tbody tr:not([style*="display: none"])').length;
    const totalRows = document.querySelectorAll('tbody tr').length;
    
    const displayText = document.querySelector('.text-sm.text-gray-700');
    if (displayText) {
        displayText.textContent = `Menampilkan ${visibleRows} dari ${totalRows} data`;
    }
}

// Tambahkan event listener untuk pencarian real-time
document.getElementById('searchInput').addEventListener('keyup', searchMachines);

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

    // Simulasi loading
    setTimeout(function() {
        document.getElementById('tableLoader').style.display = 'none';
        document.getElementById('machineTable').style.display = 'table-row-group';
    }, 1000);
});

// Fungsi untuk konfirmasi delete dengan SweetAlert2 dan verifikasi password
function confirmDelete(machineId, machineName) {
    Swal.fire({
        title: 'Verifikasi Password',
        html: `
            <p class="mb-3">Mesin "${machineName}" akan dihapus secara permanen!</p>
            <input type="password" id="password" class="swal2-input" placeholder="Masukkan password Anda">
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const password = document.getElementById('password').value;
            if (!password) {
                Swal.showValidationMessage('Password harus diisi');
                return false;
            }
            return password;
        }
    }).then((result) => {
        if (result.isConfirmed) {   
            const password = result.value;
            const form = document.getElementById(`delete-form-${machineId}`);
            
            // Tambahkan input password ke form
            const passwordInput = document.createElement('input');
            passwordInput.type = 'hidden';
            passwordInput.name = 'password';
            passwordInput.value = password;
            form.appendChild(passwordInput);
            
            form.submit();
        }
    });
}

// Handle flash messages
document.addEventListener('DOMContentLoaded', function() {
    // Success message
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 1500
        });
    @endif

    // Error message
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: "{{ session('error') }}"
        });
    @endif
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
@endsection
