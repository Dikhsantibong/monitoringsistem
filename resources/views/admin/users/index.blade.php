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
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="flex justify-between items-center px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">Manajemen Pengguna</h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.users.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        <i class="fas fa-plus mr-2"></i>Tambah Pengguna
                    </a>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="p-6">
            <!-- Search and Filter -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-4">
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <input type="text" id="search" placeholder="Cari pengguna..." class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500" onkeyup="searchUsers()">
                        </div>
                        <div>
                            <select id="role-filter" class="px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                <option value="">Semua Peran</option>
                                <option value="admin">Admin</option>
                                <option value="user">Pengguna</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Pengguna -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <table id="users-table" class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Pada</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="users-body" class="divide-y divide-gray-200">
                            @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img class="h-8 w-8 rounded-full" src="{{ $user->avatar ?? asset('images/default-avatar.png') }}" alt="">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $user->created_at->format('d M Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash-alt mr-2"></i>Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection


<script>
function searchUsers() {
    const input = document.getElementById('search').value.toLowerCase();
    const rows = document.querySelectorAll('#users-body tr');

    rows.forEach(row => {
        const name = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
        const email = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        
        if (name.includes(input) || email.includes(input)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
@push('scripts')
@endpush