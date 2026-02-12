@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-[#F3F4F6]">
        <!-- Sidebar -->
        @include('components.pemeliharaan-sidebar')
        
        <!-- Main Content -->
        <div id="main-content" class="flex-1 overflow-auto">
            <!-- Header -->
            <header class="bg-white/80 backdrop-blur-md sticky top-0 z-30 border-b border-gray-200">
                <div class="flex justify-between items-center px-8 py-4">
                    <div class="flex items-center gap-x-4">
                        <button id="mobile-menu-toggle" class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Dashboard Pemeliharaan</h1>
                            <p class="text-sm text-gray-500">Pantau aktivitas mesin dan work order secara real-time</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-x-6">
                        <!-- Date Info -->
                        <div class="hidden lg:flex items-center gap-x-2 text-sm text-gray-500 border-r pr-6 transition-all">
                            <i class="far fa-calendar-alt text-[#009BB9]"></i>
                            <span>{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
                        </div>
                        
                        <!-- User Dropdown -->
                        <div class="relative group">
                            <button id="dropdownToggle" class="flex items-center gap-x-3 p-1 rounded-full hover:bg-gray-100 transition-all" onclick="toggleDropdown()">
                                <div class="relative">
                                    <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}" class="w-10 h-10 rounded-full border-2 border-[#009BB9]/20 shadow-sm">
                                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                                </div>
                                <div class="hidden md:block text-left mr-2">
                                    <p class="text-sm font-semibold text-gray-900 leading-none">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider">{{ Auth::user()->role ?? 'User' }}</p>
                                </div>
                                <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                            </button>
                            <div id="dropdown" class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-xl border border-gray-100 hidden z-50 transform origin-top-right transition-all">
                                <div class="p-2">
                                    <a href="{{ route('user.profile') }}" class="flex items-center gap-x-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-all">
                                        <i class="fas fa-user-circle text-gray-400"></i>
                                        <span>Profil Saya</span>
                                    </a>
                                    <hr class="my-2 border-gray-50">
                                    <a href="{{ route('logout') }}" class="flex items-center gap-x-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-all" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Keluar Sesi</span>
                                    </a>
                                </div>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-8">
                <!-- Section 1: Machine Overview (Local) -->
                <div class="mb-10">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-[#009BB9] rounded-full"></span>
                            Status Pembangkit (Unit)
                        </h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Total Mesin -->
                        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all group">
                            <div class="flex justify-between items-start mb-4">
                                <div class="p-3 bg-blue-50 rounded-xl group-hover:bg-blue-100 transition-colors">
                                    <i class="fas fa-bolt text-blue-600 text-xl"></i>
                                </div>
                                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">Real-time</span>
                            </div>
                            <h3 class="text-gray-500 text-sm font-medium">Total Mesin</h3>
                            <div class="flex items-end gap-2 mt-1">
                                <span class="text-3xl font-bold text-gray-900">{{ $totalMachines }}</span>
                                <span class="text-sm text-gray-500 mb-1">Unit</span>
                            </div>
                        </div>

                        <!-- Operasi -->
                        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all group">
                            <div class="flex justify-between items-start mb-4">
                                <div class="p-3 bg-green-50 rounded-xl group-hover:bg-green-100 transition-colors">
                                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                </div>
                                @php $opPercent = $totalMachines > 0 ? round(($operatingMachines/$totalMachines)*100) : 0; @endphp
                                <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-lg">{{ $opPercent }}%</span>
                            </div>
                            <h3 class="text-gray-500 text-sm font-medium">Mesin Beroperasi</h3>
                            <div class="flex items-end gap-2 mt-1">
                                <span class="text-3xl font-bold text-gray-900">{{ $operatingMachines }}</span>
                                <span class="text-sm text-gray-500 mb-1">Unit</span>
                            </div>
                        </div>

                        <!-- Gangguan -->
                        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all group border-l-4 border-l-red-500">
                            <div class="flex justify-between items-start mb-4">
                                <div class="p-3 bg-red-50 rounded-xl group-hover:bg-red-100 transition-colors">
                                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                                </div>
                                <i class="fas fa-arrow-up text-red-500 text-xs mt-2 scale-75"></i>
                            </div>
                            <h3 class="text-gray-500 text-sm font-medium text-red-600">Unit Gangguan</h3>
                            <div class="flex items-end gap-2 mt-1">
                                <span class="text-3xl font-bold text-red-600">{{ $troubleMachines }}</span>
                                <span class="text-sm text-gray-500 mb-1">Unit</span>
                            </div>
                        </div>

                        <!-- Pemeliharaan -->
                        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all group">
                            <div class="flex justify-between items-start mb-4">
                                <div class="p-3 bg-yellow-50 rounded-xl group-hover:bg-yellow-100 transition-colors">
                                    <i class="fas fa-wrench text-yellow-600 text-xl"></i>
                                </div>
                            </div>
                            <h3 class="text-gray-500 text-sm font-medium">Pemeliharaan</h3>
                            <div class="flex items-end gap-2 mt-1">
                                <span class="text-3xl font-bold text-gray-900">{{ $maintenanceMachines }}</span>
                                <span class="text-sm text-gray-500 mb-1">Unit</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Maximo Overview -->
                <div class="mb-10">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-[#009BB9] rounded-full"></span>
                            Maximo Integration (Asset Management)
                        </h2>
                        <a href="{{ route('admin.maximo.index') }}" class="text-xs font-semibold text-[#009BB9] hover:underline flex items-center gap-1">
                            Kelola Lengkap <i class="fas fa-arrow-right scale-75"></i>
                        </a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Card: Total WO -->
                        <div class="bg-gradient-to-br from-[#009BB9] to-[#007A91] rounded-2xl p-6 shadow-lg text-white">
                            <div class="flex justify-between items-center mb-6">
                                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                    <i class="fas fa-tasks text-xl"></i>
                                </div>
                                <span class="text-[10px] uppercase tracking-widest font-bold opacity-60">Site ID: KD</span>
                            </div>
                            <p class="text-sm opacity-80 mb-1">Total Work Order</p>
                            <h4 class="text-4xl font-black mb-4">{{ number_format($maximoData['total_wo']) }}</h4>
                            <div class="mt-4 pt-4 border-t border-white/10 flex items-center gap-2">
                                <div class="w-full bg-white/20 rounded-full h-1.5">
                                    <div class="bg-white h-1.5 rounded-full" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Card: Approved WO -->
                        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 overflow-hidden relative">
                            <div class="absolute -right-4 -top-4 opacity-5 rotate-12">
                                <i class="fas fa-check-double text-9xl"></i>
                            </div>
                            <div class="flex justify-between items-start mb-6">
                                <div class="p-3 bg-indigo-50 rounded-xl">
                                    <i class="fas fa-clipboard-check text-indigo-600 text-xl"></i>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-indigo-600 font-bold bg-indigo-50 px-2 py-1 rounded-lg">Status: APPR</p>
                                </div>
                            </div>
                            <p class="text-gray-500 text-sm font-medium">Work Order Approved</p>
                            <h4 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($maximoData['appr_wo']) }}</h4>
                            <p class="text-xs text-gray-400 mt-2 italic">Menunggu eksekusi oleh tim</p>
                        </div>

                        <!-- Card: Total SR -->
                        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                            <div class="flex justify-between items-start mb-6">
                                <div class="p-3 bg-purple-50 rounded-xl">
                                    <i class="fas fa-headset text-purple-600 text-xl"></i>
                                </div>
                                <span class="text-xs text-gray-400 mt-2">Permintaan Layanan</span>
                            </div>
                            <p class="text-gray-500 text-sm font-medium">Total Service Request</p>
                            <h4 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($maximoData['total_sr']) }}</h4>
                            <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
                                <i class="fas fa-info-circle"></i> Sinkronisasi otomatis
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Column Left/Middle (2/3) -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- My Work Orders (Local) -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-5 border-b border-gray-50 flex justify-between items-center">
                                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                    <i class="fas fa-user-tag text-[#009BB9]"></i>
                                    Work Order Saya (Local)
                                </h3>
                                <span class="bg-blue-50 text-[#009BB9] text-[10px] px-2 py-1 rounded-md font-bold uppercase">{{ count($myWorkOrders) }} Aktif</span>
                            </div>
                            <div class="p-6">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead>
                                            <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                                <th class="pb-3 pr-4">Deskripsi</th>
                                                <th class="pb-3 pr-4">Status</th>
                                                <th class="pb-3 pr-4">Jadwal Selesai</th>
                                                <th class="pb-3">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50">
                                            @forelse($myWorkOrders as $wo)
                                            <tr class="group">
                                                <td class="py-4 pr-4">
                                                    <p class="text-sm font-semibold text-gray-800 group-hover:text-[#009BB9] transition-colors line-clamp-1">{{ $wo->description }}</p>
                                                    <p class="text-[10px] text-gray-400 mt-0.5 uppercase tracking-tighter">WO ID: {{ $wo->id }} | Update {{ \Carbon\Carbon::parse($wo->updated_at)->diffForHumans() }}</p>
                                                </td>
                                                <td class="py-4 pr-4">
                                                    @php
                                                        $statusClass = match(strtolower($wo->status)) {
                                                            'open' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                            'in progress' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                            default => 'bg-gray-50 text-gray-600 border-gray-100'
                                                        };
                                                    @endphp
                                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $statusClass }} uppercase">{{ $wo->status }}</span>
                                                </td>
                                                <td class="py-4 pr-4 text-xs text-gray-600 font-medium">
                                                    {{ $wo->schedule_finish ?? '-' }}
                                                </td>
                                                <td class="py-4">
                                                    @if($wo->document_path)
                                                        <a href="{{ url('storage/' . $wo->document_path) }}" target="_blank" class="p-2 bg-gray-50 rounded-lg text-gray-400 hover:text-blue-600 transition-colors">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-gray-300">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="py-10 text-center">
                                                    <img src="{{ asset('img/empty-state.svg') }}" class="w-24 h-24 mx-auto opacity-10 grayscale mb-3">
                                                    <p class="text-xs text-gray-400">Tidak ada work order yang ditugaskan ke Anda saat ini.</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Maximo Work Orders -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-5 border-b border-gray-50 flex justify-between items-center">
                                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                    <i class="fas fa-history text-orange-500"></i>
                                    Work Order Maximo Terkini
                                </h3>
                                <div class="flex gap-2">
                                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse mt-1.5"></div>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase">Oracle Connected</span>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    @forelse($maximoData['recent_wo'] as $mwo)
                                    <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-50 hover:border-gray-100 hover:bg-gray-50/50 transition-all cursor-default">
                                        <div class="p-3 bg-orange-50 rounded-lg shrink-0">
                                            <i class="fas fa-cogs text-orange-600"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start mb-0.5">
                                                <h4 class="text-sm font-bold text-gray-800 truncate">{{ $mwo['description'] }}</h4>
                                                <span class="text-[10px] font-black text-gray-300">#{{ $mwo['wonum'] }}</span>
                                            </div>
                                            <div class="flex items-center gap-x-4 text-[10px] text-gray-500 font-medium">
                                                <span class="flex items-center gap-1"><i class="far fa-clock"></i> {{ $mwo['statusdate'] }}</span>
                                                <span class="flex items-center gap-1 uppercase"><i class="fas fa-tag"></i> {{ $mwo['worktype'] }}</span>
                                                <span class="px-1.5 py-0.5 bg-gray-100 rounded-md text-gray-600 font-bold tracking-widest">{{ $mwo['status'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <p class="text-center text-xs text-gray-400 py-6">Tidak ada data terkini dari Maximo.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column Right (1/3) -->
                    <div class="space-y-8">
                        <!-- Plant Efficiency Widget -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-base font-bold text-gray-800 mb-6">Efisiensi Pembangkit</h3>
                            <div class="space-y-6">
                                @foreach($powerPlantPerformance as $plant)
                                <div>
                                    <div class="flex justify-between text-xs font-semibold mb-2">
                                        <span class="text-gray-700 uppercase tracking-wide">{{ $plant->name }}</span>
                                        <span class="text-[#009BB9]">{{ round($plant->efficiency) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-[#009BB9] to-[#007A91] h-2 rounded-full shadow-sm shadow-[#009BB9]/20" style="width: {{ $plant->efficiency }}%"></div>
                                    </div>
                                    <div class="mt-2 flex justify-between text-[10px] text-gray-400">
                                        <span>Total: {{ $plant->total_machines }} Unit</span>
                                        <span class="font-bold">Aktif: {{ $plant->operating_machines }} unit</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Upcoming Meetings -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-base font-bold text-gray-800">Meeting Hari Ini</h3>
                                <div class="bg-red-50 text-red-500 text-[10px] font-bold px-2 py-1 rounded-lg">Live</div>
                            </div>
                            <div class="space-y-4">
                                @forelse($todayMeetings as $meeting)
                                <div class="relative pl-6 border-l-2 border-[#009BB9]">
                                    <div class="absolute -left-[5px] top-0 w-2 h-2 rounded-full bg-[#009BB9]"></div>
                                    <p class="text-sm font-bold text-gray-800 leading-tight mb-1">{{ $meeting->title ?? 'Judul Meeting' }}</p>
                                    <div class="flex items-center gap-3 text-[10px] text-gray-500 font-semibold tracking-wide">
                                        <span class="flex items-center gap-1"><i class="far fa-clock text-[#009BB9]"></i> {{ \Carbon\Carbon::parse($meeting->scheduled_at)->format('H:i') }}</span>
                                        <span class="flex items-center gap-1 uppercase"><i class="fas fa-map-marker-alt text-red-400"></i> {{ $meeting->location ?? 'Meeting Room' }}</span>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-6">
                                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-2 text-gray-300">
                                        <i class="far fa-calendar-check text-xl"></i>
                                    </div>
                                    <p class="text-[11px] text-gray-400">Tidak ada jadwal hari ini</p>
                                </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- System Status -->
                        <div class="bg-gray-900 rounded-2xl p-6 text-gray-400">
                            <h3 class="text-white text-sm font-bold mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 bg-green-500 rounded-full animate-ping"></span>
                                Core Service Status
                            </h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white/5 p-3 rounded-xl border border-white/5">
                                    <p class="text-[10px] uppercase font-bold text-gray-500 mb-1">Maximo Link</p>
                                    <span class="text-xs text-green-400 font-semibold">Active</span>
                                </div>
                                <div class="bg-white/5 p-3 rounded-xl border border-white/5">
                                    <p class="text-[10px] uppercase font-bold text-gray-500 mb-1">Database</p>
                                    <span class="text-xs text-green-400 font-semibold">Healthy</span>
                                </div>
                                <div class="bg-white/5 p-3 rounded-xl border border-white/5">
                                    <p class="text-[10px] uppercase font-bold text-gray-500 mb-1">Reporting</p>
                                    <span class="text-xs text-green-400 font-semibold">Online</span>
                                </div>
                                <div class="bg-white/5 p-3 rounded-xl border border-white/5">
                                    <p class="text-[10px] uppercase font-bold text-gray-500 mb-1">Sync Service</p>
                                    <span class="text-xs text-yellow-400 font-semibold">Wait...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            var dropdown = document.getElementById('dropdown');
            dropdown.classList.toggle('hidden');
        }
        document.addEventListener('click', function(event) {
            var userDropdown = document.getElementById('dropdown');
            var userBtn = document.getElementById('dropdownToggle');
            if (userDropdown && !userDropdown.classList.contains('hidden') && !userBtn.contains(event.target) && !userDropdown.contains(event.target)) {
                userDropdown.classList.add('hidden');
            }
        });
    </script>
@endsection
