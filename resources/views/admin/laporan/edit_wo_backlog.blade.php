@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <!-- Sidebar -->
    @include('components.sidebar')

    <!-- Main Content -->
    <div id="main-content" class="flex-1 main-content">
        <header class="bg-white shadow-sm">
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

                    <!-- Menu Toggle Sidebar -->
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
                    <h1 class="text-xl font-semibold text-gray-800">Edit WO Backlog</h1>
                </div>

                @include('components.timer')
                <div class="relative">
                    <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                        <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                            class="w-7 h-7 rounded-full mr-2">
                        <span class="text-gray-700 text-sm">{{ Auth::user()->name }}</span>
                        <i class="fas fa-caret-down ml-2 text-gray-600"></i>
                    </button>
                    <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                        <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="pt-2">
            <x-admin-breadcrumb :breadcrumbs="[
                ['name' => 'Laporan SR/WO', 'url' => route('admin.laporan.sr_wo')],
                ['name' => 'Edit WO Backlog', 'url' => null]
            ]" />
        </div>

        <main class="px-6">
            <!-- Konten Edit WO Backlog -->
            <div class="bg-white rounded-lg shadow p-6 sm:p-3">
                <div class="pt-2">
                    <h2 class="text-2xl font-bold mb-4">Edit Work Order (WO) Backlog</h2>
                    <form action="{{ route('admin.laporan.update-wo-backlog', $backlog->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="no_wo" class="block text-gray-700">No WO</label>
                            <input type="text" name="no_wo" id="no_wo" 
                                value="{{ $backlog->no_wo }}"
                                class="w-full px-3 py-2 border rounded-md bg-gray-100"
                                readonly>
                        </div>

                        <div class="mb-4">
                            <label for="deskripsi" class="block text-gray-700">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" 
                                class="w-full px-3 py-2 border rounded-md" 
                                required>{{ $backlog->deskripsi }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="keterangan" class="block text-gray-700">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" 
                                class="w-full px-3 py-2 border rounded-md">{{ $backlog->keterangan }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="status" class="block text-gray-700">Status</label>
                            <select name="status" id="status" 
                                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="Open" {{ $backlog->status == 'Open' ? 'selected' : '' }}>Open</option>
                                <option value="Closed" {{ $backlog->status == 'Closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('admin.laporan.sr_wo') }}" 
                                class="bg-gray-500 text-white px-4 py-2 rounded-lg flex items-center">
                                <i class="fas fa-arrow-left mr-2"></i> Kembali
                            </a>
                            <button type="submit" 
                                class="bg-blue-500 text-white px-4 py-2 rounded-lg flex items-center">
                                <i class="fas fa-save mr-2"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

<script src="{{ asset('js/toggle.js') }}"></script> 