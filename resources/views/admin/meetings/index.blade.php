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
                        <h1 class="text-xl font-semibold text-gray-800">Laporan Rapat</h1>
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

            <div class="flex flex-col sm:flex-row justify-between items-center pt-2">
                <div class="flex justify-start w-full">
                    <x-admin-breadcrumb :breadcrumbs="[['name' => 'Laporan Rapat', 'url' => null]]" />
                </div>
            </div>
            <main class="p-6">
                <!-- Filter Section -->


                <!-- Setelah Filter Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <!-- Card Laporan Kesiapan Pembangkit -->

                    <!-- Card lainnya bisa ditambahkan di sini -->
                </div>

                <!-- Tabel Hasil Rapat -->
                <div class="bg-white rounded-lg shadow mb-6 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Hasil Rapat</h2>
                    <div class="overflow-auto">
                        <table class="min-w-full divide-y divide-gray-200 border-collapse border border-gray-200">
                            <thead class="sticky top-0">
                                <tr style="background-color: #0A749B; color: white;" class="text-center">
                                    <th class="border p-2">Judul</th>
                                    <th class="border p-2">Tanggal</th>
                                    <th class="border p-2">Departemen</th>
                                    <th class="border p-2">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($meetings ?? [] as $meeting)
                                    <tr class="odd:bg-white even:bg-gray-100">
                                        <td class="border p-2">{{ $meeting->title }}</td>
                                        <td class="border p-2">{{ $meeting->scheduled_at->format('F j, Y') }}</td>
                                        <td class="border p-2">{{ $meeting->department->name ?? 'Tidak Ada' }}</td>
                                        <td class="border p-2">{{ $meeting->status }}</td>
                                        <td class="border p-2">
                                            @php
                                                $scoreCard = ScoreCardDaily::where(
                                                    'tanggal',
                                                    $meeting->scheduled_at->format('Y-m-d'),
                                                )->first();
                                            @endphp
                                            {{ $scoreCard->skor ?? 'Tidak Ada' }}
                                        </td>
                                        <td class="border p-2">{{ $scoreCard->lokasi ?? 'Tidak Ada' }}</td>
                                        <td class="border p-2">
                                            {{ implode(', ', json_decode($scoreCard->peserta, true) ?? []) }}</td>
                                        <td class="border p-2">{{ $scoreCard->kesiapan_panitia ?? 'N/A' }}</td>
                                        <td class="border p-2">{{ $scoreCard->kesiapan_bahan ?? 'N/A' }}</td>
                                        <td class="border p-2">{{ $scoreCard->aktifitas_luar ?? 'N/A' }}</td>
                                        <td class="border p-2">{{ $scoreCard->gangguan_diskusi ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
