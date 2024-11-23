@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md">
        <div class="p-4">
            <h2 class="text-xl font-bold text-blue-600">Pantera</h2>
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
        <h1 class="text-2xl font-bold">Dukungan</h1>
        <p>Jika Anda memerlukan bantuan, silakan hubungi tim dukungan kami:</p>
        
        <div class="mt-4">
            <h2 class="text-lg font-semibold">Kontak Tim Dukungan</h2>
            <p>Email: <a href="mailto:support@example.com" class="text-blue-500 hover:underline">support@example.com</a></p>
            <p>Telepon: <span class="text-gray-700">+62 123 456 789</span></p>
        </div>

        <div class="mt-4">
            <h2 class="text-lg font-semibold">FAQ</h2>
            <p>Berikut adalah beberapa pertanyaan yang sering diajukan:</p>
            <ul class="mt-2">
                <li><strong>Bagaimana cara mengatur akun saya?</strong> <br> Anda dapat mengatur akun Anda melalui halaman pengaturan di dashboard.</li>
                <li><strong>Di mana saya bisa menemukan dokumentasi?</strong> <br> Dokumentasi lengkap dapat ditemukan di halaman dokumentasi.</li>
                <li><strong>Bagaimana cara menghubungi dukungan?</strong> <br> Anda dapat menghubungi dukungan melalui email atau telepon yang tertera di atas.</li>
            </ul>
        </div>
    </div>
</div>
@endsection