@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    @include('components.pemeliharaan-sidebar')
    <!-- Main Content -->
    <div id="main-content" class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-10">
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
                    <h1 class="text-xl font-semibold text-gray-800">Master Labor</h1>
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
        <script>
            function toggleDropdown() {
                var dropdown = document.getElementById('dropdown');
                dropdown.classList.toggle('hidden');
            }
            document.addEventListener('click', function(event) {
                var userDropdown = document.getElementById('dropdown');
                var userBtn = document.getElementById('dropdownToggle');
                if (userDropdown && !userDropdown.classList.contains('hidden') && !userBtn.contains(event.target) && !userDropdown.contains(event.target)) {
                    userDropdown.classList.add('hidden');
                }
            });
        </script>
        <!-- Main Content -->
        <main class="px-6 pt-6">
            <div class="container mx-auto">
                @if(session('success'))
                    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
                @endif
                <form action="{{ route('pemeliharaan.master-labor.store') }}" method="POST" class="mb-6 bg-white p-4 rounded shadow">
                    @csrf
                    <div class="mb-2">
                        <label class="block text-gray-700">Nama Labor</label>
                        <input type="text" name="nama" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div class="mb-2">
                        <label class="block text-gray-700">Bidang</label>
                        <select name="bidang" class="w-full border rounded px-3 py-2" required>
                            <option value="">Pilih Bidang</option>
                            <option value="listrik">Listrik</option>
                            <option value="mesin">Mesin</option>
                            <option value="kontrol">Kontrol</option>
                            <option value="alat bantu">Alat Bantu</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Tambah Labor</button>
                </form>
                <div class="bg-white rounded shadow p-4">
                    <h2 class="text-lg font-semibold mb-2">Daftar Labor</h2>
                    <table class="w-full table-auto">
                        <thead>
                            <tr>
                                <th class="border px-2 py-1 text-center">No</th>
                                <th class="border px-2 py-1 text-center">Nama</th>
                                <th class="border px-2 py-1 text-center">Bidang</th>
                                <th class="border px-2 py-1 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($labors as $labor)
                            <tr>
                                <td class="border px-2 py-1 text-center">{{ $loop->iteration }}</td>
                                <td class="border px-2 py-1">{{ $labor->nama }}</td>
                                <td class="border px-2 py-1 capitalize">{{ $labor->bidang }}</td>
                                <td class="border px-2 py-1 text-center">
                                    <button type="button" class="bg-yellow-400 text-white px-2 py-1 rounded hover:bg-yellow-500 text-xs btn-edit-labor" data-id="{{ $labor->id }}" data-nama="{{ $labor->nama }}" data-bidang="{{ $labor->bidang }}">Edit</button>
                                    <form action="{{ route('pemeliharaan.master-labor.destroy', $labor->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus labor ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 text-xs">Hapus</button>
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
<!-- Modal Edit Labor -->
<div id="editLaborModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-lg font-bold mb-4">Edit Labor</h2>
        <form id="editLaborForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="editLaborId">
            <div class="mb-2">
                <label class="block text-gray-700">Nama Labor</label>
                <input type="text" name="nama" id="editLaborNama" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-2">
                <label class="block text-gray-700">Bidang</label>
                <select name="bidang" id="editLaborBidang" class="w-full border rounded px-3 py-2" required>
                    <option value="">Pilih Bidang</option>
                    <option value="listrik">Listrik</option>
                    <option value="mesin">Mesin</option>
                    <option value="kontrol">Kontrol</option>
                    <option value="alat bantu">Alat Bantu</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeEditLaborModal()" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Batal</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
function closeEditLaborModal() {
    document.getElementById('editLaborModal').classList.add('hidden');
}
document.querySelectorAll('.btn-edit-labor').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('editLaborId').value = this.dataset.id;
        document.getElementById('editLaborNama').value = this.dataset.nama;
        document.getElementById('editLaborBidang').value = this.dataset.bidang;
        document.getElementById('editLaborForm').action = '/pemeliharaan/master-labor/' + this.dataset.id + '/update';
        document.getElementById('editLaborModal').classList.remove('hidden');
    });
});
</script>
@endpush
@endsection
