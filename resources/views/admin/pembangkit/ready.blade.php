@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md">
        <div class="p-4">
            <img src="{{ asset('logo/navlogo.png') }}" alt="Logo Aplikasi Rapat Harian" class="w-40 h-15">
        </div>
        <nav class="mt-4">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                <i class="fas fa-home mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.daftar_hadir.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.daftar_hadir.index') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                <i class="fas fa-list mr-3"></i>
                <span>Daftar Hadir</span>
            </a>
            <a href="{{ route('admin.pembangkit.ready') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.pembangkit.ready') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                <i class="fas fa-check mr-3"></i>
                <span>Kesiapan Pembangkit</span>
            </a>
            <a href="{{ route('admin.laporan.sr_wo') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.laporan.sr_wo') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                <i class="fas fa-file-alt mr-3"></i>
                <span>Laporan SR/WO</span>
            </a>
            <a href="{{ route('admin.machine-monitor') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.machine-monitor') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                <i class="fas fa-cogs mr-3"></i>
                <span>Monitor Mesin</span>
            </a>
            <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.users') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                <i class="fas fa-users mr-3"></i>
                <span>Manajemen Pengguna</span>
            </a>
            <a href="{{ route('admin.meetings') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.meetings') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                <i class="fas fa-chart-bar mr-3"></i>
                <span>Laporan Rapat</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.settings') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                <i class="fas fa-cog mr-3"></i>
                <span>Pengaturan</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <header class="bg-white shadow-sm">
            <div class="flex justify-between items-center px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">Kesiapan Pembangkit</h1>
            </div>
        </header>

        <main class="p-6">
            <!-- Konten Kesiapan Pembangkit -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Status Pembangkit</h2>
                
                <!-- Tabel Status Pembangkit -->
                <table class="min-w-full bg-white border border-gray-300">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Nama Unit</th>
                            <th class="py-2 px-4 border-b">Status</th>
                            <th class="py-2 px-4 border-b">Kapasitas (MW)</th>
                            <th class="py-2 px-4 border-b">Ketersediaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($units as $unit)
                        <tr>
                            <td class="py-2 px-4 border-b">{{ $unit->name }}</td>
                            <td class="py-2 px-4 border-b">{{ $unit->status }}</td>
                            <td class="py-2 px-4 border-b">{{ $unit->capacity }}</td>
                            <td class="py-2 px-4 border-b">{{ $unit->availability ? 'Tersedia' : 'Tidak Tersedia' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>
@endsection 