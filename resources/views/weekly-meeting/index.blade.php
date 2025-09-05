@extends('layouts.app')
@section('title', 'Weekly Meeting')
@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .nav-link {
            color: #ffffff;
            text-decoration: none;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
            position: relative;
            font-weight: 500;
        }
        .nav-link:hover {
            color: #4299e1;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 50%;
            background-color: #4299e1;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .nav-link:hover::after {
            width: 100%;
        }
        .nav-link.active {
            color: #A8D600;
        }
        .nav-link.active::after {
            width: 100%;
        }
        .login-button {
            background-color: #4299e1;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .login-button:hover {
            background-color: #3182ce;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .nav-link-mobile {
            display: block;
            padding: 0.75rem 1rem;
            color: #ffffff;
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        .nav-link-mobile:hover {
            background-color: #2d3748;
            color: #A8D600;
            border-left-color: #A8D600;
        }
        .login-mobile {
            background-color: #4299e1;
            color: white !important;
            border-radius: 0.375rem;
            margin: 0.5rem 1rem;
        }
        .login-mobile:hover {
            background-color: #3182ce !important;
            border-left-color: transparent !important;
        }
        .fas {
            display: inline-flex;
            align-items: center;
        }
        .shadow-lg {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
    </style>
@endpush
@section('content')
<!-- Navbar manual -->
<nav class="fixed w-full top-0 z-50" style="background: linear-gradient(to right, #1a1a1a, #2d3748); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="#" class="flex items-center">
                    <img src="{{ asset('logo/navlogo.png') }}" alt="Logo" class="h-8">
                </a>
            </div>
            <!-- Menu Desktop -->
            <div class="hidden md:flex items-center ">
                <ul class="flex space-x-8">
                    <li><a href="#" class="nav-link" style="color:#fff;">Home</a></li>
                    <li><a href="#map" class="nav-link" style="color:#fff;">Peta Pembangkit</a></li>
                    <li><a href="#live-data" class="nav-link" style="color:#fff;">Live Data</a></li>
                    <li><a href="{{ route('dashboard.pemantauan') }}" class="nav-link" style="color:#fff;">Dashboard Pemantauan</a></li>
                    <li><a href="https://sites.google.com/view/pemeliharaan-upkendari" class="nav-link" style="color:#fff;" target="_blank">Bid. Pemeliharaan</a></li>
                    <li><a href="{{ route('notulen.form') }}" class="nav-link" style="color:#fff;">Notulen</a></li>
                    <li class="relative group">
                        <button class="nav-link flex items-center focus:outline-none" id="menu-lainnya-btn" style="color:#fff;">
                            Menu Lainnya
                            <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="absolute left-0 mt-2 w-56 bg-white rounded shadow-lg z-50 hidden group-hover:block group-focus:block" id="menu-lainnya-dropdown">
                            <a href="{{ route('calendar.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-100"><i class="fas fa-calendar-alt mr-1"></i> Kalender</a>
                            <a href="{{ route('kinerja.pemeliharaan') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-100"><i class="fas fa-chart-line mr-1"></i> Kinerja Pemeliharaan</a>
                            <a href="{{ route('weekly-meeting.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-100">Weekly Meeting</a>
                        </div>
                    </li>
                    <li>
                        <a href="{{ route('login') }}" class="login-button" style="background:#4299e1;color:#fff;padding:0.5rem 1.5rem;border-radius:0.375rem;font-weight:500;display:flex;align-items:center;gap:0.5rem;">
                            <i class="fas fa-user mr-2"></i> Login
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Menu Mobile -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden pb-4">
            <ul class="space-y-4">
                <li><a href="#" class="nav-link-mobile" style="color:#fff;">Home</a></li>
                <li><a href="#map" class="nav-link-mobile" style="color:#fff;">Peta Pembangkit</a></li>
                <li><a href="#live-data" class="nav-link-mobile" style="color:#fff;">Live Data Unit Operasional</a></li>
                <li><a href="{{ route('dashboard.pemantauan') }}" class="nav-link-mobile" style="color:#fff;">Dashboard Pemantauan</a></li>
                <li><a href="https://sites.google.com/view/pemeliharaan-upkendari" class="nav-link-mobile" style="color:#fff;" target="_blank">Bid. Pemeliharaan</a></li>
                <li><a href="{{ route('notulen.form') }}" class="nav-link-mobile" style="color:#fff;">Notulen</a></li>
                <li><a href="{{ route('calendar.index') }}" class="nav-link-mobile" style="color:#fff;"><i class="fas fa-calendar-alt mr-1"></i> Kalender</a></li>
                <li><a href="{{ route('kinerja.pemeliharaan') }}" class="nav-link-mobile" style="color:#fff;"><i class="fas fa-chart-line mr-1"></i> Kinerja Pemeliharaan</a></li>
                <li><a href="{{ route('weekly-meeting.index') }}" class="nav-link-mobile" style="color:#fff;">Weekly Meeting</a></li>
                <li>
                    <a href="{{ route('login') }}" class="nav-link-mobile login-mobile" style="background:#4299e1;color:#fff !important;border-radius:0.375rem;margin:0.5rem 1rem;">
                        <i class="fas fa-user mr-2"></i> Login
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mx-auto py-8 mt-24">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-blue-800">Weekly Meeting</h1>
            <div class="text-gray-600">Periode: {{ $startOfWeek->format('d M Y') }} - {{ $endOfWeek->format('d M Y') }}</div>
        </div>
        <div class="mt-4 md:mt-0 flex gap-2">
            <button id="tab-this-week" class="tab-btn bg-blue-600 text-white px-4 py-2 rounded-l focus:outline-none flex items-center">
                <i class="fas fa-calendar-week mr-2"></i> Minggu Ini
            </button>
            <button id="tab-next-week" class="tab-btn bg-blue-100 text-blue-700 px-4 py-2 rounded-r focus:outline-none flex items-center">
                <i class="fas fa-calendar-plus mr-2"></i> Minggu Depan
            </button>
        </div>
    </div>
    <!-- Ringkasan Card -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 rounded shadow p-4 text-center">
            <div class="text-xs text-blue-700 font-semibold uppercase tracking-wide">WO Backlog</div>
            <div class="text-3xl font-bold text-blue-900">{{ $woBacklogsThisWeek->count() }}</div>
        </div>
        <div class="bg-green-50 rounded shadow p-4 text-center">
            <div class="text-xs text-green-700 font-semibold uppercase tracking-wide">Work Order</div>
            <div class="text-3xl font-bold text-green-900">{{ $workOrdersThisWeek->count() }}</div>
        </div>
        <div class="bg-yellow-50 rounded shadow p-4 text-center">
            <div class="text-xs text-yellow-700 font-semibold uppercase tracking-wide">Service Request</div>
            <div class="text-3xl font-bold text-yellow-900">{{ $serviceRequestsThisWeek->count() }}</div>
        </div>
        <div class="bg-red-50 rounded shadow p-4 text-center">
            <div class="text-xs text-red-700 font-semibold uppercase tracking-wide">Kesiapan Pembangkit</div>
            <div class="text-3xl font-bold text-red-900">{{ $powerPlantMaintenancesThisWeek->count() }}</div>
        </div>
    </div>
    <hr class="my-6 border-blue-200">
    <div id="rekap-this-week">
        <h2 class="text-lg font-bold mb-2 text-blue-800 flex items-center"><i class="fas fa-list-alt mr-2"></i> Rekapan Pekerjaan Minggu Ini</h2>
        <!-- Tab Navigation -->
        <div class="flex gap-2 mb-4">
            <button class="tab-tipe px-4 py-2 rounded bg-blue-600 text-white font-semibold" data-tipe="wo-backlog-this">WO Backlog ({{ $woBacklogsThisWeek->count() }})</button>
            <button class="tab-tipe px-4 py-2 rounded bg-gray-200 text-gray-700 font-semibold" data-tipe="wo-this">Work Order ({{ $workOrdersThisWeek->count() }})</button>
            <button class="tab-tipe px-4 py-2 rounded bg-yellow-100 text-yellow-700 font-semibold" data-tipe="sr-this">Service Request ({{ $serviceRequestsThisWeek->count() }})</button>
            <button class="tab-tipe px-4 py-2 rounded bg-red-100 text-red-700 font-semibold" data-tipe="readiness-this">Kesiapan Pembangkit ({{ $powerPlantMaintenancesThisWeek->count() }})</button>
        </div>
        <!-- Tabel WO Backlog -->
        <div class="tab-pane-tipe" id="wo-backlog-this">
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Mulai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $no = 1; @endphp
                        @foreach($woBacklogsThisWeek as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center border border-gray-200">{{ $no++ }}</td>
                                <td class="px-6 py-4 text-blue-700 font-semibold border border-gray-200">Backlog</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $item->deskripsi }}</td>
                                <td class="px-6 py-4 border border-gray-200">
                                    {{ $unitMap[$item->unit_source] ?? $item->unit_source }}
                                </td>

                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->schedule_start)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->schedule_finish)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 border border-gray-200">@include('weekly-meeting.status-badge', ['status'=>$item->status])</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Tabel Work Order -->
        <div class="tab-pane-tipe hidden" id="wo-this">
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Mulai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $no = 1; @endphp
                        @foreach($workOrdersThisWeek as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center border border-gray-200">{{ $no++ }}</td>
                                <td class="px-6 py-4 text-green-700 font-semibold border border-gray-200">WO</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $item->description }}</td>
                                <td class="px-6 py-4 border border-gray-200">
                                    {{ $unitMap[$item->unit_source] ?? $item->unit_source }}
                                </td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->schedule_start)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->schedule_finish)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 border border-gray-200">@include('weekly-meeting.status-badge', ['status'=>$item->status])</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Tabel Service Request -->
        <div class="tab-pane-tipe hidden" id="sr-this">
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Mulai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $no = 1; @endphp
                        @foreach($serviceRequestsThisWeek as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center border border-gray-200">{{ $no++ }}</td>
                                <td class="px-6 py-4 text-yellow-700 font-semibold border border-gray-200">SR</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $item->description }}</td>
                                <td class="px-6 py-4 border border-gray-200">
                                    {{ $unitMap[$item->unit_source] ?? $item->unit_source }}
                                </td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->created_at)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->updated_at)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 border border-gray-200">@include('weekly-meeting.status-badge', ['status'=>$item->status])</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Tabel Kesiapan Pembangkit -->
        <div class="tab-pane-tipe hidden" id="readiness-this">
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Target Selesai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $no = 1; @endphp
                        @foreach($powerPlantMaintenancesThisWeek as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center border border-gray-200">{{ $no++ }}</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $unitMap[$item->unit_source] ?? $item->unit_source }}</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $item->deskripsi ?? '-' }}</td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->tanggal_mulai)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->target_selesai)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 border border-gray-200">@include('weekly-meeting.status-badge', ['status'=>$item->status])</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Tab Minggu Depan: sama, ganti id dan variabel _NextWeek -->
    <div id="rekap-next-week" class="hidden mt-10">
        <h2 class="text-lg font-bold mb-2 text-blue-800 flex items-center"><i class="fas fa-calendar-plus mr-2"></i> Rencana Pekerjaan Minggu Depan</h2>
        <div class="flex gap-2 mb-4">
            <button class="tab-tipe-next px-4 py-2 rounded bg-blue-600 text-white font-semibold" data-tipe="wo-backlog-next">WO Backlog ({{ $woBacklogsNextWeek->count() }})</button>
            <button class="tab-tipe-next px-4 py-2 rounded bg-gray-200 text-gray-700 font-semibold" data-tipe="wo-next">Work Order ({{ $workOrdersNextWeek->count() }})</button>
            <button class="tab-tipe-next px-4 py-2 rounded bg-yellow-100 text-yellow-700 font-semibold" data-tipe="sr-next">Service Request ({{ $serviceRequestsNextWeek->count() }})</button>
            <button class="tab-tipe-next px-4 py-2 rounded bg-red-100 text-red-700 font-semibold" data-tipe="readiness-next">Kesiapan Pembangkit ({{ $powerPlantMaintenancesNextWeek->count() }})</button>
        </div>
        <div class="tab-pane-tipe-next" id="wo-backlog-next">
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Mulai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $no = 1; @endphp
                        @foreach($woBacklogsNextWeek as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center border border-gray-200">{{ $no++ }}</td>
                                <td class="px-6 py-4 text-blue-700 font-semibold border border-gray-200">Backlog</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $item->deskripsi }}</td>
                                <td class="px-6 py-4 border border-gray-200">
                                    {{ $unitMap[$item->unit_source] ?? $item->unit_source }}
                                </td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->schedule_start)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->schedule_finish)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 border border-gray-200">@include('weekly-meeting.status-badge', ['status'=>$item->status])</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane-tipe-next hidden" id="wo-next">
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Mulai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $no = 1; @endphp
                        @foreach($workOrdersNextWeek as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center border border-gray-200">{{ $no++ }}</td>
                                <td class="px-6 py-4 text-green-700 font-semibold border border-gray-200">WO</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $item->description }}</td>
                                <td class="px-6 py-4 border border-gray-200">
                                    {{ $unitMap[$item->unit_source] ?? $item->unit_source }}
                                </td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->schedule_start)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->schedule_finish)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 border border-gray-200">@include('weekly-meeting.status-badge', ['status'=>$item->status])</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane-tipe-next hidden" id="sr-next">
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Mulai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $no = 1; @endphp
                        @foreach($serviceRequestsNextWeek as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center border border-gray-200">{{ $no++ }}</td>
                                <td class="px-6 py-4 text-yellow-700 font-semibold border border-gray-200">SR</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $item->description }}</td>
                                <td class="px-6 py-4 border border-gray-200">
                                    {{ $unitMap[$item->unit_source] ?? $item->unit_source }}
                                </td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->created_at)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->updated_at)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 border border-gray-200">@include('weekly-meeting.status-badge', ['status'=>$item->status])</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Tabel Kesiapan Pembangkit Minggu Depan -->
        <div class="tab-pane-tipe-next hidden" id="readiness-next">
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Target Selesai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $no = 1; @endphp
                        @foreach($powerPlantMaintenancesNextWeek as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center border border-gray-200">{{ $no++ }}</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $unitMap[$item->unit_source] ?? $item->unit_source }}</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $item->deskripsi ?? '-' }}</td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->tanggal_mulai)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-center border border-gray-200">{{ optional($item->target_selesai)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 border border-gray-200">@include('weekly-meeting.status-badge', ['status'=>$item->status])</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<hr class="my-6 border-blue-200">
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab Minggu Ini/Minggu Depan
        const tabThisWeek = document.getElementById('tab-this-week');
        const tabNextWeek = document.getElementById('tab-next-week');
        const rekapThisWeek = document.getElementById('rekap-this-week');
        const rekapNextWeek = document.getElementById('rekap-next-week');
        tabThisWeek.addEventListener('click', function() {
            tabThisWeek.classList.add('bg-blue-600', 'text-white');
            tabThisWeek.classList.remove('bg-blue-100', 'text-blue-700');
            tabNextWeek.classList.remove('bg-blue-600', 'text-white');
            tabNextWeek.classList.add('bg-blue-100', 'text-blue-700');
            rekapThisWeek.classList.remove('hidden');
            rekapNextWeek.classList.add('hidden');
        });
        tabNextWeek.addEventListener('click', function() {
            tabNextWeek.classList.add('bg-blue-600', 'text-white');
            tabNextWeek.classList.remove('bg-blue-100', 'text-blue-700');
            tabThisWeek.classList.remove('bg-blue-600', 'text-white');
            tabThisWeek.classList.add('bg-blue-100', 'text-blue-700');
            rekapThisWeek.classList.add('hidden');
            rekapNextWeek.classList.remove('hidden');
        });
        // Tab Minggu Ini per tipe
        const tipeTabs = document.querySelectorAll('.tab-tipe');
        const tipePanes = document.querySelectorAll('.tab-pane-tipe');
        tipeTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                tipeTabs.forEach(t => t.classList.remove('bg-blue-600', 'text-white'));
                tipeTabs.forEach(t => t.classList.add('bg-gray-200', 'text-gray-700'));
                this.classList.remove('bg-gray-200', 'text-gray-700');
                this.classList.add('bg-blue-600', 'text-white');
                tipePanes.forEach(pane => pane.classList.add('hidden'));
                document.getElementById(this.dataset.tipe).classList.remove('hidden');
            });
        });
        // Tab Minggu Depan per tipe
        const tipeTabsNext = document.querySelectorAll('.tab-tipe-next');
        const tipePanesNext = document.querySelectorAll('.tab-pane-tipe-next');
        tipeTabsNext.forEach(tab => {
            tab.addEventListener('click', function() {
                tipeTabsNext.forEach(t => t.classList.remove('bg-blue-600', 'text-white'));
                tipeTabsNext.forEach(t => t.classList.add('bg-gray-200', 'text-gray-700'));
                this.classList.remove('bg-gray-200', 'text-gray-700');
                this.classList.add('bg-blue-600', 'text-white');
                tipePanesNext.forEach(pane => pane.classList.add('hidden'));
                document.getElementById(this.dataset.tipe).classList.remove('hidden');
            });
        });
    });
</script>
@endsection
