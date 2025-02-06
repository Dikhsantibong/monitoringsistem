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
                        <!-- Mobile Menu Toggle -->
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
                        <h1 class="text-xl font-semibold text-gray-800">Documentation</h1>
                    </div>

                    <div class="flex items-center">
                        <div class="relative">
                            <button class="flex items-center" onclick="showLogoutConfirmation()">
                                <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                                    class="w-8 h-8 rounded-full mr-2">
                                <span class="text-gray-700">{{ Auth::user()->name }}</span>
                                <i class="fas fa-caret-down ml-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <div class="flex-1 px-6">
                @include('layouts.breadcrumbs', ['breadcrumbs' => [['title' => 'Documentation']]])
                <h1 class="text-2xl font-semibold">Dokumentasi</h1>
                <p>Berikut adalah galeri foto dokumentasi:</p>

                <div class="mt-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- @foreach ($photos as $photo)
                <div class="bg-white shadow-md p-4 rounded-lg">
                    <img src="{{ asset('storage/' . $photo->path) }}" class="w-full h-auto mb-4">
                    <p class="text-gray-700">{{ $photo->description }}</p>
                </div>
                @endforeach --}}
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ asset('js/toggle.js') }}"></script>
    @endsection
