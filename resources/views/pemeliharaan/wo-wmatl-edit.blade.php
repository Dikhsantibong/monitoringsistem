@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.pemeliharaan-sidebar')

    <div id="main-content" class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-10">
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
                    <h1 class="text-xl font-semibold text-gray-800">Detail WO Material (WMATL)</h1>
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

        <main class="px-6 mt-4 pb-8">
            <div class="flex justify-end mb-4">
                <a href="{{ route('pemeliharaan.wo-wmatl.index') }}" class="inline-flex items-center px-6 py-2.5 bg-[#009BB9] text-white text-sm font-bold rounded-xl shadow-md hover:bg-[#007b94] hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Work Order
                </a>
            </div>
            
            {{-- WO Header Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Work Order Number</p>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $wo['wonum'] }}</h2>
                    </div>
                    <div class="flex items-center gap-3 flex-wrap">
                        @php $st = strtoupper($wo['status']); @endphp
                        <span class="px-3 py-1 text-sm font-semibold rounded-full
                            @if(in_array($st, ['COMP','CLOSE','RESOLVED'])) bg-green-100 text-green-800
                            @elseif(in_array($st, ['WAPPR','APPR'])) bg-blue-100 text-blue-800
                            @elseif(in_array($st, ['INPRG','IN PROGRESS'])) bg-yellow-100 text-yellow-800
                            @elseif($st === 'WMATL') bg-orange-100 text-orange-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $wo['status'] }}
                        </span>
                        @if($wo['worktype'] !== '-')
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-indigo-100 text-indigo-700">{{ $wo['worktype'] }}</span>
                        @endif
                        @if($wo['wopriority'] !== '-')
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-red-50 text-red-700">Priority: {{ $wo['wopriority'] }}</span>
                        @endif
                    </div>
                </div>
                @if($wo['description'] !== '-')
                <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs font-medium text-gray-400 mb-1">Description</p>
                    <p class="text-sm text-gray-800 whitespace-pre-wrap break-words">{{ $wo['description'] }}</p>
                </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                {{-- Identifikasi --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-id-card text-blue-500"></i> Identifikasi
                    </h3>
                    <dl class="text-sm space-y-2">
                        @foreach([
                            'WO Number' => $wo['wonum'],
                            'Parent' => $wo['parent'],
                            'Status' => $wo['status'],
                            'Status Date' => $wo['statusdate'],
                            'Work Type' => $wo['worktype'],
                            'Priority' => $wo['wopriority'],
                            'WO Class' => $wo['woclass'],
                            'WO PLN' => $wo['wonumpln'],
                            'Anggaran' => $wo['anggaran'],
                        ] as $label => $value)
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500 whitespace-nowrap">{{ $label }}</dt>
                            <dd class="font-medium text-gray-800 text-right">{{ $value }}</dd>
                        </div>
                        @endforeach
                    </dl>
                </div>

                {{-- Asset & Lokasi --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-green-500"></i> Asset & Lokasi
                    </h3>
                    <dl class="text-sm space-y-2">
                        @foreach([
                            'Asset Number' => $wo['assetnum'],
                            'Location' => $wo['location'],
                            'Site ID' => $wo['siteid'],
                            'Org ID' => $wo['orgid'],
                            'Downtime' => $wo['downtime'],
                        ] as $label => $value)
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500 whitespace-nowrap">{{ $label }}</dt>
                            <dd class="font-medium text-gray-800 text-right">{{ $value }}</dd>
                        </div>
                        @endforeach
                    </dl>
                </div>

                {{-- People --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-users text-purple-500"></i> People
                    </h3>
                    <dl class="text-sm space-y-2">
                        @foreach([
                            'Reported By' => $wo['reportedby'],
                            'Supervisor' => $wo['supervisor'],
                            'Crew ID' => $wo['crewid'],
                            'Lead' => $wo['lead'],
                            'Owner' => $wo['owner'],
                            'Owner Group' => $wo['ownergroup'],
                            'Person Group' => $wo['persongroup'],
                            'Changed By' => $wo['changeby'],
                        ] as $label => $value)
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500 whitespace-nowrap">{{ $label }}</dt>
                            <dd class="font-medium text-gray-800 text-right">{{ $value }}</dd>
                        </div>
                        @endforeach
                    </dl>
                </div>

                {{-- Codes --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-code text-orange-500"></i> Codes & References
                    </h3>
                    <dl class="text-sm space-y-2">
                        @foreach([
                            'Job Plan (JPNUM)' => $wo['jpnum'],
                            'PM Number' => $wo['pmnum'],
                            'Failure Code' => $wo['failurecode'],
                            'Problem Code' => $wo['problemcode'],
                            'GL Account' => $wo['glaccount'],
                            'Contract' => $wo['contract'],
                            'Orig Record ID' => $wo['origrecordid'],
                            'Orig Record Class' => $wo['origrecordclass'],
                            'Has Children' => $wo['haschildren'],
                            'History Flag' => $wo['historyflag'],
                            'Rem Dur' => $wo['remdur'],
                        ] as $label => $value)
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500 whitespace-nowrap">{{ $label }}</dt>
                            <dd class="font-medium text-gray-800 text-right">{{ $value }}</dd>
                        </div>
                        @endforeach
                    </dl>
                </div>
            </div>

            {{-- Tanggal/Jadwal --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mt-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-teal-500"></i> Tanggal & Jadwal
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach([
                        'Report Date' => $wo['reportdate'],
                        'Sched Start' => $wo['schedstart'],
                        'Sched Finish' => $wo['schedfinish'],
                        'Actual Start' => $wo['actstart'],
                        'Actual Finish' => $wo['actfinish'],
                        'Target Start' => $wo['targstartdate'],
                        'Target Completion' => $wo['targcompdate'],
                        'Change Date' => $wo['changedate'],
                        'Fail Date' => $wo['faildate'],
                    ] as $label => $value)
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-400 mb-1">{{ $label }}</p>
                        <p class="text-sm font-medium text-gray-800">{{ $value }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Cost Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mt-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <i class="fas fa-dollar-sign text-emerald-500"></i> Cost & Hours
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left border border-gray-200 text-gray-600">Category</th>
                                <th class="px-4 py-2 text-right border border-gray-200 text-blue-600">Estimated</th>
                                <th class="px-4 py-2 text-right border border-gray-200 text-green-600">Actual</th>
                                <th class="px-4 py-2 text-right border border-gray-200 text-orange-600">Outside</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border border-gray-200 font-medium">Labor Hours</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($wo['estlabhrs'], 2) }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($wo['actlabhrs'], 2) }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-right text-gray-400">-</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border border-gray-200 font-medium">Labor Cost</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($wo['estlabcost'], 2) }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($wo['actlabcost'], 2) }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($wo['outlabcost'], 2) }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border border-gray-200 font-medium">Material Cost</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($wo['estmatcost'], 2) }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($wo['actmatcost'], 2) }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($wo['outmatcost'], 2) }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border border-gray-200 font-medium">Tool Cost</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($wo['esttoolcost'], 2) }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($wo['acttoolcost'], 2) }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($wo['outtoolcost'], 2) }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border border-gray-200 font-medium">Service Cost</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($wo['estservcost'], 2) }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($wo['actservcost'], 2) }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-right text-gray-400">-</td>
                            </tr>
                            <tr class="hover:bg-gray-50 bg-gray-50">
                                <td class="px-4 py-2 border border-gray-200 font-medium">Duration (Hours)</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($wo['estdur'], 2) }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-right text-gray-400">-</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">Rem: {{ number_format($wo['remdur'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Custom Fields (WOEQ) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-5">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-cogs text-gray-500"></i> Custom Fields (WOEQ)
                    </h3>
                    <dl class="text-sm space-y-2">
                        @foreach([
                            'WOEQ1' => $wo['woeq1'],
                            'WOEQ2' => $wo['woeq2'],
                            'WOEQ3' => $wo['woeq3'],
                            'WOEQ4' => $wo['woeq4'],
                            'WOEQ5' => is_numeric($wo['woeq5']) ? number_format($wo['woeq5'], 2) : $wo['woeq5'],
                            'WOEQ6' => $wo['woeq6'],
                        ] as $label => $value)
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500 whitespace-nowrap">{{ $label }}</dt>
                            <dd class="font-medium text-gray-800 text-right">{{ $value }}</dd>
                        </div>
                        @endforeach
                    </dl>
                </div>

                {{-- Remarks --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-comment-alt text-yellow-500"></i> Remarks
                    </h3>
                    <dl class="text-sm space-y-2">
                        @foreach([
                            'Remark C' => $wo['remarkdescc'],
                            'Remark P' => $wo['remarkdescp'],
                            'Remark PLN' => $wo['remarkdescpln'],
                            'Remark R' => $wo['remarkdescr'],
                        ] as $label => $value)
                        <div class="py-2 border-b border-gray-50">
                            <dt class="text-gray-400 text-xs mb-1">{{ $label }}</dt>
                            <dd class="font-medium text-gray-800 whitespace-pre-wrap break-words">{{ $value }}</dd>
                        </div>
                        @endforeach
                    </dl>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
function toggleDropdown() {
    document.getElementById('dropdown').classList.toggle('hidden');
}
document.addEventListener('click', function(event) {
    var dd = document.getElementById('dropdown');
    var btn = document.getElementById('dropdownToggle');
    if (dd && !dd.classList.contains('hidden') && !btn.contains(event.target) && !dd.contains(event.target)) {
        dd.classList.add('hidden');
    }
});
</script>
@endsection
