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
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kesiapan Pembangkit</h2>
                    <div class="mb-4 flex flex-col lg:flex-row justify-between items-center gap-3">
                        <div class="flex flex-col lg:flex-row gap-y-3 sm:gap-y-3 space-x-4">
                            <input type="date" id="filterDate" value="{{ date('Y-m-d') }}"
                                class="px-4 py-2 border rounded-lg">

                            <div class="flex items-center">
                                <input type="text" id="searchInput" placeholder="Cari mesin, unit, atau status..."
                                    class="pl-5 pr-4 py-2 border rounded-l-lg"
                                    onkeyup="if(event.key === 'Enter') searchTables()">
                                <button onclick="searchTables()"
                                    class="bg-blue-500 text-white px-3 py-2 rounded-r-lg hover:bg-blue-600">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex space-x-4">
                            <div class="max-w-full">
                                <button onclick="confirmReset()"
                                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                                    <i class="fas fa-refresh mr-2"></i>Reset
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
                    @foreach ($units as $unit)
                        <div class="bg-white rounded-lg shadow p-6 mb-4 unit-table">
                            <div class=" overflow-auto">
                                <h1 class="text-lg font-semibold uppercase mb-5" >sistem interkoneksi Sultra ( MEGAWATT /MW )
                                </h1>

                                <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ $unit->name }}</h2>
                     
                                <!-- Tabel Status Pembangkit -->
                                <div class="table-responsive">
                                    <table class="min-w-full bg-white">
                                        <thead>
                                            <tr>
                                                    <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">
                                                    Mesin
                                                </th>
                                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">
                                                    DMN
                                                </th>
                                                <th class="px-3 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">
                                                    DMP
                                                </th>
                                                <th class="px-2 py-2.5 bg-[#0A749B] text-white text-sm font-medium tracking-wider text-center border-r border-[#0A749B]">
                                                    Beban
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
                                           
                                            @foreach ($unit->machines as $machine)
                                                <tr class="hover:bg-gray-50 border-b border-gray-200">
                                                    <td class="px-3 py-2 border-r border-gray-200 text-gray-800" data-id="{{ $machine->id }}">
                                                        {{ $machine->name }}
                                                    </td>   
                                                    <td class="px-3 py-2 border-r border-gray-200 text-center text-gray-800 w-12">
                                                        {{ $operations->where('machine_id', $machine->id)->first()->dmn ?? 'N/A' }}
                                                    </td>
                                                    <td class="px-3 py-2 border-r border-gray-200 text-center text-gray-800 w-12">
                                                        <input type="number" 
                                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:border-blue-400 text-gray-800"
                                                               style="width: 50px;"     value="{{ $operations->where('machine_id', $machine->id)->first()->dmp ?? '0' }}"
                                                            placeholder="Masukkan DMP...">
                                                    </td>
                                                    <td class="px-2 py-2 border-r border-gray-200">
                                                        <input type="number" 
                                                               class="w-12 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:border-blue-400 text-gray-800"
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
                                                        name="equipment" 
                                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:border-blue-400 text-gray-80"
                                                        style="height: 100px; width: 150px;" 
                                                        cols="30" 
                                                        rows="10"
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
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
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

        fetch(`{{ route('admin.pembangkit.get-status') }}?tanggal=${filterDate}&search=${searchInput}`)
            .then(response => response.json())
            .then(result => {
                // Hapus loading indicator
                loadingIndicator.remove();

                if (result.success) {
                    updateTablesWithSearchResult(result.data);
                } else {
                    // Sembunyikan semua tabel
                    const unitTables = document.getElementsByClassName('unit-table');
                    Array.from(unitTables).forEach(table => {
                        table.style.display = 'none';
                    });

                    // Tampilkan pesan tidak ada data
                    const noDataMessage = document.createElement('div');
                    noDataMessage.className = 'text-center py-8 text-gray-600 font-semibold text-lg';
                    noDataMessage.textContent = 'DATA TIDAK DITEMUKAN';
                    mainContent.appendChild(noDataMessage);
                }
            })
            .catch(error => {
                // Hapus loading indicator jika terjadi error
                loadingIndicator.remove();
                console.error('Error:', error);
            });
    }

    function updateTablesWithSearchResult(data) {
        const unitTables = document.getElementsByClassName('unit-table');
        const mainContent = document.querySelector('.bg-white.rounded-lg.shadow.p-6');

        // Hapus pesan "Data tidak ditemukan" jika ada
        const existingMessage = mainContent.querySelector('.text-center.py-8');
        if (existingMessage) {
            existingMessage.remove();
        }

        // Sembunyikan semua tabel dan baris terlebih dahulu
        Array.from(unitTables).forEach(table => {
            table.style.display = 'none';
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => row.style.display = 'none');
        });

        // Tampilkan hanya data yang sesuai dengan pencarian
        data.forEach(log => {
            const machineId = log.machine_id;
            const row = document.querySelector(`td[data-id="${machineId}"]`)?.closest('tr');
            const unitTable = row?.closest('.unit-table');

            if (row && unitTable) {
                unitTable.style.display = '';
                row.style.display = '';

                // Update nilai-nilai di row
                const select = row.querySelector('select');
                const inputKeterangan = row.querySelector('input[type="text"]');
                const inputBeban = row.querySelector('td:nth-child(4) input');

                if (select) {
                    select.value = log.status || '';
                    select.style.backgroundColor = getStatusColor(log.status);
                }
                if (inputKeterangan) inputKeterangan.value = log.keterangan || '';
                if (inputBeban) inputBeban.value = log.load_value || '';
            }
        });
    }

    function getStatusColor(status) {
        const colors = {
            'Operasi': '#4CAF50',
            'Standby': '#2196F3',
            'Gangguan': '#f44336',
            'Pemeliharaan': '#FF9800'
        };
        return colors[status] || '#FFFFFF';
    }

    // Event listeners
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            searchTables();
        }
    });

    document.getElementById('filterDate').addEventListener('change', searchTables);
