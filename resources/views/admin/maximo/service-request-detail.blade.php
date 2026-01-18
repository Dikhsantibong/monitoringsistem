@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.sidebar')

    <div id="main-content" class="flex-1 overflow-auto">
        <header class="bg-white shadow-sm sticky top-0">
            <div class="flex justify-between items-center px-6 py-3">
                <div class="flex items-center gap-x-3">
                    <h1 class="text-xl font-semibold text-gray-800">Detail Service Request</h1>
                </div>
                <div class="flex items-center gap-x-4">
                    <a href="{{ url()->previous() }}" class="px-3 py-2 rounded border bg-white hover:bg-gray-50 text-sm">
                        Kembali
                    </a>
                </div>
            </div>
        </header>

        <main class="px-6 mt-4">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="mb-4">
                    <div class="text-sm text-gray-500">TICKETID</div>
                    <div class="text-lg font-semibold text-gray-800">{{ $sr['ticketid'] ?? '-' }}</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border rounded p-4">
                        <div class="text-sm text-gray-500 mb-2">Info</div>
                        <dl class="text-sm space-y-2">
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">Status</dt><dd class="font-medium text-gray-800">{{ $sr['status'] ?? '-' }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">Status Date</dt><dd class="font-medium text-gray-800">{{ $sr['statusdate'] ?? '-' }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">Reported By</dt><dd class="font-medium text-gray-800">{{ $sr['reportedby'] ?? '-' }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">Report Date</dt><dd class="font-medium text-gray-800">{{ $sr['reportdate'] ?? '-' }}</dd></div>
                        </dl>
                    </div>

                    <div class="border rounded p-4">
                        <div class="text-sm text-gray-500 mb-2">Asset & Lokasi</div>
                        <dl class="text-sm space-y-2">
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">Asset</dt><dd class="font-medium text-gray-800">{{ $sr['assetnum'] ?? '-' }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">Location</dt><dd class="font-medium text-gray-800">{{ $sr['location'] ?? '-' }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">Site</dt><dd class="font-medium text-gray-800">{{ $sr['siteid'] ?? '-' }}</dd></div>
                        </dl>
                    </div>
                </div>

                <div class="mt-4 border rounded p-4">
                    <div class="text-sm text-gray-500 mb-2">Description</div>
                    <div class="text-sm text-gray-800 whitespace-pre-wrap break-words">{{ $sr['description'] ?? '-' }}</div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

