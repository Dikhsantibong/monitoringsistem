@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
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
            <h3 class="text-gray-700 text-3xl font-medium">Hapus Pengguna</h3>

            <div class="mt-8">
                <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <div class="mb-6">
                        <h4 class="text-xl font-semibold text-gray-700 mb-4">Konfirmasi Penghapusan</h4>
                        <p class="text-gray-600 mb-4">
                            Anda akan menghapus pengguna berikut:
                        </p>
                        
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-600">Nama:</p>
                                    <p class="text-gray-800">{{ $user->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-600">Email:</p>
                                    <p class="text-gray-800">{{ $user->email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-600">Role:</p>
                                    <p class="text-gray-800">{{ ucfirst($user->role) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-600">Dibuat pada:</p>
                                    <p class="text-gray-800">{{ $user->created_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-red-500"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">
                                        Peringatan: Tindakan ini tidak dapat dibatalkan. Semua data terkait pengguna ini akan dihapus secara permanen.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" id="deleteForm">
                            @csrf
                            @method('DELETE')
                            
                            <div class="flex items-center justify-between">
                                <button type="button" 
                                        onclick="confirmDelete()"
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Hapus Pengguna
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
    </div>
</div>
@endsection


<script>
// Pastikan SweetAlert2 sudah dimuat
document.addEventListener('DOMContentLoaded', function() {
    window.confirmDelete = function() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Tindakan ini tidak dapat dibatalkan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit form
                document.getElementById('deleteForm').submit();
            }
        });
    }
});

// Tampilkan pesan sukses/error seperti sebelumnya
@if(session('success'))
    Swal.fire({
        title: 'Berhasil!',
        text: '{{ session("success") }}',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
    }).then(() => {
        window.location.href = '{{ route("admin.users") }}';
    });
@endif

@if(session('error'))
    Swal.fire({
        title: 'Error!',
        text: '{{ session("error") }}',
        icon: 'error',
        confirmButtonText: 'OK'
    });
@endif
</script>
@push('scripts')