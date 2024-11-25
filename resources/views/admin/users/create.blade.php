@extends('layouts.app')
@section('content')
<div class="flex h-screen bg-gray-50">
   <!-- Sidebar -->
   <aside class="w-64 bg-white shadow-md">
       <div class="p-4">
           <h2 class="text-xl font-bold text-blue-600">ADMIN PANEL</h2>
       </div>
       <nav class="mt-4">
           <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
               <i class="fas fa-home mr-3"></i>
               <span>Dashboard</span>
           </a>
           <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.users*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
               <i class="fas fa-users mr-3"></i>
               <span>Manajemen Pengguna</span>
           </a>
       </nav>
   </aside>
    <!-- Main Content -->
   <div class="flex-1 overflow-x-hidden overflow-y-auto">
       <div class="container mx-auto px-6 py-8">
           <h3 class="text-gray-700 text-3xl font-medium">Tambah Pengguna Baru</h3>
            <div class="mt-8">
               <form action="{{ route('admin.users.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                   @csrf
                    <div class="mb-4">
                       <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                           Nama
                       </label>
                       <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror"
                           id="name" 
                           type="text" 
                           name="name" 
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
                       <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror"
                           id="email" 
                           type="email" 
                           name="email" 
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
                       <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
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
                       <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('password') border-red-500 @enderror"
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
                       <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
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
@section('scripts')
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