@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        @include('components.user-sidebar')

        <!-- Main Content -->
        <div id="main-content" class="flex-1 overflow-auto">
            <!-- Header -->
            <header class="bg-white shadow-sm sticky top-0">
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
                        <!--  Menu Toggle Sidebar-->
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
                        <h1 class="text-xl font-semibold text-gray-800">Machine Monitor</h1>
                    </div>
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
            </header>

            <!-- Machine Monitor Content -->
            <main class="px-6">
                @include('layouts.breadcrumbs', ['breadcrumbs' => [['title' => 'Machine Monitor']]])
                
            

                <!-- Power Plant Status Cards -->
                @foreach($powerPlants as $powerPlant)
                <div class="bg-white rounded-lg shadow p-6 mb-4">
                    <!-- Judul dan Informasi Unit -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <div class="w-full">    
                                <div class="flex justify-between items-center mb-2">
                                    <h1 class="text-lg font-semibold uppercase">STATUS MESIN - {{ $powerPlant->name }}</h1>
                                    @php
                                        $lastUpdate = $machineStatusLogs
                                            ->whereIn('machine_id', $powerPlant->machines->pluck('id'))
                                            ->max('updated_at');
                                        $formattedLastUpdate = $lastUpdate 
                                            ? \Carbon\Carbon::parse($lastUpdate)->format('d/m/Y H:i:s')
                                            : '-';
                                    @endphp
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium">Update Terakhir:</span>
                                        <span class="ml-1">{{ $formattedLastUpdate }}</span>
                                    </div>
                                </div>

                                <!-- Status Summary Cards -->
                                <div class="grid grid-cols-5 gap-4 mb-4">
                                    @php
                                        $filteredLogs = $machineStatusLogs->filter(function($log) use ($date) {
                                            return $log->created_at->format('Y-m-d') === $date;
                                        });

                                        $totalDMP = $filteredLogs->whereIn('machine_id', $powerPlant->machines->pluck('id'))
                                            ->sum('dmp');
                                        $totalDMN = $filteredLogs->whereIn('machine_id', $powerPlant->machines->pluck('id'))
                                            ->sum('dmn');
                                        $totalBeban = $filteredLogs->whereIn('machine_id', $powerPlant->machines->pluck('id'))
                                            ->where('status', 'Operasi')
                                            ->sum('load_value');
                                    @endphp
                                    
                                    <div class="bg-blue-50 p-3 rounded-lg">
                                        <p class="text-sm text-gray-600">DMN:</p>
                                        <p class="text-xl font-bold text-blue-700">{{ number_format($totalDMN, 1) }} MW</p>
                                    </div>
                                    <div class="bg-green-50 p-3 rounded-lg">
                                        <p class="text-sm text-gray-600">DMP:</p>
                                        <p class="text-xl font-bold text-green-700">{{ number_format($totalDMP, 1) }} MW</p>
                                    </div>
                                    <div class="bg-red-50 p-3 rounded-lg">
                                        <p class="text-sm text-gray-600">Derating:</p>
                                        <p class="text-xl font-bold text-red-700">
                                            {{ number_format($totalDMN - $totalDMP, 1) }} MW 
                                            @if($totalDMN > 0)
                                                ({{ number_format((($totalDMN - $totalDMP) / $totalDMN) * 100, 1) }}%)
                                            @else
                                                (0%)
                                            @endif
                                        </p>
                                    </div>
                                    <div class="bg-purple-50 p-3 rounded-lg">
                                        <p class="text-sm text-gray-600">Total Beban:</p>
                                        <p class="text-xl font-bold text-purple-700">{{ number_format($totalBeban, 1) }} MW</p>
                                    </div>
                                    <div class="bg-orange-50 p-3 rounded-lg">
                                        <p class="text-sm text-gray-600">Total HOP:</p>
                                        <p class="text-xl font-bold text-orange-700">{{ number_format($powerPlant->hop_value ?? 0, 1) }} Hari</p>
                                    </div>
                                </div>

                                <!-- Machine Status Summary -->
                                <div class="grid grid-cols-7 gap-4">
                                    @php
                                        $machineCount = $powerPlant->machines->count();
                                        $operasiCount = $filteredLogs->whereIn('machine_id', $powerPlant->machines->pluck('id'))->where('status', 'Operasi')->count();
                                        $gangguanCount = $filteredLogs->whereIn('machine_id', $powerPlant->machines->pluck('id'))->where('status', 'Gangguan')->count();
                                        $pemeliharaanCount = $filteredLogs->whereIn('machine_id', $powerPlant->machines->pluck('id'))->where('status', 'Pemeliharaan')->count();
                                        $standbyCount = $filteredLogs->whereIn('machine_id', $powerPlant->machines->pluck('id'))->where('status', 'Standby')->count();
                                        $overhaulCount = $filteredLogs->whereIn('machine_id', $powerPlant->machines->pluck('id'))->where('status', 'Overhaul')->count();
                                        $mothballedCount = $filteredLogs->whereIn('machine_id', $powerPlant->machines->pluck('id'))->where('status', 'Mothballed')->count();
                                    @endphp
                                    
                                    <div class="bg-gray-100 p-4 rounded-lg shadow-md hover:bg-gray-200 transition duration-300">
                                        <p class="text-sm text-gray-700 font-medium">Total Mesin</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ $machineCount }}</p>
                                    </div>
                                    <div class="bg-emerald-100 p-4 rounded-lg shadow-md hover:bg-emerald-200 transition duration-300">
                                        <p class="text-sm text-emerald-700 font-medium">Operasi</p>
                                        <p class="text-2xl font-bold text-emerald-900">{{ $operasiCount }}</p>
                                    </div>
                                    <div class="bg-rose-100 p-4 rounded-lg shadow-md hover:bg-rose-200 transition duration-300">
                                        <p class="text-sm text-rose-700 font-medium">Gangguan</p>
                                        <p class="text-2xl font-bold text-rose-900">{{ $gangguanCount }}</p>
                                    </div>
                                    <div class="bg-amber-100 p-4 rounded-lg shadow-md hover:bg-amber-200 transition duration-300">
                                        <p class="text-sm text-amber-700 font-medium">Pemeliharaan</p>
                                        <p class="text-2xl font-bold text-amber-900">{{ $pemeliharaanCount }}</p>
                                    </div>
                                    <div class="bg-sky-100 p-4 rounded-lg shadow-md hover:bg-sky-200 transition duration-300">
                                        <p class="text-sm text-sky-700 font-medium">Standby</p>
                                        <p class="text-2xl font-bold text-sky-900">{{ $standbyCount }}</p>
                                    </div>
                                    <div class="bg-violet-100 p-4 rounded-lg shadow-md hover:bg-violet-200 transition duration-300">
                                        <p class="text-sm text-violet-700 font-medium">Overhaul</p>
                                        <p class="text-2xl font-bold text-violet-900">{{ $overhaulCount }}</p>
                                    </div>
                                    <div class="bg-gray-100 p-4 rounded-lg shadow-md hover:bg-gray-200 transition duration-300">
                                        <p class="text-sm text-gray-700 font-medium">Mothballed</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ $mothballedCount }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Machine Status Table -->
                    <div class="table-responsive">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">No</th>
                                    <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Mesin</th>
                                    <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">DMN (MW)</th>
                                    <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">DMP (MW)</th>
                                    <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Beban (MW)</th>
                                    <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Status</th>
                                    <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Kronologi</th>
                                    <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Deskripsi</th>
                                    <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Action Plan</th>
                                    <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">Progres</th>
                                    <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center">Target Selesai</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @forelse($powerPlant->machines as $index => $machine)
                                    @php
                                        $log = $filteredLogs->firstWhere('machine_id', $machine->id);
                                        $status = $log?->status ?? '-';
                                        $statusClass = match($status) {
                                            'Operasi' => 'bg-green-100 text-green-800',
                                            'Standby' => 'bg-blue-100 text-blue-800',
                                            'Gangguan' => 'bg-red-100 text-red-800',
                                            'Pemeliharaan' => 'bg-orange-100 text-orange-800',
                                            'Overhaul' => 'bg-violet-100 text-violet-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                    @endphp
                                    <tr class="hover:bg-gray-50 border border-gray-200">
                                        <td class="px-3 py-2 border-r border-gray-200 text-center">{{ $index + 1 }}</td>
                                        <td class="px-3 py-2 border-r border-gray-200">{{ $machine->name }}</td>
                                        <td class="px-3 py-2 border-r border-gray-200 text-center">{{ $log?->dmn ?? '-' }}</td>
                                        <td class="px-3 py-2 border-r border-gray-200 text-center">{{ $log?->dmp ?? '-' }}</td>
                                        <td class="px-3 py-2 border-r border-gray-200 text-center">{{ $log?->load_value ?? '-' }}</td>
                                        <td class="px-3 py-2 border-r border-gray-200 text-center">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                                {{ $status }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 border-r border-gray-200">{{ $log?->kronologi ?? '-' }}</td>
                                        <td class="px-3 py-2 border-r border-gray-200">{{ $log?->deskripsi ?? '-' }}</td>
                                        <td class="px-3 py-2 border-r border-gray-200">{{ $log?->action_plan ?? '-' }}</td>
                                        <td class="px-3 py-2 border-r border-gray-200">{{ $log?->progres ?? '-' }}</td>
                                        <td class="px-3 py-2 text-center">
                                            {{ $log?->target_selesai ? \Carbon\Carbon::parse($log->target_selesai)->format('d/m/Y') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="px-3 py-4 text-center text-gray-500">
                                            Tidak ada data mesin untuk unit ini
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </main>
        </div>
    </div>
    <script src="{{ asset('js/toggle.js') }}"></script>
@endsection


    <script>
        function filterData(date) {
            // Tampilkan loading indicator
            const tableBody = document.querySelector('tbody');
            tableBody.innerHTML = `
                <tr>
                    <td colspan="12" class="px-6 py-4 text-center">
                        <i class="fas fa-spinner fa-spin"></i> Loading...
                    </td>
                </tr>
            `;

            // Fetch data dengan AJAX
            fetch(`{{ route('user.machine.monitor') }}?date=${date}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update tabel dengan data baru
                    document.querySelector('.overflow-x-auto').innerHTML = data.html;
                } else {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="12" class="px-6 py-4 text-center text-red-500">
                                Tidak ada data untuk ditampilkan pada tanggal tersebut
                            </td>
                        </tr>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="12" class="px-6 py-4 text-center text-red-500">
                            Terjadi kesalahan saat memuat data
                        </td>
                    </tr>
                `;
            });
        }

        function toggleDropdown() {
            const dropdown = document.getElementById('dropdown');
            dropdown.classList.toggle('hidden');
        }

        // Menutup dropdown jika klik di luar
        window.onclick = function(event) {
            if (!event.target.matches('.flex.items-center')) {
                const dropdowns = document.getElementsByClassName("absolute");
                for (let i = 0; i < dropdowns.length; i++) {
                    const openDropdown = dropdowns[i];
                    if (!openDropdown.classList.contains('hidden')) {
                        openDropdown.classList.add('hidden');
                    }
                }
            }
        }

        const themeToggle = document.getElementById('theme-toggle');
        themeToggle.addEventListener('change', () => {
            document.body.classList.toggle('dark', themeToggle.checked);
        });
    </script>
    @push('scripts')
@endpush
