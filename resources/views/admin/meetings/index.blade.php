@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-gray-50 overflow-auto">
        <!-- Sidebar -->
        <aside id="mobile-menu"
            class="fixed z-20 overflow-hidden transform transition-transform duration-300 md:relative md:translate-x-0 h-screen w-64 bg-[#0A749B] shadow-md text-white hidden md:block md:shadow-lg">
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
                <div class="flex justify-between items-center px-6 py-2">
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
                    <h1 class="text-xl font-semibold text-gray-800">Laporan Rapat</h1>
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
            <div class="flex justify-between items-center pt-2">
                <x-admin-breadcrumb :breadcrumbs="[['name' => 'Laporan Rapat', 'url' => null]]" />
                <div class="flex items-center space-x-4">
                    <button onclick="exportMeetings()"
                        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        <i class="fas fa-download mr-2"></i>Ekspor
                    </button>
                    <button onclick="uploadMeeting()"
                        class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                        <i class="fas fa-upload mr-2"></i>Upload Rapat
                    </button>
                    <button onclick="openModal()" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                        <i class="fas fa-plus mr-2"></i>Buat Rapat Baru
                    </button>
                </div>
            </div>
            <main class="p-6">
                <!-- Filter Section -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Filter Rapat</h2>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rentang Tanggal</label>
                                <input type="date"
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                                    id="date-filter">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Departemen</label>
                                <select class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                                    id="department-filter">
                                    <option value="">Semua Departemen</option>
                                    @foreach ($departments ?? [] as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                                    id="status-filter">
                                    <option value="">Semua Status</option>
                                    <option value="scheduled">Terjadwal</option>
                                    <option value="completed">Selesai</option>
                                    <option value="cancelled">Dibatalkan</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button onclick="applyFilter()"
                                    class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                    <i class="fas fa-filter mr-2"></i>Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Hasil Rapat -->
                <div class="bg-white rounded-lg shadow mb-6 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Hasil Rapat</h2>
                    <table class="min-w-full divide-y divide-gray-200 border-collapse border border-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-center font-medium text-gray-500">Judul</th>
                                <th class="px-6 py-3 text-center font-medium text-gray-500">Tanggal</th>
                                <th class="px-6 py-3 text-center font-medium text-gray-500">Departemen</th>
                                <th class="px-6 py-3 text-center font-medium text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($meetings ?? [] as $meeting)
                                <tr class="odd:bg-white even:bg-gray-100">
                                    <td>{{ $meeting->title }}</td>
                                    <td>{{ $meeting->scheduled_at->format('F j, Y') }}</td>
                                    <td>{{ $meeting->department->name ?? 'Tidak Ada' }}</td>
                                    <td>{{ $meeting->status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal -->
    <div id="createMeetingModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 md:w-1/2">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Buat Rapat Baru</h2>
            <iframe src="{{ route('admin.meetings.create') }}" class="w-full h-96" frameborder="0"></iframe>
            <button onclick="closeModal()"
                class="mt-4 bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">Tutup</button>
        </div>
    </div>

    <style>
        .modal-enter {
            opacity: 0;
            transform: scale(0.7);
        }

        .modal-enter-active {
            opacity: 1;
            transform: scale(1);
            transition: opacity 0.3s, transform 0.3s;
        }

        .modal-leave {
            opacity: 1;
            transform: scale(1);
        }

        .modal-leave-active {
            opacity: 0;
            transform: scale(0.7);
            transition: opacity 0.3s, transform 0.3s;
        }
    </style>
    <script src="{{ asset('js/toggle.js') }}"></script>
    <script>
        document.getElementById('upload-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('{{ route('admin.meetings.upload') }}', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('upload-message').innerText = data.message;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('upload-message').innerText = 'Upload failed.';
                });
        });

        function openModal() {
            const modal = document.getElementById('createMeetingModal');
            modal.classList.remove('hidden');
            modal.classList.add('modal-enter');
            setTimeout(() => {
                modal.classList.remove('modal-enter');
                modal.classList.add('modal-enter-active');
            }, 10); // Delay untuk memastikan animasi diterapkan
        }

        function closeModal() {
            const modal = document.getElementById('createMeetingModal');
            modal.classList.remove('modal-enter-active');
            modal.classList.add('modal-leave');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('modal-leave');
            }, 300); // Delay untuk menunggu animasi selesai
        }
    </script>

    @push('scripts')
    @endpush
@endsection
