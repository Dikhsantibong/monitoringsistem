@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.sidebar')
    
    <div id="main-content" class="flex-1 overflow-auto">
        <!-- Header -->
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
                    <!-- Desktop Menu Toggle -->
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
                    <h1 class="text-xl font-semibold text-gray-800">Manajemen Laporan</h1>
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
                // ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['name' => 'Laporan ', 'url' => route('admin.laporan.index')],
                ['name' => 'Manajemen', 'url' => null]
            ]" />
        </div>

        <!-- Content -->
        <div class="container mx-auto px-4 py-8">
            <!-- Tab Navigation -->
            <div class="mb-4 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                    <li class="mr-2">
                        <a href="#" class="inline-block p-4 border-b-2 rounded-t-lg tab-btn {{ request('tab') == 'sr' ? 'active' : '' }}" data-tab="sr">
                            Service Request (SR)
                            <span class="ml-2 px-2 py-1 text-xs rounded-full bg-blue-100">
                                {{ $serviceRequests->count() }}
                            </span>
                        </a>
                    </li>
                    <li class="mr-2">
                        <a href="#" class="inline-block p-4 border-b-2 rounded-t-lg tab-btn {{ request('tab') == 'wo' ? 'active' : '' }}" data-tab="wo">
                            Work Order (WO)
                            <span class="ml-2 px-2 py-1 text-xs rounded-full bg-red-100">
                                {{ $workOrders->count() }}
                            </span>
                        </a>
                    </li>
                    <li class="mr-2">
                        <a href="#" class="inline-block p-4 border-b-2 rounded-t-lg tab-btn {{ request('tab') == 'backlog' ? 'active' : '' }}" data-tab="backlog">
                            WO Backlog
                            <span class="ml-2 px-2 py-1 text-xs rounded-full bg-green-100">
                                {{ $backlogs->count() }}
                            </span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- SR Table -->
            <div id="sr-tab" class="tab-content {{ request('tab') == 'sr' ? 'active' : 'hidden' }}">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Manajemen Service Request</h3>
                        <div class="flex items-center gap-3">
                            <form action="{{ route('admin.laporan.print', ['type' => 'sr']) }}" 
                                  method="GET" 
                                  target="_blank"
                                  class="flex items-center gap-2 m-0"
                                  data-type="sr">
                                <div class="flex items-center bg-white rounded-lg border h-10">
                                    <input type="date" 
                                           name="start_date" 
                                           class="h-full px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg appearance-none"
                                           value="{{ request('start_date', now()->format('Y-m-d')) }}">
                                    <span class="text-gray-500 px-2">s/d</span>
                                    <input type="date" 
                                           name="end_date" 
                                           class="h-full px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg appearance-none"
                                           value="{{ request('end_date', now()->format('Y-m-d')) }}">
                                </div>
                                <button type="submit"
                                        class="h-10 px-6 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 inline-flex items-center justify-center">
                                    <i class="fas fa-print mr-2"></i>Print
                                </button>
                            </form>
                            <a href="{{ route('admin.laporan.create-sr') }}" 
                               class="h-10 px-6 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 inline-flex items-center justify-center">
                                <i class="fas fa-plus mr-2"></i>Tambah SR
                            </a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID SR</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioritas</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($serviceRequests as $index => $sr)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">SR-{{ str_pad($sr->id, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-6 py-4 border border-gray-200">{{ $sr->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border border-gray-200
                                            {{ $sr->status == 'Open' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $sr->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $sr->priority }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                        {{ optional($sr->powerPlant)->name ?? 'Unit tidak tersedia' }}
                                    </td>
                                    <td data-date="{{ $sr->created_at }}" class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                        {{ $sr->created_at }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm border border-gray-200">
                                        <button type="button"
                                                data-delete 
                                                data-type="sr" 
                                                data-id="{{ $sr->id }}" 
                                                class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- WO Table -->
            <div id="wo-tab" class="tab-content {{ request('tab') == 'wo' ? 'active' : 'hidden' }}">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Manajemen Work Order</h3>
                        <div class="flex items-center gap-3">
                            <form action="{{ route('admin.laporan.print', ['type' => 'wo']) }}" 
                                  method="GET" 
                                  target="_blank"
                                  class="flex items-center gap-2 m-0"
                                  data-type="wo">
                                <div class="flex items-center bg-white rounded-lg border h-10">
                                    <input type="date" 
                                           name="start_date" 
                                           class="h-full px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg appearance-none"
                                           value="{{ request('start_date', now()->format('Y-m-d')) }}">
                                    <span class="text-gray-500 px-2">s/d</span>
                                    <input type="date" 
                                           name="end_date" 
                                           class="h-full px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg appearance-none"
                                           value="{{ request('end_date', now()->format('Y-m-d')) }}">
                                </div>
                                <button type="submit"
                                        class="h-10 px-6 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 inline-flex items-center justify-center">
                                    <i class="fas fa-print mr-2"></i>Print
                                </button>
                            </form>
                            <a href="{{ route('admin.laporan.create-wo') }}" 
                               class="h-10 px-6 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 inline-flex items-center justify-center">
                                <i class="fas fa-plus mr-2"></i>Tambah WO
                            </a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID WO</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioritas</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($workOrders as $index => $wo)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">WO-{{ str_pad($wo->id, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                        {{ optional($wo->powerPlant)->name ?? 'Unit tidak tersedia' }}
                                    </td>
                                    <td class="px-6 py-4 border border-gray-200">{{ $wo->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $wo->type == 'CM' ? 'bg-blue-100 text-blue-600' : 
                                               ($wo->type == 'PM' ? 'bg-green-100 text-green-600' : 
                                               ($wo->type == 'PDM' ? 'bg-yellow-100 text-yellow-600' : 
                                               ($wo->type == 'PAM' ? 'bg-purple-100 text-purple-600' : 
                                               ($wo->type == 'OH' ? 'bg-red-100 text-red-600' : 
                                               ($wo->type == 'EJ' ? 'bg-indigo-100 text-indigo-600' : 
                                               'bg-gray-100 text-gray-600'))))) }}">
                                            {{ $wo->type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $wo->status == 'Open' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $wo->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $wo->priority }}</td>
                                    <td data-date="{{ $wo->created_at }}" class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                        {{ $wo->created_at }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm border border-gray-200">
                                        <button type="button"
                                                data-delete 
                                                data-type="wo" 
                                                data-id="{{ $wo->id }}" 
                                                class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Backlog Table -->
            <div id="backlog-tab" class="tab-content {{ request('tab') == 'backlog' ? 'active' : 'hidden' }}">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Manajemen WO Backlog</h3>
                        <div class="flex items-center gap-3">
                            <form action="{{ route('admin.laporan.print', ['type' => 'backlog']) }}" 
                                  method="GET" 
                                  target="_blank"
                                  class="flex items-center gap-2 m-0"
                                  data-type="backlog">
                                <div class="flex items-center bg-white rounded-lg border h-10">
                                    <input type="date" 
                                           name="start_date" 
                                           class="h-full px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg appearance-none"
                                           value="{{ request('start_date', now()->format('Y-m-d')) }}">
                                    <span class="text-gray-500 px-2">s/d</span>
                                    <input type="date" 
                                           name="end_date" 
                                           class="h-full px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg appearance-none"
                                           value="{{ request('end_date', now()->format('Y-m-d')) }}">
                                </div>
                                <button type="submit"
                                        class="h-10 px-6 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 inline-flex items-center justify-center">
                                    <i class="fas fa-print mr-2"></i>Print
                                </button>
                            </form>
                            <a href="{{ route('admin.laporan.create-backlog') }}" 
                               class="h-10 px-6 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 inline-flex items-center justify-center">
                                <i class="fas fa-plus mr-2"></i>Tambah Backlog
                            </a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Backlog</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Backlog</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($backlogs as $index => $backlog)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">BL-{{ str_pad($backlog->id, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-6 py-4 border border-gray-200">{{ $backlog->deskripsi }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $backlog->tanggal_backlog }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $backlog->status == 'Open' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $backlog->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                        {{ optional($backlog->powerPlant)->name ?? 'Unit tidak tersedia' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $backlog->keterangan }}</td>
                                    <td data-date="{{ $backlog->created_at }}" class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                        {{ $backlog->created_at }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm border border-gray-200">
                                        <button type="button"
                                                data-delete 
                                                data-type="backlog" 
                                                data-id="{{ $backlog->id }}" 
                                                class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Password Verification Modal -->
<div id="passwordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-xl font-bold mb-4">Verifikasi Password</h2>
        <p class="mb-4">Masukkan password Anda untuk melanjutkan penghapusan</p>
        <input type="password" id="deletePassword" class="w-full p-2 border rounded mb-4" placeholder="Masukkan password">
        <div class="flex justify-end gap-2">
            <button onclick="closePasswordModal()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
            <button onclick="confirmDelete()" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Hapus</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let deleteData = {
    type: null,
    id: null
};

function handleDelete(type, id) {
    deleteData.type = type;
    deleteData.id = id;
    document.getElementById('passwordModal').classList.remove('hidden');
    document.getElementById('passwordModal').classList.add('flex');
    document.getElementById('deletePassword').value = '';
}

function closePasswordModal() {
    document.getElementById('passwordModal').classList.add('hidden');
    document.getElementById('passwordModal').classList.remove('flex');
}

function confirmDelete() {
    const password = document.getElementById('deletePassword').value;
    
    fetch("{{ route('admin.laporan.verify-delete') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            type: deleteData.type,
            id: deleteData.id,
            password: password
        })
    })
    .then(response => response.json())
    .then(data => {
        closePasswordModal();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message
            });
        }
    })
    .catch(error => {
        closePasswordModal();
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat menghapus data'
        });
    });
}

