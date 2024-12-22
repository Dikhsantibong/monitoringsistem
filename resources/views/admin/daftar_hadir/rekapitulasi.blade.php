@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-gray-50 overflow-auto">
        <!-- Sidebar -->
       @include('components.sidebar')


        <!-- Main Content -->
        <div id="main-content" class="flex-1 overflow-auto">
            <header class="bg-white shadow-sm sticky z-10">
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
                    <h1 class="text-xl font-semibold text-gray-800">Daftar Hadir</h1>
                    <!-- User Dropdown -->
                    <div class="relative">
                        <button id="user-menu-button" class="flex items-center gap-2 hover:text-gray-600"
                            onclick="toggleUserDropdown()">
                            <img src="{{ asset('avatars/' . Auth::user()->avatar) }}" alt="User Avatar"
                                class="w-8 h-8 rounded-full">
                            <span>{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-sm"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="user-dropdown"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 hidden">
                            <a href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
            <div class="flex items-center pt-2">
                <x-admin-breadcrumb :breadcrumbs="[['name' => 'Daftar Hadir', 'url' => null]]" />
            </div>
<div class="container mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Rekapitulasi Kehadiran Rapat</h1>
    </div>

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!-- Total Kehadiran -->
        <div class="bg-blue-500 rounded-lg shadow p-6 flex items-center">
            <i class="fas fa-users text-white text-4xl mr-4"></i>
            <div class="flex flex-col text-white">
                <div class="font-bold text-3xl">{{ $statistik['total'] }}</div>
                <div class="text-sm">Total Kehadiran</div>
            </div>
        </div>

        <!-- Tepat Waktu -->
        <div class="bg-green-500 rounded-lg shadow p-6 flex items-center">
            <i class="fas fa-clock text-white text-4xl mr-4"></i>
            <div class="flex flex-col text-white">
                <div class="font-bold text-3xl">{{ $statistik['tepat_waktu'] }}</div>
                <div class="text-sm">Tepat Waktu</div>
            </div>
        </div>

        <!-- Terlambat -->
        <div class="bg-red-500 rounded-lg shadow p-6 flex items-center">
            <i class="fas fa-exclamation-circle text-white text-4xl mr-4"></i>
            <div class="flex flex-col text-white">
                <div class="font-bold text-3xl">{{ $statistik['terlambat'] }}</div>
                <div class="text-sm">Terlambat</div>
            </div>
        </div>

        <!-- Persentase Ketepatan -->
        <div class="bg-purple-500 rounded-lg shadow p-6 flex items-center">
            <i class="fas fa-calculator text-white text-4xl mr-4"></i>
            <div class="flex flex-col text-white">
                <div class="font-bold text-3xl">{{ $statistik['persentase_tepat'] }}%</div>
                <div class="text-sm">Persentase Tepat Waktu</div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form action="{{ route('admin.daftar_hadir.rekapitulasi') }}" method="GET" class="flex gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Awal</label>
                <input type="date" name="tanggal_awal" 
                       value="{{ request('tanggal_awal', now()->startOfMonth()->format('Y-m-d')) }}" 
                       class="w-full rounded-md border-gray-300">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" 
                       value="{{ request('tanggal_akhir', now()->endOfMonth()->format('Y-m-d')) }}"
                       class="w-full rounded-md border-gray-300">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Rekapitulasi -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Divisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jabatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Hadir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($attendances as $hadir)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ Carbon\Carbon::parse($hadir->time)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">{{ $hadir->name }}</td>
                        <td class="px-6 py-4">{{ $hadir->division }}</td>
                        <td class="px-6 py-4">{{ $hadir->position }}</td>
                        <td class="px-6 py-4">{{ Carbon\Carbon::parse($hadir->time)->format('H:i:s') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ Carbon\Carbon::parse($hadir->time)->format('H:i:s') <= '08:00:00' 
                                   ? 'bg-green-100 text-green-800' 
                                   : 'bg-red-100 text-red-800' }}">
                                {{ Carbon\Carbon::parse($hadir->time)->format('H:i:s') <= '08:00:00' 
                                   ? 'Tepat Waktu' 
                                   : 'Terlambat' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data kehadiran untuk periode yang dipilih
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection