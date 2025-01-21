@extends('layouts.app')

@push('styles')
    <style>
        #timer {
            font-size: 2em;
            font-weight: bold;
            color: #333;
            margin: 10px 0;
            display: none;
            /* Sembunyikan timer secara default */
        }
        .table-responsive {
            overflow-x: auto; /* Mengizinkan scroll horizontal */
            width: 100%; /* Memaksimalkan lebar kolom */
        }
        table {
            width: 100%; /* Memastikan tabel mengambil lebar penuh */
            table-layout: auto; /* Mengizinkan kolom untuk menyesuaikan lebar */
        }
    </style>
@endpush

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="flex h-screen bg-gray-50 overflow-auto">
        <!-- Sidebar -->
      @include('components.sidebar')

        <!-- Main Content -->
        <div id="main-content" class="flex-1 overflow-auto">{{-- tes --}}
            <header class="bg-white shadow-sm sticky top-0 z-10">
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

                        <h1 class="text-xl font-semibold text-gray-800">Penyusunan Data Pembangkit</h1>
                    </div>

                    <div id="timer" class="text-lg font-bold text-gray-800">00:00:00</div>
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

            <div class="flex items-center pt-2">
                <x-admin-breadcrumb :breadcrumbs="[['name' => 'Kesiapan Pembangkit', 'url' => null]]" />
            </div>
            <main class="px-6">
                <!-- Konten Kesiapan Pembangkit -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h1 class="text-xl font-semibold text-gray-800">Penyusunan Data Pembangkit</h1>
                        <a href="{{ route('admin.machine-status.view') }}" 
                           class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                            <i class="fas fa-eye mr-2"></i>Lihat Status Mesin
                        </a>
                    </div>
                    <div class="mb-4 flex flex-col lg:flex-row justify-between items-center gap-3">
                        <div class="flex flex-col lg:flex-row gap-y-3 sm:gap-y-3 space-x-4">
                            <!-- Filter Unit -->
                            @if(session('unit') === 'mysql')
                            <div class="flex items-center">
                                <label for="unit-source" class="text-sm text-gray-700 font-medium mr-2">Filter Unit:</label>
                                <select id="unit-source" 
                                        class="border rounded px-3 py-2 text-sm w-40"
                                        onchange="loadData()">
                                    <option value="">Semua Unit</option>
                                    @forelse($powerPlants as $plant)
                                        <option value="{{ $plant->unit_source }}" {{ request('unit_source') == $plant->unit_source ? 'selected' : '' }}>
                                            {{ $plant->name }}
                                        </option>
                                    @empty
                                        <option value="" disabled>Tidak ada unit tersedia</option>
                                    @endforelse
                                </select>
                            </div>
                            @endif

                            <input type="date" id="filterDate" value="{{ date('Y-m-d') }}"
                                class="px-4 py-2 border rounded-lg">

                            <div class="relative">
                                <div class="relative">
                                    <input type="text" 
                                           id="searchInput" 
                                           placeholder="Cari mesin, unit, atau status..."
                                           onkeyup="if(event.key === 'Enter') searchTables()"
                                           class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex space-x-4">
                            <div class="max-w-full">
                                <button id="refreshButton" 
                                        onclick="loadData()"
                                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                    <i class="fas fa-redo mr-2"></i>Muat Ulang
                                </button>
                            </div>

                            <div class="max-w-full">
                                <button onclick="saveData()"
                                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                                    <i class="fas fa-save mr-2"></i>Simpan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Search Bar -->
                    <h1 class="text-lg font-semibold uppercase mb-5">KESIAPAN PEMBANGKIT UP KENDARI ( MEGAWATT )</h1>

                    @forelse ($units as $unit)
                        <div class="bg-white rounded-lg shadow p-6 mb-4 unit-table">
                            <div class="overflow-auto">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-lg font-semibold text-gray-800">{{ $unit->name }}</h2>
                                    <div class="flex items-center gap-x-2">
                                        <label for="hop_{{ $unit->id }}" class="text-sm font-medium text-gray-700">HOP:</label>
                                        <input type="number" 
                                               id="hop_{{ $unit->id }}" 
                                               name="hop_{{ $unit->id }}"
                                               class="w-24 px-3 py-1 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                               placeholder="Masukkan HOP"
                                               min="0"
                                               value="{{ old('hop_' . $unit->id) }}">
                                        <span class="text-sm text-gray-600">hari</span>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="min-w-full bg-white">
                                        <thead>
                                            <tr>
                                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">
                                                    No
                                                </th>
                                                    <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">
                                                    Mesin
                                                </th>
                                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">
                                                    Daya Mampu Slim (MW)
                                                </th>
                                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">
                                                    Daya Mampu Pasok (MW)
                                                </th>
                                                <th class="px-2 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">
                                                    Beban (MW)
                                                </th>
                                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">
                                                    Status
                                                </th>
                                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">
                                                    Comp
                                                </th>
                                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">
                                                    Equipment
                                                </th>
                                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">
                                                    Deskripsi
                                                </th>
                                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">
                                                    Kronologi
                                                </th>
                                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">
                                                    Action Plan
                                                </th>
                                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center">
                                                    Progres
                                                </th>
                                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center">
                                                    Tanggal Mulai
                                                </th>
                                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center">
                                                    Target Selesai
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-sm">
                                            @forelse($unit->machines as $machine)
                                                <tr class="hover:bg-gray-50 border border-gray-200">
                                                    <td class="px-3 py-2 border-r border-gray-200 text-center text-gray-800 w-12">
                                                        {{ $loop->iteration }}
                                                    </td>
                                                    <td class="px-3 py-2 border-r border-gray-200 text-gray-800" data-id="{{ $machine->id }}">
                                                        {{ $machine->name }}
                                                    </td>   
                                                    <td class="px-3 py-2 border-r border-gray-200 text-center text-gray-800 w-12" style="width: 100px;">
                                                        {{ $operations->where('machine_id', $machine->id)->first()->dmp ?? 'N/A' }}
                                                    </td>
                                                    <td class="px-3 py-2 border-r border-gray-200 text-center text-gray-800 w-12">
                                                        <input type="number" 
                                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:border-blue-400 text-gray-800"
                                                               style="width: 100px;"     value="{{ $operations->where('machine_id', $machine->id)->first()->dmn ?? '0' }}"
                                                            placeholder="Masukkan DMP...">
                                                    </td>
                                                    <td class="px-2 py-2 border-r border-gray-200">
                                                        <input type="number" 
                                                               name="load_value[{{ $machine->id }}]"
                                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:border-blue-400"
                                                               step="0.01"
                                                               min="0"
                                                               style="width: 100px;"
                                                               value="{{ $operations->where('machine_id', $machine->id)->first()->load_value ?? '0' }}"
                                                               placeholder="Masukkan beban...">
                                                    </td>
                                                    <td class="px-3 py-2 border-r border-gray-200">
                                                        <select class="w-12 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:border-blue-400"
                                                            style="width: 100px;"
                                                            onchange="this.style.backgroundColor = this.options[this.selectedIndex].style.backgroundColor">
                                                            <option value="" style="background-color: #FFFFFF">Pilih Status</option>
                                                            <option value="Operasi" style="background-color: #4CAF50">Operasi</option>
                                                            <option value="Standby" style="background-color: #2196F3">Standby</option>
                                                            <option value="Gangguan" style="background-color: #f44336">Gangguan</option>
                                                            <option value="Pemeliharaan" style="background-color: #FF9800">Pemeliharaan</option>
                                                            <option value="Mothballed" style="background-color: #9E9E9E">Mothballed</option>
                                                            <option value="Overhaul" style="background-color: #673AB7">Overhaul</option>
                                                        </select>
                                                    </td>
                                                    <td class="px-3 py-2 border-r border-gray-200">
                                                        <select class="w-12 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:border-blue-400 system-select"
                                                                onchange="updateComponentOptions(this)" 
                                                                name="sistem[{{ $machine->id }}]"
                                                                style="width: 130px;">
                                                            <option value="">Pilih Komponen</option>
                                                            <option value="MESIN">MESIN</option>
                                                            <option value="GENERATOR">GENERATOR</option>
                                                            <option value="PANEL_SINKRON">PANEL SINKRON</option>
                                                            <option value="KUBIKAL">KUBIKAL</option>
                                                            <option value="AUXILIARY">AUXILIARY</option>
                                                        </select>
                                                    </td>
                                                    <td class="px-3 py-2 border-r border-gray-200">
                                                        <textarea 
                                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:border-blue-400 text-gray-80"
                                                        style="height: 100px; width: 150px;" 
                                                        cols="30" 
                                                        rows="10"
                                                        name="equipment[{{ $machine->id }}]" 
                                                        oninput="autoResize(this)">
                                                    </textarea>
                                                        </select>
                                                    </td>
                                                    <td class="px-3 py-2 border-r border-gray-200">
                                                        <textarea 
                                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:border-blue-400 text-gray-800"
                                                            rows="2" 
                                                            placeholder="Masukkan deskripsi..." 
                                                            style="height: 100px; width: 300px;" 
                                                            name="deskripsi[{{ $machine->id }}]" 
                                                            oninput="autoResize(this)">{{ $operations->where('machine_id', $machine->id)->first()->deskripsi ?? '' }}</textarea>
                                                    </td>
                                                    <td class="px-3 py-2 border-r border-gray-200">
                                                        <textarea 
                                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:border-blue-400 text-gray-800"
                                                            rows="2" 
                                                            placeholder="Masukkan kronologi..." 
                                                            style="height: 100px; width: 300px;" 
                                                            name="kronologi[{{ $machine->id }}]" 
                                                            oninput="autoResize(this)"></textarea>
                                                    </td>
                                                    <td class="px-3 py-2 border-r border-gray-200">
                                                        <textarea 
                                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:border-blue-400 text-gray-800"
                                                            rows="2" 
                                                            placeholder="Masukkan action plan..." 
                                                            style="height: 100px; width: 300px;" 
                                                            name="action_plan[{{ $machine->id }}]" 
                                                            oninput="autoResize(this)"></textarea>
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <textarea 
                                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:border-blue-400 text-gray-800"
                                                            rows="2" 
                                                            placeholder="Masukkan progres..." 
                                                            style="height: 100px; width: 300px;" 
                                                            name="progres[{{ $machine->id }}]" 
                                                            oninput="autoResize(this)">{{ $operations->where('machine_id', $machine->id)->first()->progres ?? '' }}</textarea>
                                                    </td>   
                                                    <td class="px-3 py-2">
                                                        <input type="date" 
                                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:border-blue-400 text-gray-800" 
                                                            name="tanggal_mulai[{{ $machine->id }}]">
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <input type="date" 
                                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:border-blue-400 text-gray-800" 
                                                            name="target_selesai[{{ $machine->id }}]">
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="14" class="px-3 py-4 text-center text-gray-500">
                                                        Tidak ada data mesin untuk unit ini
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-lg shadow p-6 mb-4">
                            <p class="text-center text-gray-500">Tidak ada data unit yang tersedia</p>
                        </div>
                    @endforelse
                </div>
            </main>
        </div>
    </div>
    </div>

    <script>
        let timerInterval;
        let startTime;
        let elapsedTime = 0; // Menyimpan waktu yang telah berlalu
        let isRunning = false;

        // Cek apakah timer sedang berjalan saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const storedStartTime = localStorage.getItem('startTime');
            const storedElapsedTime = localStorage.getItem('elapsedTime');
            const storedIsRunning = localStorage.getItem('isRunning');

            if (storedStartTime && storedIsRunning === 'true') {
                startTime = new Date(parseInt(storedStartTime));
                elapsedTime = parseInt(storedElapsedTime) || 0; // Ambil waktu yang telah berlalu
                isRunning = true;
                updateTimerDisplay(); // Perbarui tampilan timer
                timerInterval = setInterval(updateTimer, 1000); // Mulai interval

                // Tampilkan timer
                document.getElementById('timer').style.display = 'block'; // Tampilkan timer
            } else {
                // Jika timer tidak berjalan, sembunyikan timer
                document.getElementById('timer').style.display = 'none';
            }
        });

        function updateTimer() {
            const now = new Date();
            elapsedTime += 1000; // Tambahkan 1 detik ke waktu yang telah berlalu
            localStorage.setItem('elapsedTime', elapsedTime); // Simpan waktu yang telah berlalu

            updateTimerDisplay(); // Perbarui tampilan timer
        }

        function updateTimerDisplay() {
            const totalElapsedTime = elapsedTime + (isRunning ? new Date() - startTime : 0);
            const hours = Math.floor(totalElapsedTime / (1000 * 60 * 60));
            const minutes = Math.floor((totalElapsedTime % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((totalElapsedTime % (1000 * 60)) / 1000);

            const timerDisplay = document.getElementById('timer');
            timerDisplay.textContent = `${padNumber(hours)}:${padNumber(minutes)}:${padNumber(seconds)}`;
        }

        function padNumber(number) {
            return number.toString().padStart(2, '0');
        }
    </script>
@endsection
<script src="{{ asset('js/toggle.js') }}"></script>
<script>
    function searchTables() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const filterDate = document.getElementById('filterDate').value;

        // Tampilkan loading indicator
        const mainContent = document.querySelector('.bg-white.rounded-lg.shadow.p-6');
        const loadingIndicator = document.createElement('div');
        loadingIndicator.className = 'text-center py-4';
        loadingIndicator.innerHTML = `
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            <div class="mt-2 text-gray-600">Memuat data...</div>
        `;
        mainContent.appendChild(loadingIndicator);

        // Sembunyikan semua tabel dan baris terlebih dahulu
        const unitTables = document.getElementsByClassName('unit-table');
        Array.from(unitTables).forEach(table => {
            const unitName = table.querySelector('h2').textContent.toLowerCase();
            const shouldShowUnit = unitName.includes(searchInput);
            let hasMatchingMachine = false;

            // Cek setiap baris mesin dalam tabel
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const machineName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const machineMatches = machineName.includes(searchInput);
                
                if (machineMatches || shouldShowUnit) {
                    row.style.display = '';
                    hasMatchingMachine = true;
                } else {
                    row.style.display = 'none';
                }
            });

            // Tampilkan/sembunyikan tabel berdasarkan hasil pencarian
            table.style.display = (hasMatchingMachine || shouldShowUnit) ? '' : 'none';
        });

        // Hapus loading indicator
        loadingIndicator.remove();

        // Tampilkan pesan jika tidak ada hasil
        const existingMessage = mainContent.querySelector('.no-results-message');
        if (existingMessage) {
            existingMessage.remove();
        }

        const visibleTables = Array.from(unitTables).filter(table => table.style.display !== 'none');
        if (visibleTables.length === 0) {
            const noResultsMessage = document.createElement('div');
            noResultsMessage.className = 'text-center py-8 text-gray-600 font-semibold text-lg no-results-message';
            noResultsMessage.textContent = 'DATA TIDAK DITEMUKAN';
            mainContent.appendChild(noResultsMessage);
        }
    }

    // Event listener untuk input pencarian
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchValue = this.value.trim();
        if (searchValue.length >= 1) { // Mulai pencarian setelah 1 karakter
            searchTables();
        } else {
            // Tampilkan semua data jika input kosong
            const unitTables = document.getElementsByClassName('unit-table');
            Array.from(unitTables).forEach(table => {
                table.style.display = '';
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => row.style.display = '');
            });

            // Hapus pesan tidak ada hasil jika ada
            const noResultsMessage = document.querySelector('.no-results-message');
            if (noResultsMessage) {
                noResultsMessage.remove();
            }
        }
    });

    function saveData() {
        const unitSource = document.getElementById('unit-source')?.value || '';
        const data = {
            logs: [],
            hops: [],
            unit_source: unitSource
        };
        const tables = document.querySelectorAll('.unit-table table');
        const tanggal = document.getElementById('filterDate').value;

        tables.forEach(table => {
            const unitTable = table.closest('.unit-table');
            const powerPlantId = unitTable.querySelector('input[id^="hop_"]').id.split('_')[1];
            const hopValue = unitTable.querySelector(`input[id="hop_${powerPlantId}"]`).value;

            // Tambahkan data HOP
            if (hopValue) {
                data.hops.push({
                    power_plant_id: powerPlantId,
                    tanggal: tanggal,
                    hop_value: hopValue
                });
            }

            // Tambahkan data status mesin (kode yang sudah ada)
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const machineId = row.querySelector('td[data-id]').getAttribute('data-id');
                const statusSelect = row.querySelector('select');
                const componentSelect = row.querySelector('.system-select');
                
                // Perbaikan pengambilan nilai equipment
                const equipmentTextarea = row.querySelector('textarea[name^="equipment"]');
                const equipmentValue = equipmentTextarea ? equipmentTextarea.value.trim() : '';
                
                // Ambil nilai-nilai lain
                const dmpInput = row.querySelector('td:nth-child(3) input');
                const inputDeskripsi = row.querySelector(`textarea[name="deskripsi[${machineId}]"]`);
                const inputActionPlan = row.querySelector(`textarea[name="action_plan[${machineId}]"]`);
                const inputBeban = row.querySelector('td:nth-child(4) input');
                const inputProgres = row.querySelector(`textarea[name="progres[${machineId}]"]`);
                const inputKronologi = row.querySelector(`textarea[name="kronologi[${machineId}]"]`);
                const inputTanggalMulai = row.querySelector(`input[name="tanggal_mulai[${machineId}]"]`);
                const inputTargetSelesai = row.querySelector(`input[name="target_selesai[${machineId}]"]`);

                // Perbaiki selector untuk input beban
                const loadInput = row.querySelector('td:nth-child(5) input[type="number"]'); // Sesuaikan dengan posisi kolom beban
                const loadValue = loadInput ? parseFloat(loadInput.value) || 0 : 0;

                // Debug log untuk memastikan nilai beban terambil
                console.log('Load value for machine', machineId, ':', loadValue);

                if (statusSelect && statusSelect.value) {
                    data.logs.push({
                        machine_id: machineId,
                        tanggal: tanggal,
                        hop: hopValue,
                        status: statusSelect.value,
                        component: componentSelect ? componentSelect.value : null,
                        equipment: equipmentValue,
                        dmn: row.querySelector('td:nth-child(2)').textContent.trim(),
                        dmp: dmpInput ? dmpInput.value.trim() : null,
                        load_value: loadValue, // Pastikan nilai load_value selalu terisi
                        deskripsi: inputDeskripsi ? inputDeskripsi.value.trim() : null,
                        action_plan: inputActionPlan ? inputActionPlan.value.trim() : null,
                        progres: inputProgres ? inputProgres.value.trim() : null,
                        kronologi: inputKronologi ? inputKronologi.value.trim() : null,
                        tanggal_mulai: inputTanggalMulai ? inputTanggalMulai.value : null,
                        target_selesai: inputTargetSelesai ? inputTargetSelesai.value : null
                    });
                }
            });
        });

        // Debug log untuk melihat data yang akan dikirim
        console.log('Data yang akan dikirim:', data);

        fetch('{{ route('admin.pembangkit.save-status') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Data berhasil disimpan!',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    // Setelah pesan sukses, muat ulang data
                    loadData();
                });
            } else {
                throw new Error(result.message);
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message
            });
        });
    }

    function confirmReset() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data akan direset kecuali status Gangguan, DMN, dan DMP!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, reset!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                resetForm();
                Swal.fire(
                    'Reset!',
                    'Data telah direset kecuali status Gangguan, DMN, dan DMP.',
                    'success'
                );
            }
        });
    }

    function resetForm() {
        const tables = document.querySelectorAll('.unit-table table');
        tables.forEach(table => {
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                // Cek status saat ini
                const statusSelect = row.querySelector('select');
                const currentStatus = statusSelect.value;
                const machineId = row.querySelector('td[data-id]').getAttribute('data-id');
                
                // Jika status bukan 'Gangguan', reset field-field yang diizinkan
                if (currentStatus !== 'Gangguan') {
                    // Reset status dropdown dan warnanya
                    statusSelect.value = '';
                    statusSelect.style.backgroundColor = '';
                    
                    // Reset input beban
                    const inputBeban = row.querySelector('td:nth-child(4) input');
                    if (inputBeban) inputBeban.value = '';
                    
                    // Reset komponen dropdown
                    const componentSelect = row.querySelector('.system-select');
                    if (componentSelect) componentSelect.value = '';
                    
                    // Reset equipment
                    const equipmentTextarea = row.querySelector('textarea[name="equipment"]');
                    if (equipmentTextarea) equipmentTextarea.value = '';
                    
                    // Reset deskripsi
                    const deskripsiTextarea = row.querySelector(`textarea[name="deskripsi[${machineId}]"]`);
                    if (deskripsiTextarea) deskripsiTextarea.value = '';
                    
                    // Reset kronologi
                    const kronologiTextarea = row.querySelector(`textarea[name="kronologi[${machineId}]"]`);
                    if (kronologiTextarea) kronologiTextarea.value = '';
                    
                    // Reset action plan
                    const actionPlanTextarea = row.querySelector(`textarea[name="action_plan[${machineId}]"]`);
                    if (actionPlanTextarea) actionPlanTextarea.value = '';
                    
                    // Reset progres
                    const progresTextarea = row.querySelector(`textarea[name="progres[${machineId}]"]`);
                    if (progresTextarea) progresTextarea.value = '';
                    
                    // Reset tanggal
                    const tanggalMulaiInput = row.querySelector(`input[name="tanggal_mulai[${machineId}]"]`);
                    if (tanggalMulaiInput) tanggalMulaiInput.value = '';
                    
                    const targetSelesaiInput = row.querySelector(`input[name="target_selesai[${machineId}]"]`);
                    if (targetSelesaiInput) targetSelesaiInput.value = '';
                }
            });
        });

        // Muat ulang data DMN dan DMP dari MachineOperation
        const tanggal = document.getElementById('filterDate').value;
        fetch(`{{ route('admin.pembangkit.get-status') }}?tanggal=${tanggal}`)
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Update DMN dan DMP dari data MachineOperation
                    result.data.forEach(log => {
                        const machineId = log.machine_id;
                        const row = document.querySelector(`td[data-id="${machineId}"]`)?.closest('tr');
                        
                        if (row) {
                            // Update DMN
                            const dmnCell = row.querySelector('td:nth-child(2)');
                            if (dmnCell) {
                                const dmnValue = log.dmn !== null ? log.dmn : 'N/A';
                                dmnCell.textContent = dmnValue;
                            }
                            
                            // Update DMP
                            const dmpCell = row.querySelector('td:nth-child(3)');
                            if (dmpCell) {
                                const dmpValue = log.dmp !== null ? log.dmp : 'N/A';
                                dmpCell.textContent = dmpValue;
                            }
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Jika gagal mengambil data baru, pertahankan nilai DMN dan DMP yang ada
            });
    }

    // Fungsi untuk memuat data
    function loadData() {
        const tanggal = document.getElementById('filterDate').value;
        const unitSource = document.getElementById('unit-source')?.value || '';
        const refreshButton = document.getElementById('refreshButton');
        
        // Nonaktifkan tombol dan tambahkan animasi
        refreshButton.disabled = true;
        const icon = refreshButton.querySelector('.fa-redo');
        if (icon) icon.classList.add('fa-spin');

        // Tampilkan loading indicator
        Swal.fire({
            title: 'Memuat Data',
            text: 'Mohon tunggu sebentar...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const params = new URLSearchParams({
            tanggal: tanggal,
            unit_source: unitSource
        });

        fetch(`{{ route('admin.pembangkit.get-status') }}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
        })
        .then(response => {
            // Log response status
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP error! status: ${response.status}, message: ${text}`);
                });
            }
            return response.json();
        })
        .then(result => {
            // Log hasil response
            console.log('Response data:', result);
            
            if (result.success) {
                // Pastikan data ada sebelum update form
                if (!result.data) {
                    throw new Error('Data tidak ditemukan');
                }
                
                try {
                    updateFormWithData(result.data);
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil dimuat!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } catch (updateError) {
                    throw new Error(`Error saat update form: ${updateError.message}`);
                }
            } else {
                throw new Error(result.message || 'Gagal memuat data');
            }
        })
        .catch(error => {
            console.error('Detailed error:', error);
            
            // Tampilkan pesan error yang lebih detail
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: `Terjadi kesalahan saat memuat data: ${error.message}`,
                confirmButtonText: 'OK'
            });
        })
        .finally(() => {
            // Aktifkan kembali tombol dan hentikan animasi
            refreshButton.disabled = false;
            icon.classList.remove('fa-spin');
        });
    }

    // Fungsi untuk mengupdate form dengan data
    function updateFormWithData(data) {
        console.log('Updating form with data:', data);
        
        if (!data || (!data.logs && !data.hops)) {
            console.error('Invalid data structure:', data);
            throw new Error('Format data tidak valid');
        }

        const tables = document.querySelectorAll('.unit-table table');
        
        tables.forEach(table => {
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                try {
                    const machineId = row.querySelector('td[data-id]')?.getAttribute('data-id');
                    if (!machineId) {
                        console.warn('Missing machine ID for row:', row);
                        return;
                    }

                    const machineData = data.logs?.find(d => d.machine_id == machineId);
                    console.log(`Processing machine ${machineId}:`, machineData);

                    if (machineData) {
                        // Update status
                        const statusSelect = row.querySelector('select');
                        if (statusSelect) {
                            statusSelect.value = machineData.status || '';
                            statusSelect.style.backgroundColor = getStatusColor(machineData.status);
                        }

                        // Update load value
                        const loadInput = row.querySelector('input[name^="load_value"]');
                        if (loadInput) {
                            loadInput.value = machineData.load_value || '';
                        }

                        // Update DMN
                        const dmnInput = row.querySelector('input[type="number"]:nth-of-type(1)');
                        if (dmnInput) {
                            dmnInput.value = machineData.dmn || '';
                        }

                        // Update component
                        const componentSelect = row.querySelector('.system-select');
                        if (componentSelect) {
                            componentSelect.value = machineData.component || '';
                        }

                        // Update equipment
                        const equipmentTextarea = row.querySelector(`textarea[name^="equipment"]`);
                        if (equipmentTextarea) {
                            equipmentTextarea.value = machineData.equipment || '';
                        }

                        // Update deskripsi
                        const deskripsiTextarea = row.querySelector(`textarea[name^="deskripsi"]`);
                        if (deskripsiTextarea) {
                            deskripsiTextarea.value = machineData.deskripsi || '';
                        }

                        // Update kronologi
                        const kronologiTextarea = row.querySelector(`textarea[name^="kronologi"]`);
                        if (kronologiTextarea) {
                            kronologiTextarea.value = machineData.kronologi || '';
                        }

                        // Update action plan
                        const actionPlanTextarea = row.querySelector(`textarea[name^="action_plan"]`);
                        if (actionPlanTextarea) {
                            actionPlanTextarea.value = machineData.action_plan || '';
                        }

                        // Update progres
                        const progresTextarea = row.querySelector(`textarea[name^="progres"]`);
                        if (progresTextarea) {
                            progresTextarea.value = machineData.progres || '';
                        }

                        // Update dates
                        const tanggalMulaiInput = row.querySelector(`input[name^="tanggal_mulai"]`);
                        if (tanggalMulaiInput) {
                            tanggalMulaiInput.value = machineData.tanggal_mulai || '';
                        }

                        const targetSelesaiInput = row.querySelector(`input[name^="target_selesai"]`);
                        if (targetSelesaiInput) {
                            targetSelesaiInput.value = machineData.target_selesai || '';
                        }
                    }
                } catch (rowError) {
                    console.error('Error processing row:', rowError);
                }
            });
        });

        // Update HOP values
        if (data.hops) {
            data.hops.forEach(hop => {
                try {
                    const hopInput = document.getElementById(`hop_${hop.power_plant_id}`);
                    if (hopInput) {
                        hopInput.value = hop.hop_value || '';
                    }
                } catch (hopError) {
                    console.error('Error updating HOP:', hopError);
                }
            });
        }
    }

    // Helper function untuk warna status
    function getStatusColor(status) {
        const colors = {
            'Operasi': '#4CAF50',
            'Standby': '#2196F3',
            'Gangguan': '#f44336',
            'Pemeliharaan': '#FF9800',
            'Mothballed': '#9E9E9E',
            'Overhaul': '#673AB7'
        };
        return colors[status] || '#FFFFFF';
    }

    // Load data saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        loadData();
    });

    // Event listener untuk perubahan tanggal
    document.getElementById('filterDate').addEventListener('change', loadData);
</script>
<script>
    function autoResize(textarea) {
        // Set tinggi minimum default
        const minHeight = 60;
        
        // Simpan tinggi scroll saat ini
        const currentScrollHeight = textarea.scrollHeight;
        const currentHeight = parseInt(window.getComputedStyle(textarea).height);
        
        // Hanya resize jika konten benar-benar melebihi area yang tersedia
        if (currentScrollHeight > currentHeight) {
            textarea.style.height = 'auto';
            textarea.style.height = Math.max(minHeight, textarea.scrollHeight) + 'px';
        }
    }

    // Event listener untuk textarea
    document.querySelectorAll('textarea').forEach(textarea => {
        // Set tinggi awal yang tetap
        textarea.style.height = '60px';
        textarea.style.overflow = 'hidden';
        
        // Tambahkan event listener
        textarea.addEventListener('input', function() {
            // Cek apakah scroll diperlukan
            if (this.scrollHeight > this.clientHeight) {
                autoResize(this);
            }
        });
    });
</script>
<script>
    // Tambahkan event listener untuk textarea
    document.querySelectorAll('textarea').forEach(textarea => {
        textarea.addEventListener('click', function() {
            this.style.width = '100%'; // Atur lebar menjadi 100% saat diklik
        });
    });
</script>
<script>
const componentOptions = {
    MESIN: [
        'Cylinder Head',
        'Blok Mesin'
    ],
    GENERATOR: [
        'Stator',
        'Generator',
        'Bearing',
        'Exciter'
    ],
    PANEL_SINKRON: [
        'MCB',
        'Relay',
        'Kontaktor',
        'Fuse'
    ],
    KUBIKAL: [
        'MCB',
        'CT',
        'PT',
        'Busbar',
        'Relay'
    ],
    AUXILIARY: [
        'AVR',
        'PCC'
    ]
};

