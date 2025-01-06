@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <x-sidebar />

    <div id="main-content" class="flex-1 overflow-auto">
        <div class="container mx-auto px-2 sm:px-6 py-4 sm:py-8">
            <div class="bg-white rounded-lg shadow p-3 sm:p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Pembahasan Lain-lain</h2>
                    <a href="{{ route('admin.other-discussions.create') }}" class="btn bg-blue-500 text-white hover:bg-blue-600 rounded-lg px-4 py-2">
                        <i class="fas fa-plus mr-2"></i> Tambah Data
                    </a>
                </div>

                <!-- Filter Section -->
                <div class="mb-4 flex gap-4 items-center">
                    <div class="flex flex-col">
                        <label for="unit-filter" class="text-sm font-medium text-gray-700">Filter Unit</label>
                        <select id="unit-filter" class="mt-1 rounded-lg border-gray-300">
                            <option value="">Semua Unit</option>
                            @foreach(\App\Models\OtherDiscussion::UNITS as $unit)
                                <option value="{{ $unit }}">{{ $unit }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label for="status-filter" class="text-sm font-medium text-gray-700">Filter Status</label>
                        <select id="status-filter" class="mt-1 rounded-lg border-gray-300">
                            <option value="">Semua Status</option>
                            @foreach(\App\Models\OtherDiscussion::STATUSES as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Wrapper for Table with Shadow -->
                <div class="overflow-x-auto shadow-md rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 border-collapse border border-gray-200">
                        <thead class="bg-[#0A749B]" style="height: 50px;">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-16">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-24">No SR</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-24">No WO</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-32">Unit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-[300px]">Topik</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-[300px]">Sasaran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-32">Tingkat Resiko</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-32">Tingkat Prioritas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-[300px]">Komitmen Sebelum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-[300px]">Komitmen Selanjutnya</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-32">PIC</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-24">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-32">Deadline</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-200">
                            @forelse($discussions ?? [] as $index => $discussion)
                            <tr class="hover:bg-gray-50 transition-colors border border-gray-200 border-l-0 border-r-0">
                                <td class="px-4 py-2 whitespace-nowrap border">{{ $index + 1 }}</td>
                                <td class="px-4 py-2 whitespace-nowrap border">{{ $discussion->sr_number }}</td>
                                <td class="px-4 py-2 whitespace-nowrap border">{{ $discussion->wo_number }}</td>
                                <td class="px-4 py-2 whitespace-nowrap border bg-gray-50">{{ $discussion->unit }}</td>
                                <td class="px-4 py-2 whitespace-normal break-words w-[300px] border">{{ $discussion->topic }}</td>
                                <td class="px-4 py-2 whitespace-normal break-words w-[300px] border">{{ $discussion->target }}</td>
                                <td class="px-4 py-2 whitespace-nowrap border bg-gray-50">{{ $discussion->risk_level_label }}</td>
                                <td class="px-4 py-2 whitespace-nowrap border bg-gray-50">{{ $discussion->priority_level }}</td>
                                <td class="px-4 py-2 whitespace-normal break-words w-[300px] border">{{ $discussion->previous_commitment }}</td>
                                <td class="px-4 py-2 whitespace-normal break-words w-[300px] border">{{ $discussion->next_commitment }}</td>
                                <td class="px-4 py-2 whitespace-nowrap border">{{ $discussion->pic }}</td>
                                <td class="px-4 py-2 whitespace-nowrap border bg-gray-50">
                                    <span class="px-2 py-1 rounded text-sm 
                                        {{ $discussion->status === 'Closed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $discussion->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap border">{{ $discussion->deadline->format('d/m/Y') }}</td>
                                <td class="px-4 py-2 whitespace-nowrap border">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.other-discussions.edit', $discussion->id) }}" 
                                           class="text-white btn bg-indigo-500 hover:bg-indigo-600 rounded-lg px-3 py-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="confirmDelete({{ $discussion->id }})" 
                                                class="text-white btn bg-red-500 hover:bg-red-600 rounded-lg px-3 py-1">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="14" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada data yang tersedia
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4 flex justify-between items-center">
                    <div class="text-sm text-gray-700">
                        Menampilkan 
                        {{ ($discussions->currentPage() - 1) * $discussions->perPage() + 1 }} 
                        hingga 
                        {{ min($discussions->currentPage() * $discussions->perPage(), $discussions->total()) }} 
                        dari 
                        {{ $discussions->total() }} 
                        data
                    </div>
                    <div>
                        {{ $discussions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom scrollbar */
.overflow-x-auto::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #ddd;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #ccc;
}

/* Responsive table styles */
.overflow-x-auto {
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
}

@media (max-width: 640px) {
    .container {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    table {
        display: block;
        width: 100%;
    }
}

/* Shadow indicators for table scroll */
.overflow-x-auto::after,
.overflow-x-auto::before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 15px;
    z-index: 2;
    pointer-events: none;
}

.overflow-x-auto::before {
    left: 0;
    background: linear-gradient(to right, rgba(255,255,255,0.9), rgba(255,255,255,0));
}

.overflow-x-auto::after {
    right: 0;
    background: linear-gradient(to left, rgba(255,255,255,0.9), rgba(255,255,255,0));
}
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Data ini akan dihapus secara permanen',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/other-discussions/${id}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endpush
@endsection 