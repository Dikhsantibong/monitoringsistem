@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <style>
        /* Sidebar */
        aside {
            background-color: #0A749B; /* Warna biru kehijauan */
            color: white;
        }
    
        /* Link di Sidebar */
        aside nav a {
            color: white; /* Teks default putih */
            display: flex;
            align-items: center;
            padding: 12px 16px;
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s; /* Animasi transisi */
        }
    
        /* Link di Sidebar saat Hover */
        aside nav a:hover {
            background-color: white; /* Latar belakang putih */
            color: black; /* Teks berubah menjadi hitam */
        }
    
        /* Aktif Link */
        aside nav a.bg-yellow-500 {
            background-color: white;
            color: #000102;
        }
    </style>
    <!-- Sidebar -->
    <aside class="w-64 shadow-lg hidden md:block">
        <div class="p-4">
            <img src="{{ asset('logo/navlogo.png') }}" alt="Logo Aplikasi" class="w-40 h-15">
        </div>
        <nav class="mt-4">
            <a href="{{ route('user.dashboard') }}" >
                <i class="fas fa-home mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('user.machine.monitor') }}" class="bg-yellow-500">
                <i class="fas fa-cogs mr-3"></i>
                <span>Machine Monitor</span>
            </a>
            <a href="{{ route('daily.meeting') }}">
                <i class="fas fa-users mr-3"></i>
                <span>Daily Meeting</span>
            </a>
            <a href="{{ route('monitoring') }}">
                <i class="fas fa-chart-line mr-3"></i>
                <span>Monitoring</span>
            </a>
            <a href="{{ route('documentation') }}">
                <i class="fas fa-book mr-3"></i>
                <span>Documentation</span>
            </a>
            <a href="{{ route('support') }}">
                <i class="fas fa-headset mr-3"></i>
                <span>Support</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0">
            <div class="flex justify-between items-center px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">Machine Monitor</h1>
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