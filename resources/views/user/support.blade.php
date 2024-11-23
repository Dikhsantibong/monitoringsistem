@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg">
        <div class="p-4">
            <h2 class="text-xl font-bold text-blue-700">PLN NUSANTARA POWER KENDARI</h2>
        </div>
        <nav class="mt-4">
            <a href="{{ route('user.dashboard') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('user.dashboard') ? 'bg-blue-100 text-blue-800' : 'text-gray-700 hover:bg-blue-100' }}">
                <i class="fas fa-home mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('daily.meeting') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('daily.meeting') ? 'bg-blue-100 text-blue-800' : 'text-gray-700 hover:bg-blue-100' }}">
                <i class="fas fa-users mr-3"></i>
                <span>Daily Meeting</span>
            </a>
            <a href="{{ route('monitoring') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('monitoring') ? 'bg-blue-100 text-blue-800' : 'text-gray-700 hover:bg-blue-100' }}">
                <i class="fas fa-chart-line mr-3"></i>
                <span>Monitoring</span>
            </a>
            <a href="{{ route('documentation') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('documentation') ? 'bg-blue-100 text-blue-800' : 'text-gray-700 hover:bg-blue-100' }}">
                <i class="fas fa-book mr-3"></i>
                <span>Documentation</span>
            </a>
            <a href="{{ route('support') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('support') ? 'bg-blue-100 text-blue-800' : 'text-gray-700 hover:bg-blue-100' }}">
                <i class="fas fa-headset mr-3"></i>
                <span>Support</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 p-6">
        <h1 class="text-2xl font-bold text-blue-700">Dukungan</h1>
        <p>Jika Anda memerlukan bantuan, silakan hubungi tim dukungan kami:</p>
        
        <div class="mt-4">
            <h2 class="text-lg font-semibold text-blue-700">Kontak Tim Dukungan</h2>
            <p>Email: <a href="mailto:support@example.com" class="text-blue-600 hover:underline">support@example.com</a></p>
            <p>Telepon: <span class="text-gray-800">+62 123 456 789</span></p>
        </div>

        <div class="mt-4">
            <h2 class="text-lg font-semibold text-blue-700">FAQ</h2>
            <p>Berikut adalah beberapa pertanyaan yang sering diajukan:</p>
            <ul class="mt-2">
                <li><strong>Bagaimana cara mengatur akun saya?</strong> <br> Anda dapat mengatur akun Anda melalui halaman pengaturan di dashboard.</li>
                <li><strong>Di mana saya bisa menemukan dokumentasi?</strong> <br> Dokumentasi lengkap dapat ditemukan di halaman dokumentasi.</li>
                <li><strong>Bagaimana cara menghubungi dukungan?</strong> <br> Anda dapat menghubungi dukungan melalui email atau telepon yang tertera di atas.</li>
            </ul>
        </div>

        <div class="mt-4">
            <h2 class="text-lg font-semibold text-blue-700">Hubungi Admin</h2>
            <a href="{{ route('admin.dashboard') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                <i class="fas fa-user-cog mr-2"></i>Hubungi Admin
            </a>
        </div>
    </div>
</div>
@section('styles')
@endsection