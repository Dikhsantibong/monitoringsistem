@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.sidebar')

    <div id="main-content" class="flex-1 main-content">
        <header class="bg-white shadow-sm sticky top-0 z-10">
            <div class="flex justify-between items-center px-6 py-3">
                <h1 class="text-xl font-semibold text-gray-800">Data Unit Pembangkit</h1>
                @include('components.timer')
            </div>
        </header>

        <div class="p-6">
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6">
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Daftar Unit Pembangkit</h2>
                    
                    <!-- Search dan Tombol Tambah -->
                    <div class="mb-4 flex flex-row gap-3 justify-end">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.machine-monitor') }}" 
                               class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Kembali
                            </a>
                            <a href="{{ route('admin.power-plants.create') }}"
                                class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Tambah Unit
                            </a>
                        </div>
                        <div class="flex items-center">
                            <div class="flex w-full max-w-md">
                                <input type="text" 
                                       id="search" 
                                       placeholder="Cari unit..." 
                                       class="w-full px-4 py-2 border rounded-l-lg focus:outline-none focus:border-blue-500">
                                <button type="button" 
                                        id="searchButton"
                                        class="bg-blue-500 p-2 rounded-tr-lg rounded-br-lg text-white font-semibold hover:bg-blue-800 transition-colors">
                                    Search
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Table Content -->
                    <div class="bg-white rounded-lg shadow p-6 mb-4">
                        <div class="overflow-auto">
                            <table class="min-w-full divide-y divide-gray-200 border-collapse border border-gray-200">
                                <thead>
                                    <tr style="background-color: #0A749B; color: white">
                                        <th class="px-6 py-3 text-center text-sm font-medium uppercase">No</th>
                                        <th class="px-6 py-3 text-center text-sm font-medium uppercase">Nama Unit</th>
                                        <th class="px-6 py-3 text-center text-sm font-medium uppercase">Lokasi</th>
                                        <th class="px-6 py-3 text-center text-sm font-medium uppercase">Jumlah Mesin</th>
                                        <th class="px-6 py-3 text-center text-sm font-medium uppercase">Status</th>
                                        <th class="px-6 py-3 text-center text-sm font-medium uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($powerPlants as $index => $unit)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="text-center py-2 whitespace-nowrap border border-gray-300">
                                            {{ ($powerPlants->currentPage() - 1) * $powerPlants->perPage() + $loop->iteration }}
                                        </td>
                                        <td class="text-center p-2 whitespace-nowrap border border-gray-300">
                                            <div class="text-sm font-medium text-gray-900">{{ $unit->name }}</div>
                                        </td>
                                        <td class="text-center p-2 whitespace-nowrap border border-gray-300">
                                            <div class="text-sm text-gray-500">
                                                {{ number_format($unit->latitude, 6) }}, {{ number_format($unit->longitude, 6) }}
                                            </div>
                                        </td>
                                        <td class="text-center py-2 whitespace-nowrap border border-gray-300">
                                            <div class="text-sm text-gray-900">{{ $unit->machines->count() }} Mesin</div>
                                        </td>
                                        <td class="text-center py-2 whitespace-nowrap border border-gray-300">
                                            @php
                                                $activeIssues = $unit->machines->flatMap->statusLogs
                                                    ->where('status', 'Gangguan')
                                                    ->count();
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $activeIssues > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                {{ $activeIssues > 0 ? 'Ada Gangguan' : 'Normal' }}
                                            </span>
                                        </td>
                                        <td class="py-2 whitespace-nowrap flex justify-center gap-2">
                                            <!-- Tombol Lihat Data Mesin -->
                                            <a href="{{ route('admin.machine-monitor.show') }}?power_plant_id={{ $unit->id }}" 
                                               class="text-white btn bg-blue-500 hover:bg-blue-600 rounded-lg border p-2"
                                               title="Lihat Data Mesin">
                                                <i class="fas fa-cogs"></i>
                                            </a>

                                            <!-- Tombol Edit yang sudah ada -->
                                            <a href="{{ route('admin.power-plants.edit', $unit->id) }}"
                                               class="text-white btn bg-indigo-500 hover:bg-indigo-900 rounded-lg border p-2">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <!-- Form Delete yang sudah ada -->
                                            <form id="delete-form-{{ $unit->id }}"
                                                  action="{{ route('admin.power-plants.destroy', $unit->id) }}"
                                                  method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        onclick="confirmDelete({{ $unit->id }}, '{{ $unit->name }}')"
                                                        class="text-white btn bg-red-500 hover:bg-red-600 rounded-lg p-2">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            Tidak ada data unit yang tersedia
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <div class="mt-4 flex justify-between items-center">
                                <div class="text-sm text-gray-700">
                                    Menampilkan 
                                    {{ ($powerPlants->currentPage() - 1) * $powerPlants->perPage() + 1 }} 
                                    hingga 
                                    {{ min($powerPlants->currentPage() * $powerPlants->perPage(), $powerPlants->total()) }} 
                                    dari 
                                    {{ $powerPlants->total() }} 
                                    entri
                                </div>
                                <div class="flex items-center gap-1">
                                    @if (!$powerPlants->onFirstPage())
                                        <a href="{{ $powerPlants->previousPageUrl() }}" 
                                           class="px-3 py-1 bg-[#0A749B] text-white rounded">Sebelumnya</a>
                                    @endif

                                    @foreach ($powerPlants->getUrlRange(1, $powerPlants->lastPage()) as $page => $url)
                                        @if ($page == $powerPlants->currentPage())
                                            <span class="px-3 py-1 bg-[#0A749B] text-white rounded">{{ $page }}</span>
                                        @else
                                            <a href="{{ $url }}" 
                                               class="px-3 py-1 rounded {{ $page == $powerPlants->currentPage() 
                                                   ? 'bg-[#0A749B] text-white' 
                                                   : 'bg-white text-[#0A749B] border border-[#0A749B]' }}">
                                                {{ $page }}
                                            </a>
                                        @endif
                                    @endforeach

                                    @if ($powerPlants->hasMorePages())
                                        <a href="{{ $powerPlants->nextPageUrl() }}" 
                                           class="px-3 py-1 bg-[#0A749B] text-white rounded">Selanjutnya</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

