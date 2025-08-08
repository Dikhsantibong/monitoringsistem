@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        @include('components.pemeliharaan-sidebar')
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
            <!-- Dashboard Content -->
            <main class="px-6 pt-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-blue-500 rounded-lg shadow p-6 flex items-center">
                        <i class="fas fa-bolt mr-4 text-white" style="font-size: 24px;"></i>
                        <div>
                            <h3 class="text-white text-sm font-medium">Total Mesin</h3>
                            <p class="text-2xl font-bold text-white mt-2">{{ $totalMachines }}</p>
                        </div>
                    </div>
                    <div class="bg-green-500 rounded-lg shadow p-6 flex items-center">
                        <i class="fas fa-check-circle mr-4 text-white" style="font-size: 24px;"></i>
                        <div>
                            <h3 class="text-white text-sm font-medium">Mesin Beroperasi</h3>
                            <p class="text-2xl font-bold text-white mt-2">{{ $operatingMachines }}</p>
                        </div>
                    </div>
                    <div class="bg-red-500 rounded-lg shadow p-6 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-4 text-white" style="font-size: 24px;"></i>
                        <div>
                            <h3 class="text-white text-sm font-medium">Mesin Gangguan</h3>
                            <p class="text-2xl font-bold text-white mt-2">{{ $troubleMachines }}</p>
                        </div>
                    </div>
                    <div class="bg-yellow-500 rounded-lg shadow p-6 flex items-center">
                        <i class="fas fa-wrench mr-4 text-white" style="font-size: 24px;"></i>
                        <div>
                            <h3 class="text-white text-sm font-medium">Dalam Pemeliharaan</h3>
                            <p class="text-2xl font-bold text-white mt-2">{{ $maintenanceMachines}}</p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Kinerja Unit Pembangkit</h3>
                        <div class="space-y-4">
                            @foreach($powerPlantPerformance as $plant)
                            <div class="flex flex-col">
                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-700">{{ $plant->name }}</span>
                                    <span class="text-gray-900 font-medium">{{ $plant->efficiency }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $plant->efficiency }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Pemeliharaan Terbaru</h3>
                        <div class="space-y-4">
                            @foreach($recentMaintenances as $maintenance)
                            <div class="border-l-4 border-blue-500 pl-4">
                                <p class="text-sm text-gray-600">{{ $maintenance->machine->name ?? '-' }}</p>
                                <p class="text-gray-800">{{ $maintenance->description }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($maintenance->created_at)->diffForHumans() }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Jadwal Meeting Hari Ini</h3>
                        <div class="space-y-4">
                            @forelse($todayMeetings as $meeting)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $meeting->title }}</p>
                                    <p class="text-sm text-gray-600">{{ $meeting->scheduled_at->format('H:i') }}</p>
                                </div>
                                <span class="px-3 py-1 text-xs font-medium rounded-full 
                                    {{ $meeting->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $meeting->status }}
                                </span>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center">Tidak ada meeting hari ini</p>
                            @endforelse
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Work Order Saya</h3>
                        <div class="space-y-4">
                            @forelse($myWorkOrders as $wo)
                            <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg">
                                <i class="fas fa-tools text-blue-500 mt-1"></i>
                                <div>
                                    <p class="text-gray-800 font-semibold">{{ $wo->description }}</p>
                                    <p class="text-xs text-gray-500 mb-1">Status: <span class="font-bold">{{ $wo->status }}</span></p>
                                    <p class="text-xs text-gray-500 mb-1">Jadwal Selesai: {{ $wo->schedule_finish }}</p>
                                    @if($wo->document_path)
                                        <a href="{{ url('storage/' . $wo->document_path) }}" target="_blank" class="text-blue-600 underline text-xs">Lihat Dokumen</a>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-1">Update: {{ \Carbon\Carbon::parse($wo->updated_at)->diffForHumans() }}</p>
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center">Tidak ada work order aktif</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
@endsection