</script>

<script>
    function saveData() {
        const data = [];
        const tables = document.querySelectorAll('.unit-table table');
        const tanggal = document.getElementById('filterDate').value;

        tables.forEach(table => {
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const machineId = row.querySelector('td[data-id]').getAttribute('data-id');
                const statusSelect = row.querySelector('select');
                const componentSelect = row.querySelector('.system-select'); // Mengambil nilai component
                const equipmentTextarea = row.querySelector('td:nth-child(7) textarea[name="equipment"]'); // Mengambil nilai equipment
                
                // Ambil nilai DMN dan DMP
                const dmnCell = row.querySelector('td:nth-child(2)');
                const dmpInput = row.querySelector('td:nth-child(3) input'); // Mengambil input DMP
                
                const inputDeskripsi = row.querySelector(`textarea[name="deskripsi[${machineId}]"]`);
                const inputActionPlan = row.querySelector(`textarea[name="action_plan[${machineId}]"]`);
                const inputBeban = row.querySelector('td:nth-child(4) input');
                const inputProgres = row.querySelector(`textarea[name="progres[${machineId}]"]`);
                const inputKronologi = row.querySelector(`textarea[name="kronologi[${machineId}]"]`);
                const inputTanggalMulai = row.querySelector(`input[name="tanggal_mulai[${machineId}]"]`);
                const inputTargetSelesai = row.querySelector(`input[name="target_selesai[${machineId}]"]`);

                if (statusSelect && statusSelect.value) {
                    data.push({
                        machine_id: machineId,
                        tanggal: tanggal,
                        status: statusSelect.value,
                        component: componentSelect ? componentSelect.value : null,
                        equipment: equipmentTextarea ? equipmentTextarea.value.trim() : null,
                        dmn: dmnCell ? dmnCell.textContent.trim() : 'N/A',
                        dmp: dmpInput ? dmpInput.value.trim() : 'N/A',
                        deskripsi: inputDeskripsi ? inputDeskripsi.value.trim() : null,
                        action_plan: inputActionPlan ? inputActionPlan.value.trim() : null,
                        load_value: inputBeban ? parseFloat(inputBeban.value) || null : null,
                        progres: inputProgres ? inputProgres.value.trim() : null,
                        kronologi: inputKronologi ? inputKronologi.value.trim() : null,
                        tanggal_mulai: inputTanggalMulai ? inputTanggalMulai.value : null,
                        target_selesai: inputTargetSelesai ? inputTargetSelesai.value : null
                    });
                }
            });
        });
        
        // Debug: Tampilkan data yang akan dikirim
        console.log('Data yang akan dikirim:', data);

        if (data.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih status terlebih dahulu!'
            });
            return;
        }

        // Tampilkan loading indicator
        Swal.fire({
            title: 'Menyimpan Data',
            text: 'Mohon tunggu...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

            fetch('{{ route('admin.pembangkit.save-status') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    logs: data
                })
            })
            .then(response => response.json())
            .then(result => {
                console.log(result);
                if (result.success) {
                    Swal.fire({
                        icon: 'success',    
                        title: 'Berhasil',
                        text: 'Data berhasil disimpan!',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // Refresh data setelah berhasil simpan
                        loadData();
                    });
                } else {
                    throw new Error(result.message || 'Gagal menyimpan data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
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
            text: "Data akan direset!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, reset!'
        }).then((result) => {
            if (result.isConfirmed) {
                resetForm();
                Swal.fire(
                    'Reset!',
                    'Data telah direset.',
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
                const select = row.querySelector('select');
                const inputKeterangan = row.querySelector('input[type="text"]');
                const inputBeban = row.querySelector('td:nth-child(4) input');

                // Reset semua input
                select.value = '';
                select.style.backgroundColor = '';
                inputKeterangan.value = '';
                inputBeban.value = '';
            });
        });

    }

    // Fungsi untuk memuat data
    function loadData() {
        const tanggal = document.getElementById('filterDate').value;

        // Tampilkan loading indicator
        Swal.fire({
            title: 'Memuat Data',
            text: 'Mohon tunggu...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(`{{ route('admin.pembangkit.get-status') }}?tanggal=${tanggal}`)
            .then(response => response.json())
            .then(result => {
                Swal.close();
                if (result.success) {
                    updateFormWithData(result.data);
                } else {
                    resetForm();
                    Swal.fire({
                        icon: 'info',
                        title: 'Informasi',
                        text: result.message || 'Tidak ada data untuk tanggal ini'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat mengambil data!'
                });
            });
    }

    // Fungsi untuk mengupdate form dengan data
    function updateFormWithData(data) {
        const tables = document.querySelectorAll('.unit-table table');
        resetForm();

        tables.forEach(table => {
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const machineId = row.querySelector('td[data-id]').getAttribute('data-id');
                const machineData = data.find(d => d.machine_id == machineId);

                if (machineData) {
                    // Update status
                    const statusSelect = row.querySelector('select');
                    if (statusSelect && machineData.status) {
                        statusSelect.value = machineData.status;
                        statusSelect.style.backgroundColor = getStatusColor(machineData.status);
                    }

                    // Update component
                    const componentSelect = row.querySelector('.system-select');
                    if (componentSelect && machineData.component) {
                        componentSelect.value = machineData.component;
                    }

                    // Update equipment
                    const equipmentTextarea = row.querySelector('td:nth-child(7) textarea[name="equipment"]');
                    if (equipmentTextarea && machineData.equipment) {
                        equipmentTextarea.value = machineData.equipment;
                        autoResize(equipmentTextarea);
                    }

                    // Update kolom lainnya
                    const inputBeban = row.querySelector('td:nth-child(4) input');
                    if (inputBeban) {
                        inputBeban.value = machineData.load_value || '';
                    }

                    // Update deskripsi
                    const inputDeskripsi = row.querySelector(`textarea[name="deskripsi[${machineId}]"]`);
                    if (inputDeskripsi) {
                        inputDeskripsi.value = machineData.deskripsi || '';
                    }

                    // Update kronologi
                    const inputKronologi = row.querySelector(`textarea[name="kronologi[${machineId}]"]`);
                    if (inputKronologi) {
                        inputKronologi.value = machineData.kronologi || '';
                    }

                    // Update action plan
                    const inputActionPlan = row.querySelector(`textarea[name="action_plan[${machineId}]"]`);
                    if (inputActionPlan) {
                        inputActionPlan.value = machineData.action_plan || '';
                    }

                    // Update progres
                    const inputProgres = row.querySelector(`textarea[name="progres[${machineId}]"]`);
                    if (inputProgres) {
                        inputProgres.value = machineData.progres || '';
                    }

                    // Update tanggal mulai
                    const inputTanggalMulai = row.querySelector(`input[name="tanggal_mulai[${machineId}]"]`);
                    if (inputTanggalMulai) {
                        inputTanggalMulai.value = machineData.tanggal_mulai || '';
                    }

                    // Update target selesai
                    const inputTargetSelesai = row.querySelector(`input[name="target_selesai[${machineId}]"]`);
                    if (inputTargetSelesai) {
                        inputTargetSelesai.value = machineData.target_selesai || '';
                    }

                    // Update DMN dan DMP (sebagai text saja, karena readonly)
                    const dmnCell = row.querySelector('td:nth-child(2)');
                    const dmpCell = row.querySelector('td:nth-child(3)');
                    if (dmnCell) dmnCell.textContent = machineData.dmn || 'N/A';
                    if (dmpCell) dmpCell.textContent = machineData.dmp || 'N/A';
                }
            });
        });
    }

    // Helper function untuk mendapatkan warna status
    function getStatusColor(status) {
        const colors = {
            'Operasi': '#4CAF50',
            'Standby': '#2196F3',
            'Gangguan': '#f44336',
            'Pemeliharaan': '#FF9800'
        };
        return colors[status] || '#FFFFFF';
    }

    // Fungsi reset form yang diperbaiki
    function resetForm() {
        const tables = document.querySelectorAll('.unit-table table');
        tables.forEach(table => {
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const select = row.querySelector('select');
                const inputBeban = row.querySelector('td:nth-child(4) input');
                const machineId = row.querySelector('td[data-id]').getAttribute('data-id');

                // Reset semua input
                if (select) {
                    select.value = '';
                    select.style.backgroundColor = '';
                }
                if (inputBeban) {
                    inputBeban.value = '';
                }

                // Reset semua textarea
                const textareas = row.querySelectorAll('textarea');
                textareas.forEach(textarea => {
                    textarea.value = '';
                });

                // Reset target selesai
                const inputTargetSelesai = row.querySelector(`input[name="target_selesai[${machineId}]"]`);
                if (inputTargetSelesai) {
                    inputTargetSelesai.value = '';
                }

                // Reset DMN dan DMP cells
                const dmnCell = row.querySelector('td:nth-child(2)');
                const dmpCell = row.querySelector('td:nth-child(3)');
                if (dmnCell) dmnCell.textContent = 'N/A';
                if (dmpCell) dmpCell.textContent = 'N/A';
            });
        });
    }

    // Event listener untuk tanggal
    document.getElementById('filterDate').addEventListener('change', loadData);

    // Load data saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        loadData();


    });
</script>
<script>
    function autoResize(textarea) {
        textarea.style.height = 'auto'; // Reset height
        textarea.style.height = textarea.scrollHeight + 'px'; // Set to scroll height
        textarea.style.width = '100%'; // Atur lebar menjadi 100% saat diklik
    }
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
