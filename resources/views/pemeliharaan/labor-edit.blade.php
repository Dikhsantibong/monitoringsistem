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
                    <h1 class="text-xl font-semibold text-gray-800">Detail & Edit Work Order</h1>
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
            {{-- Status Messages --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl shadow-sm" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl shadow-sm" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                </div>
            @endif

            <div class="flex justify-end mb-4">
                <a href="{{ route('pemeliharaan.labor-saya') }}" class="inline-flex items-center px-6 py-2.5 bg-[#009BB9] text-white text-sm font-bold rounded-xl shadow-md hover:bg-[#007b94] hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
                </a>
            </div>
            
            {{-- WO Header Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Work Order Number</p>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $workOrder->wonum }}</h2>
                    </div>
                    <div class="flex items-center gap-3 flex-wrap">
                        @php $st = strtoupper($workOrder->status); @endphp
                        <span class="px-3 py-1 text-sm font-semibold rounded-full
                            @if(in_array($st, ['COMP','CLOSE','RESOLVED'])) bg-green-100 text-green-800
                            @elseif(in_array($st, ['WAPPR','APPR'])) bg-blue-100 text-blue-800
                            @elseif(in_array($st, ['INPRG','IN PROGRESS'])) bg-yellow-100 text-yellow-800
                            @elseif($st === 'WMATL') bg-orange-100 text-orange-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $workOrder->status }}
                        </span>
                        @if($workOrder->worktype !== '-')
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-indigo-100 text-indigo-700">{{ $workOrder->worktype }}</span>
                        @endif
                        @if($workOrder->wopriority !== '-')
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-red-50 text-red-700">Priority: {{ $workOrder->wopriority }}</span>
                        @endif
                    </div>
                </div>
                @if($workOrder->description !== '-')
                <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs font-medium text-gray-400 mb-1">Description</p>
                    <p class="text-sm text-gray-800 whitespace-pre-wrap break-words">{{ $workOrder->description }}</p>
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
                            'WO Number' => $workOrder->wonum,
                            'Parent' => $workOrder->parent,
                            'Status' => $workOrder->status,
                            'Status Date' => $workOrder->statusdate,
                            'Work Type' => $workOrder->worktype,
                            'Priority' => $workOrder->wopriority,
                            'WO Class' => $workOrder->woclass,
                            'WO PLN' => $workOrder->wonumpln,
                            'Anggaran' => $workOrder->anggaran,
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
                            'Asset Number' => $workOrder->assetnum,
                            'Location' => $workOrder->location,
                            'Site ID' => $workOrder->siteid,
                            'Org ID' => $workOrder->orgid,
                            'Downtime' => $workOrder->downtime,
                        ] as $label => $value)
                        <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                            <dt class="text-gray-500 whitespace-nowrap">{{ $label }}</dt>
                            <dd class="font-medium text-gray-800 text-right">{{ $value }}</dd>
                        </div>
                        @endforeach
                    </dl>
                </div>

                {{-- People & Actions --}}
                <div class="space-y-5">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <i class="fas fa-users text-purple-500"></i> People
                        </h3>
                        <dl class="text-sm space-y-2">
                            @foreach([
                                'Reported By' => $workOrder->reportedby,
                                'Supervisor' => $workOrder->supervisor,
                                'Crew ID' => $workOrder->crewid,
                                'Lead' => $workOrder->lead,
                                'Owner' => $workOrder->owner,
                                'Owner Group' => $workOrder->ownergroup,
                                'Person Group' => $workOrder->persongroup,
                                'Changed By' => $workOrder->changeby,
                            ] as $label => $value)
                            <div class="flex justify-between gap-4 py-1 border-b border-gray-50">
                                <dt class="text-gray-500 whitespace-nowrap">{{ $label }}</dt>
                                <dd class="font-medium text-gray-800 text-right">{{ $value }}</dd>
                            </div>
                            @endforeach
                        </dl>
                    </div>

                    {{-- Jobcard Actions Card --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-l-4 border-l-[#009BB9]">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <i class="fas fa-file-pdf text-[#009BB9]"></i> Jobcard Document
                        </h3>
                        <div class="space-y-3">
                            @if(!empty($workOrder->jobcard_exists) && $workOrder->jobcard_exists === true)
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-100 mb-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-check-circle text-blue-600 mr-3 text-lg"></i>
                                        <span class="text-xs font-medium text-blue-800">Tersedia: JOBCARD_{{ $workOrder->wonum }}.pdf</span>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('pemeliharaan.jobcard.edit', ['wonum' => $workOrder->wonum]) }}"
                                       class="flex-1 inline-flex justify-center items-center px-3 py-2.5 bg-yellow-500 text-white text-xs font-bold rounded-xl hover:bg-yellow-600 transition-colors shadow-sm">
                                        <i class="fas fa-edit mr-2"></i> Edit Dokumen
                                    </a>
                                    <a href="{{ route('pemeliharaan.jobcard.download', ['path' => $workOrder->jobcard_path]) }}"
                                       class="flex-1 inline-flex justify-center items-center px-3 py-2.5 bg-gray-800 text-white text-xs font-bold rounded-xl hover:bg-gray-900 transition-colors shadow-sm">
                                        <i class="fas fa-download mr-2"></i> Download
                                    </a>
                                </div>
                            @elseif(strtoupper($workOrder->status) === 'APPR')
                                <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-100 mb-3">
                                    <p class="text-xs text-yellow-700 leading-relaxed text-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Jobcard belum di-generate.
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('pemeliharaan.jobcard.generate') }}">
                                    @csrf
                                    <input type="hidden" name="wonum" value="{{ $workOrder->wonum }}">
                                    <button type="submit" 
                                            class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-green-600 text-white text-xs font-bold rounded-xl hover:bg-green-700 transition-all shadow-md"
                                            onclick="return confirm('Generate jobcard untuk WO {{ $workOrder->wonum }}?')">
                                        <i class="fas fa-magic mr-2"></i> Generate Jobcard Sekarang
                                    </button>
                                </form>
                            @else
                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-100 flex items-start gap-3">
                                    <i class="fas fa-info-circle text-gray-400 mt-0.5"></i>
                                    <p class="text-xs text-gray-600 leading-relaxed">
                                        Jobcard hanya dapat di-generate jika status Work Order adalah <span class="font-bold text-[#009BB9]">APPR</span>.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Codes --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-code text-orange-500"></i> Codes & References
                    </h3>
                    <dl class="text-sm space-y-2">
                        @foreach([
                            'Job Plan (JPNUM)' => $workOrder->jpnum,
                            'PM Number' => $workOrder->pmnum,
                            'Failure Code' => $workOrder->failurecode,
                            'Problem Code' => $workOrder->problemcode,
                            'GL Account' => $workOrder->glaccount,
                            'Contract' => $workOrder->contract,
                            'Orig Record ID' => $workOrder->origrecordid,
                            'Orig Record Class' => $workOrder->origrecordclass,
                            'Has Children' => $workOrder->haschildren,
                            'History Flag' => $workOrder->historyflag,
                            'Rem Dur' => $workOrder->remdur,
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
                        'Report Date' => $workOrder->reportdate,
                        'Sched Start' => $workOrder->schedstart,
                        'Sched Finish' => $workOrder->schedfinish,
                        'Actual Start' => $workOrder->actstart,
                        'Actual Finish' => $workOrder->actfinish,
                        'Target Start' => $workOrder->targstartdate,
                        'Target Completion' => $workOrder->targcompdate,
                        'Change Date' => $workOrder->changedate,
                        'Fail Date' => $workOrder->faildate,
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
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($workOrder->estlabhrs, 2) }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($workOrder->actlabhrs, 2) }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-right text-gray-400">-</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border border-gray-200 font-medium">Labor Cost</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($workOrder->estlabcost, 2) }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($workOrder->actlabcost, 2) }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($workOrder->outlabcost, 2) }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border border-gray-200 font-medium">Material Cost</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($workOrder->estmatcost, 2) }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($workOrder->actmatcost, 2) }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($workOrder->outmatcost, 2) }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border border-gray-200 font-medium">Tool Cost</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($workOrder->esttoolcost, 2) }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($workOrder->acttoolcost, 2) }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($workOrder->outtoolcost, 2) }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border border-gray-200 font-medium">Service Cost</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($workOrder->estservcost, 2) }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-right">{{ number_format($workOrder->actservcost, 2) }}</td>
                                <td class="px-4 py-2 border border-gray-200 text-right text-gray-400">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Custom Fields & Remarks --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-5">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-cogs text-gray-500"></i> Custom Fields (WOEQ)
                    </h3>
                    <dl class="text-sm space-y-2">
                        @foreach([
                            'WOEQ1' => $workOrder->woeq1,
                            'WOEQ2' => $workOrder->woeq2,
                            'WOEQ3' => $workOrder->woeq3,
                            'WOEQ4' => $workOrder->woeq4,
                            'WOEQ5' => is_numeric($workOrder->woeq5) ? number_format($workOrder->woeq5, 2) : $workOrder->woeq5,
                            'WOEQ6' => $workOrder->woeq6,
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
                            'Remark C' => $workOrder->remarkdescc,
                            'Remark P' => $workOrder->remarkdescp,
                            'Remark PLN' => $workOrder->remarkdescpln,
                            'Remark R' => $workOrder->remarkdescr,
                        ] as $label => $value)
                        <div class="py-2 border-b border-gray-50">
                            <dt class="text-gray-400 text-xs mb-1">{{ $label }}</dt>
                            <dd class="font-medium text-gray-800 whitespace-pre-wrap break-words text-sm">{{ $value }}</dd>
                        </div>
                        @endforeach
                    </dl>
                </div>
            </div>

            {{-- Update Status Pembanding (MySQL) --}}
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