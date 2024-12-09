@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md">
        <div class="p-4">
            <img src="{{ asset('logo/navlogo.png') }}" alt="Logo Aplikasi Rapat Harian" class="w-40 h-15">
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
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 overflow-x-hidden overflow-y-auto">
        <div class="container mx-auto px-6 py-8">
            <div class="flex justify-between items-center">
                <h3 class="text-gray-700 text-3xl font-medium">Detail Mesin</h3>
                <div class="flex space-x-4">
                    <button onclick="openEditModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </button>
                    <button onclick="confirmDelete()" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                        <i class="fas fa-trash mr-2"></i>Hapus
                    </button>
                </div>
            </div>

            <!-- Machine Details -->
            <div class="bg-white shadow rounded-lg mt-6">
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-lg font-semibold mb-4">Informasi Dasar</h4>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm text-gray-600">Nama Mesin</p>
                                    <p class="text-lg font-medium">{{ $machine->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Kode Mesin</p>
                                    <p class="text-lg font-medium">{{ $machine->code }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Status</p>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium
                                        {{ $machine->status === 'START' ? 'bg-green-100 text-green-800' : 
                                           ($machine->status === 'STOP' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ $machine->status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold mb-4">Statistik</h4>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm text-gray-600">Total Jam Operasi</p>
                                    <p class="text-lg font-medium">{{ $machine->metrics->sum('operation_hours') ?? 0 }} jam</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Efisiensi</p>
                                    <p class="text-lg font-medium">{{ number_format($machine->metrics->avg('efficiency') ?? 0, 1) }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Mesin</h3>
            <form id="editForm">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Mesin</label>
                    <input type="text" name="name" value="{{ $machine->name }}" 
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Kode Mesin</label>
                    <input type="text" name="code" value="{{ $machine->code }}" 
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                        <option value="START" {{ $machine->status === 'START' ? 'selected' : '' }}>Start</option>
                        <option value="STOP" {{ $machine->status === 'STOP' ? 'selected' : '' }}>Stop</option>
                        <option value="PARALLEL" {{ $machine->status === 'PARALLEL' ? 'selected' : '' }}>Parallel</option>
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeEditModal()" 
                            class="mr-2 px-4 py-2 text-gray-500 hover:text-gray-700">Batal</button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openEditModal() {
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('{{ route("admin.machines.update", $machine->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            name: formData.get('name'),
            code: formData.get('code'),
            status: formData.get('status'),
            _method: 'PUT'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Gagal mengupdate mesin');
        }
    });
});

function confirmDelete() {
    if (confirm('Apakah Anda yakin ingin menghapus mesin ini?')) {
        fetch('{{ route("admin.machines.destroy", $machine->id) }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("admin.machine-monitor") }}';
            } else {
                alert('Gagal menghapus mesin');
            }
        });
    }
}

// Close modal when clicking outside
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>
@endpush
@endsection 