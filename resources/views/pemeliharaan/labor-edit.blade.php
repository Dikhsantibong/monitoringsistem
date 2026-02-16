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

            <div class="bg-white rounded-lg shadow p-6">
                <div class="mb-6 flex justify-between items-start">
                    <div>
                        <div class="text-sm text-gray-500 uppercase tracking-wider font-semibold">WONUM</div>
                        <div class="text-2xl font-bold text-gray-800">{{ $workOrder->wonum }}</div>
                    </div>
                    <div class="flex items-center">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold {{ in_array(strtoupper($workOrder->status), ['COMP', 'CLOSE', 'CLOSED']) ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $workOrder->status }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Column 1: Info & Jadwal -->
                    <div class="space-y-6">
                        <div class="border border-gray-100 rounded-xl p-5 bg-gray-50/30">
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center">
                                <i class="fas fa-info-circle mr-2 text-blue-500"></i> Informasi Utama
                            </div>
                            <dl class="text-sm space-y-3">
                                <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                                    <dt class="text-gray-500">Parent</dt>
                                    <dd class="font-medium text-gray-800">{{ $workOrder->parent ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                                    <dt class="text-gray-500">Work Type</dt>
                                    <dd class="font-medium text-gray-800">{{ $workOrder->worktype ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-gray-500">Priority</dt>
                                    <dd class="font-medium text-gray-800">{{ $workOrder->wopriority ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="border border-gray-100 rounded-xl p-5 bg-gray-50/30">
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center">
                                <i class="fas fa-calendar-alt mr-2 text-purple-500"></i> Jadwal Pekerjaan
                            </div>
                            <dl class="text-sm space-y-3">
                                <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                                    <dt class="text-gray-500">Schedule Start</dt>
                                    <dd class="font-medium text-gray-800">{{ $workOrder->schedstart ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                                    <dt class="text-gray-500">Schedule Finish</dt>
                                    <dd class="font-medium text-gray-800">{{ $workOrder->schedfinish ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-gray-500">Report Date</dt>
                                    <dd class="font-medium text-gray-800">{{ $workOrder->reportdate ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Column 2: Asset, Lokasi & Actions -->
                    <div class="space-y-6">
                        <div class="border border-gray-100 rounded-xl p-5 bg-gray-50/30">
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center">
                                <i class="fas fa-map-marker-alt mr-2 text-red-500"></i> Asset & Lokasi
                            </div>
                            <dl class="text-sm space-y-3">
                                <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                                    <dt class="text-gray-500">Asset Number</dt>
                                    <dd class="font-medium text-gray-800">{{ $workOrder->assetnum ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                                    <dt class="text-gray-500">Location</dt>
                                    <dd class="font-medium text-gray-800">{{ $workOrder->location ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                                    <dt class="text-gray-500">Site ID</dt>
                                    <dd class="font-medium text-gray-800">{{ $workOrder->siteid ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-gray-500">Downtime</dt>
                                    <dd class="font-medium text-gray-800">{{ $workOrder->downtime ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Jobcard Actions -->
                        <div class="border border-gray-100 rounded-xl p-5 bg-white shadow-sm border-l-4 border-l-blue-500">
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">
                                Jobcard Document
                            </div>
                            <div class="space-y-3">
                                @if(!empty($workOrder->jobcard_exists) && $workOrder->jobcard_exists === true)
                                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-100">
                                        <div class="flex items-center">
                                            <i class="fas fa-file-pdf text-blue-600 mr-3 text-lg"></i>
                                            <span class="text-xs font-medium text-blue-800">Tersedia: JOBCARD_{{ $workOrder->wonum }}.pdf</span>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('pemeliharaan.jobcard.edit', ['wonum' => $workOrder->wonum]) }}"
                                           class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-yellow-500 text-white text-xs font-bold rounded-lg hover:bg-yellow-600 transition-colors shadow-sm">
                                            <i class="fas fa-edit mr-2"></i> Edit Dokumen
                                        </a>
                                        <a href="{{ route('pemeliharaan.jobcard.download', ['path' => $workOrder->jobcard_path]) }}"
                                           class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-gray-800 text-white text-xs font-bold rounded-lg hover:bg-gray-900 transition-colors shadow-sm">
                                            <i class="fas fa-download mr-2"></i> Download
                                        </a>
                                    </div>
                                @elseif(strtoupper($workOrder->status) === 'APPR')
                                    <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-100 mb-3">
                                        <p class="text-xs text-yellow-700 leading-relaxed">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> Jobcard belum tersedia untuk Work Order ini.
                                        </p>
                                    </div>
                                    <form method="POST" action="{{ route('pemeliharaan.jobcard.generate') }}">
                                        @csrf
                                        <input type="hidden" name="wonum" value="{{ $workOrder->wonum }}">
                                        <button type="submit" 
                                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700 transition-all shadow-sm"
                                                onclick="return confirm('Generate jobcard untuk WO {{ $workOrder->wonum }}?')">
                                            <i class="fas fa-magic mr-2"></i> Generate Jobcard Sekarang
                                        </button>
                                    </form>
                                @else
                                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-100 flex items-start gap-3">
                                        <i class="fas fa-info-circle text-gray-400 mt-0.5"></i>
                                        <p class="text-xs text-gray-600 leading-relaxed">
                                            Jobcard hanya dapat di-generate jika status Work Order adalah <span class="font-bold text-blue-600">APPR</span>.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description Card -->
                <div class="mt-6 border border-gray-100 rounded-xl p-5 bg-gray-50/30">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 flex items-center">
                        <i class="fas fa-align-left mr-2 text-gray-500"></i> Deskripsi Pekerjaan
                    </div>
                    <div class="text-sm text-gray-800 bg-white p-4 rounded-lg border border-gray-100 min-h-[100px] whitespace-pre-wrap leading-relaxed shadow-inner">
                        {{ $workOrder->description }}
                    </div>
                </div>

                <!-- Status Unit Update Form -->
                <div class="mt-6 border border-indigo-100 rounded-xl p-6 bg-indigo-50/20 shadow-sm">
                    <div class="text-sm font-bold text-indigo-800 mb-4 flex items-center">
                        <i class="fas fa-sync-alt mr-2"></i> Update Status Pembanding (MySQL)
                    </div>
                    <form action="{{ route('pemeliharaan.labor-saya.update', $workOrder->wonum) }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        @csrf
                        <div class="md:col-span-3">
                            <label class="block text-[10px] uppercase font-bold text-indigo-400 mb-1 ml-1 tracking-widest">Pilih Status Unit</label>
                            <select name="status_unit" class="w-full px-4 py-2.5 bg-white border border-indigo-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 outline-none transition-all text-sm font-medium">
                                <option value="-" {{ ($workOrder->status_unit ?? '') == '-' ? 'selected' : '' }}>- Pilih Status -</option>
                                <option value="APPR" {{ ($workOrder->status_unit ?? '') == 'APPR' ? 'selected' : '' }}>APPR</option>
                                <option value="WMATL" {{ ($workOrder->status_unit ?? '') == 'WMATL' ? 'selected' : '' }}>WMATL</option>
                                <option value="INPRG" {{ ($workOrder->status_unit ?? '') == 'INPRG' ? 'selected' : '' }}>INPRG</option>
                                <option value="COMP" {{ ($workOrder->status_unit ?? '') == 'COMP' ? 'selected' : '' }}>COMP</option>
                                <option value="CLOSE" {{ ($workOrder->status_unit ?? '') == 'CLOSE' ? 'selected' : '' }}>CLOSE</option>
                                <option value="WAPPR" {{ ($workOrder->status_unit ?? '') == 'WAPPR' ? 'selected' : '' }}>WAPPR</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-2.5 px-4 rounded-xl hover:bg-indigo-700 transition-all shadow-md flex items-center justify-center text-sm transform hover:scale-[1.02] active:scale-[0.98]">
                            <i class="fas fa-save mr-2"></i> Simpan Status
                        </button>
                    </form>
                    <p class="text-[10px] text-gray-400 mt-2 ml-1 italic font-medium">
                        *Status ini akan disinkronkan ke laporan monitoring pusat.
                    </p>
                </div>

                <!-- Footer / Back Button -->
                <div class="mt-8 pt-6 border-t border-gray-100 flex justify-between items-center">
                    <a href="{{ route('pemeliharaan.labor-saya') }}" class="group flex items-center px-4 py-2 bg-gray-100 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-200 transition-all">
                        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> Kembali ke Daftar
                    </a>
                    <div class="text-[10px] text-gray-400 font-medium">
                        Work Order Detail View | Power Plant Monitoring System
                    </div>
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