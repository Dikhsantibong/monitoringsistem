@extends('layouts.app')

@section('content')

<style>
    #sidebar {
    width: 16rem; /* Default width */
}
#sidebar.collapsed {
    width: 4rem; /* Width in collapsed state */
}
.sidebar-text {
    display: inline-block; /* Show text by default */
}
#sidebar.collapsed .sidebar-text {
    display: none; /* Hide text when collapsed */
}
#sidebar.collapsed .mr-3 {
    margin-right: 0; /* Remove icon margin when collapsed */
}

</style>
<div class="flex h-screen bg-gray-50 overflow-auto">
    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 bg-[#0A749B] shadow-md text-white transition-all duration-300">
        <div class="p-4 flex justify-between items-center">
            <img src="{{ asset('logo/navlogo.png') }}" alt="Logo" class="w-40 h-15">
            <button id="toggleSidebar" class="text-white text-xl focus:outline-none">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <nav id="sidebarContent" class="mt-4">
            <!-- Navigasi Sidebar -->
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-home mr-3"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>
            <a href="{{ route('admin.pembangkit.ready') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.pembangkit.ready') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-check mr-3"></i>
                <span class="sidebar-text">Kesiapan Pembangkit</span>
            </a>
            <a href="{{ route('admin.laporan.sr_wo') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.laporan.sr_wo') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-file-alt mr-3"></i>
                <span class="sidebar-text">Laporan SR/WO</span>
            </a>
            <a href="{{ route('admin.machine-monitor') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.machine-monitor') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-cogs mr-3"></i>
                <span class="sidebar-text">Monitor Mesin</span>
            </a>
            <a href="{{ route('admin.daftar_hadir.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.daftar_hadir.index') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-list mr-3"></i>
                <span class="sidebar-text">Daftar Hadir</span>
            </a>
            <a href="{{ route('admin.score-card.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.score-card.*') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-clipboard-list mr-3"></i>
                <span class="sidebar-text">Score Card Daily</span>
            </a>
            <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.users') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-users mr-3"></i>
                <span class="sidebar-text">Manajemen Pengguna</span>
            </a>
            <a href="{{ route('admin.meetings') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.meetings') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-chart-bar mr-3"></i>
                <span class="sidebar-text">Laporan Rapat</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.settings') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-cog mr-3"></i>
                <span class="sidebar-text">Pengaturan</span>
            </a>
        </nav>
    </aside>
    <!-- Main Content -->
    <div class="flex-1 overflow-x-hidden overflow-y-auto">
        <!-- Konten utama -->
        <div class="container mx-auto px-6 py-8">
            <h3 class="text-gray-700 text-3xl font-medium">Tambah Mesin Baru</h3>

            <div class="mt-8">
                <form id="createMachineForm" action="{{ route('admin.machine-monitor.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    @csrf
                    
                    <!-- Nama Mesin -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                            Nama Mesin <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                               required>
                    </div>

                    <!-- Kode Mesin -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="code">
                            Kode Mesin <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="code" 
                               id="code" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                               required>
                    </div>

                    <!-- Kategori Mesin -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="category_id">
                            Kategori Mesin <span class="text-red-500">*</span>
                        </label>
                        <select name="category_id" 
                                id="category_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                            <option value="SEO">SEO</option>
                        </select>
                    </div>

                    <!-- Lokasi -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="location">
                            Lokasi Mesin <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="location" 
                               id="location" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                               required>
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" 
                                id="status" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                required>
                            <option value="">Pilih Status</option>
                            <option value="START">Start</option>
                            <option value="STOP">Stop</option>
                            <option value="PARALLEL">Parallel</option>
                        </select>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                            Deskripsi
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                  rows="3"></textarea>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="flex items-center justify-between">
                        <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Simpan Mesin
                        </button>
                        <a href="{{ route('admin.machine-monitor') }}" 
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
document.getElementById('createMachineForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const data = {
        _token: '{{ csrf_token() }}',
        name: document.getElementById('name').value,
        code: document.getElementById('code').value,
        category_id: document.getElementById('category_id').value,
        location: document.getElementById('location').value,
        status: document.getElementById('status').value,
        description: document.getElementById('description').value
    };

    fetch(this.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Berhasil!',
                text: data.message,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '{{ route("admin.machine-monitor") }}';
            });
        } else {
            Swal.fire({
                title: 'Gagal!',
                html: data.message.split('\n').join('<br>'),
                icon: 'error'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Terjadi kesalahan saat menambahkan mesin',
            icon: 'error'
        });
    });
});

// Tambahkan logika untuk mengubah ukuran sidebar dan menyembunyikan teks pada mode ikon.
const toggleSidebarButton = document.getElementById('toggleSidebar');
const sidebar = document.getElementById('sidebar');
const sidebarContent = document.getElementById('sidebarContent');
const sidebarTexts = document.querySelectorAll('.sidebar-text');

toggleSidebarButton.addEventListener('click', () => {
    // Toggle ukuran sidebar
    sidebar.classList.toggle('w-16');
    sidebar.classList.toggle('w-64');

    // Toggle teks di dalam sidebar
    sidebarTexts.forEach(text => {
        if (sidebar.classList.contains('w-16')) {
            text.classList.add('hidden');
        } else {
            text.classList.remove('hidden');
        }
    });

    // Geser konten ke kiri
    sidebar.classList.toggle('ml-0');
    sidebar.classList.toggle('ml-64');
});
</script>
@push('scripts')
@endpush
@endsection
