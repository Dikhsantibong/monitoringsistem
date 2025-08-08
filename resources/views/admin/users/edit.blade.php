@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <!-- Sidebar -->
    @include('components.sidebar')

    <!-- Main Content -->
    <div class="flex-1 overflow-x-hidden overflow-y-auto">
    <header class="bg-white shadow-sm sticky top-0 z-10">
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

                        <h1 class="text-xl font-semibold text-gray-800">Edit Pengguna</h1>
                    </div>

                    <div class="relative">
                        <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                            <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                                class="w-7 h-7 rounded-full mr-2">
                            <span class="text-gray-700 text-sm">{{ Auth::user()->name }}</span>
                            <i class="fas fa-caret-down ml-2 text-gray-600"></i>
                        </button>
                        <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                            <a href="{{ route('logout') }}" 
                               class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                               onclick="event.preventDefault(); 
                                        document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                                <input type="hidden" name="redirect" value="{{ route('homepage') }}">
                            </form>
                        </div>
                    </div>
                </div>
            </header>

        <div class="container mx-auto px-6 py-8">
            <div class="mt-8">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                            Nama
                        </label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                            id="name" 
                            type="text" 
                            name="name" 
                            value="{{ old('name', $user->name) }}"
                            required>
                        @error('name')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                            Email
                        </label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email', $user->email) }}"
                            required>
                        @error('email')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="role">
                            Role
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                            id="role" 
                            name="role">
                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="pemeliharaan" {{ $user->role === 'pemeliharaan' ? 'selected' : '' }}>Pemeliharaan</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                            Password Baru (kosongkan jika tidak ingin mengubah)
                        </label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                            id="password" 
                            type="password" 
                            name="password">
                        @error('password')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password_confirmation">
                            Konfirmasi Password Baru
                        </label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                            id="password_confirmation" 
                            type="password" 
                            name="password_confirmation">
                    </div>

                    <div class="flex items-center justify-between">
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                            type="submit">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.users') }}" 
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 