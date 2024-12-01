@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-yellow-500 shadow-lg hidden md:block">
        <div class="p-4">
            <h2 class="text-xl font-bold text-blue-600">PLN NUSANTARA POWER KENDARI</h2>
        </div>
        <nav class="mt-4">
            <a href="{{ route('user.dashboard') }}" class="flex items-center px-4 py-3 bg-yellow-500 text-blue-700">
                <i class="fas fa-home mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('user.machine.monitor') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-yellow-500">
                <i class="fas fa-cogs mr-3"></i>
                <span>Machine Monitor</span>
            </a>
            <a href="{{ route('daily.meeting') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-yellow-500">
                <i class="fas fa-users mr-3"></i>
                <span>Daily Meeting</span>
            </a>
            <a href="{{ route('monitoring') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-yellow-500">
                <i class="fas fa-chart-line mr-3"></i>
                <span>Monitoring</span>
            </a>
            <a href="{{ route('documentation') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-yellow-500">
                <i class="fas fa-book mr-3"></i>
                <span>Documentation</span>
            </a>
            <a href="{{ route('support') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-yellow-500">
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
                <div class="relative">
                    <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                        <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin.png') }}" 
                             class="w-8 h-8 rounded-full mr-2">
                        <span class="text-gray-700">{{ Auth::user()->name }}</span>
                        <i class="fas fa-caret-down ml-2"></i>
                    </button>
                    <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                        <a href="{{ route('user.profile') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
                        <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="p-6">
            <!-- Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-yellow-500 rounded-lg shadow p-6">
                    <h3 class="text-white text-sm font-medium">Progress Harian</h3>
                    <p class="text-2xl font-bold text-white mt-2">85%</p>
                </div>
                <div class="bg-green-500 rounded-lg shadow p-6">
                    <h3 class="text-white text-sm font-medium">Status Aktivitas</h3>
                    <p class="text-2xl font-bold text-white mt-2">Aktif</p>
                </div>
                <div class="bg-blue-500 rounded-lg shadow p-6">
                    <h3 class="text-white text-sm font-medium">Notifikasi</h3>
                    <p class="text-2xl font-bold text-white mt-2">3 Baru</p>
                </div>
            </div>

            <!-- Calendar & Tasks -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Jadwal Meeting</h3>
                    <div id="calendar" class="min-h-[300px]">
                        <!-- Calendar widget -->
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

            <!-- Tema Dashboard -->
            <section class="mt-6">
                <h2 class="text-xl font-semibold">Tema Dashboard</h2>
                <div class="bg-white rounded-lg shadow p-4">
                    <label for="theme-toggle" class="flex items-center">
                        <input type="checkbox" id="theme-toggle" class="mr-2">
                        <span>Mode Gelap</span>
                    </label>
                </div>
            </section>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('dropdown');
        dropdown.classList.toggle('hidden');
    }

    window.addEventListener('click', function(event) {
        const dropdown = document.getElementById('dropdown');
        const toggleButton = document.getElementById('dropdownToggle');

        if (!toggleButton.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>
@endpush
