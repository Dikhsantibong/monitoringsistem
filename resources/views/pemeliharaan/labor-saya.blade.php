@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    @include('components.pemeliharaan-sidebar')
    <!-- Main Content -->
    <div id="main-content" class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-10">
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
                    <h1 class="text-xl font-semibold text-gray-800">Work Order - Labor Saya</h1>
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
        <script>
            function toggleDropdown() {
                var dropdown = document.getElementById('dropdown');
                dropdown.classList.toggle('hidden');
            }
            document.addEventListener('click', function(event) {
                var userDropdown = document.getElementById('dropdown');
                var userBtn = document.getElementById('dropdownToggle');
                if (userDropdown && !userDropdown.classList.contains('hidden') && !userBtn.contains(event.target) && !userDropdown.contains(event.target)) {
                    userDropdown.classList.add('hidden');
                }
            });
        </script>
        <!-- Main Table Content -->
        <main class="px-6 pt-6">
            <div class="bg-white rounded shadow p-4 overflow-x-auto">
                <table class="min-w-full table-fixed divide-y divide-gray-200 border whitespace-nowrap">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-center">No</th>
                            <th class="px-4 py-2 text-center">ID</th>
                            <th class="px-4 py-2 text-center">Deskripsi</th>
                            <th class="px-4 py-2 text-center">Kendala</th>
                            <th class="px-4 py-2 text-center">Tindak Lanjut</th>
                            <th class="px-4 py-2 text-center">Document</th>
                            <th class="px-4 py-2 text-center">Type</th>
                            <th class="px-4 py-2 text-center">Status</th>
                            <th class="px-4 py-2 text-center">Priority</th>
                            <th class="px-4 py-2 text-center">Jadwal Mulai</th>
                            <th class="px-4 py-2 text-center">Jadwal Selesai</th>
                          
                            <th class="px-4 py-2 text-center">Labor</th>
                            <th class="px-4 py-2 text-center">nama labor</th>
                            <th class="px-4 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workOrders as $wo)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-center border border-gray-200">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2 border border-gray-200">{{ $wo->id }}</td>
                            <td class="px-4 py-2 border border-gray-200">{{ $wo->description }}</td>
                            <td class="px-4 py-2 border border-gray-200">{{ $wo->kendala }}</td>
                            <td class="px-4 py-2 border border-gray-200">{{ $wo->tindak_lanjut }}</td>
                            <td class="px-4 py-2 border border-gray-200 text-center">
                                @if($wo->document_path)
                                    <a href="{{ url('storage/' . $wo->document_path) }}" target="_blank" class="text-blue-600 underline text-xs">Lihat Dokumen</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-2 border border-gray-200 text-center">
                                <span class="px-2 py-1 rounded-full {{ $wo->type == 'PM' ? 'bg-blue-100 text-blue-600' : 'bg-yellow-100 text-yellow-600' }}">
                                    {{ $wo->type }}
                                </span>
                            </td>
                            <td class="px-4 py-2 border border-gray-200 text-center">
                                <span class="px-2 py-1 rounded-full {{ $wo->status == 'Open' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                    {{ $wo->status }}
                                </span>
                            </td>
                            <td class="px-4 py-2 border border-gray-200 text-center">
                                <span class="px-2 py-1 rounded-full {{ $wo->priority == 'Low' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                    {{ $wo->priority }}
                                </span>
                            </td>
                            <td class="px-4 py-2 border border-gray-200">{{ $wo->schedule_start }}</td>
                            <td class="px-4 py-2 border border-gray-200">{{ $wo->schedule_finish }}</td>
                            <td class="px-4 py-2 border border-gray-200">{{ $wo->labor }}</td>
                           
                            <td class="px-4 py-2 border border-gray-200">
                                @if(is_array($wo->labors))
                                    {{ implode(', ', $wo->labors) }}
                                @elseif(is_string($wo->labors))
                                    {{ $wo->labors }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-2 border border-gray-200 text-center">
                                <a href="{{ route('pemeliharaan.labor-saya.edit', $wo->id) }}" class="inline-block px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 text-xs"><i class="fas fa-edit"></i> Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="18" class="text-center py-4">Tidak ada data work order untuk labor Anda.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>
@endsection
