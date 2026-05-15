@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.sidebar')

    <div id="main-content" class="flex-1 overflow-auto">
        <header class="bg-white shadow-sm sticky top-0 z-10">
            <div class="flex justify-between items-center px-6 py-3">
                <div class="flex items-center gap-x-3">
                    <button id="mobile-menu-toggle"
                        class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#009BB9] hover:text-white">
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <button id="desktop-menu-toggle"
                        class="hidden md:block relative items-center justify-center rounded-md text-gray-400 hover:bg-[#009BB9] p-2 hover:text-white">
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800">Service Request Detail</h1>
                </div>
                <div class="flex items-center gap-x-4 relative">
                    <a href="{{ route('admin.maximo.index', ['tab' => 'sr']) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                    <div class="relative">
                        <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                            <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}" class="w-8 h-8 rounded-full mr-2">
                            <span class="text-gray-700">{{ Auth::user()->name }}</span>
                            <i class="fas fa-caret-down ml-2"></i>
                        </button>
                        <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                            <a href="{{ route('user.profile') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="px-6 mt-4 pb-8">
            {{-- Header Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Service Request</p>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $sr['ticketid'] }}</h2>
                    </div>
                    <div class="flex items-center gap-3 flex-wrap">
                        @php $st = strtoupper($sr['status']); @endphp
                        <span class="px-3 py-1 text-sm font-semibold rounded-full
                            @if(in_array($st, ['COMP','CLOSE','CLOSED','RESOLVED'])) bg-green-100 text-green-800
                            @elseif(in_array($st, ['WAPPR','APPR'])) bg-blue-100 text-blue-800
                            @elseif(in_array($st, ['INPRG','IN PROGRESS','QUEUED'])) bg-yellow-100 text-yellow-800
                            @elseif($st === 'NEW') bg-purple-100 text-purple-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $sr['status'] }}
                        </span>
                        @if($sr['faultpriority'] !== '-')
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-red-50 text-red-700">{{ $sr['faultpriority'] }}</span>
                        @endif
                        @if($sr['faulttype'] !== '-')
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-orange-50 text-orange-700">{{ $sr['faulttype'] }}</span>
                        @endif
                        @if($sr['class'] !== '-')
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-indigo-50 text-indigo-700">{{ $sr['class'] }}</span>
                        @endif
                    </div>
                </div>
                @if($sr['description'] !== '-')
                <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs font-medium text-gray-400 mb-1">Description</p>
                    <p class="text-sm text-gray-800 font-medium">{{ $sr['description'] }}</p>
                </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                {{-- Identifikasi --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i> Identifikasi
                    </h3>
                    <dl class="text-sm space-y-2">
                        @foreach([
                            'Ticket ID' => $sr['ticketid'],
                            'Ticket UID' => $sr['ticketuid'],
                            'Class' => $sr['class'],
                            'Status' => $sr['status'],
                            'Status Date' => $sr['statusdate'],
                            'Fault Priority' => $sr['faultpriority'],
                            'Fault Type' => $sr['faulttype'],
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
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500">Asset</dt>
                            <dd class="font-medium text-gray-800 text-right">{{ $sr['assetnum'] }}</dd>
                        </div>
                        @if($sr['asset_description'] !== '-')
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500">Asset Description</dt>
                            <dd class="font-medium text-gray-800 text-right text-xs">{{ $sr['asset_description'] }}</dd>
                        </div>
                        @endif
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500">Location</dt>
                            <dd class="font-medium text-gray-800 text-right">{{ $sr['location'] }}</dd>
                        </div>
                        @if($sr['location_description'] !== '-')
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500">Location Description</dt>
                            <dd class="font-medium text-gray-800 text-right text-xs">{{ $sr['location_description'] }}</dd>
                        </div>
                        @endif
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500">Site ID</dt>
                            <dd class="font-medium text-gray-800 text-right">{{ $sr['siteid'] }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500">Org ID</dt>
                            <dd class="font-medium text-gray-800 text-right">{{ $sr['orgid'] }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- People --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-users text-purple-500"></i> People
                    </h3>
                    <dl class="text-sm space-y-2">
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500">Reported By</dt>
                            <dd class="font-medium text-gray-800 text-right">{{ $sr['reportedby'] }}</dd>
                        </div>
                        @if($sr['reportedby_name'] !== '-')
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500">Nama Pelapor</dt>
                            <dd class="font-medium text-gray-800 text-right">{{ $sr['reportedby_name'] }}</dd>
                        </div>
                        @endif
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500">Affected Person</dt>
                            <dd class="font-medium text-gray-800 text-right">{{ $sr['affectedperson'] }}</dd>
                        </div>
                        @if($sr['affectedperson_name'] !== '-')
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500">Nama Affected</dt>
                            <dd class="font-medium text-gray-800 text-right">{{ $sr['affectedperson_name'] }}</dd>
                        </div>
                        @endif
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500">Owner</dt>
                            <dd class="font-medium text-gray-800 text-right">{{ $sr['owner'] }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500">Owner Group</dt>
                            <dd class="font-medium text-gray-800 text-right">{{ $sr['ownergroup'] }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500">Supervisor</dt>
                            <dd class="font-medium text-gray-800 text-right">{{ $sr['supervisor'] }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500">Changed By</dt>
                            <dd class="font-medium text-gray-800 text-right">{{ $sr['changeby'] }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Kerja & Biaya --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-briefcase text-orange-500"></i> Kerja & Biaya
                    </h3>
                    <dl class="text-sm space-y-2">
                        @foreach([
                            'Shift' => $sr['shift'],
                            'Work Group' => $sr['workgroup'],
                            'Opr Group' => $sr['oprgroup'],
                            'GL Account' => $sr['glaccount'],
                            'Act Labor Hours' => number_format($sr['actlabhrs'], 2),
                            'Act Labor Cost' => number_format($sr['actlabcost'], 2),
                        ] as $label => $value)
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500 whitespace-nowrap">{{ $label }}</dt>
                            <dd class="font-medium text-gray-800 text-right">{{ $value }}</dd>
                        </div>
                        @endforeach
                    </dl>
                </div>
            </div>

            {{-- Tanggal --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mt-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-teal-500"></i> Tanggal & Jadwal
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach([
                        'Report Date' => $sr['reportdate'],
                        'Affected Date' => $sr['affecteddate'],
                        'Status Date' => $sr['statusdate'],
                        'Target Start' => $sr['targetstart'],
                        'Target Finish' => $sr['targetfinish'],
                        'Actual Start' => $sr['actualstart'],
                        'Actual Finish' => $sr['actualfinish'],
                        'Change Date' => $sr['changedate'],
                    ] as $label => $value)
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-400 mb-1">{{ $label }}</p>
                        <p class="text-sm font-medium text-gray-800">{{ $value }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Flags & Requirements --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mt-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <i class="fas fa-flag text-red-500"></i> Flags & Requirements
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @php
                    $flags = [
                        'Need Downtime' => ['val' => $sr['needdt'], 'isText' => true],
                        'Need DT' => ['val' => $sr['needdowntime'], 'isText' => false],
                        'Need LOTO' => ['val' => $sr['needloto'], 'isText' => false],
                        'Need ECP' => ['val' => $sr['needecp'], 'isText' => false],
                        'Need Eng Align' => ['val' => $sr['needengaln'], 'isText' => false],
                        'Need Safety App' => ['val' => $sr['needsafapp'], 'isText' => false],
                    ];
                    @endphp
                    @foreach($flags as $label => $flag)
                    @php
                        if ($flag['isText']) {
                            $isActive = strtoupper($flag['val']) === 'YA' || $flag['val'] === '1';
                            $display = $flag['val'];
                        } else {
                            $isActive = $flag['val'] == 1;
                            $display = $isActive ? 'Ya' : 'Tidak';
                        }
                    @endphp
                    <div class="p-3 rounded-lg text-center {{ $isActive ? 'bg-red-50 border border-red-200' : 'bg-gray-50 border border-gray-200' }}">
                        <p class="text-xs text-gray-400 mb-1">{{ $label }}</p>
                        <p class="text-sm font-bold {{ $isActive ? 'text-red-700' : 'text-gray-500' }}">{{ $display }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Codes --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-5">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-code text-gray-500"></i> Codes & References
                    </h3>
                    <dl class="text-sm space-y-2">
                        @foreach([
                            'Failure Code' => $sr['failurecode'],
                            'Problem Code' => $sr['problemcode'],
                            'Orig Record ID' => $sr['origrecordid'],
                            'Orig Record Class' => $sr['origrecordclass'],
                            'History Flag' => $sr['historyflag'],
                            'Has Long Desc' => $sr['hasld'],
                        ] as $label => $value)
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500 whitespace-nowrap">{{ $label }}</dt>
                            <dd class="font-medium text-gray-800 text-right">{{ $value }}</dd>
                        </div>
                        @endforeach
                    </dl>
                </div>

                {{-- Solution --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-500"></i> Solution
                    </h3>
                    @if($sr['solution'] !== '-')
                    <div class="p-4 bg-green-50 rounded-lg text-sm text-gray-800 whitespace-pre-wrap break-words">{{ $sr['solution'] }}</div>
                    @else
                    <div class="p-4 bg-gray-50 rounded-lg text-sm text-gray-400 italic">Tidak ada data solution</div>
                    @endif
                </div>
            </div>

            {{-- Long Description / Detil SR --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mt-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <i class="fas fa-file-alt text-orange-500"></i> Detil SR (Long Description)
                    @if($sr['hasld'] == 1)
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">hasld=1</span>
                    @endif
                </h3>
                @if($sr['longdescription'] !== '-')
                <div class="p-4 bg-gray-50 rounded-lg text-sm text-gray-800 whitespace-pre-wrap break-words leading-relaxed">{{ $sr['longdescription'] }}</div>
                @else
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-700">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Data long description tidak dapat diambil.
                    @if($sr['hasld'] == 1)
                    <br>Flag <code>hasld=1</code> menunjukkan data ada di database, tetapi tabel LONGDESCRIPTION tidak dapat diakses.
                    @endif
                </div>
                @endif
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
