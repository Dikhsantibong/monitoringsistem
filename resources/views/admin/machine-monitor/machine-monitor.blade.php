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
            <!-- Tambahkan link lainnya di sini -->
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 overflow-x-hidden overflow-y-auto">
        <div class="container mx-auto px-6 py-8">
            <h3 class="text-gray-700 text-3xl font-medium">Daftar Mesin</h3>

            <div class="mt-8">
                <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Nama Mesin</th>
                            <th class="py-3 px-6 text-left">Kode Mesin</th>
                            <th class="py-3 px-6 text-left">Status</th>
                            <th class="py-3 px-6 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        @foreach($machines as $machine)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6">{{ $machine->name }}</td>
                            <td class="py-3 px-6">{{ $machine->code }}</td>
                            <td class="py-3 px-6">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    {{ $machine->status === 'START' ? 'bg-green-100 text-green-800' : 
                                       ($machine->status === 'STOP' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $machine->status }}
                                </span>
                            </td>
                            <td class="py-3 px-6">
                                <a href="{{ route('admin.machines.show', $machine->id) }}" class="text-blue-500 hover:text-blue-700">Lihat</a>
                                <a href="{{ route('admin.machines.edit', $machine->id) }}" class="text-green-500 hover:text-green-700 ml-4">Edit</a>
                                <button onclick="confirmDelete({{ $machine->id }})" class="text-red-500 hover:text-red-700 ml-4">Hapus</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(machineId) {
    if (confirm('Apakah Anda yakin ingin menghapus mesin ini?')) {
        fetch(`/admin/machine-monitor/${machineId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal menghapus mesin');
            }
        });
    }
}
</script>
@endsection
