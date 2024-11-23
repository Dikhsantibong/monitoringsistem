@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md">
        <div class="p-4">
            <h2 class="text-xl font-bold text-blue-600">Pantera</h2>
        </div>
        <nav class="mt-4">
            <a href="#" class="flex items-center px-4 py-3 bg-blue-50 text-blue-700">
                <i class="fas fa-home mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-gray-600 hover:bg-blue-50">
                <i class="fas fa-users mr-3"></i>
                <span>Daily Meeting</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-gray-600 hover:bg-blue-50">
                <i class="fas fa-chart-line mr-3"></i>
                <span>Monitoring</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-gray-600 hover:bg-blue-50">
                <i class="fas fa-book mr-3"></i>
                <span>Documentation</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-gray-600 hover:bg-blue-50">
                <i class="fas fa-headset mr-3"></i>
                <span>Support</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="flex justify-between items-center px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">Dashboard</h1>
                <div class="flex items-center">
                    <div class="relative">
                        <button class="flex items-center" onclick="showLogoutConfirmation()">
                            <img src="{{ Auth::user()->avatar ?? asset('images/default-avatar.png') }}" 
                                 class="w-8 h-8 rounded-full mr-2">
                            <span class="text-gray-700">{{ Auth::user()->name }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="p-6">
            <!-- Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-600 text-sm font-medium">Progress Harian</h3>
                    <p class="text-2xl font-bold text-gray-800 mt-2">85%</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-600 text-sm font-medium">Status Aktivitas</h3>
                    <p class="text-2xl font-bold text-green-500 mt-2">Aktif</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-600 text-sm font-medium">Notifikasi</h3>
                    <p class="text-2xl font-bold text-blue-500 mt-2">3 Baru</p>
                </div>
            </div>

            <!-- Calendar & Tasks -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Jadwal Meeting</h3>
                    <div id="calendar" class="min-h-[300px]">
                        <!-- Calendar widget akan dirender di sini -->
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Tugas Saat Ini</h3>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" class="mr-3">
                            <span class="text-gray-700">Review dokumen project</span>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" class="mr-3">
                            <span class="text-gray-700">Meeting tim development</span>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" class="mr-3">
                            <span class="text-gray-700">Update status project</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Fungsi untuk konfirmasi logout
    function showLogoutConfirmation() {
        Swal.fire({
            title: 'Apakah Anda yakin ingin keluar?',
            text: "Anda akan keluar dari sistem",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Keluar!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("logout") }}';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                form.appendChild(csrfToken);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Notifikasi saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session("success") }}',
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session("error") }}',
                timer: 3000,
                timerProgressBar: true
            });
        @endif
    });
</script>
@endpush
