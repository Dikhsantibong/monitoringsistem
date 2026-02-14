@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.pemeliharaan-sidebar')
    <div class="flex-1 main-content">
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
                    <h1 class="text-xl font-semibold text-gray-800">Dashboard Pemeliharaan</h1>
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
        <main class="px-6 pt-6">
            <div class="mb-4 flex justify-end">
                <form method="GET" action="" class="flex gap-2">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari WO..." class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Cari</button>
                    @if(!empty($search))
                        <a href="{{ route('pemeliharaan.wo-wmatl.index') }}" class="px-4 py-2 rounded border">Reset</a>
                    @endif
                </form>
            </div>
            <div class="bg-white rounded shadow p-4 overflow-x-auto">
                <table class="min-w-full table-fixed divide-y divide-gray-200 border whitespace-nowrap">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">ID WO</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Labor</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jadwal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workOrders as $wo)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-center border border-gray-200">{{ $loop->iteration + ($workOrdersPaginator ? ($workOrdersPaginator->currentPage() - 1) * $workOrdersPaginator->perPage() : 0) }}</td>
                            <td class="px-4 py-2 border border-gray-200 text-center">
                                <a href="{{ route('pemeliharaan.wo-wmatl.edit', $wo->id) }}" class="inline-block px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                            <td class="px-4 py-2 border border-gray-200">{{ $wo->id }}</td>
                            <td class="px-4 py-2 border border-gray-200 max-w-[200px] overflow-hidden text-ellipsis whitespace-nowrap">{{ $wo->description }}</td>
                            <td class="px-4 py-2 border border-gray-200 text-center">
                                <span class="px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">{{ $wo->status }}</span>
                            </td>
                            <td class="px-4 py-2 border border-gray-200">{{ $wo->labor }}</td>
                            <td class="px-4 py-2 border border-gray-200">{{ $wo->schedule_start }} - {{ $wo->schedule_finish }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Tidak ada WO Material dengan status WMATL.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-end">
                @if($workOrdersPaginator)
                    {{ $workOrdersPaginator->appends(['search' => $search])->links() }}
                @endif
            </div>
        </main>
    </div>
</div>
@endsection
