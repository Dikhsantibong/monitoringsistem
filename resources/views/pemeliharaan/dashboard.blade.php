@extends('layouts.app')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    @include('components.pemeliharaan-sidebar')
    <!-- Main Content -->
    <div id="main-content" class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-30">
            <div class="flex justify-between items-center px-6 py-3">
                <div class="flex items-center gap-x-3">
                    <button id="mobile-menu-toggle"
                        class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#009BB9] hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <button id="desktop-menu-toggle"
                        class="hidden md:block relative items-center justify-center rounded-md text-gray-400 hover:bg-[#009BB9] p-2 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
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
                        <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10 p-2">
                            <a href="{{ route('user.profile') }}"
                                class="block px-4 py-2 text-sm text-gray-800 hover:bg-gray-200 rounded">Profile</a>
                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-gray-800 hover:bg-gray-200 rounded"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="px-6 py-4">
            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex flex-col">
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Work Order</h3>
                        <p class="text-2xl font-bold text-gray-800 mt-2">{{ number_format($totalWO) }}</p>
                        <div class="mt-4 text-xs font-bold text-blue-600">
                            <i class="fas fa-database mr-1"></i> Data Maximo
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex flex-col">
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">WO In Progress</h3>
                        <p class="text-2xl font-bold text-gray-800 mt-2">{{ number_format($openWO) }}</p>
                        <div class="mt-4 text-xs font-bold text-orange-600">
                            <i class="fas fa-spinner fa-spin mr-1"></i> Sedang Diproses
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex flex-col">
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">WO Closed</h3>
                        <p class="text-2xl font-bold text-gray-800 mt-2">{{ number_format($closedWO) }}</p>
                        <div class="mt-4 text-xs font-bold text-green-600">
                            <i class="fas fa-check-circle mr-1"></i> Berhasil Ditutup
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex flex-col">
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">SR Open</h3>
                        <p class="text-2xl font-bold text-gray-800 mt-2">{{ number_format($openSR) }}</p>
                        <div class="mt-4 text-xs font-bold text-red-600">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Menunggu Respon
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Status Chart -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Status Work Order</h3>
                        <div class="text-xs text-red-600 font-bold">Live Data</div>
                    </div>
                    <canvas id="woStatusChart" height="200"></canvas>
                </div>

                <!-- Recent Work Orders Table -->
                <div class="md:col-span-2 bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Work Order Terbaru (Maximo)</h3>
                        <a href="{{ route('pemeliharaan.labor-saya') }}" class="text-xs font-bold text-blue-600 hover:underline">LIHAT SEMUA</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="border-b border-gray-100">
                                <tr class="text-xs font-bold text-gray-400 uppercase">
                                    <th class="py-3 px-2">WONUM</th>
                                    <th class="py-3 px-2">Deskripsi</th>
                                    <th class="py-3 px-2">Status</th>
                                    <th class="py-3 px-2 text-right">Update</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($recentWorkOrders as $wo)
                                <tr>
                                    <td class="py-4 px-2">
                                        <span class="text-sm font-bold text-blue-600 font-mono">{{ $wo->wonum }}</span>
                                    </td>
                                    <td class="py-4 px-2">
                                        <div class="flex flex-col">
                                            <span class="text-sm text-gray-800 font-medium truncate max-w-[300px]">{{ $wo->description }}</span>
                                            <span class="text-[10px] text-gray-400 uppercase font-bold">{{ $wo->worktype }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-2">
                                        @php
                                            $st = strtoupper($wo->status);
                                            $color = match($st) {
                                                'COMP', 'CLOSE' => 'bg-green-100 text-green-700',
                                                'INPRG' => 'bg-blue-100 text-blue-700',
                                                'WMATL' => 'bg-yellow-100 text-yellow-700',
                                                'APPR' => 'bg-purple-100 text-purple-700',
                                                default => 'bg-gray-100 text-gray-700'
                                            };
                                        @endphp
                                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $color }}">
                                            {{ $wo->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-2 text-right">
                                        <span class="text-[10px] text-gray-500 font-medium">{{ isset($wo->statusdate) ? \Carbon\Carbon::parse($wo->statusdate)->diffForHumans() : '-' }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-400 italic text-sm">Tidak ada data ditemukan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/toggle.js') }}"></script>
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

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('woStatusChart').getContext('2d');
        const data = @json($woStatusData);
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.counts,
                    backgroundColor: ['#f59e42', '#10b981', '#6366f1'],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } },
                    title: { display: false }
                },
                cutout: '70%'
            }
        });
    });
</script>
@endpush
