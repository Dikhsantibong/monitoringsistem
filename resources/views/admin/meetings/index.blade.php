@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#0A749B] shadow-md">
        <div class="p-4">
            <img src="{{ asset('logo/navlogo.png') }}" alt="Logo Aplikasi Rapat Harian" class="w-40 h-15">
        </div>  
        <nav class="mt-4">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-home mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.score-card.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.score-card.*') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-clipboard-list mr-3"></i>
                <span>Score Card Daily</span>
            </a>
            <a href="{{ route('admin.daftar_hadir.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.daftar_hadir.index') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-list mr-3"></i>
                <span>Daftar Hadir</span>
            </a>
            <a href="{{ route('admin.pembangkit.ready') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.pembangkit.ready') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-check mr-3"></i>
                <span>Kesiapan Pembangkit</span>
            </a>
            <a href="{{ route('admin.laporan.sr_wo') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.laporan.sr_wo') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-file-alt mr-3"></i>
                <span>Laporan SR/WO</span>
            </a>
            <a href="{{ route('admin.machine-monitor') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.machine-monitor') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-cogs mr-3"></i>
                <span>Monitor Mesin</span>
            </a>
            <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.users') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-users mr-3"></i>
                <span>Manajemen Pengguna</span>
            </a>
            <a href="{{ route('admin.meetings') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.meetings') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-chart-bar mr-3"></i>
                <span>Laporan Rapat</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.settings') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                <i class="fas fa-cog mr-3"></i>
                <span>Pengaturan</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <header class="bg-white shadow-sm">
            <div class="flex justify-between items-center px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">Laporan Rapat</h1>
                
                <div class="flex items-center space-x-4">
                    <button onclick="exportMeetings()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        <i class="fas fa-download mr-2"></i>Ekspor
                    </button>
                    <button onclick="uploadMeeting()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                        <i class="fas fa-upload mr-2"></i>Upload Rapat
                    </button>
                    <button onclick="openModal()" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                        <i class="fas fa-plus mr-2"></i>Buat Rapat Baru
                    </button>
                </div>
                
            </div>
            <x-admin-breadcrumb :breadcrumbs="[
                ['name' => 'Laporan Rapat', 'url' => null]
            ]" />
        </header>

        <main class="p-6">
            <!-- Filter Section -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Filter Rapat</h2>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rentang Tanggal</label>
                            <input type="date" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" id="date-filter">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Departemen</label>
                            <select class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" id="department-filter">
                                <option value="">Semua Departemen</option>
                                @foreach($departments ?? [] as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" id="status-filter">
                                <option value="">Semua Status</option>
                                <option value="scheduled">Terjadwal</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button onclick="applyFilter()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                <i class="fas fa-filter mr-2"></i>Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Hasil Rapat -->
            <div class="bg-white rounded-lg shadow mb-6 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Hasil Rapat</h2>
                <table class="min-w-full divide-y divide-gray-200 border-collapse border border-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-center font-medium text-gray-500">Judul</th>
                            <th class="px-6 py-3 text-center font-medium text-gray-500">Tanggal</th>
                            <th class="px-6 py-3 text-center font-medium text-gray-500">Departemen</th>
                            <th class="px-6 py-3 text-center font-medium text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($meetings ?? [] as $meeting)
                            <tr class="odd:bg-white even:bg-gray-100">
                                <td>{{ $meeting->title }}</td>
                                <td>{{ $meeting->scheduled_at->format('F j, Y') }}</td>
                                <td>{{ $meeting->department->name ?? 'Tidak Ada' }}</td>
                                <td>{{ $meeting->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<!-- Modal -->
<div id="createMeetingModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 md:w-1/2">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Buat Rapat Baru</h2>
        <iframe src="{{ route('admin.meetings.create') }}" class="w-full h-96" frameborder="0"></iframe>
        <button onclick="closeModal()" class="mt-4 bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">Tutup</button>
    </div>
</div>

<style>
    .modal-enter {
        opacity: 0;
        transform: scale(0.7);
    }
    .modal-enter-active {
        opacity: 1;
        transform: scale(1);
        transition: opacity 0.3s, transform 0.3s;
    }
    .modal-leave {
        opacity: 1;
        transform: scale(1);
    }
    .modal-leave-active {
        opacity: 0;
        transform: scale(0.7);
        transition: opacity 0.3s, transform 0.3s;
    }
</style>

<script>
    document.getElementById('upload-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('{{ route("admin.meetings.upload") }}', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('upload-message').innerText = data.message;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('upload-message').innerText = 'Upload failed.';
        });
    });

    function openModal() {
        const modal = document.getElementById('createMeetingModal');
        modal.classList.remove('hidden');
        modal.classList.add('modal-enter');
        setTimeout(() => {
            modal.classList.remove('modal-enter');
            modal.classList.add('modal-enter-active');
        }, 10); // Delay untuk memastikan animasi diterapkan
    }

    function closeModal() {
        const modal = document.getElementById('createMeetingModal');
        modal.classList.remove('modal-enter-active');
        modal.classList.add('modal-leave');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('modal-leave');
        }, 300); // Delay untuk menunggu animasi selesai
    }
</script>

@push('scripts')
@endpush
@endsection 