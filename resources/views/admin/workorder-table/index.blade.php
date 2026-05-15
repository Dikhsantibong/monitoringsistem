@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.sidebar')

    <div id="main-content" class="flex-1 overflow-auto">
        <header class="bg-white shadow-sm sticky top-0 z-10">
            <div class="flex justify-between items-center px-6 py-3">
                <div class="flex items-center gap-x-3">
                    <button id="mobile-menu-toggle" class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#009BB9] hover:text-white">
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                    </button>
                    <button id="desktop-menu-toggle" class="hidden md:block relative items-center justify-center rounded-md text-gray-400 hover:bg-[#009BB9] p-2 hover:text-white">
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800">
                        <i class="fas fa-table mr-2 text-blue-600"></i>Data Lengkap Work Order (APPR)
                    </h1>
                </div>
                <div class="flex items-center gap-x-4 relative">
                    <a href="{{ route('admin.maximo.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Maximo
                    </a>
                    <div class="relative">
                        <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                            <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}" class="w-8 h-8 rounded-full mr-2">
                            <span class="text-gray-700">{{ Auth::user()->name }}</span>
                            <i class="fas fa-caret-down ml-2"></i>
                        </button>
                        <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                            <a href="{{ route('user.profile') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="px-6 mt-4 pb-8">
            @if($error)
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>{{ $error }}
            </div>
            @endif

            {{-- Info Card --}}
            <div class="mb-4 bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-database text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-blue-800">Total Data Work Order (APPR)</p>
                        <p class="text-2xl font-bold text-blue-900">{{ number_format($totalRecords) }} <span class="text-sm font-normal text-blue-600">records</span></p>
                    </div>
                </div>
                <div class="text-xs text-blue-500">SITEID: KD | PREFIX: WO | STATUS: APPR</div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                {{-- Search & Controls --}}
                <form method="GET" action="{{ route('admin.workorder-table.index') }}" id="woTableForm">
                    <div class="flex flex-col md:flex-row md:items-end gap-3 mb-4">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cari Data</label>
                            <div class="flex">
                                <input type="text" name="search" value="{{ $search }}" placeholder="Cari WONUM, Description, Asset, Location, JPNUM, PM, Reported By, dll..." class="w-full px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-r-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-search mr-1"></i> Cari
                                </button>
                            </div>
                        </div>
                        <div class="w-32">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Per Halaman</label>
                            <select name="per_page" onchange="document.getElementById('woTableForm').submit()" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                @foreach([10, 25, 50, 100] as $pp)
                                <option value="{{ $pp }}" {{ $perPage == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($search)
                        <a href="{{ route('admin.workorder-table.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300 transition-colors">
                            <i class="fas fa-times mr-1"></i> Reset
                        </a>
                        @endif
                    </div>
                    <input type="hidden" name="sort_by" value="{{ $sortBy }}">
                    <input type="hidden" name="sort_dir" value="{{ $sortDir }}">
                </form>

                {{-- Table --}}
                <div class="overflow-x-auto border border-gray-200 rounded-lg" style="max-height: 70vh;">
                    <table class="min-w-max divide-y divide-gray-200 text-xs" id="woDataTable">
                        <thead class="bg-gray-100 sticky top-0 z-[5]">
                            <tr>
                                <th class="px-3 py-2 text-center whitespace-nowrap border border-gray-200 bg-gray-100">No</th>
                                @php
                                $headers = [
                                    'WONUM' => 'WO Number',
                                    'PARENT' => 'Parent',
                                    'WORKTYPE' => 'Work Type',
                                    'WOPRIORITY' => 'Priority',
                                    'DESCRIPTION' => 'Description',
                                    'ASSETNUM' => 'Asset',
                                    'LOCATION' => 'Location',
                                    'STATUSDATE' => 'Status Date',
                                    'REPORTDATE' => 'Report Date',
                                    'REPORTEDBY' => 'Reported By',
                                    'SCHEDSTART' => 'Sched Start',
                                    'SCHEDFINISH' => 'Sched Finish',
                                    'ACTSTART' => 'Act Start',
                                    'ACTFINISH' => 'Act Finish',
                                    'TARGSTARTDATE' => 'Target Start',
                                    'TARGCOMPDATE' => 'Target Comp',
                                    'JPNUM' => 'Job Plan',
                                    'PMNUM' => 'PM Number',
                                    'FAILURECODE' => 'Failure Code',
                                    'PROBLEMCODE' => 'Problem Code',
                                    'ESTDUR' => 'Est Duration',
                                    'ESTLABHRS' => 'Est Lab Hrs',
                                    'ESTMATCOST' => 'Est Mat Cost',
                                    'ESTLABCOST' => 'Est Lab Cost',
                                    'ESTTOOLCOST' => 'Est Tool Cost',
                                    'ESTSERVCOST' => 'Est Serv Cost',
                                    'ACTLABHRS' => 'Act Lab Hrs',
                                    'ACTMATCOST' => 'Act Mat Cost',
                                    'ACTLABCOST' => 'Act Lab Cost',
                                    'ACTTOOLCOST' => 'Act Tool Cost',
                                    'ACTSERVCOST' => 'Act Serv Cost',
                                    'OUTLABCOST' => 'Out Lab Cost',
                                    'OUTMATCOST' => 'Out Mat Cost',
                                    'OUTTOOLCOST' => 'Out Tool Cost',
                                    'DOWNTIME' => 'Downtime',
                                    'REMDUR' => 'Rem Duration',
                                    'SUPERVISOR' => 'Supervisor',
                                    'CREWID' => 'Crew ID',
                                    'PERSONGROUP' => 'Person Group',
                                    'LEAD' => 'Lead',
                                    'OWNER' => 'Owner',
                                    'OWNERGROUP' => 'Owner Group',
                                    'HASCHILDREN' => 'Has Children',
                                    'HISTORYFLAG' => 'History',
                                    'CONTRACT' => 'Contract',
                                    'GLACCOUNT' => 'GL Account',
                                    'WOCLASS' => 'WO Class',
                                    'ORIGRECORDID' => 'Orig Record',
                                    'ORIGRECORDCLASS' => 'Orig Class',
                                    'CHANGEBY' => 'Changed By',
                                    'CHANGEDATE' => 'Change Date',
                                    'FAILDATE' => 'Fail Date',
                                    'WOEQ1' => 'WOEQ1',
                                    'WOEQ2' => 'WOEQ2',
                                    'WOEQ3' => 'WOEQ3',
                                    'WOEQ4' => 'WOEQ4',
                                    'WOEQ5' => 'WOEQ5',
                                    'WOEQ6' => 'WOEQ6',
                                    'ANGGARAN' => 'Anggaran',
                                    'WONUMPLN' => 'WO PLN',
                                    'REMARKDESCC' => 'Remark C',
                                    'REMARKDESCP' => 'Remark P',
                                    'REMARKDESCPLN' => 'Remark PLN',
                                    'REMARKDESCR' => 'Remark R',
                                    'ORGID' => 'Org ID',
                                    'SITEID' => 'Site ID',
                                ];
                                $sortableList = ['WONUM','PARENT','STATUSDATE','WORKTYPE','DESCRIPTION','ASSETNUM','LOCATION','WOPRIORITY','REPORTDATE','SCHEDSTART','SCHEDFINISH','ACTSTART','ACTFINISH','TARGSTARTDATE','TARGCOMPDATE','ESTDUR','ESTLABHRS','ACTLABHRS','PMNUM','JPNUM','REPORTEDBY','SUPERVISOR','CREWID','FAILURECODE','PROBLEMCODE'];
                                @endphp
                                @foreach($headers as $col => $label)
                                <th class="px-3 py-2 text-center whitespace-nowrap border border-gray-200 bg-gray-100 {{ in_array($col, $sortableList) ? 'cursor-pointer hover:bg-gray-200' : '' }}"
                                    @if(in_array($col, $sortableList))
                                    onclick="sortTable('{{ $col }}')"
                                    title="Sort by {{ $label }}"
                                    @endif>
                                    {{ $label }}
                                    @if($sortBy === $col)
                                    <i class="fas fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ml-1 text-blue-600"></i>
                                    @elseif(in_array($col, $sortableList))
                                    <i class="fas fa-sort ml-1 text-gray-300"></i>
                                    @endif
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                        @if($workOrders && count($workOrders) > 0)
                            @foreach($workOrders as $i => $wo)
                            @php $wo = (object) array_change_key_case((array) $wo, CASE_LOWER); @endphp
                            <tr class="hover:bg-blue-50 transition-colors">
                                <td class="px-3 py-2 text-center border border-gray-200 font-medium">{{ ($workOrders->currentPage() - 1) * $workOrders->perPage() + $loop->iteration }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200 font-semibold text-blue-700">{{ $wo->wonum ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->parent ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200"><span class="px-2 py-0.5 rounded bg-indigo-100 text-indigo-700 font-medium">{{ $wo->worktype ?? '-' }}</span></td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->wopriority ?? '-' }}</td>
                                <td class="px-3 py-2 border border-gray-200"><span class="inline-block w-72 break-words whitespace-normal">{{ $wo->description ?? '-' }}</span></td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->assetnum ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->location ?? '-' }}</td>
                                @php
                                $dateCols = ['statusdate','reportdate','schedstart','schedfinish','actstart','actfinish','targstartdate','targcompdate','changedate','faildate','woeq6'];
                                $numCols = ['estdur','estlabhrs','estmatcost','estlabcost','esttoolcost','estservcost','actlabhrs','actmatcost','actlabcost','acttoolcost','actservcost','outlabcost','outmatcost','outtoolcost','downtime','remdur','woeq5'];
                                @endphp
                                @foreach(['statusdate','reportdate'] as $dc)
                                <td class="px-3 py-2 text-center border border-gray-200 whitespace-nowrap">
                                    @if(isset($wo->$dc) && $wo->$dc)
                                    <span class="text-xs">{{ \Carbon\Carbon::parse($wo->$dc)->format('d-m-Y H:i') }}</span>
                                    @else - @endif
                                </td>
                                @endforeach
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->reportedby ?? '-' }}</td>
                                @foreach(['schedstart','schedfinish','actstart','actfinish','targstartdate','targcompdate'] as $dc)
                                <td class="px-3 py-2 text-center border border-gray-200 whitespace-nowrap">
                                    @if(isset($wo->$dc) && $wo->$dc)
                                    <span class="text-xs">{{ \Carbon\Carbon::parse($wo->$dc)->format('d-m-Y H:i') }}</span>
                                    @else - @endif
                                </td>
                                @endforeach
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->jpnum ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->pmnum ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->failurecode ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->problemcode ?? '-' }}</td>
                                @foreach(['estdur','estlabhrs','estmatcost','estlabcost','esttoolcost','estservcost','actlabhrs','actmatcost','actlabcost','acttoolcost','actservcost','outlabcost','outmatcost','outtoolcost'] as $nc)
                                <td class="px-3 py-2 text-right border border-gray-200">{{ isset($wo->$nc) ? number_format($wo->$nc, 2) : '-' }}</td>
                                @endforeach
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->downtime ?? '-' }}</td>
                                <td class="px-3 py-2 text-right border border-gray-200">{{ isset($wo->remdur) ? number_format($wo->remdur, 2) : '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->supervisor ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->crewid ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->persongroup ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->lead ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->owner ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->ownergroup ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->haschildren ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->historyflag ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->contract ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->glaccount ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->woclass ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->origrecordid ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->origrecordclass ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->changeby ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200 whitespace-nowrap">
                                    @if(isset($wo->changedate) && $wo->changedate)
                                    <span class="text-xs">{{ \Carbon\Carbon::parse($wo->changedate)->format('d-m-Y H:i') }}</span>
                                    @else - @endif
                                </td>
                                <td class="px-3 py-2 text-center border border-gray-200 whitespace-nowrap">
                                    @if(isset($wo->faildate) && $wo->faildate)
                                    <span class="text-xs">{{ \Carbon\Carbon::parse($wo->faildate)->format('d-m-Y H:i') }}</span>
                                    @else - @endif
                                </td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->woeq1 ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->woeq2 ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->woeq3 ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->woeq4 ?? '-' }}</td>
                                <td class="px-3 py-2 text-right border border-gray-200">{{ isset($wo->woeq5) ? number_format($wo->woeq5, 2) : '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200 whitespace-nowrap">
                                    @if(isset($wo->woeq6) && $wo->woeq6)
                                    <span class="text-xs">{{ \Carbon\Carbon::parse($wo->woeq6)->format('d-m-Y H:i') }}</span>
                                    @else - @endif
                                </td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->anggaran ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->wonumpln ?? '-' }}</td>
                                <td class="px-3 py-2 border border-gray-200">{{ $wo->remarkdescc ?? '-' }}</td>
                                <td class="px-3 py-2 border border-gray-200">{{ $wo->remarkdescp ?? '-' }}</td>
                                <td class="px-3 py-2 border border-gray-200">{{ $wo->remarkdescpln ?? '-' }}</td>
                                <td class="px-3 py-2 border border-gray-200">{{ $wo->remarkdescr ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->orgid ?? '-' }}</td>
                                <td class="px-3 py-2 text-center border border-gray-200">{{ $wo->siteid ?? '-' }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="99" class="text-center py-8 text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-3 block text-gray-300"></i>
                                    Tidak ada data Work Order dengan status APPR
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($workOrders && $workOrders->hasPages())
                <div class="mt-4 flex flex-col md:flex-row justify-between items-center gap-3">
                    <div class="text-sm text-gray-700">
                        Menampilkan {{ ($workOrders->currentPage() - 1) * $workOrders->perPage() + 1 }}
                        hingga {{ min($workOrders->currentPage() * $workOrders->perPage(), $workOrders->total()) }}
                        dari {{ number_format($workOrders->total()) }} entri
                    </div>
                    <div class="flex items-center gap-1 flex-wrap">
                        @if (!$workOrders->onFirstPage())
                            <a href="{{ $workOrders->appends(['search' => $search, 'per_page' => $perPage, 'sort_by' => $sortBy, 'sort_dir' => $sortDir])->previousPageUrl() }}" class="px-3 py-1 bg-[#0A749B] text-white rounded text-sm">Sebelumnya</a>
                        @endif
                        @php
                            $currentPage = $workOrders->currentPage();
                            $lastPage = $workOrders->lastPage();
                            $start = max(1, $currentPage - 3);
                            $end = min($lastPage, $currentPage + 3);
                        @endphp
                        @if($start > 1)
                            <a href="{{ $workOrders->appends(['search' => $search, 'per_page' => $perPage, 'sort_by' => $sortBy, 'sort_dir' => $sortDir])->url(1) }}" class="px-3 py-1 rounded bg-white text-[#0A749B] border border-[#0A749B] text-sm">1</a>
                            @if($start > 2)<span class="px-2 text-gray-400">...</span>@endif
                        @endif
                        @for($page = $start; $page <= $end; $page++)
                            @if ($page == $currentPage)
                                <span class="px-3 py-1 bg-[#0A749B] text-white rounded text-sm">{{ $page }}</span>
                            @else
                                <a href="{{ $workOrders->appends(['search' => $search, 'per_page' => $perPage, 'sort_by' => $sortBy, 'sort_dir' => $sortDir])->url($page) }}" class="px-3 py-1 rounded bg-white text-[#0A749B] border border-[#0A749B] text-sm">{{ $page }}</a>
                            @endif
                        @endfor
                        @if($end < $lastPage)
                            @if($end < $lastPage - 1)<span class="px-2 text-gray-400">...</span>@endif
                            <a href="{{ $workOrders->appends(['search' => $search, 'per_page' => $perPage, 'sort_by' => $sortBy, 'sort_dir' => $sortDir])->url($lastPage) }}" class="px-3 py-1 rounded bg-white text-[#0A749B] border border-[#0A749B] text-sm">{{ $lastPage }}</a>
                        @endif
                        @if ($workOrders->hasMorePages())
                            <a href="{{ $workOrders->appends(['search' => $search, 'per_page' => $perPage, 'sort_by' => $sortBy, 'sort_dir' => $sortDir])->nextPageUrl() }}" class="px-3 py-1 bg-[#0A749B] text-white rounded text-sm">Selanjutnya</a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </main>
    </div>
</div>

<script>
function sortTable(column) {
    const form = document.getElementById('woTableForm');
    const sortByInput = form.querySelector('input[name="sort_by"]');
    const sortDirInput = form.querySelector('input[name="sort_dir"]');
    if (sortByInput.value === column) {
        sortDirInput.value = sortDirInput.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortByInput.value = column;
        sortDirInput.value = 'asc';
    }
    form.submit();
}
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
