@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.sidebar')

    <div id="main-content" class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-20">
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

                    <!-- Desktop Menu Toggle -->
                    <button id="desktop-menu-toggle"
                        class="hidden md:block relative items-center justify-center rounded-md text-gray-400 hover:bg-[#009BB9] p-2 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                        aria-controls="desktop-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    <h1 class="text-xl font-semibold text-gray-800">Status Mesin</h1>
                </div>

                @include('components.timer')
                
                <!-- User Dropdown -->
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

        <!-- Breadcrumbs -->
        <div class="flex items-center pt-2">
            <x-admin-breadcrumb :breadcrumbs="[
                // ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['name' => 'Kesiapan Pembangkit', 'url' => route('admin.pembangkit.ready')],
                ['name' => 'Status Mesin', 'url' => null]
            ]" />
        </div>

        <!-- Main Content -->
        <div class="container mx-auto px-4 py-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Status Mesin</h2>
                    
                    <!-- Filter Tanggal -->
                    <div class="flex items-center space-x-4">
                        <input type="date" 
                               id="filterDate" 
                               value="{{ $date }}"
                               class="px-4 py-2 border rounded-lg"
                               onchange="filterData()">
                    </div>
                </div>

                <!-- Tab Navigation -->
                <div class="mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-4">
                            @foreach($units as $unit)
                                <button onclick="switchUnit('{{ $unit }}')" 
                                        class="tab-btn py-4 px-6 font-medium text-sm {{ $selectedUnit === $unit ? 'active-tab' : 'text-gray-500' }}"
                                        id="{{ str_replace(' ', '-', strtolower($unit)) }}-tab">
                                    {{ $unit }}
                                </button>
                            @endforeach
                        </nav>
                    </div>
                </div>

                <!-- Tabel Data -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-[#0A749B]">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Mesin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">DMN</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">DMP</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Beban</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Component</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Equipment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Deskripsi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Kronologi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Action Plan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Progress</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $index => $log)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">{{ $log->machine->name }}</td>
                                    <td class="px-6 py-4">{{ $log->dmn ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ $log->dmp ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ $log->load_value ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            @switch($log->status)
                                                @case('Operasi') bg-green-100 text-green-800 @break
                                                @case('Standby') bg-blue-100 text-blue-800 @break
                                                @case('Gangguan') bg-red-100 text-red-800 @break
                                                @case('Pemeliharaan') bg-orange-100 text-orange-800 @break
                                                @default bg-gray-100 text-gray-800
                                            @endswitch">
                                            {{ $log->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">{{ $log->component }}</td>
                                    <td class="px-6 py-4">{{ $log->equipment }}</td>
                                    <td class="px-6 py-4">{{ $log->deskripsi }}</td>
                                    <td class="px-6 py-4">{{ $log->kronologi }}</td>
                                    <td class="px-6 py-4">{{ $log->action_plan }}</td>
                                    <td class="px-6 py-4">{{ $log->progres }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-6 py-4 text-center text-gray-500">
                                        Tidak ada data untuk ditampilkan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function switchUnit(unit) {
    const date = document.getElementById('filterDate').value;
    window.location.href = `{{ route('admin.machine-status.view') }}?unit=${encodeURIComponent(unit)}&date=${date}`;
}

function filterData() {
    const date = document.getElementById('filterDate').value;
    const urlParams = new URLSearchParams(window.location.search);
    const unit = urlParams.get('unit') || '{{ $units[0] }}';
    window.location.href = `{{ route('admin.machine-status.view') }}?unit=${encodeURIComponent(unit)}&date=${date}`;
}

// Add dropdown toggle functionality
function toggleDropdown() {
    const dropdown = document.getElementById('dropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('dropdown');
    const dropdownToggle = document.getElementById('dropdownToggle');
    
    if (!dropdown.contains(event.target) && !dropdownToggle.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});
</script>

<style>
.active-tab {
    border-bottom: 2px solid #3B82F6;
    color: #2563EB;
}
</style>
@endsection 