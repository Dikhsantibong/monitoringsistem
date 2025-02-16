@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-gray-50 overflow-auto">
        <!-- Sidebar -->
        @include('components.sidebar')

        <!-- Main Content -->
        <div class="flex-1 overflow-x-hidden overflow-y-auto">
            <!-- Header -->
            <header class="bg-white shadow-sm sticky top-0 z-20">
                <div class="flex justify-between items-center px-6 py-3">
                    <div class="flex items-center gap-x-3">
                        <h1 class="text-xl font-semibold text-gray-800">Tambah Mesin Baru</h1>
                    </div>
                    @include('components.timer')
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
            <div class="mt-3">
                <x-admin-breadcrumb :breadcrumbs="[
                            ['name' => 'Monitor Mesin', 'url' => route('admin.machine-monitor')],
                            ['name' => 'Tambah Mesin', 'url' => null]
                        ]" />
            </div>

            <!-- Konten utama -->
            <div class="container mx-auto px-6 py-8">
                <h3 class="text-gray-700 text-3xl font-medium">Tambah Mesin Baru</h3>

                <div class="mt-8">
                    <form id="createMachineForm" action="{{ route('admin.machine-monitor.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                        @csrf

                        <!-- Nama Mesin -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                                Nama Mesin <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                   required>
                        </div>

                        <!-- Tipe -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="type">
                                Tipe <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="type" 
                                   id="type" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                   required>
                        </div>

                        <!-- No. Seri -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="serial_number">
                                No. Seri <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="serial_number" 
                                   id="serial_number" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                   required>
                        </div>

                        <!-- Unit -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="power_plant_id">
                                Unit <span class="text-red-500">*</span>
                            </label>
                            <select name="power_plant_id" 
                                    id="power_plant_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    required>
                                <option value="">Pilih Unit</option>
                                @foreach(\App\Models\PowerPlant::all() as $powerPlant)
                                    <option value="{{ $powerPlant->id }}">{{ $powerPlant->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- DMN -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="dmn">
                                DMN <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="dmn" 
                                   id="dmn" 
                                   step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                   value="0" 
                                   required>
                        </div>

                        <!-- DMP -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="dmp">
                                DMP <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="dmp" 
                                   id="dmp" 
                                   step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                   value="0" 
                                   required>
                        </div>

                        <!-- Beban (MW) -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="load_value">
                                Beban (MW) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="load_value" 
                                   id="load_value" 
                                   step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                   value="0" 
                                   required>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="flex items-center justify-end">
                            <a href="{{ route('admin.machine-monitor') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline flex items-center">
                                <i class="fas fa-arrow-left mr-2"></i> Batal
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline flex items-center ml-4">
                                <i class="fas fa-save mr-2"></i> Simpan Mesin
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endpush

    <script>
    document.getElementById('createMachineForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        
        submitButton.disabled = true;
        
        // Convert FormData to JSON
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });
        
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: data.name,
                type: data.type,
                serial_number: data.serial_number,
                power_plant_id: parseInt(data.power_plant_id),
                dmn: parseFloat(data.dmn),
                dmp: parseFloat(data.dmp),
                load_value: parseFloat(data.load_value),
                _token: data._token
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = data.redirect_url;
                });
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: error.message || 'Terjadi kesalahan saat menambahkan mesin'
            });
        })
        .finally(() => {
            submitButton.disabled = false;
        });
    });
    </script>
@endsection

<script>
// Tambahkan logika untuk mengubah ukuran sidebar dan menyembunyikan teks pada mode ikon.
const toggleSidebarButton = document.getElementById('toggleSidebar');
const sidebar = document.getElementById('sidebar');
const sidebarContent = document.getElementById('sidebarContent');
const sidebarTexts = document.querySelectorAll('.sidebar-text');

toggleSidebarButton.addEventListener('click', () => {
    // Toggle ukuran sidebar
    sidebar.classList.toggle('w-16');
    sidebar.classList.toggle('w-64');

    // Toggle teks di dalam sidebar
    sidebarTexts.forEach(text => {
        if (sidebar.classList.contains('w-16')) {
            text.classList.add('hidden');
        } else {
            text.classList.remove('hidden');
        }
    });

    // Geser konten ke kiri
    sidebar.classList.toggle('ml-0');
    sidebar.classList.toggle('ml-64');
});
</script>

<script src="{{ asset('js/toggle.js') }}"></script>
@push('scripts')
@endpush
