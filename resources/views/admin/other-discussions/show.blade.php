@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <x-sidebar />

    <div id="main-content" class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-20">
            <div class="flex justify-between items-center px-6 py-3">
                <h1 class="text-2xl font-semibold text-gray-700">Detail Pembahasan</h1>
                <a href="{{ route('admin.other-discussions.index') }}" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </header>

        <!-- Filter Info -->
        <div class="p-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h2 class="text-lg font-semibold text-blue-800 mb-2">Filter yang Digunakan:</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <span class="font-medium">Tanggal Mulai:</span>
                        <span>{{ $filters['start_date'] ?? 'Semua' }}</span>
                    </div>
                    <div>
                        <span class="font-medium">Tanggal Akhir:</span>
                        <span>{{ $filters['end_date'] ?? 'Semua' }}</span>
                    </div>
                    <div>
                        <span class="font-medium">Pencarian:</span>
                        <span>{{ $filters['search'] ?? 'Semua' }}</span>
                    </div>
                    <div>
                        <span class="font-medium">Unit:</span>
                        <span>{{ $filters['unit'] ?? 'Semua' }}</span>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No SR</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Pembahasan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Topic</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($discussions as $index => $discussion)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $discussion->sr_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $discussion->no_pembahasan }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $discussion->unit }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $discussion->topic }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $discussion->target }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $discussion->pic }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $discussion->status === 'Open' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $discussion->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $discussion->target_deadline ? \Carbon\Carbon::parse($discussion->target_deadline)->format('d/m/Y') : '-' }}
                                </td>
                            </tr>
                            @if($discussion->commitments->count() > 0)
                                <tr class="bg-gray-50">
                                    <td colspan="9" class="px-6 py-4">
                                        <div class="ml-4">
                                            <strong class="text-gray-700">Commitments:</strong>
                                            <ul class="mt-2 space-y-2">
                                                @foreach($discussion->commitments as $commitment)
                                                    <li class="text-sm">
                                                        <span class="font-medium">{{ $commitment->description }}</span>
                                                        <br>
                                                        <span class="text-gray-600">
                                                            PIC: {{ $commitment->pic }} | 
                                                            Deadline: {{ $commitment->deadline ? \Carbon\Carbon::parse($commitment->deadline)->format('d/m/Y') : '-' }} | 
                                                            Status: 
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                                {{ $commitment->status === 'Open' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                                {{ $commitment->status }}
                                                            </span>
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada data yang tersedia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 