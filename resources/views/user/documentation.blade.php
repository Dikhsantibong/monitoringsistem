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
            <a href="{{ route('user.machine.monitor') }}">
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
            <a href="{{ route('documentation') }}" class="bg-yellow-500">
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
                <h1 class="text-2xl font-semibold text-gray-800">DOCUMENTATION</h1>
                <div class="flex items-center">
                    <div class="relative">
                        <button class="flex items-center" onclick="showLogoutConfirmation()">
                            <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin.png') }}" 
                                 class="w-8 h-8 rounded-full mr-2">
                            <span class="text-gray-700">{{ Auth::user()->name }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </header>


    <!-- Main Content -->
    <div class="flex-1 p-6">
        <h1 class="text-2xl font-bold">Dokumentasi</h1>
        <p>Berikut adalah galeri foto dokumentasi:</p>
        
        <div class="mt-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- @foreach($photos as $photo)
                <div class="bg-white shadow-md p-4 rounded-lg">
                    <img src="{{ asset('storage/' . $photo->path) }}" class="w-full h-auto mb-4">
                    <p class="text-gray-700">{{ $photo->description }}</p>
                </div>
                @endforeach --}}
            </div>
        </div>
    </div>
</div>
@endsection