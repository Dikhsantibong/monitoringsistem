@extends('layouts.app')

@push('styles')
    <!-- Tambahkan Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Pastikan konten utama dapat di-scroll */
        .main-content {
            overflow-y: auto;
            /* Izinkan scroll vertikal */
            height: calc(100vh - 64px);
            /* Sesuaikan tinggi dengan mengurangi tinggi header */
        }
    </style>
@endpush

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
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-[#F3F3F3] text-white' : 'text-white  hover:bg-[#F3F3F3] hover:text-black' }}">
                    <i class="fas fa-home mr-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.pembangkit.ready') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.pembangkit.ready') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-check mr-3"></i>
                    <span>Kesiapan Pembangkit</span>
                </a>
                <a href="{{ route('admin.laporan.sr_wo') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.laporan.sr_wo') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-file-alt mr-3"></i>
                    <span>Laporan SR/WO</span>
                </a>
                <a href="{{ route('admin.machine-monitor') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.machine-monitor') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-cogs mr-3"></i>
                    <span>Monitor Mesin</span>
                </a>
                <a href="{{ route('admin.daftar_hadir.index') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.daftar_hadir.index') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-list mr-3"></i>
                    <span>Daftar Hadir</span>
                </a>
                <a href="{{ route('admin.score-card.index') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.score-card.*') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3] hover:text-black' }}">
                    <i class="fas fa-clipboard-list mr-3"></i>
                    <span>Score Card Daily</span>
                </a>
                <a href="{{ route('admin.users') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.users') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-users mr-3"></i>
                    <span>Manajemen Pengguna</span>
                </a>
                <a href="{{ route('admin.meetings') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.meetings') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-chart-bar mr-3"></i>
                    <span>Laporan Rapat</span>
                </a>
                <a href="{{ route('admin.settings') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.settings') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-cog mr-3"></i>
                    <span>Pengaturan</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div id="main-content" class="flex-1 main-content">
            <!-- Header -->
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
                    <h2 class="text-xl font-semibold text-gray-800">Score Card Daily</h2>
                    <div class="flex items-center">
                        <div class="relative">
                            <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                                <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                                    class="w-7 h-7 rounded-full mr-2">
                                <span class="text-gray-700 text-sm">{{ Auth::user()->name }}</span>
                                <i class="fas fa-caret-down ml-2 text-gray-600"></i>
                            </button>
                            <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden">
                                <a href="{{ route('profile.edit') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                                <a href="#" onclick="showLogoutConfirmation()"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Keluar</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <div class="flex items-center pt-2">
                <x-admin-breadcrumb :breadcrumbs="[['name' => 'Score Card Daily', 'url' => null]]" />
            </div>
            <!-- Main Content -->
            <div class="container mx-auto px-4">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-6">SCORE CARD DAILY</h2>

                    <div class="mb-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p>Daily Meeting Hari / Tanggal: {{ now()->format('d F Y') }}</p>
                                <p>Lokasi: Ruang Rapat Rongi</p>
                            </div>
                            <a href="{{ route('admin.score-card.create') }}"
                                class="bg-blue-500 text-white px-4 py-2 rounded flex items-center">
                                <i class="fas fa-plus mr-2"></i> Tambah Score Card
                            </a>
                        </div>
                    </div>
                    <div class="overflow-auto">
                        <table class="min-w-full bg-white border">
                            <thead>
                                <tr style="background-color: #0A749B; color: white;" class="text-center">
                                    <th class="border p-2">No</th>
                                    <th class="border p-2">Peserta</th>
                                    <th class="border p-2">Awal</th>
                                    <th class="border p-2">Akhir</th>
                                    <th class="border p-2">Skor</th>
                                    <th class="border p-2">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($scoreCards as $index => $card)
                                    <tr>
                                        <td class="border p-2 text-center">{{ $index + 1 }}</td>
                                        <td class="border p-2">{{ $card->peserta }}</td>
                                        <td class="border p-2 text-center">{{ $card->awal }}</td>
                                        <td class="border p-2 text-center">{{ $card->akhir }}</td>
                                        <td class="border p-2 text-center">{{ $card->skor }}</td>
                                        <td class="border p-2">{{ $card->keterangan }}</td>
                                    </tr>
                                @endforeach
                                <!-- Tambahkan baris untuk total score -->
                                <tr>
                                    <td colspan="4" class="border p-2 text-right font-bold">Total Score:</td>
                                    <td class="border p-2 text-center font-bold">{{ $totalScore ?? '0' }}</td>
                                    <td class="border p-2"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/toggle.js') }}"></script>
@endsection
