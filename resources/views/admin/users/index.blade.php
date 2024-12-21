@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-gray-50 overflow-hidden">
        <!-- Sidebar -->
       @include('components.sidebar')

        <!-- Main Content -->
        <div id="main-content" class="flex-1 overflow-auto">
            <!-- Header -->
            <header class="bg-white shadow-sm">
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
                                <div class="flex-1 flex items-center ">
                                    <div class="flex">
                                        <input type="text" id="search" placeholder="Cari pengguna..."
                                            class="w-full px-4 py-2 border rounded-l-lg focus:outline-none focus:border-blue-500"
                                            onkeyup="searchUsers()">
                                        <input type="button" value="Search"
                                            class="bg-blue-500 p-2 rounded-tr-lg rounded-br-lg text-white font-semibold hover:bg-blue-800 transition-colors">
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
                                    <tbody id="users-body" class="divide-y divide-gray-200">
                                        @forelse($users as $index => $user)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="text-center py-2 whitespace-nowrap border border-gray-300">
                                                    {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                                                </td>
                                                <td class="text-center p-2 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <img class="h-8 w-8 rounded-full"
                                                            src="{{ $user->avatar ?? asset('images/default-avatar.png') }}"
                                                            alt="">
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
                                                        {{ $user->created_at->format('d M Y') }}
                                                    </div>
                                                </td>
                                                <td class="py-2 whitespace-nowrap flex justify-center gap-2">
                                                    <div>
                                                        <a href="{{ route('admin.users.edit', $user->id) }}"
                                                            class="text-white btn bg-indigo-500 hover:bg-indigo-900 rounded-lg border">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </div>

                                                    <form id="delete-form-{{ $user->id }}"
                                                        action="{{ route('admin.users.destroy', $user->id) }}"
                                                        method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')"
                                                            class="text-white btn bg-red-500 hover:bg-red-600 rounded-lg">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
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
                                <div class="mt-4">
                                    <div class="flex justify-between items-center">
                                        <div class="text-sm text-gray-700">
                                            Showing 
                                            {{ ($users->currentPage()-1) * $users->perPage() + 1 }} 
                                            to 
                                            {{ min($users->currentPage() * $users->perPage(), $users->total()) }} 
                                            of 
                                            {{ $users->total() }} 
                                            entries
                                        </div>
                                        
                                        <div class="flex gap-2">
                                            @if (!$users->onFirstPage())
                                                <a href="{{ $users->previousPageUrl() }}" 
                                                   class="px-3 py-1 bg-[#0A749B] text-white rounded">Previous</a>
                                            @endif

                                            @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                                                <a href="{{ $url }}" 
                                                   class="px-3 py-1 rounded {{ $page == $users->currentPage() 
                                                       ? 'bg-[#0A749B] text-white' 
                                                       : 'bg-white text-[#0A749B] border border-[#0A749B]' }}">
                                                    {{ $page }}
                                                </a>
                                            @endforeach

                                            @if ($users->hasMorePages())
                                                <a href="{{ $users->nextPageUrl() }}" 
                                                   class="px-3 py-1 bg-[#0A749B] text-white rounded">Next</a>
                                            @endif
                                        </div>
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

    <script src="{{ asset('js/toggle.js') }}"></script>

    <script>
        function searchUsers() {
            const input = document.getElementById('search').value.toLowerCase();
            const roleFilter = document.getElementById('role-filter').value.toLowerCase();
            const tbody = document.getElementById('users-body');
            const rows = Array.from(tbody.getElementsByTagName('tr')).filter(row => !row.classList.contains('no-data-row'));

            // Urutkan rows berdasarkan role (admin di atas)
            rows.sort((a, b) => {
                const roleA = a.querySelector('td:nth-child(4) span')?.textContent.toLowerCase() || '';
                const roleB = b.querySelector('td:nth-child(4) span')?.textContent.toLowerCase() || '';
                
                if (roleA === 'admin' && roleB !== 'admin') return -1;
                if (roleA !== 'admin' && roleB === 'admin') return 1;
                return 0;
            });

            let visibleCount = 0;
            rows.forEach(row => {
                const nameCell = row.querySelector('td:nth-child(2) .text-sm.font-medium');
                const emailCell = row.querySelector('td:nth-child(3) .text-sm');
                const roleCell = row.querySelector('td:nth-child(4) span');

                if (!nameCell || !emailCell || !roleCell) return;

                const name = nameCell.textContent.toLowerCase().trim();
                const email = emailCell.textContent.toLowerCase().trim();
                const role = roleCell.textContent.toLowerCase().trim();

                const matchesSearch = !input || 
                                    name.includes(input) || 
                                    email.includes(input);
                const matchesRole = !roleFilter || role === roleFilter;

                if (matchesSearch && matchesRole) {
                    row.style.display = '';
                    visibleCount++;
                    // Update nomor urut
                    const numberCell = row.querySelector('td:first-child');
                    if (numberCell) {
                        numberCell.textContent = visibleCount;
                    }
                } else {
                    row.style.display = 'none';
                }
            });

            // Hapus pesan "tidak ada data" yang lama jika ada
            const existingNoData = tbody.querySelector('.no-data-row');
            if (existingNoData) {
                existingNoData.remove();
            }

            // Tampilkan pesan jika tidak ada hasil
            if (visibleCount === 0) {
                const noDataRow = document.createElement('tr');
                noDataRow.className = 'no-data-row';
                noDataRow.innerHTML = `
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Tidak ada data pengguna yang sesuai dengan pencarian
                    </td>
                `;
                tbody.appendChild(noDataRow);
            }

            // Urutkan ulang baris yang terlihat
            rows.filter(row => row.style.display !== 'none')
                .forEach(row => tbody.appendChild(row));
        }

        // Filter ketika memilih role
        document.getElementById('role-filter').addEventListener('change', searchUsers);

        // Debounce untuk input pencarian
        let searchTimeout;
        document.getElementById('search').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(searchUsers, 300);
        });

        // Inisialisasi pencarian dan pengurutan saat halaman dimuat
        document.addEventListener('DOMContentLoaded', searchUsers);

        function confirmDelete(userId, userName) {
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: `Anda akan menghapus pengguna ${userName}!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + userId).submit();

                    // Tampilkan pesan sukses setelah penghapusan
                    Swal.fire(
                        'Terhapus!',
                        'Pengguna berhasil dihapus.',
                        'success'
                    );
                }
            });
        }

        let timerInterval;
        let startTime;
        let elapsedTime = 0; // Menyimpan waktu yang telah berlalu
        let isRunning = false;

        // Cek apakah timer sedang berjalan saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const storedStartTime = localStorage.getItem('startTime');
            const storedElapsedTime = localStorage.getItem('elapsedTime');
            const storedIsRunning = localStorage.getItem('isRunning');

            if (storedStartTime && storedIsRunning === 'true') {
                startTime = new Date(parseInt(storedStartTime));
                elapsedTime = parseInt(storedElapsedTime) || 0; // Ambil waktu yang telah berlalu
                isRunning = true;
                updateTimerDisplay(); // Perbarui tampilan timer
                timerInterval = setInterval(updateTimer, 1000); // Mulai interval

                // Tampilkan timer
                document.getElementById('timer').style.display = 'block'; // Tampilkan timer
            } else {
                // Jika timer tidak berjalan, sembunyikan timer
                document.getElementById('timer').style.display = 'none';
            }
        });

        function updateTimer() {
            const now = new Date();
            elapsedTime += 1000; // Tambahkan 1 detik ke waktu yang telah berlalu
            localStorage.setItem('elapsedTime', elapsedTime); // Simpan waktu yang telah berlalu

            updateTimerDisplay(); // Perbarui tampilan timer
        }

        function updateTimerDisplay() {
            const totalElapsedTime = elapsedTime + (isRunning ? new Date() - startTime : 0);
            const hours = Math.floor(totalElapsedTime / (1000 * 60 * 60));
            const minutes = Math.floor((totalElapsedTime % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((totalElapsedTime % (1000 * 60)) / 1000);

            const timerDisplay = document.getElementById('timer');
            timerDisplay.textContent = `${padNumber(hours)}:${padNumber(minutes)}:${padNumber(seconds)}`;
        }

        function padNumber(number) {
            return number.toString().padStart(2, '0');
        }

        function filterUsers() {
            // Panggil searchUsers() untuk menerapkan filter dan pencarian sekaligus
            searchUsers();
        }

        // Tambahkan event listener untuk input pencarian
        document.getElementById('search').addEventListener('keyup', searchUsers);
    </script>
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
    </style>
@endsection
