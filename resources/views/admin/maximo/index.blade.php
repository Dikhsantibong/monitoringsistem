@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.sidebar')

    <div id="main-content" class="flex-1 overflow-auto">
        <header class="bg-white shadow-sm sticky z-10">
            <div class="flex justify-between items-center px-6 py-3">
                <h1 class="text-xl font-semibold text-gray-800">
                    Maximo Akses - Work Order
                </h1>
                @include('components.timer')
            </div>
        </header>

        <main class="px-6 mt-4">
            <div class="bg-white rounded-lg shadow p-6">

                <h2 class="text-lg font-semibold mb-4">
                    Data Work Order (SITEID: KD)
                </h2>

                @if(!empty($errorDetail))
                <div class="mt-4 bg-gray-100 border border-gray-300 p-4 rounded text-sm">
                    <p class="font-semibold mb-2">Detail Error (Debug):</p>
            
                    @if(isset($errorDetail['oracle_code']))
                        <p><strong>Oracle Code:</strong> ORA-{{ $errorDetail['oracle_code'] }}</p>
                    @endif
            
                    @if(isset($errorDetail['message']))
                        <p class="break-all"><strong>Message:</strong> {{ $errorDetail['message'] }}</p>
                    @endif
            
                    @if(isset($errorDetail['sql']))
                        <p class="mt-2"><strong>SQL:</strong></p>
                        <pre class="bg-white p-2 border rounded text-xs overflow-x-auto">
            {{ $errorDetail['sql'] }}
                        </pre>
                    @endif
            
                    @if(isset($errorDetail['bindings']))
                        <p class="mt-2"><strong>Bindings:</strong></p>
                        <pre class="bg-white p-2 border rounded text-xs">
            {{ json_encode($errorDetail['bindings'], JSON_PRETTY_PRINT) }}
                        </pre>
                    @endif
                </div>
            @endif
                           

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300">
                        <thead style="background:#0A749B;color:white">
                            <tr>
                                <th class="px-4 py-2">No</th>
                                <th class="px-4 py-2">WO</th>
                                <th class="px-4 py-2">Parent</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Status Date</th>
                                <th class="px-4 py-2">Work Type</th>
                                <th class="px-4 py-2">Description</th>
                                <th class="px-4 py-2">Asset</th>
                                <th class="px-4 py-2">Location</th>
                                <th class="px-4 py-2">Site</th>
                            </tr>
                        </thead>

                        <tbody>
                        @forelse($formattedData as $i => $wo)
                            <tr class="border-b hover:bg-gray-100">
                                <td class="px-4 py-2">{{ $i+1 }}</td>
                                <td class="px-4 py-2">{{ $wo['wonum'] }}</td>
                                <td class="px-4 py-2">{{ $wo['parent'] }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 rounded text-xs
                                        {{ $wo['status'] === 'COMP' ? 'bg-green-100 text-green-700' :
                                           ($wo['status'] === 'WAPPR' ? 'bg-yellow-100 text-yellow-700' :
                                           'bg-gray-100 text-gray-700') }}">
                                        {{ $wo['status'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-2">
                                    {{ $wo['statusdate']
                                        ? $wo['statusdate']->format('d M Y H:i')
                                        : '-' }}
                                </td>
                                <td class="px-4 py-2">{{ $wo['worktype'] }}</td>
                                <td class="px-4 py-2 truncate max-w-md">
                                    {{ $wo['description'] }}
                                </td>
                                <td class="px-4 py-2">{{ $wo['assetnum'] }}</td>
                                <td class="px-4 py-2">{{ $wo['location'] }}</td>
                                <td class="px-4 py-2">{{ $wo['siteid'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-6 text-gray-500">
                                    Tidak ada data Work Order
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </main>
    </div>
</div>
@endsection
