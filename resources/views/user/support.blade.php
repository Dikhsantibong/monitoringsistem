@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        @include('components.user-sidebar')

        <!-- Main Content -->
        <div id="main-content" class="flex-1 overflow-auto">
            <!-- Header -->
            <header class="bg-white shadow-sm sticky top-0">
                <div class="flex justify-between items-center px-6 py-3">
                    <div class="flex items-center gap-x-3">
                        <button id="mobile-menu-toggle"
                            class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#009BB9] hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                            aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true" data-slot="icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                        <!--  Menu Toggle Sidebar-->
                        <button id="desktop-menu-toggle"
                            class="hidden md:block relative items-center justify-center rounded-md text-gray-400 hover:bg-[#009BB9] p-2 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                            aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true" data-slot="icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800">Support</h1>
                    </div>
                    <!-- Mobile Menu Toggle -->

                    <div class="relative">
                        <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                            <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                                class="w-8 h-8 rounded-full mr-2">
                            <span class="text-gray-700">{{ Auth::user()->name }}</span>
                            <i class="fas fa-caret-down ml-2"></i>
                        </button>
                        <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                            <a href="{{ route('user.profile') }}"
                                class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
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
            <div class="flex-1 px-6">
                @include('layouts.breadcrumbs', ['breadcrumbs' => [['title' => 'Support']]])
                <h1 class="text-2xl font-semibold text-gray-800 mb-1">Dukungan</h1>
                <p class="text-gray-700 mb-3">Jika Anda memerlukan bantuan, silakan hubungi tim dukungan kami:</p>

                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800">Kontak Tim Dukungan</h2>
                    <p>Email: <a href="mailto:support@example.com"
                            class="text-blue-600 hover:underline">support@example.com</a></p>
                    <p>Telepon: <span class="text-gray-800">+62 123 456 789</span></p>
                </div>

                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800">FAQ</h2>
                    <p>Berikut adalah beberapa pertanyaan yang sering diajukan:</p>
                    <ul class="mt-2 list-disc list-inside text-gray-700">
                        <li><strong>Bagaimana cara mengatur akun saya?</strong> <br> Anda dapat mengatur akun Anda melalui
                            halaman pengaturan di dashboard.</li>
                        <li><strong>Di mana saya bisa menemukan dokumentasi?</strong> <br> Dokumentasi lengkap dapat
                            ditemukan di halaman dokumentasi.</li>
                        <li><strong>Bagaimana cara menghubungi dukungan?</strong> <br> Anda dapat menghubungi dukungan
                            melalui email atau telepon yang tertera di atas.</li>
                    </ul>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800">Hubungi Admin</h2>
                    <a href="{{ route('admin.dashboard') }}"
                        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 inline-block mt-2">
                        <i class="fas fa-user-cog mr-2"></i>Hubungi Admin
                    </a>
                </div>
            </div>
        </div>
        <script src="{{ asset('js/toggle.js') }}"></script>
    @endsection
