@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.pemeliharaan-sidebar')
    <div class="flex-1 main-content">
        <header class="bg-white shadow-sm sticky top-0">
            <div class="flex justify-between items-center px-6 py-3">
                <div class="flex items-center gap-x-3">
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
                    <h1 class="text-xl font-semibold text-gray-800">Daftar Pengajuan Material</h1>
                </div>
                <div class="flex items-center gap-x-4 relative">
                    <!-- User Dropdown -->
                    <div class="relative">
                        <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                            <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                                class="w-8 h-8 rounded-full mr-2">
                            <span class="text-gray-700">{{ Auth::user()->name }}</span>
                            <i class="fas fa-caret-down ml-2"></i>
                        </button>
                        <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                            <a href="{{ route('user.profile') }}"
                                class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <main class="px-6 pt-6">
            <div class="mb-4 flex justify-end">
                <a href="{{ route('pemeliharaan.pengajuan-material.create') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Ajukan Material Baru
                </a>
            </div>
            <div class="bg-white rounded-lg shadow p-6 w-full">
                <h2 class="text-lg font-semibold mb-4">Daftar Pengajuan Material Anda</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 border-b text-center">No</th>
                                <th class="px-4 py-2 border-b text-center">Nama File</th>
                                <th class="px-4 py-2 border-b text-center">Tanggal</th>
                                <th class="px-4 py-2 border-b text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($files as $i => $file)
                                <tr>
                                    <td class="px-4 py-2 border-b text-center border-r">{{ $i+1 }}</td>
                                    <td class="px-4 py-2 border-b text-center border-r">{{ $file->filename }}</td>
                                    <td class="px-4 py-2 border-b text-center border-r">{{ $file->created_at }}</td>
                                    <td class="px-4 py-2 border-b text-center border-r">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('pemeliharaan.pengajuan-material.edit', $file->id) }}" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Edit</a>
                                            <a href="{{ asset('storage/' . $file->path) }}" target="_blank" class="bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-600">Download</a>
                                            <button class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 delete-btn" data-id="{{ $file->id }}">Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-2 text-center text-gray-500">Belum ada pengajuan material.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="mt-4 flex justify-between items-center">
                    <div class="text-sm text-gray-700">
                        Menampilkan 
                        {{ ($files->currentPage() - 1) * $files->perPage() + 1 }} 
                        hingga 
                        {{ min($files->currentPage() * $files->perPage(), $files->total()) }} 
                        dari 
                        {{ $files->total() }} 
                        entri
                    </div>
                    <div class="flex items-center gap-1">
                        @if (!$files->onFirstPage())
                            <a href="{{ $files->previousPageUrl() }}" class="px-3 py-1 bg-[#0A749B] text-white rounded">Sebelumnya</a>
                        @endif
                        @foreach ($files->getUrlRange(1, $files->lastPage()) as $page => $url)
                            @if ($page == $files->currentPage())
                                <span class="px-3 py-1 bg-[#0A749B] text-white rounded">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-1 rounded {{ $page == $files->currentPage() ? 'bg-[#0A749B] text-white' : 'bg-white text-[#0A749B] border border-[#0A749B]' }}">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if ($files->hasMorePages())
                            <a href="{{ $files->nextPageUrl() }}" class="px-3 py-1 bg-[#0A749B] text-white rounded">Selanjutnya</a>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Event delegation untuk tombol hapus
function handleDeleteClick(e) {
    if (e.target.classList.contains('delete-btn')) {
        const btn = e.target;
        const id = btn.dataset.id;
        const row = btn.closest('tr');
        if (!confirm('Yakin ingin menghapus data dan file ini?')) return;
        fetch(`/pemeliharaan/pengajuan-material/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                row.remove();
                alert('Data berhasil dihapus.');
            } else {
                alert('Gagal menghapus data.');
            }
        })
        .catch(() => alert('Gagal menghapus data.'));
    }
}
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.querySelector('table tbody');
    if (tbody) {
        tbody.addEventListener('click', handleDeleteClick);
    }
});
</script>
