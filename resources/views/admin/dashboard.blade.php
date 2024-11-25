@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md">
        <div class="p-4">
            <h2 class="text-xl font-bold text-blue-600">Aplikasi Rapat Harian</h2>
        </div>
        <nav class="mt-4">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                <i class="fas fa-home mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.machine-monitor') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.machine-monitor') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                <i class="fas fa-cogs mr-3"></i>
                <span>Monitor Mesin</span>
            </a>
            <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.users') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                <i class="fas fa-users mr-3"></i>
                <span>Manajemen Pengguna</span>
            </a>
            <a href="{{ route('admin.meetings') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.meetings') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                <i class="fas fa-chart-bar mr-3"></i>
                <span>Laporan Rapat</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.settings') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}">
                <i class="fas fa-cog mr-3"></i>
                <span>Pengaturan</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="flex justify-between items-center px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">Dashboard Admin</h1>
                <div class="flex items-center">
                    <div class="relative">
                        <button class="flex items-center dropdown-toggle" onclick="toggleDropdown()">
                            <img src="{{ Auth::user()->avatar ?? asset('images/default-avatar.png') }}" 
                                 class="w-8 h-8 rounded-full mr-2">
                            <span class="text-gray-700">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-gray-500"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div id="userDropdown" class="hidden dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="p-6">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-blue-500 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <i class="fas fa-users text-blue-500 text-xl mr-3"></i>
                        <div>
                            <h3 class="text-white text-sm font-medium">Total Pengguna</h3>
                            <p class="text-2xl font-bold text-white mt-1" id="total-users">
                                {{ $totalUsers }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-green-500 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <i class="fas fa-calendar text-green-500 text-xl mr-3"></i>
                        <div>
                            <h3 class="text-white text-sm font-medium">Rapat Hari Ini</h3>
                            <p class="text-2xl font-bold text-white mt-1" id="today-meetings">
                                {{ $todayMeetings }}
                            </p>
                        </div>  
                    </div>
                </div>
                <div class="bg-yellow-500 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <i class="fas fa-users text-yellow-500 text-xl mr-3"></i>
                        <div>
                            <h3 class="text-white text-sm font-medium">Pengguna Aktif</h3>
                            <p class="text-2xl font-bold text-white mt-1" id="active-users">
                                {{ $activeUsers }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Pengguna</h3>
                    <canvas id="activityChart" class="w-full h-64"></canvas>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistik Rapat</h3>
                    <canvas id="meetingChart" class="w-full h-64"></canvas>
                </div>
            </div>

            <!-- Recent Activities Table -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Aktivitas Terbaru</h3>
                        <button onclick="exportActivities()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                            <i class="fas fa-download mr-2"></i>Ekspor
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table id="activities-table" class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aktivitas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengguna</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($recentActivities as $activity)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $activity->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $activity->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $activity->created_at->diffForHumans() }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $activity->status_color }}">
                                            {{ $activity->status }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
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
<script>
// Inisialisasi DataTables
$(document).ready(function() {
    $('#activities-table').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[2, 'desc']]
    });
});

// Chart Aktivitas Pengguna
const activityCtx = document.getElementById('activityChart').getContext('2d');
const activityChart = new Chart(activityCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($activityChartData['labels']) !!},
        datasets: [{
            label: 'Aktivitas Pengguna',
            data: {!! json_encode($activityChartData['data']) !!},
            borderColor: 'rgb(59, 130, 246)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Chart Statistik Meeting
const meetingCtx = document.getElementById('meetingChart').getContext('2d');
const meetingChart = new Chart(meetingCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($meetingChartData['labels']) !!},
        datasets: [{
            label: 'Rapat',
            data: {!! json_encode($meetingChartData['data']) !!},
            backgroundColor: 'rgba(59, 130, 246, 0.5)'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Fungsi untuk export aktivitas
function exportActivities() {
    window.location.href = '{{ route("admin.activities.export") }}';
}

// Fungsi untuk toggle dropdown
function toggleDropdown() {w
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('hidden');
}

// Tutup dropdown ketika mengklik di luar
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('userDropdown');
    const button = event.target.closest('button');
    
    if (!button && !dropdown.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});
</script>
@endpush
