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
                            <select id="unitTableFilter" name="unit" onchange="filterTableByUnit()" 
                                    class="block w-full border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">Semua</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit }}" {{ request('unit') == $unit ? 'selected' : '' }}>
                                        {{ $unit }}
                                    </option>
                                @endforeach
                            </select>
                        </div> --}}

                        <!-- Filter Status -->
                        {{-- <div>
                            <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="statusTableFilter" name="status" onchange="filterTableByStatus()" 
                                    class="block w-full border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">Semua</option>
                                <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                                <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                                <option value="Overdue" {{ request('status') == 'Overdue' ? 'selected' : '' }}>Overdue</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-end">
                            <button type="submit" class="btn bg-blue-500 text-white hover:bg-blue-600 rounded-lg px-4 py-2">
                                <i class="fas fa-filter mr-2"></i> Filter
                            </button>
                        </div> --}}
                    </form>
                </div>

                <!-- Tab Navigation (tambahkan setelah bagian filter) -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button id="active-tab" 
                                onclick="switchTab('active')"
                                class="tab-btn border-b-2 py-4 px-1 text-sm font-medium">
                            Data Aktif
                            <span class="ml-2 px-2 py-0.5 text-xs bg-blue-100 text-blue-600 rounded-full">
                                {{ $activeDiscussions->total() }}
                            </span>
                        </button>

                        <button id="target-overdue-tab" 
                                onclick="switchTab('target-overdue')"
                                class="tab-btn border-b-2 py-4 px-1 text-sm font-medium">
                            Melewati Deadline Sasaran
                            <span class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-600 rounded-full">
                                {{ $targetOverdueDiscussions->total() }}
                            </span>
                        </button>

                        <button id="commitment-overdue-tab" 
                                onclick="switchTab('commitment-overdue')"
                                class="tab-btn border-b-2 py-4 px-1 text-sm font-medium">
                            Melewati Deadline Komitmen
                            <span class="ml-2 px-2 py-0.5 text-xs bg-yellow-100 text-yellow-600 rounded-full">
                                {{ $commitmentOverdueDiscussions->total() }}
                            </span>
                        </button>

                        <button id="closed-tab" 
                                onclick="switchTab('closed')"
                                class="tab-btn border-b-2 py-4 px-1 text-sm font-medium">
                            Data Selesai
                            <span class="ml-2 px-2 py-0.5 text-xs bg-green-100 text-green-600 rounded-full">
                                {{ $closedDiscussions->total() }}
                            </span>
                        </button>
                    </nav>
                </div>

                <!-- Tab Contents -->
                <!-- Active Discussions Tab -->
                <div id="active-content" class="tab-content">
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">PIC Sasaran</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Tingkat Resiko</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Tingkat Prioritas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Komitmen & Deadline</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">PIC Komitmen</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Status</th>
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
                                        <td class="px-6 py-4 whitespace-nowrap max-w-[200px] truncate border border-gray-200">{{ $discussion->topic }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            <div class="mb-1">{{ $discussion->target }}</div>
                                            <div class="text-sm text-gray-500">
                                                Deadline: {{ $discussion->target_deadline ? \Carbon\Carbon::parse($discussion->target_deadline)->format('d/m/Y') : '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            @if($discussion->department_id && $discussion->section_id)
                                                {{ $discussion->pic }}
                                            @else
                                                -
                                            @endif
                                        </td>
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
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            @if($discussion->commitments && $discussion->commitments->count() > 0)
                                                @foreach($discussion->commitments as $commitment)
                                                    <div class="mb-2 p-2 border rounded">
                                                        <div class="text-sm">{{ $commitment->description }}</div>
                                                        <div class="text-xs text-gray-500 flex items-center justify-between mt-1">
                                                            <span>Deadline: {{ \Carbon\Carbon::parse($commitment->deadline)->format('d/m/Y') }}</span>
                                                            <span class="px-2 py-1 rounded-full text-xs
                                                                @if(strtoupper($commitment->status) === 'OPEN') 
                                                                    bg-red-100 text-red-800 border border-red-200
                                                                @elseif(strtoupper($commitment->status) === 'CLOSED')
                                                                    bg-green-100 text-green-800 border border-green-200
                                                                @endif">
                                                                {{ ucfirst(strtolower($commitment->status)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-gray-500">Tidak ada komitmen</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            @if($discussion->commitments && $discussion->commitments->count() > 0)
                                                @foreach($discussion->commitments as $commitment)
                                                    <div class="mb-2 p-2 border rounded">
                                                        @if($commitment->pic)
                                                            <div class="text-sm">{{ $commitment->pic }}</div>
                                                        @else
                                                            <span class="text-gray-500">-</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            <span class="px-2 py-1 text-sm rounded
                                                @if($discussion->status == 'Open') bg-blue-100 text-blue-800
                                                @elseif($discussion->status == 'Closed') bg-green-100 text-green-800
                                                @elseif($discussion->status == 'Overdue') bg-red-100 text-red-800
                                                @endif">
                                                {{ $discussion->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            {{ $discussion->deadline ? \Carbon\Carbon::parse($discussion->deadline)->format('d/m/Y') : '-' }}
                                        </td>   
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex items-center space-x-3">
                                                <!-- Edit -->
                                                <a href="{{ route('admin.other-discussions.edit', $discussion->id) }}" 
                                                   onclick="editDiscussion({{ $discussion->id }}); return false;"
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

                <!-- Target Overdue Tab -->
                <div id="target-overdue-content" class="tab-content hidden">
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">PIC Sasaran</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Risk Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Priority Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Komitmen & Deadline</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">PIC Komitmen</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">PIC Pembahasan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Deadline</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($targetOverdueDiscussions as $index => $discussion)
                                    <tr class="hover:bg-red-50">
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $discussion->sr_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $discussion->wo_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $discussion->unit }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap max-w-[200px] truncate border border-gray-200">{{ $discussion->topic }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap max-w-[200px] truncate border border-gray-200">{{ $discussion->target }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            @if($discussion->department_id && $discussion->section_id)
                                                {{ $discussion->pic }}
                                            @else
                                                -
                                            @endif
                                        </td>
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
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            @if($discussion->commitments && $discussion->commitments->count() > 0)
                                                @foreach($discussion->commitments as $commitment)
                                                    <div class="mb-2 p-2 border rounded">
                                                        <div class="text-sm">{{ $commitment->description }}</div>
                                                        <div class="text-xs text-gray-500 flex items-center justify-between">
                                                            <span>Deadline: {{ \Carbon\Carbon::parse($commitment->deadline)->format('d/m/Y') }}</span>
                                                            <span class="px-2 py-1 rounded-full text-xs
                                                                {{ $commitment->status === 'Open' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                                                {{ $commitment->status }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-gray-500">Tidak ada komitmen</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            @if($discussion->commitments && $discussion->commitments->count() > 0)
                                                @foreach($discussion->commitments as $commitment)
                                                    <div class="mb-2 p-2 border rounded">
                                                        @if($commitment->pic)
                                                            <div class="text-sm">{{ $commitment->pic }}</div>
                                                        @else
                                                            <span class="text-gray-500">-</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            {{ $discussion->pic }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            <span class="px-2 py-1 text-sm rounded
                                                @if($discussion->status == 'Open') bg-blue-100 text-blue-800
                                                @elseif($discussion->status == 'Closed') bg-green-100 text-green-800
                                                @elseif($discussion->status == 'Overdue') bg-red-100 text-red-800
                                                @endif">
                                                {{ $discussion->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            {{ $discussion->deadline ? \Carbon\Carbon::parse($discussion->deadline)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex items-center space-x-3">
                                                <a href="#" onclick="editDiscussion({{ $discussion->id }})" 
                                                   class="text-blue-500 hover:text-blue-700">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="confirmDelete({{ $discussion->id }})"
                                                        class="text-red-500 hover:text-red-700">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <button onclick="updateStatus({{ $discussion->id }}, 'Closed')"
                                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md text-sm">
                                                    Selesai
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13" class="px-6 py-4 text-center text-gray-500">
                                            Tidak ada diskusi yang melewati deadline sasaran
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $targetOverdueDiscussions->links() }}
                    </div>
                </div>

                <!-- Commitment Overdue Tab -->
                <div id="commitment-overdue-content" class="tab-content hidden">
                    <div class="overflow-x-auto shadow-md rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-yellow-600">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">No SR</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">No WO</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Unit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Topik</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Sasaran</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">PIC Sasaran</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Tingkat Resiko</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Tingkat Prioritas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Komitmen & Deadline</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">PIC Komitmen</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Deadline</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($commitmentOverdueDiscussions as $index => $discussion)
                                    <tr class="hover:bg-yellow-50">
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $discussion->sr_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $discussion->wo_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $discussion->unit }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap max-w-[200px] truncate border border-gray-200">{{ $discussion->topic }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            <div class="mb-1">{{ $discussion->target }}</div>
                                            <div class="text-sm text-gray-500">
                                                Deadline: {{ $discussion->target_deadline ? \Carbon\Carbon::parse($discussion->target_deadline)->format('d/m/Y') : '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            @if($discussion->department_id && $discussion->section_id)
                                                {{ $discussion->pic }}
                                            @else
                                                -
                                            @endif
                                        </td>
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
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            @if($discussion->commitments && $discussion->commitments->count() > 0)
                                                @foreach($discussion->commitments as $commitment)
                                                    <div class="mb-2 p-2 border rounded">
                                                        <div class="text-sm">{{ $commitment->description }}</div>
                                                        <div class="text-xs text-gray-500 flex items-center justify-between">
                                                            <span>Deadline: {{ \Carbon\Carbon::parse($commitment->deadline)->format('d/m/Y') }}</span>
                                                            <span class="px-2 py-1 rounded-full text-xs
                                                                {{ $commitment->status === 'Open' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                                                {{ $commitment->status }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-gray-500">Tidak ada komitmen</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            @if($discussion->commitments && $discussion->commitments->count() > 0)
                                                @foreach($discussion->commitments as $commitment)
                                                    <div class="mb-2 p-2 border rounded">
                                                        @if($commitment->pic)
                                                            <div class="text-sm">{{ $commitment->pic }}</div>
                                                        @else
                                                            <span class="text-gray-500">-</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            <span class="px-2 py-1 text-sm rounded
                                                @if($discussion->status == 'Open') bg-blue-100 text-blue-800
                                                @elseif($discussion->status == 'Closed') bg-green-100 text-green-800
                                                @elseif($discussion->status == 'Overdue') bg-red-100 text-red-800
                                                @endif">
                                                {{ $discussion->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            {{ $discussion->deadline ? \Carbon\Carbon::parse($discussion->deadline)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex items-center space-x-3">
                                                <a href="{{ route('admin.other-discussions.edit', $discussion->id) }}" 
                                                   onclick="editDiscussion({{ $discussion->id }}); return false;"
                                                   class="text-blue-500 hover:text-blue-700">
                                                    <i class="fas fa-edit text-lg"></i>
                                                </a>
                                                
                                                <button onclick="confirmDelete({{ $discussion->id }})"
                                                        class="text-red-500 hover:text-red-700">
                                                    <i class="fas fa-trash text-lg"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13" class="px-6 py-4 text-center text-gray-500">
                                            Tidak ada diskusi dengan komitmen yang melewati deadline
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $commitmentOverdueDiscussions->links() }}
                    </div>
                </div>

                <!-- Tabel Data Selesai -->
                <div id="closed-content" class="tab-content hidden">
                    <div class="overflow-x-auto shadow-md rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-green-600">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">No SR</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">No WO</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Unit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Topik</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Sasaran</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">PIC Sasaran</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Risk Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Tingkat Prioritas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Komitmen</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">PIC</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Deadline</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Tanggal Selesai</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($closedDiscussions as $index => $discussion)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $discussion->sr_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $discussion->wo_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $discussion->unit }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap max-w-[200px] truncate border border-gray-200">{{ $discussion->topic }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            <div class="mb-1">{{ $discussion->target }}</div>
                                            <div class="text-sm text-gray-500">
                                                Deadline: {{ $discussion->target_deadline ? \Carbon\Carbon::parse($discussion->target_deadline)->format('d/m/Y') : '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            @if($discussion->department_id && $discussion->section_id)
                                                {{ $discussion->pic }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            <span class="px-2 py-1 rounded text-sm bg-blue-100 text-blue-800">
                                                {{ $discussion->risk_level }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            <span class="px-2 py-1 rounded text-sm bg-purple-100 text-purple-800">
                                                {{ $discussion->priority_level }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            @if($discussion->commitments && $discussion->commitments->count() > 0)
                                                @foreach($discussion->commitments as $commitment)
                                                    <div class="mb-2 p-2 border rounded">
                                                        <div>{{ $commitment->description }}</div>
                                                        <div class="text-sm text-gray-500 flex items-center justify-between">
                                                            <span>Deadline: {{ \Carbon\Carbon::parse($commitment->deadline)->format('d/m/Y') }}</span>
                                                            <span class="px-2 py-1 rounded-full text-xs
                                                                {{ $commitment->status === 'Open' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                                                {{ $commitment->status }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-gray-500">Tidak ada komitmen</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">{{ $discussion->pic }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            <span class="px-2 py-1 rounded text-sm 
                                                {{ $discussion->status === 'Closed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $discussion->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            {{ $discussion->target_deadline ? \Carbon\Carbon::parse($discussion->target_deadline)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            {{ $discussion->closed_at ? \Carbon\Carbon::parse($discussion->closed_at)->format('d/m/Y') : '-' }}
                                        </td>
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

/* Tambahkan style untuk memastikan warna background tetap terlihat */
.status-select option {
    background-color: white !important;
    color: black !important;
}

/* Styling untuk status badge */
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
}

.status-badge.open {
    background-color: #FEE2E2;
    color: #991B1B;
    border: 1px solid #FECACA;
}

.status-badge.closed {
    background-color: #D1FAE5;
    color: #065F46;
    border: 1px solid #A7F3D0;
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
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Buat form untuk delete request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ url("/admin/other-discussions") }}/' + id;
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = '{{ csrf_token() }}';
                
                form.appendChild(methodInput);
                form.appendChild(tokenInput);
                document.body.appendChild(form);

                // Tampilkan loading
                Swal.fire({
                    title: 'Menghapus data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        form.submit();
                    }
                });
            }
        });
    }
    function switchTab(tabName) {
        // Sembunyikan semua konten tab
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Hapus kelas aktif dari semua tab
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-blue-500', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Tampilkan konten tab yang dipilih
        document.getElementById(`${tabName}-content`).classList.remove('hidden');
        
        // Aktifkan tab yang dipilih
        const selectedTab = document.getElementById(`${tabName}-tab`);
        selectedTab.classList.remove('border-transparent', 'text-gray-500');
        selectedTab.classList.add('border-blue-500', 'text-blue-600');
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
        console.log('Checking overdue status...');
        
        fetch('{{ route('admin.overdue-discussions.check') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Check result:', data);
            if (data.success) {
                if (data.count > 0) {
                    // Tampilkan notifikasi dan refresh halaman
                    Swal.fire({
                        title: 'Status Updated',
                        text: `${data.count} diskusi telah dipindahkan ke Overdue`,
                        icon: 'info',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                }
            } else {
                console.error('Error:', data.message);
            }
        })
        .catch(error => {
            console.error('Error checking overdue status:', error);
        });
    }

    // Jalankan pengecekan setiap 30 detik (untuk testing)
    setInterval(checkAndUpdateOverdueStatus, 30000);

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

    // Fungsi untuk menghapus diskusi overdue (terpisah)
    function confirmDeleteOverdue(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
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
                form.action = '{{ url("/admin/overdue-discussions") }}/' + id;
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = '{{ csrf_token() }}';
                
                form.appendChild(methodInput);
                form.appendChild(tokenInput);
                document.body.appendChild(form);

                Swal.fire({
                    title: 'Menghapus data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        form.submit();
                    }
                });
            }
        });
    }

    // Fungsi edit yang terpisah
    function editDiscussion(id) {
        // Tampilkan loading
        Swal.fire({
            title: 'Mohon tunggu...',
            text: 'Membuka form edit...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
                window.location.href = "{{ url('/admin/other-discussions') }}/" + id + "/edit";
            }
        });
    }

    // Tambahkan ini untuk handling pesan sukses setelah update
    document.addEventListener('DOMContentLoaded', function() {
        // Cek jika ada parameter success di URL
        const urlParams = new URLSearchParams(window.location.search);
        const successMessage = urlParams.get('success');
        
        if (successMessage) {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Data berhasil diperbarui',
                icon: 'success',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                // Hapus parameter success dari URL
                const newUrl = window.location.pathname;
                window.history.replaceState({}, document.title, newUrl);
            });
        }
    });

    // Set tab aktif saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        switchTab('active');
    });

    function updateStatus(selectElement) {
        const discussionId = selectElement.dataset.id;
        const newStatus = selectElement.value;

        fetch(`{{ route('admin.other-discussions.update-status') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                discussion_id: discussionId,
                status: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Status Berhasil Diperbarui',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    if (newStatus === 'Closed') {
                        // Reload halaman untuk memperbarui tampilan
                        window.location.reload();
                    }
                });
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: error.message
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const statusSelects = document.querySelectorAll('.status-select');
        
        statusSelects.forEach(select => {
            // Set warna awal
            updateStatusStyle(select);
            
            // Update warna saat status berubah
            select.addEventListener('change', function() {
                updateStatusStyle(this);
            });
        });
    });

    function updateStatusStyle(select) {
        // Hapus semua class warna yang ada
        select.classList.remove(
            'bg-red-100', 'text-red-800', 'border-red-200',
            'bg-green-100', 'text-green-800', 'border-green-200'
        );
        
        // Tambahkan class sesuai status yang dipilih
        if (select.value === 'Open') {
            select.classList.add('bg-red-100', 'text-red-800', 'border-red-200');
        } else if (select.value === 'Closed') {
            select.classList.add('bg-green-100', 'text-green-800', 'border-green-200');
        }
    }
</script>
@push('scripts')
@endpush
@endsection 