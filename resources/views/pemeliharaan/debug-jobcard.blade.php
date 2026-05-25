@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Debug Jobcard — {{ $wonum }}</h1>
        <div class="flex flex-wrap gap-3 text-sm">
            <a href="{{ route('pemeliharaan.debug-oracle') }}" class="text-blue-600 hover:underline">Oracle Debugger</a>
            <a href="{{ route('pemeliharaan.debug-jobcard', $wonum) }}?format=json" class="text-gray-600 hover:underline" target="_blank" rel="noopener">JSON</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form method="GET" action="{{ route('pemeliharaan.debug-jobcard', ['wonum' => '__WONUM__']) }}" class="flex flex-wrap gap-4 items-end" id="jobcard-debug-form">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">WONUM lain</label>
                <input type="text" name="wonum_input" value="{{ $wonum }}" class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. WO11636">
            </div>
            <div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow">
                    Buka WO
                </button>
            </div>
        </form>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8 text-sm text-blue-900">
        <p><strong>WONUM diminta:</strong> {{ $wonum }}</p>
        <p><strong>Semua WO terkait:</strong> {{ implode(', ', $allWonums) }}</p>
        @if($childWoError)
            <p class="text-red-700 mt-2"><strong>Child WO error:</strong> {{ $childWoError }}</p>
        @endif
    </div>

    <h2 class="text-xl font-semibold text-gray-800 mb-4">WPLABOR</h2>
    @include('pemeliharaan.partials.oracle-table-block', [
        'title' => 'WPLABOR',
        'rows' => $wplabor['rows'] ?? [],
        'error' => $wplabor['error'] ?? null,
    ])

    <h2 class="text-xl font-semibold text-gray-800 mb-4 mt-2">Hazard &amp; Precaution</h2>
    @include('pemeliharaan.partials.hazard-debug', ['hazardDebug' => $hazardDebug])

    <h2 class="text-xl font-semibold text-gray-800 mb-4 mt-2">Safety lainnya</h2>
    @include('pemeliharaan.partials.oracle-table-block', [
        'title' => 'WOSAFETYLINK',
        'rows' => $wosafetylink['rows'] ?? [],
        'error' => $wosafetylink['error'] ?? null,
    ])
    @include('pemeliharaan.partials.oracle-table-block', [
        'title' => 'WOSAFETYPLAN',
        'rows' => $wosafetyplan['rows'] ?? [],
        'error' => $wosafetyplan['error'] ?? null,
    ])
</div>

<script>
document.getElementById('jobcard-debug-form').addEventListener('submit', function (e) {
    e.preventDefault();
    var wonum = this.querySelector('[name="wonum_input"]').value.trim();
    if (!wonum) return;
    var base = @json(url('/pemeliharaan/debug-jobcard'));
    window.location.href = base + '/' + encodeURIComponent(wonum);
});
</script>
@endsection
