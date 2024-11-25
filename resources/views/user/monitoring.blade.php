@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-yellow-500 shadow-lg">
        <div class="p-4">
            <h2 class="text-xl font-bold text-blue-600">PLN NUSANTARA POWER KENDARI</h2>
        </div>
        <nav class="mt-4">
            <a href="{{ route('user.dashboard') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-yellow-500">
                <i class="fas fa-home mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('user.machine.monitor') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-yellow-500">
                <i class="fas fa-cogs mr-3"></i>
                <span>Machine Monitor</span>
            </a>
            <a href="{{ route('daily.meeting') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-yellow-500">
                <i class="fas fa-users mr-3"></i>
                <span>Daily Meeting</span>
            </a>
            <a href="{{ route('monitoring') }}" class="flex items-center px-4 py-3 bg-yellow-500 text-blue-700">
                <i class="fas fa-chart-line mr-3"></i>
                <span>Monitoring</span>
            </a>
            <a href="{{ route('documentation') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-yellow-500">
                <i class="fas fa-book mr-3"></i>
                <span>Documentation</span>
            </a>
            <a href="{{ route('support') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-yellow-500">
                <i class="fas fa-headset mr-3"></i>
                <span>Support</span>
            </a>
        </nav>
    </aside>

     <!-- Main Content -->
     <div class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="flex justify-between items-center px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">Monitoring</h1>
                <div class="relative">
                    <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                        <img src="{{ Auth::user()->avatar ?? asset('images/default-avatar.png') }}" 
                             class="w-8 h-8 rounded-full mr-2">
                        <span class="text-gray-700">{{ Auth::user()->name }}</span>
                        <i class="fas fa-caret-down ml-2"></i>
                    </button>
                    <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                        <a href="{{ route('user.profile') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
                        <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </header>

    <!-- Main Content -->
    <div class="flex-1 p-6">
        <h1 class="text-2xl font-bold">Monitoring Proyek</h1>
        <p>Berikut adalah status proyek yang sedang berjalan:</p>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="text-lg font-semibold">Grafik Status Proyek</h2>
                <canvas id="projectStatusChart"></canvas>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="text-lg font-semibold">Detail Proyek</h2>
                <div class="space-y-4">
                    <div class="bg-white rounded-lg shadow p-4">
                        <h3 class="text-lg font-semibold">Proyek A</h3>
                        <p>Status: <span class="text-green-500">Aktif</span></p>
                        <p>Progress: 75%</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <h3 class="text-lg font-semibold">Proyek B</h3>
                        <p>Status: <span class="text-red-500">Tertunda</span></p>
                        <p>Progress: 50%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx2 = document.getElementById('projectStatusChart').getContext('2d');
    const projectStatusChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: ['Proyek A', 'Proyek B'],
            datasets: [{
                label: 'Status Proyek',
                data: [75, 50],
                backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(255, 99, 132, 0.2)'],
                borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
            }
        }
    });
</script>
@endpush
@endsection