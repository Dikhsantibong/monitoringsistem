@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#0A749B] shadow-md">
        <div class="p-4">
            <img src="{{ asset('logo/navlogo.png') }}" alt="Logo Aplikasi Rapat Harian" class="w-40 h-15">
        </div>
        <nav class="mt-4">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-[#F3F3F3] text-white' : 'text-white  hover:bg-[#F3F3F3] hover:text-black' }}">
                <i class="fas fa-home mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.score-card.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.score-card.*') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3] hover:text-black' }}">
                <i class="fas fa-clipboard-list mr-3"></i>
                <span>Score Card Daily</span>
            </a>
            <a href="{{ route('admin.daftar_hadir.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.daftar_hadir.index') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-list mr-3"></i>
                <span>Daftar Hadir</span>
            </a>
            <a href="{{ route('admin.pembangkit.ready') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.pembangkit.ready') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-check mr-3"></i>
                <span>Kesiapan Pembangkit</span>
            </a>
            <a href="{{ route('admin.laporan.sr_wo') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.laporan.sr_wo') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-file-alt mr-3"></i>
                <span>Laporan SR/WO</span>
            </a>
            <a href="{{ route('admin.machine-monitor') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.machine-monitor') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-cogs mr-3"></i>
                <span>Monitor Mesin</span>
            </a>
            <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.users') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-users mr-3"></i>
                <span>Manajemen Pengguna</span>
            </a>
            <a href="{{ route('admin.meetings') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.meetings') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-chart-bar mr-3"></i>
                <span>Laporan Rapat</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.settings') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-cog mr-3"></i>
                <span>Pengaturan</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-6">Tambah Score Card Daily</h2>
                
                <form action="{{ route('admin.score-card.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="tanggal" class="block text-gray-700 mb-2">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                    </div>
                    <div class="mb-3">
                        <label for="lokasi" class="block text-gray-700 mb-2">Lokasi</label>
                        <input type="text" name="lokasi" id="lokasi" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                    </div>
                    <div class="mb-3">
                        <label for="peserta" class="block text-gray-700 mb-2">Peserta</label>
                        <textarea name="peserta" id="peserta" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="waktu_mulai" class="block text-gray-700 mb-2">Waktu Mulai</label>
                        <input type="time" name="waktu_mulai" id="waktu_mulai" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                    </div>
                    <div class="mb-3">
                        <label for="waktu_selesai" class="block text-gray-700 mb-2">Waktu Selesai</label>
                        <input type="time" name="waktu_selesai" id="waktu_selesai" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Simpan</button>
                        <a href="{{ route('admin.score-card.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 