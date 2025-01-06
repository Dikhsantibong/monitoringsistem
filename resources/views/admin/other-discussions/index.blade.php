@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <x-sidebar />

    <div id="main-content" class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-20">
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
                    <h1 class="text-xl font-semibold text-gray-800">Pembahasan Lain-lain</h1>
                </div>

                @include('components.timer')
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

        <!-- Breadcrumbs -->
        <div class="mt-3">
            <x-admin-breadcrumb :breadcrumbs="[
                ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['name' => 'Pembahasan Lain-lain', 'url' => null]
            ]" />
        </div>

        <!-- Main Content -->
        <div class="container mx-auto px-2 sm:px-6 py-4 sm:py-8">
            <div class="bg-white rounded-lg shadow p-3 sm:p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Pembahasan Lain-lain</h2>
                    <a href="{{ route('admin.other-discussions.create') }}" class="btn bg-blue-500 text-white hover:bg-blue-600 rounded-lg px-4 py-2">
                        <i class="fas fa-plus mr-2"></i> Tambah Data
                    </a>
                </div>

                <!-- Filter Section -->
                <div class="mb-6 bg-white p-4 rounded-lg shadow">
                    <form action="{{ route('admin.other-discussions.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div class="relative">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                            <div class="relative">
                                <input type="text" 
                                       name="search" 
                                       id="search" 
                                       placeholder="Cari topik, PIC, unit..."
                                       value="{{ request('search') }}"
                                       class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Unit -->
                        <div>
                            <label for="unit-filter" class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                            <select id="unit-filter" 
                                    name="unit" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 bg-gray-50">
                                <option value="">Semua Unit</option>
                                @foreach(\App\Models\OtherDiscussion::UNITS as $unit)
                                    <option value="{{ $unit }}" {{ request('unit') == $unit ? 'selected' : '' }}>
                                        {{ $unit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Tanggal Mulai -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                            <input type="date" 
                                   name="start_date" 
                                   id="start_date"
                                   value="{{ request('start_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>

                        <!-- Filter Tanggal Akhir -->
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                            <input type="date" 
                                   name="end_date" 
                                   id="end_date"
                                   value="{{ request('end_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>

                        <!-- Tombol Filter -->
                        <div class="md:col-span-4 flex justify-end space-x-2">
                            <button type="reset" 
                                    onclick="window.location.href='{{ route('admin.other-discussions.index') }}'"
                                    class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                <i class="fas fa-undo mr-2"></i>Reset
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i class="fas fa-filter mr-2"></i>Filter
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Wrapper for Table with Shadow -->
                <div class="overflow-x-auto shadow-md rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 border-collapse border border-gray-200">
                        <thead class="bg-[#0A749B]" style="height: 50px;">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-16">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-24">No SR</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-24">No WO</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-32">Unit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-[300px]">Topik</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-[300px]">Sasaran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-32">Tingkat Resiko</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-32">Tingkat Prioritas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-[300px]">Komitmen Sebelum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-[300px]">Komitmen Selanjutnya</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-32">PIC</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-24">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-32">Deadline</th>
                                {{-- <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-24">Aksi</th> --}}
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-200">
                            @forelse($discussions ?? [] as $index => $discussion)
                            <tr class="hover:bg-gray-50 transition-colors border border-gray-200 border-l-0 border-r-0">
                                <td class="px-4 py-2 whitespace-nowrap border">{{ $index + 1 }}</td>
                                <td class="px-4 py-2 whitespace-nowrap border">{{ $discussion->sr_number }}</td>
                                <td class="px-4 py-2 whitespace-nowrap border">{{ $discussion->wo_number }}</td>
                                <td class="px-4 py-2 whitespace-nowrap border bg-gray-50">{{ $discussion->unit }}</td>
                                <td class="px-4 py-2 whitespace-normal break-words w-[300px] border">{{ $discussion->topic }}</td>
                                <td class="px-4 py-2 whitespace-normal break-words w-[300px] border">{{ $discussion->target }}</td>
                                <td class="px-4 py-2 whitespace-nowrap border bg-gray-50">
                                    <span class="px-2 py-1 rounded text-sm bg-blue-100 text-blue-800">
                                        {{ $discussion->risk_level_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap border bg-gray-50">
                                    <span class="px-2 py-1 rounded text-sm bg-purple-100 text-purple-800">
                                        {{ $discussion->priority_level }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 whitespace-normal break-words w-[300px] border">{{ $discussion->previous_commitment }}</td>
                                <td class="px-4 py-2 whitespace-normal break-words w-[300px] border">{{ $discussion->next_commitment }}</td>
                                <td class="px-4 py-2 whitespace-nowrap border">{{ $discussion->pic }}</td>
                                <td class="px-4 py-2 whitespace-nowrap border bg-gray-50">
                                    <span class="px-2 py-1 rounded text-sm 
                                        {{ $discussion->status === 'Closed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $discussion->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap border">{{ $discussion->deadline->format('d/m/Y') }}</td>
                                {{-- <td class="px-4 py-2 whitespace-nowrap border">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.other-discussions.edit', $discussion->id) }}" 
                                           class="text-white btn bg-indigo-500 hover:bg-indigo-600 rounded-lg px-3 py-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="confirmDelete({{ $discussion->id }})" 
                                                class="text-white btn bg-red-500 hover:bg-red-600 rounded-lg px-3 py-1">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td> --}}
                            </tr>
                            @empty
                            <tr>
                                <td colspan="14" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada data yang tersedia
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4 flex justify-between items-center">
                    <div class="text-sm text-gray-700">
                        Menampilkan 
                        {{ ($discussions->currentPage() - 1) * $discussions->perPage() + 1 }} 
                        hingga 
                        {{ min($discussions->currentPage() * $discussions->perPage(), $discussions->total()) }} 
                        dari 
                        {{ $discussions->total() }} 
                        data
                    </div>
                    <div>
                        {{ $discussions->links() }}
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


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validasi tanggal
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');

        startDate.addEventListener('change', function() {
            endDate.min = this.value;
        });

        endDate.addEventListener('change', function() {
            startDate.max = this.value;
        });

        // Auto submit saat memilih unit
        document.getElementById('unit-filter').addEventListener('change', function() {
            this.form.submit();
        });

        // Debounce search
        let searchTimeout;
        document.getElementById('search').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    });
    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Data ini akan dihapus secara permanen',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/other-discussions/${id}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@push('scripts')
@endpush
@endsection 