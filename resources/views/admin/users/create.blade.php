@extends('layouts.app')
@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
   <!-- Sidebar -->
   <aside class="w-64 bg-[#0A749B] shadow-md">
    <div class="p-4">
        <img src="{{ asset('logo/navlogo.png') }}" alt="Logo Aplikasi Rapat Harian" class="w-40 h-15">
    </div>
    <nav class="mt-4">
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
            <i class="fas fa-home mr-3"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.score-card.index') }}"
            class="flex items-center px-4 py-3 {{ request()->routeIs('admin.score-card.*') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
            <i class="fas fa-clipboard-list mr-3"></i>
            <span>Score Card Daily</span>
        </a>
        <a href="{{ route('admin.daftar_hadir.index') }}"
            class="flex items-center px-4 py-3 {{ request()->routeIs('admin.daftar_hadir.index') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
            <i class="fas fa-list mr-3"></i>
            <span>Daftar Hadir</span>
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
   <div class="flex-1 overflow-x-hidden overflow-y-auto">
       <div class="container mx-auto px-6 py-8">
           <header class="bg-white shadow-sm">
               <div class="flex justify-between items-center px-6 py-4">
                   <h1 class="text-2xl font-semibold text-gray-800">Tambah Pengguna Baru</h1>
               </div>
               <x-admin-breadcrumb :breadcrumbs="[
                   ['name' => 'Manajemen Pengguna', 'url' => route('admin.users')],
                   ['name' => 'Tambah Pengguna', 'url' => null]
               ]" />
           </header>
            <div class="mt-8">
               <form action="{{ route('admin.users.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                   @csrf
                    <div class="mb-4">
                       <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                           Nama
                       </label>
                       <input type="text" 
                              name="name" 
                              id="name" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                              value="{{ old('name') }}"
                              required>
                       @error('name')
                           <p class="text-red-500 text-xs italic">{{ $message }}</p>
                       @enderror
                   </div>
                    <div class="mb-4">
                       <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                           Email
                       </label>
                       <input type="email" 
                              name="email" 
                              id="email" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                              value="{{ old('email') }}"
                              required>
                       @error('email')
                           <p class="text-red-500 text-xs italic">{{ $message }}</p>
                       @enderror
                   </div>
                    <div class="mb-4">
                       <label class="block text-gray-700 text-sm font-bold mb-2" for="role">
                           Role
                       </label>
                       <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" 
                               id="role" 
                               name="role"
                               required>
                           <option value="user">User</option>
                           <option value="admin">Admin</option>
                       </select>
                   </div>
                    <div class="mb-4">
                       <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                           Password
                       </label>
                       <input class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                           id="password" 
                           type="password" 
                           name="password"
                           required>
                       @error('password')
                           <p class="text-red-500 text-xs italic">{{ $message }}</p>
                       @enderror
                   </div>
                    <div class="mb-6">
                       <label class="block text-gray-700 text-sm font-bold mb-2" for="password_confirmation">
                           Konfirmasi Password
                       </label>
                       <input class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                           id="password_confirmation" 
                           type="password" 
                           name="password_confirmation"
                           required>
                   </div>
                    <div class="flex items-center justify-between">
                       <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                           type="submit" onclick="return Swal.fire({
                               title: 'Konfirmasi',
                               text: 'Apakah Anda yakin ingin menambahkan pengguna baru?',
                               icon: 'question',
                               showCancelButton: true,
                               confirmButtonText: 'Ya, Tambahkan',
                               cancelButtonText: 'Batal',
                               reverseButtons: true
                           }).then((result) => {
                               if (result.isConfirmed) {
                                   return true;
                               } else {
                                   return false;
                               }
                           });">
                           Tambah Pengguna
                       </button>
                       <a href="{{ route('admin.users') }}" 
                          class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                           Batal
                       </a>
                   </div>
               </form>
           </div>
       </div>
   </div>
</div>

@endsection

@push('scripts')
<script>
document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin menambahkan pengguna baru?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Tambahkan',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Tampilkan loading
            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit form
            this.submit();
        }
    });
});

// Tampilkan pesan sukses jika ada
@if(session('success'))
    Swal.fire({
        title: 'Berhasil!',
        text: '{{ session("success") }}',
        icon: 'success',
        timer: 3000,
        showConfirmButton: false
    });
@endif

// Tampilkan pesan error jika ada
@if(session('error'))
    Swal.fire({
        title: 'Error!',
        text: '{{ session("error") }}',
        icon: 'error',
        confirmButtonText: 'OK'
    });
@endif
</script>
@endpush