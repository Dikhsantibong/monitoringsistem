@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <x-sidebar />

    <div id="main-content" class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-20">
            <div class="flex justify-between items-center px-6 py-3">
                <div class="flex items-center gap-x-3">
                    <!-- Mobile Menu Toggle -->
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

                    <!-- Desktop Menu Toggle -->
                    <button id="desktop-menu-toggle"
                        class="hidden md:block relative items-center justify-center rounded-md text-gray-400 hover:bg-[#009BB9] p-2 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                        aria-controls="desktop-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800">Pembahasan Lain-lain</h1>
                </div>

                @include('components.timer')
                <div class="relative">
                    <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                        <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                            class="w-7 h-7 rounded-full mr-2">
                        <span class="text-gray-700 text-sm">{{ Auth::user()->name }}</span>
                        <i class="fas fa-caret-down ml-2 text-gray-600"></i>
                    </button>
                    <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                        <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Breadcrumbs -->
        <div class="mt-3">
            <x-admin-breadcrumb :breadcrumbs="[
                ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['name' => 'Pembahasan Lain-lain', 'url' => null]
            ]" />
        </div>

        <!-- Main Content -->
        <div class="container mx-auto px-2 sm:px-6 py-4 sm:py-8">
            <div class="bg-white rounded-lg shadow p-3 sm:p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Pembahasan Lain-lain</h2>
                    <a href="{{ route('admin.other-discussions.create') }}" class="btn bg-blue-500 text-white hover:bg-blue-600 rounded-lg px-4 py-2">
                        <i class="fas fa-plus mr-2"></i> Tambah Data
                    </a>
                </div>

                <!-- Filter Section -->
                <div class="mb-6 bg-white p-4 rounded-lg shadow">
                    <form action="{{ route('admin.other-discussions.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div class="relative">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                            <div class="relative">
                                <input type="text" 
                                       name="search" 
                                       id="search" 
                                       placeholder="Cari topik, PIC, unit..."
                                       value="{{ request('search') }}"
                                       class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Unit -->
                        {{-- <div>
                            <label for="unit-filter" class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                            <select id="unit-filter" 
                                    name="unit" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 bg-gray-50">
                                <option value="">Semua Unit</option>
                                @foreach(\App\Models\OtherDiscussion::UNITS as $unit)
                                    <option value="{{ $unit }}" {{ request('unit') == $unit ? 'selected' : '' }}>
                                        {{ $unit }}
                                    </option>
                                @endforeach
                            </select>
                        </div> --}}

                        <!-- Filter Tanggal Mulai -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                            <input type="date" 
                                   name="start_date" 
                                   id="start_date"
                                   value="{{ request('start_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>

                        <!-- Filter Tanggal Akhir -->
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                            <input type="date" 
                                   name="end_date" 
                                   id="end_date"
                                   value="{{ request('end_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>

                        <!-- Tombol Filter -->
                        <div class="md:col-span-4 flex justify-end space-x-2">
                            <button type="reset" 
                                    onclick="window.location.href='{{ route('admin.other-discussions.index') }}'"
                                    class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                <i class="fas fa-undo mr-2"></i>Reset
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i class="fas fa-filter mr-2"></i>Filter
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tab Navigation -->
                <div class="mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex">
                            <button onclick="switchTab('active')" 
                                    class="tab-btn active-tab py-4 px-6 font-medium text-sm"
                                    id="active-tab">
                                Data Aktif
                            </button>
                            <button onclick="switchTab('overdue')" 
                                    class="tab-btn py-4 px-6 font-medium text-sm"
                                    id="overdue-tab">
                                Melewati Deadline
                                @if($overdueDiscussions->total() > 0)
                                    <span class="ml-2 bg-red-500 text-white px-2 py-1 rounded-full text-xs">
                                        {{ $overdueDiscussions->total() }}
                                    </span>
                                @endif
                            </button>
                            <button onclick="switchTab('closed')" 
                                    class="tab-btn py-4 px-6 font-medium text-sm"
                                    id="closed-tab">
                                Data Selesai
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Tabel Data Aktif -->
                <div id="active-content" class="tab-content">
                    <div class="overflow-x-auto shadow-md rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-[#0A749B]">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">No SR</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">No WO</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">
                                        <div class="flex items-center justify-between">
                                            <span>Unit</span>
                                            <div class="relative">
                                                <select id="unitTableFilter" onchange="filterTableByUnit()" 
                                                        class="appearance-none bg-transparent text-white cursor-pointer pl-2 pr-6 py-0 text-sm focus:outline-none">
                                                    <option value="" class="text-gray-700">Semua</option>
                                                    @foreach(\App\Models\PowerPlant::select('name')->distinct()->get() as $powerPlant)
                                                        <option value="{{ $powerPlant->name }}" class="text-gray-700">{{ $powerPlant->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
                                                    <svg class="h-4 w-4 fill-current text-white" viewBox="0 0 20 20">
                                                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Topik</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Target</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Risk Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Priority Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Previous Commitment</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Next Commitment</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">PIC</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">
                                        <div class="flex items-center justify-between">
                                            <span>Status</span>
                                            <div class="relative">
                                                <select id="statusTableFilter" onchange="filterTableByStatus()" 
                                                        class="appearance-none bg-transparent text-white cursor-pointer pl-2 pr-6 py-0 text-sm focus:outline-none">
                                                    <option value="" class="text-gray-700">Semua</option>
                                                    <option value="Open" class="text-gray-700">Open</option>
                                                    <option value="Closed" class="text-gray-700">Closed</option>
                                                    <option value="Overdue" class="text-gray-700">Overdue</option>
                                                </select>
                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
                                                    <svg class="h-4 w-4 fill-current text-white" viewBox="0 0 20 20">
                                                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Deadline</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($activeDiscussions as $index => $discussion)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $discussion->sr_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $discussion->wo_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $discussion->unit }}</td>
                                        <td class="px-6 py-4 whitespace-normal border border-gray-200 max-w-[400px] break-words">{{ $discussion->topic }}</td>
                                        <td class="px-6 py-4 whitespace-normal border border-gray-200 max-w-[400px] break-words">{{ $discussion->target }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            <span class="px-2 py-1 text-sm rounded
                                                @if($discussion->risk_level == 'R') bg-green-100 text-green-800
                                                @elseif($discussion->risk_level == 'MR') bg-yellow-100 text-yellow-800  
                                                @elseif($discussion->risk_level == 'MT') bg-orange-100 text-orange-800
                                                @elseif($discussion->risk_level == 'T') bg-red-100 text-red-800
                                                @endif">
                                                {{ $discussion->risk_level }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            <span class="px-2 py-1 text-sm rounded
                                                @if($discussion->priority_level == 'Low') bg-green-100 text-green-800
                                                @elseif($discussion->priority_level == 'Medium') bg-yellow-100 text-yellow-800
                                                @elseif($discussion->priority_level == 'High') bg-red-100 text-red-800
                                                @endif">
                                                {{ $discussion->priority_level }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $discussion->previous_commitment }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $discussion->next_commitment }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $discussion->pic }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            <span class="px-2 py-1 text-sm rounded
                                                @if($discussion->status == 'Open') bg-blue-100 text-blue-800
                                                @elseif($discussion->status == 'Closed') bg-green-100 text-green-800
                                                @elseif($discussion->status == 'Overdue') bg-red-100 text-red-800
                                                @endif">
                                                {{ $discussion->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ \Carbon\Carbon::parse($discussion->deadline)->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex items-center space-x-3">
                                                <!-- Edit -->
                                                <a href="{{ route('admin.other-discussions.edit', $discussion->id) }}"
                                                   class="text-blue-500 hover:text-blue-700">
                                                    <i class="fas fa-edit text-lg"></i>
                                                </a>
                                                
                                                <!-- Delete -->
                                                <button onclick="confirmDelete({{ $discussion->id }})"
                                                        class="text-red-500 hover:text-red-700">
                                                    <i class="fas fa-trash text-lg"></i>
                                                </button>

                                                <!-- Status Closed -->
                                                {{-- <button onclick="updateStatus({{ $discussion->id }}, 'Closed')"
                                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-md text-sm flex items-center">
                                                    <i class="fas fa-check-circle mr-1.5"></i> Selesai
                                                </button> --}}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="14" class="px-6 py-4 text-center text-gray-500">
                                            Tidak ada data aktif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $activeDiscussions->appends(request()->except('active_page'))->links() }}
                    </div>
                </div>

                <!-- Tabel Data Selesai -->
                <div id="closed-content" class="tab-content hidden">
                    <div class="overflow-x-auto shadow-md rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-[#0A749B]">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">No SR</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">No WO</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Unit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Topik</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Sasaran</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Tingkat Resiko</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Tingkat Prioritas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Komitmen Sebelum</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Komitmen Selanjutnya</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">PIC</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Deadline</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Tanggal Selesai</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($closedDiscussions as $index => $discussion)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $discussion->sr_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $discussion->wo_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $discussion->unit }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap max-w-[400px] break-words">{{ $discussion->topic }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap max-w-[400px] break-words">{{ $discussion->target }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 rounded text-sm bg-blue-100 text-blue-800">
                                                {{ $discussion->risk_level }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 rounded text-sm bg-purple-100 text-purple-800">
                                                {{ $discussion->priority_level }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $discussion->previous_commitment }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $discussion->next_commitment }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $discussion->pic }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 rounded text-sm 
                                                {{ $discussion->status === 'Closed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $discussion->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $discussion->deadline->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $discussion->closed_at->format('d/m/Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="14" class="px-6 py-4 text-center text-gray-500">
                                            Tidak ada data selesai
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $closedDiscussions->appends(request()->except('closed_page'))->links() }}
                    </div>
                </div>

                <!-- Tambahkan section untuk tabel overdue -->
                <div id="overdue-content" class="tab-content hidden">
                    <div class="overflow-x-auto shadow-md rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-red-600">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">No SR</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">No WO</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Unit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Topik</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Sasaran</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Tingkat Resiko</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Tingkat Prioritas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Komitmen Sebelum</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Komitmen Selanjutnya</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">PIC</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Deadline</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Tanggal Selesai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($overdueDiscussions as $index => $discussion)
                                    <tr class="hover:bg-red-50">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $discussion->sr_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $discussion->wo_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $discussion->unit }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $discussion->topic }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $discussion->target }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 rounded text-sm bg-blue-100 text-blue-800">
                                                {{ $discussion->risk_level }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 rounded text-sm bg-purple-100 text-purple-800">
                                                {{ $discussion->priority_level }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $discussion->previous_commitment }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $discussion->next_commitment }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $discussion->pic }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 rounded text-sm 
                                                {{ $discussion->status === 'Closed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $discussion->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $discussion->deadline->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-red-600">
                                            {{ $discussion->overdue_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex items-center space-x-3">
                                                <!-- Edit -->
                                                <a href="{{ route('admin.other-discussions.edit', $discussion->original_id) }}"
                                                   class="text-blue-500 hover:text-blue-700">
                                                    <i class="fas fa-edit text-lg"></i>
                                                </a>
                                                
                                                <!-- Delete -->
                                                <button onclick="confirmDeleteOverdue({{ $discussion->id }})"
                                                        class="text-red-500 hover:text-red-700">
                                                    <i class="fas fa-trash text-lg"></i>
                                                </button>

                                                <!-- Status Closed -->
                                                {{-- <button onclick="updateOverdueStatus({{ $discussion->id }}, 'Closed')"
                                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-md text-sm flex items-center">
                                                    <i class="fas fa-check-circle mr-1.5"></i> Selesai
                                                </button> --}}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="14" class="px-6 py-4 text-center text-gray-500">
                                            Tidak ada data yang melewati deadline
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $overdueDiscussions->appends(request()->except('overdue_page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom scrollbar */
.overflow-x-auto::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #ddd;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #ccc;
}

/* Responsive table styles */
.overflow-x-auto {
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
}

@media (max-width: 640px) {
    .container {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    table {
        display: block;
        width: 100%;
    }
}

/* Shadow indicators for table scroll */
.overflow-x-auto::after,
.overflow-x-auto::before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 15px;
    z-index: 2;
    pointer-events: none;
}

.overflow-x-auto::before {
    left: 0;
    background: linear-gradient(to right, rgba(255,255,255,0.9), rgba(255,255,255,0));
}

.overflow-x-auto::after {
    right: 0;
    background: linear-gradient(to left, rgba(255,255,255,0.9), rgba(255,255,255,0));
}

.active-tab {
    border-bottom-color: #3B82F6;
    color: #2563EB;
}
</style>

<script src="{{ asset('js/toggle.js') }}"></script>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validasi tanggal
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');

        startDate.addEventListener('change', function() {
            endDate.min = this.value;
        });

        endDate.addEventListener('change', function() {
            startDate.max = this.value;
        });

        // Auto submit saat memilih unit
        document.getElementById('unit-filter').addEventListener('change', function() {
            this.form.submit();
        });

        // Debounce search
        let searchTimeout;
        document.getElementById('search').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });

        // Set active tab from URL parameter or default to 'active'
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'active';
        switchTab(activeTab);

        // Reset filter saat berpindah tab
        const tabButtons = document.querySelectorAll('.tab-btn');
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('unitTableFilter').value = '';
                document.getElementById('statusTableFilter').value = '';
            });
        });
    });
    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Data ini akan dihapus secara permanen',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/other-discussions/${id}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    function switchTab(tab) {
        // Remove active class from all tabs and content
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active-tab', 'border-blue-500', 'text-blue-600');
            btn.classList.add('text-gray-500');
        });
        
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Add active class to selected tab and show content
        const selectedTab = document.getElementById(`${tab}-tab`);
        const selectedContent = document.getElementById(`${tab}-content`);
        
        selectedTab.classList.add('active-tab', 'border-blue-500', 'text-blue-600');
        selectedTab.classList.remove('text-gray-500');
        selectedContent.classList.remove('hidden');
    }
    function updateStatus(id, status) {
        const statusText = status === 'Closed' ? 'selesai' : 'overdue';
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Status akan diubah menjadi ${statusText}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: status === 'Closed' ? '#10B981' : '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, ubah status!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/other-discussions/${id}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: status })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Status berhasil diperbarui',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'Terjadi kesalahan saat mengubah status',
                        icon: 'error'
                    });
                });
            }
        });
    }
    function checkAndUpdateOverdueStatus() {
        const rows = document.querySelectorAll('tr[data-deadline]');
        const now = new Date();
        
        rows.forEach(row => {
            const deadline = new Date(row.dataset.deadline);
            if (now > deadline && row.dataset.status === 'Open') {
                // Kirim request untuk update status ke overdue
                fetch(`/admin/other-discussions/${row.dataset.id}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: 'Overdue' })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Refresh halaman jika ada perubahan status
                        window.location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    }

    // Jalankan pengecekan setiap menit
    setInterval(checkAndUpdateOverdueStatus, 60000);

    // Jalankan pengecekan saat halaman dimuat
    document.addEventListener('DOMContentLoaded', checkAndUpdateOverdueStatus);

    // Fungsi update status untuk tombol Selesai
    function updateStatus(id, status) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Status akan diubah menjadi selesai',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, selesaikan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/other-discussions/${id}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: status })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Status berhasil diperbarui',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'Terjadi kesalahan saat mengubah status',
                        icon: 'error'
                    });
                });
            }
        });
    }

    // Fungsi untuk konfirmasi delete data overdue
    function confirmDeleteOverdue(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim request delete
                fetch(`/admin/overdue-discussions/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Data berhasil dihapus',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'Terjadi kesalahan saat menghapus data',
                        icon: 'error'
                    });
                });
            }
        });
    }

    // Fungsi untuk update status overdue
    function updateOverdueStatus(id, status) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Status akan diubah menjadi selesai',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, selesaikan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/overdue-discussions/${id}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: status })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Status berhasil diperbarui',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'Terjadi kesalahan saat mengubah status',
                        icon: 'error'
                    });
                });
            }
        });
    }

    // Fungsi filter untuk Unit
    function filterTableByUnit() {
        const unit = document.getElementById('unitTableFilter').value;
        const tables = ['active-content', 'closed-content', 'overdue-content'];
        let totalVisible = 0;

        tables.forEach(tableId => {
            const rows = document.querySelectorAll(`#${tableId} tbody tr`);
            let visibleCount = 0;

            rows.forEach(row => {
                const unitCell = row.querySelector('td:nth-child(4)'); // Sesuaikan dengan posisi kolom unit
                if (!unit || (unitCell && unitCell.textContent.trim() === unit)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            totalVisible += visibleCount;
        });

        // Update counter jika ada
        const counter = document.getElementById('visibleCounter');
        if (counter) {
            counter.textContent = totalVisible;
        }
    }

    // Fungsi filter untuk Status
    function filterTableByStatus() {
        const status = document.getElementById('statusTableFilter').value;
        const tables = ['active-content', 'closed-content', 'overdue-content'];
        let totalVisible = 0;

        tables.forEach(tableId => {
            const rows = document.querySelectorAll(`#${tableId} tbody tr`);
            let visibleCount = 0;

            rows.forEach(row => {
                const statusCell = row.querySelector('td:nth-child(12)'); // Sesuaikan dengan posisi kolom status
                if (!status || (statusCell && statusCell.textContent.trim().includes(status))) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            totalVisible += visibleCount;
        });

        // Update counter jika ada
        const counter = document.getElementById('visibleCounter');
        if (counter) {
            counter.textContent = totalVisible;
        }
    }
</script>
@push('scripts')
@endpush
@endsection 