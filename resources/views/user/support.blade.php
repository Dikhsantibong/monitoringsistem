@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-yellow-500 shadow-lg hidden md:block">
        <div class="p-4">
            <h2 class="text-xl font-bold text-blue-600">PLN NUSANTARA POWER KENDARI</h2>
        </div>
        <nav class="mt-4">
            <a href="{{ route('user.dashboard') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('user.dashboard') ? 'bg-yellow-500 text-blue-700' : 'text-gray-600 hover:bg-yellow-500' }}">
                <i class="fas fa-home mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('user.machine.monitor') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-yellow-500">
                <i class="fas fa-cogs mr-3"></i>
                <span>Machine Monitor</span>
            </a>
            <a href="{{ route('daily.meeting') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('daily.meeting') ? 'bg-yellow-500 text-blue-700' : 'text-gray-600 hover:bg-yellow-500' }}">
                <i class="fas fa-users mr-3"></i>
                <span>Daily Meeting</span>
            </a>
            <a href="{{ route('monitoring') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('monitoring') ? 'bg-yellow-500 text-blue-700' : 'text-gray-600 hover:bg-yellow-500' }}">
                <i class="fas fa-chart-line mr-3"></i>
                <span>Monitoring</span>
            </a>
            <a href="{{ route('documentation') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('documentation') ? 'bg-yellow-500 text-blue-700' : 'text-gray-600 hover:bg-yellow-500' }}">
                <i class="fas fa-book mr-3"></i>
                <span>Documentation</span>
            </a>
            <a href="{{ route('support') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('support') ? 'bg-yellow-500 text-blue-700' : 'text-gray-600 hover:bg-yellow-500' }}">
                <i class="fas fa-headset mr-3"></i>
                <span>Support</span>
            </a>
        </nav>
    </aside>


    <div class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="flex justify-between items-center px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">Support</h1>
                <div class="relative">
                    <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                        <img src="{{ Auth::user()->avatar ?? asset('images/default-avatar.png') }}" 
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

    <!-- Main Content -->
    <div class="flex-1 p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Dukungan</h1>
        <p class="text-gray-700 mb-6">Jika Anda memerlukan bantuan, silakan hubungi tim dukungan kami:</p>
        
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800">Kontak Tim Dukungan</h2>
            <p>Email: <a href="mailto:support@example.com" class="text-blue-600 hover:underline">support@example.com</a></p>
            <p>Telepon: <span class="text-gray-800">+62 123 456 789</span></p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800">FAQ</h2>
            <p>Berikut adalah beberapa pertanyaan yang sering diajukan:</p>
            <ul class="mt-2 list-disc list-inside text-gray-700">
                <li><strong>Bagaimana cara mengatur akun saya?</strong> <br> Anda dapat mengatur akun Anda melalui halaman pengaturan di dashboard.</li>
                <li><strong>Di mana saya bisa menemukan dokumentasi?</strong> <br> Dokumentasi lengkap dapat ditemukan di halaman dokumentasi.</li>
                <li><strong>Bagaimana cara menghubungi dukungan?</strong> <br> Anda dapat menghubungi dukungan melalui email atau telepon yang tertera di atas.</li>
            </ul>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800">Hubungi Admin</h2>
            <a href="{{ route('admin.dashboard') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 inline-block mt-2">
                <i class="fas fa-user-cog mr-2"></i>Hubungi Admin
            </a>
        </div>
    </div>
</div>
@endsection