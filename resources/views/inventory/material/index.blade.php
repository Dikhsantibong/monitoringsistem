@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.inventory-sidebar')

    <div id="main-content" class="flex-1 main-content">
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
                    <h1 class="text-xl font-semibold text-gray-800">Dashboard Inventory</h1>
                </div>
                <div class="flex items-center gap-x-4 relative">
                    <!-- User Dropdown -->
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
            </div>
        </header>

        <div class="p-6">
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Daftar Material Inventory</h2>
                    <!-- Toolbar: Search -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div></div>
                        <div class="border rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Pencarian</h3>
                            <form method="GET" action="{{ route('inventory.material.index') }}" class="flex gap-2">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari code, deskripsi, kategori..." class="flex-1 border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Cari</button>
                                @if(request('search'))
                                    <a href="{{ route('inventory.material.index') }}" class="px-4 py-2 rounded border">Reset</a>
                                @endif
                            </form>
                        </div>
                    </div>
                    @if($lastUpdate)
                        <div class="mb-4 text-sm text-gray-600">
                            <span class="font-semibold">Terakhir diupdate:</span> {{ \Carbon\Carbon::parse($lastUpdate)->translatedFormat('d F Y H:i') }}
                        </div>
                    @endif
                    <!-- Tabel Data Material Inventory -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 border-b text-center bg-gray-100 border-r">No</th>
                                        <th class="px-4 py-2 border-b text-center bg-gray-100 border-r">Inventory Statistic Code</th>
                                        <th class="px-4 py-2 border-b text-center bg-gray-100 border-r">Inventory Statistic Desc</th>
                                        <th class="px-4 py-2 border-b text-center bg-gray-100 border-r">Stock Code</th>
                                        <th class="px-4 py-2 border-b text-center bg-gray-100 border-r">Description</th>
                                        <th class="px-4 py-2 border-b text-center bg-gray-100 border-r">Quantity</th>
                                        <th class="px-4 py-2 border-b text-center bg-gray-100 border-r">Inventory Price</th>
                                        <th class="px-4 py-2 border-b text-center bg-gray-100 border-r">Inventory Value</th>
                                        <th class="px-4 py-2 border-b text-center bg-gray-100 border-r">Waktu Update</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($materials as $material)
                                        <tr>
                                            <td class="px-4 py-2 border-b border-r border-gray-200">{{ $loop->iteration }}</td>
                                            <td class="px-4 py-2 border-b border-r border-gray-200">{{ $material->inventory_statistic_code }}</td>
                                            <td class="px-4 py-2 border-b border-r border-gray-200 " style="width: 200px;">{{ $material->inventory_statistic_desc }}</td>
                                            <td class="px-4 py-2 border-b border-r border-gray-200">{{ $material->stock_code }}</td>
                                            <td class="px-4 py-2 border-b border-r border-gray-200">{{ $material->description }}</td>
                                            <td class="px-4 py-2 border-b border-r border-gray-200 text-right">{{ $material->quantity }}</td>
                                            <td class="px-4 py-2 border-b border-r border-gray-200 text-right">{{ $material->inventory_price }}</td>
                                            <td class="px-4 py-2 border-b border-r border-gray-200 text-right">{{ $material->inventory_value }}</td>
                                            <td class="px-4 py-2 border-b border-r border-gray-200 text-right">{{ $material->updated_at }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-2 text-center text-gray-500">Belum ada data material master.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection
