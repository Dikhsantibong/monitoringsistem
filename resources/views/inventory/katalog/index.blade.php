@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.inventory-sidebar')
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
                    <h1 class="text-xl font-semibold text-gray-800">Data Pengajuan Katalog</h1>
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
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama file..." class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Cari</button>
                    @if(!empty($search))
                        <a href="{{ route('inventory.katalog.index') }}" class="px-4 py-2 rounded border">Reset</a>
                    @endif
                </form>
            </div>
            <div class="bg-white rounded-lg shadow p-6 w-full">
                <h2 class="text-lg font-semibold mb-4">Daftar Pengajuan Katalog</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 border-b text-center">No</th>
                                <th class="px-4 py-2 border-b text-center">No Part</th>
                                <th class="px-4 py-2 border-b text-center">Nama Material</th>
                                <th class="px-4 py-2 border-b text-center">Di Ajukan Oleh</th>
                                <th class="px-4 py-2 border-b text-center">Tanggal Upload</th>
                                <th class="px-4 py-2 border-b text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($files as $index => $file)
                                <tr>
                                    <td class="px-4 py-2 border-b text-center border-r">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 border-b text-center border-r">
                                        {{ $file->no_part ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 border-b text-center border-r">
                                        {{ $file->nama_material ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 border-b text-center border-r">
                                        @php
                                            // Normalize user_id: remove extra spaces and non-breaking spaces
                                            $userNameRaw = $file->user_id ?? '-';
                                            $userName = preg_replace('/\s+/u', ' ', trim(str_replace("\xc2\xa0", ' ', $userNameRaw)));
                                            $userNameUpper = strtoupper($userName);

                                            $bgMap = [
                                                'MIKUASI PEMELIHARAAN'      => 'bg-green-200 text-green-900',
                                                'SABILAMBO PEMELIHARAAN'    => 'bg-green-200 text-green-900',
                                                'WINNING PEMELIHARAAN'      => 'bg-yellow-200 text-yellow-900',
                                                'RONGI PEMELIHARAAN'        => 'bg-red-200 text-red-900',
                                                'EREKE PEMELIHARAAN'        => 'bg-pink-200 text-pink-900',
                                                'PASARWAJO PEMELIHARAAN'    => 'bg-purple-200 text-purple-900',
                                                'BAU-BAU PEMELIHARAAN'      => 'bg-indigo-200 text-indigo-900',
                                                'RAHA PEMELIHARAAN'         => 'bg-orange-200 text-orange-900',
                                                'POASIA PEMELIHARAAN'       => 'bg-teal-200 text-teal-900',
                                                'WUA-WUA PEMELIHARAAN'      => 'bg-cyan-200 text-cyan-900',
                                                'LANGARA PEMELIHARAAN'      => 'bg-lime-200 text-lime-900',
                                                'KOLAKA PEMELIHARAAN'       => 'bg-fuchsia-200 text-fuchsia-900',
                                                'INVENTORY UP KENDARI'      => 'bg-gray-300 text-gray-900',
                                            ];
                                            $bgClass = $bgMap[$userNameUpper] ?? 'bg-gray-200 text-gray-500';
                                        @endphp
                                        <span class="text-sm rounded-full px-2 py-1 {{ $bgClass }}">
                                            {{ $userName }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 border-b text-center border-r">
                                        {{ \Carbon\Carbon::parse($file->created_at)->format('d-m-Y H:i') }}
                                        @if(\Carbon\Carbon::parse($file->created_at)->gt(now()->subDay()))
                                            <span class="ml-2 inline-block bg-green-500 text-white text-xs px-2 py-1 rounded">Baru</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 border-b text-center border-r">
                                        <a href="{{ Storage::url($file->path) }}" target="_blank" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 mr-2">Lihat</a>
                                        <a href="{{ asset('storage/' . $file->path) }}" download class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Download</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-2 text-center text-gray-500">Belum ada data pengajuan katalog.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Custom Pagination -->
                <div class="mt-4 flex justify-between items-center">
                    <div class="text-sm text-gray-700">
                        Menampilkan 
                        {{ ($files->currentPage() - 1) * $files->perPage() + 1 }} 
                        hingga 
                        {{ min($files->currentPage() * $files->perPage(), $files->total()) }} 
                        dari 
                        {{ $files->total() }} 
                        entri
                    </div>
                    <div class="flex items-center gap-1">
                        @if (!$files->onFirstPage())
                            <a href="{{ $files->previousPageUrl() }}" class="px-3 py-1 bg-[#0A749B] text-white rounded">Sebelumnya</a>
                        @endif
                        @foreach ($files->getUrlRange(1, $files->lastPage()) as $page => $url)
                            @if ($page == $files->currentPage())
                                <span class="px-3 py-1 bg-[#0A749B] text-white rounded">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-1 rounded {{ $page == $files->currentPage() ? 'bg-[#0A749B] text-white' : 'bg-white text-[#0A749B] border border-[#0A749B]' }}">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if ($files->hasMorePages())
                            <a href="{{ $files->nextPageUrl() }}" class="px-3 py-1 bg-[#0A749B] text-white rounded">Selanjutnya</a>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<script>
    function toggleDropdown() {
        var dropdown = document.getElementById('dropdown');
        dropdown.classList.toggle('hidden');
    }
    // Optional: close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        var dropdown = document.getElementById('dropdown');
        var button = document.getElementById('dropdownToggle');
        if (!dropdown.contains(event.target) && !button.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>
@endsection
