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
        <main class="px-6 pt-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-blue-100 rounded-lg p-5 flex flex-col items-center">
                    <div class="text-3xl font-bold text-blue-700">{{ $totalMaterial ?? 0 }}</div>
                    <div class="text-gray-700 mt-2">Total Material</div>
                </div>
                <div class="bg-green-100 rounded-lg p-5 flex flex-col items-center">
                    <div class="text-3xl font-bold text-green-700">{{ $totalKategori ?? 0 }}</div>
                    <div class="text-gray-700 mt-2">Kategori Material</div>
                </div>
                <div class="bg-yellow-100 rounded-lg p-5 flex flex-col items-center">
                    <div class="text-3xl font-bold text-yellow-700">{{ $totalKatalog ?? 0 }}</div>
                    <div class="text-gray-700 mt-2">Total Pengajuan Katalog</div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 w-full mb-6">
                <h2 class="text-lg font-semibold mb-4">Grafik Pengajuan Katalog 12 Bulan Terakhir</h2>
                <canvas id="katalogChart" height="100"></canvas>
            </div>
            <div class="bg-white rounded-lg shadow p-6 w-full">
                <h2 class="text-lg font-semibold mb-4">Informasi Inventory</h2>
                <ul class="list-disc ml-6 text-gray-700">
                    <li>Data material dan katalog diupdate secara real-time.</li>
                    <li>Gunakan menu Data Material untuk melihat detail stok dan kategori.</li>
                    <li>Menu Data Pengajuan Katalog menampilkan seluruh pengajuan katalog yang telah dilakukan.</li>
                    <li>Grafik di atas membantu memantau tren pengajuan katalog setiap bulan.</li>
                </ul>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const katalogData = @json($katalogPerBulan);
                const labels = katalogData.map(item => item.bulan);
                const data = katalogData.map(item => item.total);
                const ctx = document.getElementById('katalogChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah Pengajuan Katalog',
                            data: data,
                            backgroundColor: 'rgba(59, 130, 246, 0.5)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            title: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            </script>
        </main>
    </div>
</div>
@endsection