// Tutup modal jika user klik di luar modal
document.getElementById('passwordModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePasswordModal();
    }
});

// Handle tombol Enter pada input password
document.getElementById('deletePassword').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        confirmDelete();
    }
});

// Tab switching functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-btn');
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-tab');
            
            // Update active tab
            tabs.forEach(t => t.classList.remove('active', 'border-blue-500'));
            this.classList.add('active', 'border-blue-500');
            
            // Show correct content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(`${targetId}-tab`).classList.remove('hidden');
        });
    });
});

// Toggle dropdown menu
function toggleDropdown() {
    document.getElementById('dropdown').classList.toggle('hidden');
}

// Close dropdown when clicking outside
window.onclick = function(event) {
    if (!event.target.matches('#dropdownToggle')) {
        var dropdowns = document.getElementsByClassName("dropdown");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (!openDropdown.classList.contains('hidden')) {
                openDropdown.classList.add('hidden');
            }
        }
    }
}

// Tambahkan event listener untuk tombol hapus
document.addEventListener('DOMContentLoaded', function() {
    // Tambahkan event listener untuk semua tombol hapus
    document.querySelectorAll('[data-delete]').forEach(button => {
        button.addEventListener('click', function() {
            const type = this.getAttribute('data-type');
            const id = this.getAttribute('data-id');
            handleDelete(type, id);
        });
    });
});

