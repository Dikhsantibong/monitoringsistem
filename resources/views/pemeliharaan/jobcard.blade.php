@extends('layouts.app')

@section('content')
<div class="flex">
    @include('components.pemeliharaan-sidebar')
	<main class="flex-1 p-4 md:p-6">
		<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
			<div>
				<h1 class="text-xl font-semibold text-gray-900">Jobcard (WO Closed)</h1>
				<div class="text-sm text-gray-500">Total: <b>{{ number_format($workOrders->total()) }}</b> dokumen</div>
			</div>
			<form method="GET" action="{{ route('pemeliharaan.jobcard') }}" class="flex items-center gap-2 w-full md:w-auto">
				<div class="flex-1 md:w-72 relative">
					<input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari WO (id, deskripsi, type, priority)" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
				</div>
				<button type="submit" class="bg-blue-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-blue-700 transition">Cari</button>
				@if(!empty($q))
					<a href="{{ route('pemeliharaan.jobcard') }}" class="px-3 py-2 rounded-lg border text-sm hover:bg-gray-50 transition">Reset</a>
				@endif
			</form>
		</div>

        <div class="bg-white rounded-lg shadow p-4">
            @if($workOrders->isEmpty())
                <div class="text-gray-500 text-sm">Tidak ada dokumen.</div>
            @else
				<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
					@foreach($workOrders as $wo)
						<div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm hover:shadow-md transition">
							<div class="flex items-start justify-between gap-3">
								<div class="min-w-0">
									<div class="text-xs text-gray-500">WO #{{ $wo->id }} • <span class="font-medium">{{ $wo->powerPlant->name ?? '-' }}</span></div>
									<div class="mt-1 font-semibold text-gray-900 text-sm leading-snug line-clamp-2">{{ $wo->description }}</div>
								</div>
								<span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 shrink-0">Closed</span>
							</div>
							<div class="mt-3 grid grid-cols-2 gap-y-1 gap-x-2 text-xs text-gray-600">
								<div><span class="text-gray-500">Type</span>: <b>{{ $wo->type ?? '-' }}</b></div>
								<div><span class="text-gray-500">Priority</span>: <b>{{ $wo->priority ?? '-' }}</b></div>
								<div><span class="text-gray-500">Finish</span>: <b>{{ $wo->schedule_finish ? \Carbon\Carbon::parse($wo->schedule_finish)->format('d/m/Y') : '-' }}</b></div>
								<div><span class="text-gray-500">Updated</span>: <b>{{ $wo->updated_at ? $wo->updated_at->format('d/m/Y H:i') : '-' }}</b></div>
							</div>
							@if($wo->document_path)
								<div class="mt-4 flex items-center justify-between gap-3">
									<div class="flex items-center gap-2 text-xs text-gray-500 min-w-0">
										<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
										<span class="truncate" title="{{ basename($wo->document_path) }}">{{ basename($wo->document_path) }}</span>
									</div>
									<div class="flex items-center gap-2 shrink-0">
										<a href="{{ route('admin.laporan.download-document', $wo->id) }}" class="inline-flex items-center gap-2 text-sm px-3 py-1.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" /></svg>
											<span>Unduh</span>
										</a>
									</div>
								</div>
							@endif
						</div>
					@endforeach
				</div>
                <div class="mt-4">{{ $workOrders->links() }}</div>
            @endif
        </div>
    </main>
</div>
@endsection