<script>
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Verifikasi Password',
        html: `
            <p class="mb-3">Unit "${name}" akan dihapus secara permanen!</p>
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
            const form = document.getElementById(`delete-form-${id}`);
            
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

// Fungsi pencarian
function searchPowerPlants() {
    const searchTerm = document.getElementById('search').value;
    const url = new URL(window.location.href);
    url.searchParams.set('search', searchTerm);

    // Tampilkan loader jika diperlukan
    // document.getElementById('tableLoader').style.display = 'table-row-group';

    fetch(url)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Update tbody dengan hasil pencarian
            const newTbody = doc.querySelector('tbody');
            if (newTbody) {
                document.querySelector('tbody').innerHTML = newTbody.innerHTML;
            }

            // Update pagination jika ada
            const newPagination = doc.querySelector('.mt-4.flex.justify-between.items-center');
            const currentPagination = document.querySelector('.mt-4.flex.justify-between.items-center');
            if (newPagination && currentPagination) {
                currentPagination.innerHTML = newPagination.innerHTML;
            }

            // Update URL tanpa reload
            window.history.pushState({}, '', url);
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const searchButton = document.getElementById('searchButton');

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
        searchInput.addEventListener('input', debounce(searchPowerPlants, 500));
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchPowerPlants();
            }
        });
    }

    // Event listener untuk tombol search
    if (searchButton) {
        searchButton.addEventListener('click', searchPowerPlants);
    }
});
</script>

<style>
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
</style>
@endsection 