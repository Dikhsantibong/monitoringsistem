@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-gray-50 overflow-hidden">
        <!-- Sidebar -->
       @include('components.sidebar')

        <!-- Main Content -->
        <div id="main-content" class="flex-1 overflow-auto">
            <!-- Header -->
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
                        <h1 class="text-xl font-semibold text-gray-800">Manajemen Pengguna</h1>
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
            <div class="flex items-center pt-2">
                <x-admin-breadcrumb :breadcrumbs="[['name' => 'Manajemen Pengguna', 'url' => null]]" />
            </div>

            <!-- Content -->
            <main class="px-6">
                <!-- Search and Filter -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Daftar Pengguna</h2>
                        <div class="mb-4 flex flex-col sm:flex-row gap-3 justify-between space-x-4">
                            <div class="border">
                                <select id="role-filter"
                                    class="px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500 w-full"
                                    onchange="filterUsers()">
                                    <option value="">Semua Peran</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">Pengguna</option>
                                </select>
                            </div>
                            <div class="flex flex-col md:flex-row gap-3 items-center">
                                <div class="flex-1 flex items-center">
                                    <div class="flex">
                                        <input type="text" 
                                               id="search" 
                                               placeholder="Cari pengguna..." 
                                               class="w-full px-4 py-2 border rounded-l-lg focus:outline-none focus:border-blue-500">
                                        <button type="button" 
                                                id="searchButton"
                                                class="bg-blue-500 p-2 rounded-tr-lg rounded-br-lg text-white font-semibold hover:bg-blue-800 transition-colors">
                                            Search
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('admin.users.create') }}"
                                        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors items-center sm:flex">
                                        <i class="fas fa-plus mr-2"></i>
                                        Tambah Pengguna
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow p-6 mb-4">
                            <div class="overflow-auto">
                                <table id="users-table" class="min-w-full divide-y divide-gray-200 border-collapse border border-gray-200">
                                    <thead>
                                        <tr style="background-color: #0A749B; color: white">
                                            <th class="px-6 py-3 text-center text-sm font-medium uppercase">No</th>
                                            <th class="px-6 py-3 text-center text-sm font-medium uppercase">Nama</th>
                                            <th class="px-6 py-3 text-center text-sm font-medium uppercase">Email</th>
                                            <th class="px-6 py-3 text-center text-sm font-medium uppercase">Peran</th>
                                            <th class="px-6 py-3 text-center text-sm font-medium uppercase">Dibuat Pada</th>
                                            <th class="px-6 py-3 text-center text-sm font-medium uppercase">Aksi</th>
                                        </tr>
                                    </thead>
                                    
                                    <!-- Loader tbody -->
                                    <tbody id="tableLoader" style="display: none;">
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="flex justify-center items-center">
                                                    <div class="loader-circle"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>

                                    <!-- Data tbody -->
                                    <tbody id="users-body" class="divide-y divide-gray-200">
                                        @forelse($users as $index => $user)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="text-center py-2 whitespace-nowrap border border-gray-300">
                                                    {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                                                </td>
                                                <td class="text-center p-2 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                {{ $user->name }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="p-2 whitespace-nowrap border border-gray-300">
                                                    <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                                </td>
                                                <td class="text-center py-2 whitespace-nowrap border border-gray-300">
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                        {{ ucfirst($user->role) }}
                                                    </span>
                                                </td>
                                                <td class="text-center py-2 whitespace-nowrap border border-gray-300">
                                                    <div class="text-sm text-gray-900">
                                                        {{ optional($user->created_at)->format('d M Y') ?: '-' }}
                                                    </div>
                                                </td>
                                                <td class="py-2 whitespace-nowrap flex justify-center gap-2">
                                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                                        class="text-white btn bg-indigo-500 hover:bg-indigo-900 rounded-lg border">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <button type="button" 
                                                            onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')"
                                                            class="text-white btn bg-red-500 hover:bg-red-600 rounded-lg">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>

                                                    <form id="delete-form-{{ $user->id }}" 
                                                          action="{{ route('admin.users.destroy', $user->id) }}" 
                                                          method="POST" 
                                                          class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                                    Tidak ada data pengguna yang tersedia
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <!-- Pagination -->
                                <div class="mt-4 flex justify-between items-center">
                                    <div class="text-sm text-gray-700">
                                        Menampilkan 
                                        {{ ($users->currentPage() - 1) * $users->perPage() + 1 }} 
                                        hingga 
                                        {{ min($users->currentPage() * $users->perPage(), $users->total()) }} 
                                        dari 
                                        {{ $users->total() }} 
                                        entri
                                    </div>
                                    <div class="flex items-center gap-1">
                                        @if (!$users->onFirstPage())
                                            <a href="{{ $users->previousPageUrl() }}" 
                                               class="px-3 py-1 bg-[#0A749B] text-white rounded">Sebelumnya</a>
                                        @endif

                                        @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                                            @if ($page == $users->currentPage())
                                                <span class="px-3 py-1 bg-[#0A749B] text-white rounded">{{ $page }}</span>
                                            @else
                                                <a href="{{ $url }}" 
                                                   class="px-3 py-1 rounded {{ $page == $users->currentPage() 
                                                       ? 'bg-[#0A749B] text-white' 
                                                       : 'bg-white text-[#0A749B] border border-[#0A749B]' }}">
                                                    {{ $page }}
                                                </a>
                                            @endif
                                        @endforeach

                                        @if ($users->hasMorePages())
                                            <a href="{{ $users->nextPageUrl() }}" 
                                               class="px-3 py-1 bg-[#0A749B] text-white rounded">Selanjutnya</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    </div>
    <script type="text/javascript">
    // Fungsi pencarian yang diperbarui
    window.searchUsers = function() {
        const searchTerm = document.getElementById('search').value;
        const roleFilter = document.getElementById('role-filter').value;
        
        // Tampilkan loader
        document.getElementById('tableLoader').style.display = 'table-row-group';
        document.getElementById('users-body').style.display = 'none';

        // Buat URL dengan parameter pencarian
        const url = new URL(window.location.href);
        url.searchParams.set('search', searchTerm);
        url.searchParams.set('role', roleFilter);
        url.searchParams.set('page', '1'); 

        // Lakukan fetch ke URL yang sama
        fetch(url)
            .then(response => response.text())
            .then(html => {
                // Parse HTML response
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Update tbody dengan hasil pencarian
                const newTbody = doc.getElementById('users-body');
                if (newTbody) {
                    document.getElementById('users-body').innerHTML = newTbody.innerHTML;
                }

                // Update pagination
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
                document.getElementById('users-body').innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-red-500">
                            Terjadi kesalahan saat mencari data
                        </td>
                    </tr>
                `;
            })
            .finally(() => {
                // Sembunyikan loader dan tampilkan hasil
                document.getElementById('tableLoader').style.display = 'none';
                document.getElementById('users-body').style.display = 'table-row-group';
            });
    };

    // Event listeners dengan debounce
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        const searchButton = document.getElementById('searchButton');
        const roleFilter = document.getElementById('role-filter');

        // Debounce function
        function debounce(func, wait) {
            let timeout;
            return function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => func(), wait);
            };
        }

        // Tambahkan event listeners
        if (searchInput) {
            searchInput.addEventListener('input', debounce(window.searchUsers, 500));
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    window.searchUsers();
                }
            });
        }

        if (searchButton) {
            searchButton.addEventListener('click', window.searchUsers);
        }

        if (roleFilter) {
            roleFilter.addEventListener('change', window.searchUsers);
        }
    });

    function confirmDelete(userId, userName) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Anda akan menghapus pengguna: ${userName}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById(`delete-form-${userId}`);
                if (form) {
                    form.submit();
                }
            }
        });
    }

    // Handle flash messages
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 1500
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: "{{ session('error') }}"
            });
        @endif
    });
    </script>
    <script src="{{ asset('js/toggle.js') }}"></script>

    
    @push('scripts')
    @endpush

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
@endsection
