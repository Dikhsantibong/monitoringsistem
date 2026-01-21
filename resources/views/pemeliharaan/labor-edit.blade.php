@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.pemeliharaan-sidebar')
    <div id="main-content" class="flex-1 main-content">
        <header class="bg-white shadow-sm sticky top-0">
            <div class="flex justify-between items-center px-6 py-3">
                <div class="flex items-center gap-x-3">
                    <button id="mobile-menu-toggle"
                        class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#009BB9] hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <button id="desktop-menu-toggle"
                        class="hidden md:block relative items-center justify-center rounded-md text-gray-400 hover:bg-[#009BB9] p-2 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800">Edit Work Order</h1>
                </div>
                <div class="flex items-center gap-x-4 relative">
                    <!-- User Dropdown -->
                    <div class="relative">
                        <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                            <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                                class="w-8 h-8 rounded-full mr-2">
                            <span class="text-gray-700">{{ Auth::user()->name }}</span>
                            <i class="fas fa-caret-down ml-2"></i>
                        </button>
                        <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                            <a href="{{ route('user.profile') }}"
                                class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <main class="px-6 pt-6">
            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Error Message --}}
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow p-6 sm:p-3 w-full">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
                    <!-- Kolom Kiri -->
                    <div class="w-full">
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">ID WO (WONUM)</label>
                            <input type="text" value="{{ $workOrder->wonum }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Parent</label>
                            <input type="text" value="{{ $workOrder->parent }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Type WO</label>
                            <input type="text" value="{{ $workOrder->worktype }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Priority</label>
                            <input type="text" value="{{ $workOrder->wopriority }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Unit (Location)</label>
                            <input type="text" value="{{ $workOrder->location }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Asset Number</label>
                            <input type="text" value="{{ $workOrder->assetnum }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">Schedule Start</label>
                                <input type="text" value="{{ $workOrder->schedstart ?? '-' }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">Schedule Finish</label>
                                <input type="text" value="{{ $workOrder->schedfinish ?? '-' }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Report Date</label>
                            <input type="text" value="{{ $workOrder->reportdate ?? '-' }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Status</label>
                            <input type="text" value="{{ $workOrder->status }}" class="w-full px-3 py-2 border rounded-md bg-gray-100 font-semibold" readonly>
                        </div>
                    </div>
                    <!-- Kolom Kanan -->
                    <div class="w-full">
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Description</label>
                            <textarea class="w-full px-3 py-2 border rounded-md bg-gray-100 h-32" readonly>{{ $workOrder->description }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Site ID</label>
                            <input type="text" value="{{ $workOrder->siteid }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Downtime</label>
                            <input type="text" value="{{ $workOrder->downtime ?? '-' }}" class="w-full px-3 py-2 border rounded-md bg-gray-100" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Jobcard Document</label>
                            <div class="flex flex-col space-y-4">
                                @if(!empty($workOrder->jobcard_exists) && $workOrder->jobcard_exists === true)
                                <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                                    <div class="flex-1 flex items-center">
                                        <i class="fas fa-file-pdf text-blue-600 mr-2"></i>
                                        <span class="text-sm text-gray-700">Jobcard tersedia: JOBCARD_{{ $workOrder->wonum }}.pdf</span>
                                    </div>
                                    <a href="{{ route('pemeliharaan.jobcard.edit', ['wonum' => $workOrder->wonum]) }}"
                                       class="ml-4 inline-flex items-center px-3 py-1.5 bg-yellow-500 text-white text-sm rounded-lg hover:bg-yellow-600 transition-colors">
                                        <i class="fas fa-edit mr-2"></i>
                                        Edit Dokumen
                                    </a>
                                    <a href="{{ route('pemeliharaan.jobcard.download', ['path' => $workOrder->jobcard_path]) }}"
                                       class="ml-2 inline-flex items-center px-3 py-1.5 bg-gray-700 text-white text-sm rounded-lg hover:bg-gray-800 transition-colors">
                                        <i class="fas fa-download mr-2"></i>
                                        Download
                                    </a>
                                </div>
                                @elseif(strtoupper($workOrder->status) === 'APPR')
                                <div class="flex items-center p-3 bg-yellow-50 rounded-lg">
                                    <div class="flex-1">
                                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                                        <span class="text-sm text-yellow-700">Jobcard belum tersedia.</span>
                                    </div>
                                    <form method="POST" action="{{ route('pemeliharaan.jobcard.generate') }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="wonum" value="{{ $workOrder->wonum }}">
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors"
                                                onclick="return confirm('Generate jobcard untuk WO {{ $workOrder->wonum }}?')">
                                            <i class="fas fa-file-alt mr-2"></i>
                                            Generate Jobcard
                                        </button>
                                    </form>
                                </div>
                                @else
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i class="fas fa-info-circle text-gray-600 mr-2"></i>
                                    <span class="text-sm text-gray-700">Jobcard hanya dapat di-generate untuk Work Order dengan status APPR.</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Tombol Kembali -->
                <div class="flex justify-start space-x-4 mt-6">
                    <a href="{{ route('pemeliharaan.labor-saya') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </div>
        </main>
    </div>
</div>
<!-- Modal Signature -->
<div id="signatureModal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-4 flex flex-col items-center">
        <span class="font-bold mb-2">Gambar Tanda Tangan</span>
        <canvas id="signature-canvas" width="400" height="150" class="border mb-2"></canvas>
        <div class="flex gap-2">
            <button onclick="clearSignature()" class="bg-gray-400 text-white px-3 py-1 rounded hover:bg-gray-500">Bersihkan</button>
            <button onclick="saveSignature()" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Simpan Tanda Tangan</button>
            <button onclick="closeSignatureModal()" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Batal</button>
        </div>
    </div>
</div>
@endsection