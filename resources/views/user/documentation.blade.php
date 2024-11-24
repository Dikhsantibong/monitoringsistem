@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-yellow-500 shadow-lg">
        <div class="p-4">
            <h2 class="text-xl font-bold text-blue-600">PLN NUSANTARA POWER KENDARI</h2>
        </div>
        <nav class="mt-4">
            <a href="{{ route('user.dashboard') }}" class="flex items-center px-4 py-3 bg-yellow-500 text-blue-700">
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
            <a href="{{ route('documentation') }}" class="flex items-center px-4 py-3 bg-yellow-500 text-blue-700">
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
                <h1 class="text-2xl font-semibold text-gray-800">DOCUMENTATION</h1>
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