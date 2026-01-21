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
                    <h1 class="text-xl font-semibold text-gray-800">Detail Work Order</h1>
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
            <div class="bg-white rounded-lg shadow p-6 sm:p-3 w-full">
                <div class="w-full">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
                        <!-- Kolom Kiri -->
                        <div class="w-full">
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">WO Number</label>
                                <div class="w-full px-3 py-2 border rounded-md bg-gray-100">
                                    {{ $workOrder->wonum }}
                                </div>
                            </div>

                            @if($workOrder->parent && $workOrder->parent !== '-')
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">Parent WO</label>
                                <div class="w-full px-3 py-2 border rounded-md bg-gray-100">
                                    {{ $workOrder->parent }}
                                </div>
                            </div>
                            @endif

                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">Type</label>
                                <div class="w-full px-3 py-2 border rounded-md bg-gray-100">
                                    {{ $workOrder->worktype }}
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">Priority</label>
                                <div class="w-full px-3 py-2 border rounded-md bg-gray-100">
                                    {{ ucfirst($workOrder->wopriority) }}
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">Status</label>
                                <div class="w-full px-3 py-2 border rounded-md bg-gray-100">
                                    {{ $workOrder->status }}
                                </div>
                            </div>

                            @if($workOrder->statusdate)
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">Status Date</label>
                                <div class="w-full px-3 py-2 border rounded-md bg-gray-100">
                                    {{ \Carbon\Carbon::parse($workOrder->statusdate)->format('d M Y H:i') }}
                                </div>
                            </div>
                            @endif

                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">Location</label>
                                <div class="w-full px-3 py-2 border rounded-md bg-gray-100">
                                    {{ $workOrder->location }}
                                </div>
                            </div>

                            @if($workOrder->assetnum && $workOrder->assetnum !== '-')
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">Asset Number</label>
                                <div class="w-full px-3 py-2 border rounded-md bg-gray-100">
                                    {{ $workOrder->assetnum }}
                                </div>
                            </div>
                            @endif

                            <div class="grid grid-cols-2 gap-4">
                                @if($workOrder->schedstart)
                                <div class="mb-4">
                                    <label class="block text-gray-700 font-medium mb-2">Schedule Start</label>
                                    <div class="w-full px-3 py-2 border rounded-md bg-gray-100">
                                        {{ \Carbon\Carbon::parse($workOrder->schedstart)->format('d M Y') }}
                                    </div>
                                </div>
                                @endif

                                @if($workOrder->schedfinish)
                                <div class="mb-4">
                                    <label class="block text-gray-700 font-medium mb-2">Schedule Finish</label>
                                    <div class="w-full px-3 py-2 border rounded-md bg-gray-100">
                                        {{ \Carbon\Carbon::parse($workOrder->schedfinish)->format('d M Y') }}
                                    </div>
                                </div>
                                @endif
                            </div>

                            @if($workOrder->reportdate)
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">Report Date</label>
                                <div class="w-full px-3 py-2 border rounded-md bg-gray-100">
                                    {{ \Carbon\Carbon::parse($workOrder->reportdate)->format('d M Y H:i') }}
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="w-full">
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">Description</label>
                                <div class="w-full px-3 py-2 border rounded-md bg-gray-100 min-h-[100px]">
                                    {{ $workOrder->description }}
                                </div>
                            </div>

                            @if($workOrder->downtime && $workOrder->downtime !== '-')
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">Downtime</label>
                                <div class="w-full px-3 py-2 border rounded-md bg-gray-100">
                                    {{ $workOrder->downtime }}
                                </div>
                            </div>
                            @endif

                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">Jobcard</label>
                                <div class="flex flex-col space-y-3">
                                    @if($workOrder->jobcard_exists)
                                    <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                                        <div class="flex-1 flex items-center">
                                            <i class="fas fa-file-pdf text-blue-600 mr-2"></i>
                                            <span class="text-sm text-gray-700">JOBCARD_{{ $workOrder->wonum }}.pdf</span>
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
                                    @else
                                    <div class="flex items-center p-3 bg-yellow-50 rounded-lg">
                                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                                        <span class="text-sm text-yellow-700">Jobcard belum tersedia. Generate dilakukan di Admin Maximo.</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Kembali -->
                    <div class="flex justify-start mt-6">
                        <a href="{{ route('pemeliharaan.labor-saya') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection