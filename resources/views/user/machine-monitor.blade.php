@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-yellow-500 shadow-lg hidden md:block">
        <div class="p-4">
            <h2 class="text-xl font-bold text-blue-600">PLN NUSANTARA POWER KENDARI</h2>
        </div>
        <nav class="mt-4">
            <a href="{{ route('user.dashboard') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-yellow-500">
                <i class="fas fa-home mr-3"></i>
                <span>Dashboard</span>
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
            <a href="{{ route('user.machine.monitor') }}" class="flex items-center px-4 py-3 bg-yellow-500 text-blue-700">
                <i class="fas fa-cogs mr-3"></i>
                <span>Machine Monitor</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="flex justify-between items-center px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">Machine Monitor</h1>
                <div class="flex items-center">
                    <div class="relative">
                        <button class="flex items-center" onclick="toggleDropdown()">
                            <img src="{{ Auth::user()->avatar ?? asset('images/default-avatar.png') }}" 
                                 class="w-8 h-8 rounded-full mr-2">
                            <span class="text-gray-700">{{ Auth::user()->name }}</span>
                        </button>
                        <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                            <a href="{{ route('user.profile') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profil</a>
                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Machine Monitor Content -->
        <main class="p-6">
            <table class="min-w-full mt-4 bg-white shadow-lg rounded-lg overflow-hidden">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-gray-600 uppercase font-bold">Nama Mesin</th>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-gray-600 uppercase font-bold">Status</th>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-gray-600 uppercase font-bold">Kesehatan</th>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-gray-600 uppercase font-bold">Durasi Operasional</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($machines as $machine)
                    <tr>
                        <td class="py-2 px-4 border-b border-gray-200">{{ $machine->name }}</td>
                        <td class="py-2 px-4 border-b border-gray-200">{{ $machine->status }}</td>
                        <td class="py-2 px-4 border-b border-gray-200">{{ $machine->health_status }}</td>
                        <td class="py-2 px-4 border-b border-gray-200">{{ $machine->operational_duration }} jam</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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

    // Menutup dropdown jika klik di luar
    window.onclick = function(event) {
        if (!event.target.matches('.flex.items-center')) {
            const dropdowns = document.getElementsByClassName("absolute");
            for (let i = 0; i < dropdowns.length; i++) {
                const openDropdown = dropdowns[i];
                if (!openDropdown.classList.contains('hidden')) {
                    openDropdown.classList.add('hidden');
                }
            }
        }
    }

    const themeToggle = document.getElementById('theme-toggle');
    themeToggle.addEventListener('change', () => {
        document.body.classList.toggle('dark', themeToggle.checked);
    });
</script>
@endpush