@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Oracle Database Debugger</h1>

    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form method="GET" action="{{ route('pemeliharaan.debug-oracle') }}" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Table Name</label>
                <input type="text" name="table" value="{{ $table }}" class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. LONGDESCRIPTION">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Column (WHERE)</label>
                <input type="text" name="column" value="{{ $column }}" class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. LDKEY">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                <input type="text" name="value" value="{{ $value }}" class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. 710309 or TICKETUID">
            </div>
            <div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow">
                    Query Data
                </button>
            </div>
        </form>
    </div>

    @if($error)
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-8" role="alert">
            <p class="font-bold">Database Error</p>
            <p>{{ $error }}</p>
        </div>
    @endif

    @if(!empty($value))
        <div class="bg-white rounded-lg shadow-md p-6 overflow-x-auto">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Results ({{ count($results) }} found)</h2>
            
            @if(count($results) > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach((array)$results[0] as $key => $val)
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $key }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($results as $row)
                            <tr>
                                @foreach((array)$row as $val)
                                    <td class="px-6 py-4 whitespace-pre-wrap text-sm text-gray-900" style="max-width: 300px;">{{ is_array($val) || is_object($val) ? json_encode($val) : $val }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500 italic">No records found for {{ $column }} = {{ $value }} in table {{ $table }}.</p>
                <div class="mt-4 p-4 bg-yellow-50 text-yellow-800 rounded text-sm">
                    <strong>Tip:</strong> If querying LONGDESCRIPTION for an SR, try using the TICKETUID instead of TICKETID, because Maximo usually links LDKEY to the unique ID (TICKETUID). Try querying table <code>SR</code> with column <code>TICKETID</code> = your SR number to find its <code>TICKETUID</code> first.
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
