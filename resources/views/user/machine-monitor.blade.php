@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        <style>
            /* Sidebar */
            aside {
                background-color: #0A749B;
                /* Warna biru kehijauan */
                color: white;
            }

            /* Link di Sidebar */
            aside nav a {
                color: white;
                /* Teks default putih */
                display: flex;
                align-items: center;
                padding: 12px 16px;
                text-decoration: none;
                transition: background-color 0.3s, color 0.3s;
                /* Animasi transisi */
            }

            /* Link di Sidebar saat Hover */
            aside nav a:hover {
                background-color: white;
                /* Latar belakang putih */
                color: black;
                /* Teks berubah menjadi hitam */
            }

            /* Aktif Link */
            aside nav a.bg-yellow-500 {
                background-color: white;
                color: #000102;
            }
        </style>
        <!-- Sidebar -->
        <aside id="mobile-menu"
            class="fixed z-20 overflow-hidden transform transition-transform duration-300 md:relative md:translate-x-0 h-screen w-64 bg-[#0A749B] shadow-md text-white hidden md:block md:shadow-lg">
            <div class="p-4 flex items-center gap-3">
                <img src="{{ asset('logo/navlogo.png') }}" alt="Logo Aplikasi" class="w-40 h-15">
                <!-- Mobile Menu Toggle -->
                <button id="menu-toggle-close"
                    class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#009BB9] hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                    aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <nav class="mt-4">
                <a href="{{ route('user.dashboard') }}">
                    <i class="fas fa-home mr-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('user.machine.monitor') }}" class="bg-yellow-500">
                    <i class="fas fa-cogs mr-3"></i>
                    <span>Machine Monitor</span>
                </a>
                <a href="{{ route('daily.meeting') }}">
                    <i class="fas fa-users mr-3"></i>
                    <span>Daily Meeting</span>
                </a>
                <a href="{{ route('monitoring') }}">
                    <i class="fas fa-chart-line mr-3"></i>
                    <span>Monitoring</span>
                </a>
                <a href="{{ route('documentation') }}">
                    <i class="fas fa-book mr-3"></i>
                    <span>Documentation</span>
                </a>
                <a href="{{ route('support') }}">
                    <i class="fas fa-headset mr-3"></i>
                    <span>Support</span>
                </a>
            </nav>
        </aside>

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
                
                <!-- Judul dan Filter -->
                <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Data Kesiapan Pembangkit</h2>
                        <div class="flex items-center">
                            <label for="filterDate" class="mr-2 text-gray-600">Tanggal:</label>
                            <input type="date" 
                                   id="filterDate" 
                                   value="{{ request('date', date('Y-m-d')) }}"
                                   class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                   onchange="filterData(this.value)">
                        </div>
                    </div>
                </div>

                <!-- Tabel -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mt-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Unit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Mesin</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Beban</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">DMN</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">DMP</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Kronologi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Deskripsi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Action Plan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Progres</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Target Selesai</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($machineStatusLogs as $index => $log)
                                    <tr class="hover:bg-gray-50 border border-gray-200">
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->machine->powerPlant->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->machine->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $log->status === 'Operasi' ? 'bg-green-100 text-green-800' : 
                                                   ($log->status === 'Gangguan' ? 'bg-red-100 text-red-800' : 
                                                   'bg-yellow-100 text-yellow-800') }}">
                                                {{ $log->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->load_value }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->dmn }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->dmp }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->kronologi }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->deskripsi }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->action_plan }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->progres }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $log->target_selesai ? $log->target_selesai->format('d/m/Y') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="px-6 py-4 text-center text-gray-500 border border-gray-200">
                                            Tidak ada data untuk ditampilkan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                @if(isset($machineStatusLogs) && $machineStatusLogs->hasPages())
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-4">
                    <div class="flex-1 flex justify-between sm:hidden">
                        {{ $machineStatusLogs->links() }}
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Menampilkan
                                <span class="font-medium">{{ $machineStatusLogs->firstItem() ?? 0 }}</span>
                                sampai
                                <span class="font-medium">{{ $machineStatusLogs->lastItem() ?? 0 }}</span>
                                dari
                                <span class="font-medium">{{ $machineStatusLogs->total() }}</span>
                                hasil
                            </p>
                        </div>
                        <div>
                            {{ $machineStatusLogs->links() }}
                        </div>
                    </div>
                </div>
                @endif
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
