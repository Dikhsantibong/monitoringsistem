@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md">
        <div class="p-4">
            <h2 class="text-xl font-bold text-blue-600">PLN NUSANTARA POWER KENDARI</h2>
        </div>
        <nav class="mt-4">
            <a href="{{ route('user.dashboard') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('user.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                <i class="fas fa-home mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('daily.meeting') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('daily.meeting') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                <i class="fas fa-users mr-3"></i>
                <span>Daily Meeting</span>
            </a>
            <a href="{{ route('monitoring') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('monitoring') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                <i class="fas fa-chart-line mr-3"></i>
                <span>Monitoring</span>
            </a>
            <a href="{{ route('documentation') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('documentation') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                <i class="fas fa-book mr-3"></i>
                <span>Documentation</span>
            </a>
            <a href="{{ route('support') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('support') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                <i class="fas fa-headset mr-3"></i>
                <span>Support</span>
            </a>
        </nav>
    </aside>

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