function updateComponentOptions(systemSelect) {
    const componentSelect = systemSelect.closest('tr').querySelector('.component-select');
    const selectedSystem = systemSelect.value;
    
    componentSelect.innerHTML = '<option value="">Pilih Component</option>';
    componentSelect.disabled = !selectedSystem;
    
    if (selectedSystem) {
        componentOptions[selectedSystem].forEach(component => {
            const option = document.createElement('option');
            option.value = component;
            option.textContent = component;
            componentSelect.appendChild(option);
        });
    }
}

function addNewRow() {
    const tbody = document.getElementById('systemTableBody');
    const newRow = tbody.querySelector('.system-row').cloneNode(true);
    
    // Reset nilai-nilai pada baris baru
    newRow.querySelector('.system-select').value = '';
    newRow.querySelector('.component-select').value = '';
    newRow.querySelector('.component-select').disabled = true;
    newRow.querySelector('input[type="date"]').value = '';
    
    tbody.appendChild(newRow);
}

function deleteRow(button) {
    const tbody = document.getElementById('systemTableBody');
    if (tbody.children.length > 1) {
        button.closest('tr').remove();
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Minimal harus ada satu baris!'
        });
    }
}

// Event delegation untuk system-select
document.getElementById('systemTableBody').addEventListener('change', function(e) {
    if (e.target.classList.contains('system-select')) {
        updateComponentOptions(e.target);
    }
});
</script>
@push('scripts')
@endpush