@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Oracle Database Debugger</h1>

    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Query Tabel Umum</h2>
        <form method="GET" action="{{ route('pemeliharaan.debug-oracle') }}" class="flex flex-wrap gap-4 items-end">
            @if(!empty($wonum))
                <input type="hidden" name="wonum" value="{{ $wonum }}">
            @endif
            <div class="flex-1 min-w-[140px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Table Name</label>
                <input type="text" name="table" value="{{ $table }}" class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. LONGDESCRIPTION">
            </div>
            <div class="flex-1 min-w-[140px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Column (WHERE)</label>
                <input type="text" name="column" value="{{ $column }}" class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. LDKEY">
            </div>
            <div class="flex-1 min-w-[140px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                <input type="text" name="value" value="{{ $value }}" class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. 710309">
            </div>
            <div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow">
                    Query Data
                </button>
            </div>
        </form>
        <p class="mt-3 text-sm text-gray-500">
            Preset hazard: <code>WOHAZARD</code> + <code>WONUM</code>, <code>HAZARD</code> + <code>HAZARDID</code>, <code>PRECAUTION</code> + <code>PRECAUTIONID</code>
        </p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-8 border-l-4 border-amber-500">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Hazard &amp; Precaution (WO)</h2>
        <form method="GET" action="{{ route('pemeliharaan.debug-oracle') }}" class="flex flex-wrap gap-4 items-end">
            @if(!empty($value))
                <input type="hidden" name="table" value="{{ $table }}">
                <input type="hidden" name="column" value="{{ $column }}">
                <input type="hidden" name="value" value="{{ $value }}">
            @endif
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">WONUM</label>
                <input type="text" name="wonum" value="{{ $wonum ?? '' }}" class="w-full rounded border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500" placeholder="e.g. 1234567">
            </div>
            <div>
                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-6 rounded shadow">
                    Query Hazard Tables
                </button>
            </div>
        </form>
        <p class="mt-3 text-sm text-gray-500">
            Atau buka halaman jobcard:
            <a href="{{ route('pemeliharaan.debug-jobcard', ['wonum' => $wonum ?: 'WO11636']) }}" class="text-blue-600 hover:underline">/pemeliharaan/debug-jobcard/{wonum}</a>
        </p>
    </div>

    @if($error)
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-8" role="alert">
            <p class="font-bold">Database Error (query umum)</p>
            <p>{{ $error }}</p>
        </div>
    @endif

    @if(!empty($hazardDebug))
        @include('pemeliharaan.partials.hazard-debug', ['hazardDebug' => $hazardDebug])
        <p class="text-sm text-gray-600 mb-8">
            Tampilan jobcard lengkap:
            <a href="{{ route('pemeliharaan.debug-jobcard', $hazardDebug['requested_wonum']) }}" class="text-blue-600 hover:underline">{{ route('pemeliharaan.debug-jobcard', $hazardDebug['requested_wonum']) }}</a>
        </p>
    @endif

    @if(!empty($value))
        <div class="bg-white rounded-lg shadow-md p-6 overflow-x-auto">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Hasil Query Umum — {{ $table }} ({{ count($results) }} baris)</h2>

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
                    <strong>Tip:</strong> Untuk SR di LONGDESCRIPTION, coba <code>TICKETUID</code> sebagai LDKEY. Atau query <code>SR</code> dengan <code>TICKETID</code> untuk mendapatkan TICKETUID.
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
