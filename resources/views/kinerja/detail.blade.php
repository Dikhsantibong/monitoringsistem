@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $title }}</h1>
            <p class="text-gray-500">Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
        </div>
        <a href="{{ url()->previous() }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-700 uppercase font-semibold border-b">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        @if($isSR)
                            <th class="px-6 py-3">Ticket ID</th>
                            <th class="px-6 py-3">Summary</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Location</th>
                            <th class="px-6 py-3">Report Date</th>
                        @else
                            <th class="px-6 py-3">WONUM</th>
                            <th class="px-6 py-3">Description</th>
                            <th class="px-6 py-3">Work Type</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Schedule start</th>
                            <th class="px-6 py-3">Actual start</th>
                            <th class="px-6 py-3">Schedule finish</th>
                            <th class="px-6 py-3">Actual finish</th>
                            <th class="px-6 py-3">Location</th>
                            <th class="px-6 py-3">Report Date</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($results as $index => $row)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">{{ $index + 1 }}</td>
                            @if($isSR)
                                <td class="px-6 py-4 font-medium text-blue-600">{{ $row->ticketid }}</td>
                                <td class="px-6 py-4">{{ $row->description }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                        {{ $row->status == 'NEW' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $row->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $row->location }}</td>
                                <td class="px-6 py-4">{{ date('d/m/Y', strtotime($row->reportdate)) }}</td>
                            @else
                                <td class="px-6 py-4 font-medium text-blue-600">{{ $row->wonum }}</td>
                                <td class="px-6 py-4">{{ $row->description }}</td>
                                <td class="px-6 py-4 text-center">{{ $row->worktype }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                        @if(in_array($row->status, ['COMP', 'CLOSE'])) bg-green-100 text-green-800 
                                        @elseif(in_array($row->status, ['WAPPR', 'APPR'])) bg-yellow-100 text-yellow-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ $row->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $row->schedstart ? date('d/m/Y', strtotime($row->schedstart)) : '-' }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $row->actstart ? date('d/m/Y', strtotime($row->actstart)) : '-' }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $row->schedfinish ? date('d/m/Y', strtotime($row->schedfinish)) : '-' }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $row->actfinish ? date('d/m/Y', strtotime($row->actfinish)) : '-' }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $row->location }}</td>
                                <td class="px-6 py-4">{{ date('d/m/Y', strtotime($row->reportdate)) }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-6 py-10 text-center text-gray-500 italic">Data tidak ditemukan untuk periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* Ensure styles match dashboard aesthetic */
    body { background-color: #f8fafc; }
    .container-fluid { max-width: 1600px; margin: 0 auto; }
</style>
@endsection