// Tambahkan fungsi untuk validasi data sebelum print
function validatePrintData(formElement, event) {
    event.preventDefault(); // Hentikan submit form
    
    const startDate = formElement.querySelector('input[name="start_date"]').value;
    const endDate = formElement.querySelector('input[name="end_date"]').value;
    const type = formElement.getAttribute('data-type');
    
    // Cek data berdasarkan tipe tabel
    let tableBody;
    switch(type) {
        case 'sr':
            tableBody = document.querySelector('#sr-tab table tbody');
            break;
        case 'wo':
            tableBody = document.querySelector('#wo-tab table tbody');
            break;
        case 'backlog':
            tableBody = document.querySelector('#backlog-tab table tbody');
            break;
    }
    
    // Hitung jumlah baris yang visible
    const visibleRows = tableBody ? Array.from(tableBody.querySelectorAll('tr')).filter(row => {
        const date = row.querySelector('td[data-date]')?.getAttribute('data-date');
        if (!date) return false;
        
        const rowDate = new Date(date);
        const start = new Date(startDate);
        const end = new Date(endDate);
        end.setHours(23, 59, 59);
        
        return rowDate >= start && rowDate <= end;
    }).length : 0;

    if (visibleRows === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Data Kosong',
            text: `Data pada tanggal ${startDate} s/d ${endDate} kosong`,
            confirmButtonText: 'Tutup'
        });
    } else {
        // Jika ada data, lanjutkan submit form
        formElement.submit();
    }
}

// Tambahkan event listener untuk form print
document.addEventListener('DOMContentLoaded', function() {
    const printForms = document.querySelectorAll('form[action*="print"]');
    printForms.forEach(form => {
        // Tambahkan data-type ke form
        if (form.action.includes('type=sr')) {
            form.setAttribute('data-type', 'sr');
        } else if (form.action.includes('type=wo')) {
            form.setAttribute('data-type', 'wo');
        } else if (form.action.includes('type=backlog')) {
            form.setAttribute('data-type', 'backlog');
        }
        
        form.addEventListener('submit', function(e) {
            validatePrintData(this, e);
        });
    });
});
</script>
@push('styles')
@endpush
@endsection 

<style>
.tab-btn.active {
    border-bottom-color: #3b82f6;
    color: #3b82f6;
}
.tab-content {
    transition: all 0.3s ease-in-out;
}
</style>




{{-- @push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush --}}

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 1500
            });
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                confirmButtonText: 'Tutup'
            });
        });
    </script>
@endif