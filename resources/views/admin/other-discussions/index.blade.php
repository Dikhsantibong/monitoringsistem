@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
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
                // ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['name' => 'Pembahasan Lain-lain', 'url' => null]
            ]" />
        </div>

        <!-- Main Content -->
        <div class="container mx-auto px-2 sm:px-6 py-4 sm:py-8">
            <div class="bg-white rounded-lg shadow p-3 sm:p-6">
                <!-- Header Section - More compact with better spacing -->
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Pembahasan Lain-lain</h2>
                    
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Link Maximo - Simplified -->
                        <a href="http://maximo.plnnusantarapower.co.id/maximo/ui/?event=loadapp&value=wotrack&uisessionid=6851&_tt=mku67dchhvlb9t7lmqm05io6v" 
                           title="Link Maximo" 
                           target="_blank" 
                           class="inline-flex items-center px-3 py-1.5 border border-blue-500 rounded text-blue-500 hover:bg-blue-50">
                            <img src="{{ asset('logo/logo-maximo.png') }}" alt="Logo Maximo" class="h-4 mr-1.5">
                            <span class="text-sm">Maximo</span>
                        </a>
                        
                        <!-- Add Data Button - Simplified -->
                        <a href="{{ route('admin.other-discussions.create') }}" 
                           class="inline-flex items-center px-3 py-1.5 bg-blue-500 text-white rounded hover:bg-blue-600">
                            <i class="fas fa-plus text-sm mr-1.5"></i>
                            <span class="text-sm">Tambah Data</span>
                        </a>
                    </div>
                </div>

                <!-- Alert Messages - More compact -->
                @if(session('success') || session('warning') || session('error'))
                <div class="mb-4">
                    @if(session('success'))
                        <div class="flex items-center p-2 bg-green-100 border-l-4 border-green-500 text-green-700 text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="flex items-center p-2 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 text-sm">
                            {{ session('warning') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="flex items-center justify-between p-2 bg-red-100 border-l-4 border-red-500 text-red-700 text-sm">
                            <span>{{ session('error') }}</span>
                            <button class="text-red-500 hover:text-red-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif
                </div>
                @endif

                <!-- Filter Section - Reorganized and more compact -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <form action="{{ route('admin.other-discussions.index') }}" method="GET">
                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                            <!-- Search Box - Simplified -->
                            <div>
                                <label for="search" class="text-sm text-gray-600">Pencarian</label>
                                <div class="relative mt-1">
                                    <input type="text" 
                                           name="search" 
                                           id="search" 
                                           placeholder="Cari topik, PIC, unit..."
                                           value="{{ request('search') }}"
                                           class="w-full pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                    <i class="fas fa-search absolute left-2.5 top-2.5 text-gray-400 text-sm"></i>
                                </div>
                            </div>

                            <!-- Date Filters - Simplified -->
                            <div>
                                <label for="start_date" class="text-sm text-gray-600">Tanggal Mulai</label>
                                <input type="date" 
                                       id="start_date" 
                                       name="start_date" 
                                       value="{{ request('start_date') }}"
                                       class="mt-1 w-full px-3 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="end_date" class="text-sm text-gray-600">Tanggal Akhir</label>
                                <input type="date" 
                                       id="end_date" 
                                       name="end_date" 
                                       value="{{ request('end_date') }}"
                                       class="mt-1 w-full px-3 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                            </div>

                            <!-- Action Buttons - Consolidated -->
                            <div class="flex items-end gap-2">
                                <div class="flex-grow">
                                    <button type="submit" 
                                            class="w-full px-3 py-1.5 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">
                                        <i class="fas fa-search mr-1"></i> Filter
                                    </button>
                                </div>

                                <!-- Actions Dropdown -->
                                <div class="relative" x-data="{ open: false }">
                                    <button type="button" 
                                            @click="open = !open"
                                            class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    
                                    <div x-show="open" 
                                         @click.away="open = false"
                                         class="absolute right-0 mt-1 w-48 bg-white rounded shadow-lg z-50 border text-sm">
                                        <button type="button" 
                                                onclick="handlePrint()" 
                                                class="w-full px-4 py-2 text-left hover:bg-gray-50">
                                            <i class="fas fa-print mr-2 text-gray-600"></i>
                                            Print
                                        </button>
                                        <a href="{{ route('admin.other-discussions.export.xlsx', request()->query()) }}" 
                                           class="block px-4 py-2 hover:bg-gray-50">
                                            <i class="fas fa-file-excel mr-2 text-green-600"></i>
                                            Export Excel
                                        </a>
                                        <a href="{{ route('admin.other-discussions.export', array_merge(request()->all(), ['format' => 'pdf'])) }}" 
                                           class="block px-4 py-2 hover:bg-gray-50">
                                            <i class="fas fa-file-pdf mr-2 text-red-600"></i>
                                            Export PDF
                                        </a>
                                        <a href="{{ route('admin.other-discussions.index') }}" 
                                           class="block px-4 py-2 hover:bg-gray-50">
                                            <i class="fas fa-undo mr-2 text-gray-600"></i>
                                            Reset Filter
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tab Navigation (tambahkan setelah bagian filter) -->
                <div class="border-b border-gray-200 mb-6"><div class="lg:col-span-3">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
    
                </div>

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
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-[#0A749B]">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">No</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">No SR</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">No Pembahasan</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">
                                            <div class="flex flex-col gap-2">
                                                <span>Unit</span>
                                                <form id="unitFilterForm" action="{{ route('admin.other-discussions.index') }}" method="GET">
                                                    <input type="hidden" name="tab" value="{{ request('tab', 'active') }}">
                                                    
                                                    @foreach(request()->except(['unit', 'page', 'tab']) as $key => $value)
                                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                                    @endforeach
                                                    
                                                    <select id="unit" 
                                                            name="unit" 
                                                            onchange="this.form.submit()" 
                                                            class="w-full px-2 py-1 text-gray-700 bg-white border border-gray-300 rounded-md text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                        <option value="">Semua Unit</option>
                                                        <!-- Unit Kendari -->
                                                        <optgroup label="UP KENDARI">
                                                            @foreach($powerPlants->where('unit_source', 'mysql')->sortBy('name') as $plant)
                                                                <option value="{{ $plant->name }}" {{ request('unit') == $plant->name ? 'selected' : '' }}>
                                                                    {{ $plant->name }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                        
                                                        <!-- Unit Wua-Wua -->
                                                        <optgroup label="PLTD WUA-WUA">
                                                            @foreach($powerPlants->where('unit_source', 'mysql_wua_wua')->sortBy('name') as $plant)
                                                                <option value="{{ $plant->name }}" {{ request('unit') == $plant->name ? 'selected' : '' }}>
                                                                    {{ $plant->name }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                        
                                                        <!-- Unit Poasia -->
                                                        <optgroup label="PLTD POASIA">
                                                            @foreach($powerPlants->where('unit_source', 'mysql_poasia')->sortBy('name') as $plant)
                                                                <option value="{{ $plant->name }}" {{ request('unit') == $plant->name ? 'selected' : '' }}>
                                                                    {{ $plant->name }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                        
                                                        <!-- Unit Kolaka -->
                                                        <optgroup label="PLTD KOLAKA">
                                                            @foreach($powerPlants->where('unit_source', 'mysql_kolaka')->sortBy('name') as $plant)
                                                                <option value="{{ $plant->name }}" {{ request('unit') == $plant->name ? 'selected' : '' }}>
                                                                    {{ $plant->name }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                        
                                                        <!-- Unit Bau-Bau -->
                                                        <optgroup label="PLTD BAU-BAU">
                                                            @foreach($powerPlants->where('unit_source', 'mysql_bau_bau')->sortBy('name') as $plant)
                                                                <option value="{{ $plant->name }}" {{ request('unit') == $plant->name ? 'selected' : '' }}>
                                                                    {{ $plant->name }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    </select>
                                                </form>
                                            </div>
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase w-[300px]">Topic</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">
                                            <div class="flex flex-col gap-2">
                                                <span>Status</span>
                                                <form id="statusFilterForm" action="{{ route('admin.other-discussions.index') }}" method="GET">
                                                    @foreach(request()->except(['status', 'page']) as $key => $value)
                                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                                    @endforeach
                                                    
                                                    <select id="status-filter" 
                                                            name="status"
                                                            onchange="this.form.submit()"
                                                            class="w-full px-2 py-1 text-gray-700 bg-white border border-gray-300 rounded-md text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                        <option value="">Semua Status</option>
                                                        <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                                                        <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                                                    </select>
                                                </form>
                                            </div>
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">Dokumen</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($activeDiscussions as $index => $discussion)
                                    <tr class="hover:bg-gray-50">
                                        <!-- Row Utama -->
                                        <td class="px-4 py-3 text-sm">{{ $activeDiscussions->firstItem() + $index }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $discussion->sr_number }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            {{ $discussion->no_pembahasan }}
                                            @if($discussion->created_at->diffInHours(now()) < 24)
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    New
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">{{ $discussion->unit }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <div class="line-clamp-2">{{ $discussion->topic }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $discussion->status === 'Open' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                {{ $discussion->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($discussion->document_path)
                                                @php
                                                    $paths = json_decode($discussion->document_path) ?? [$discussion->document_path];
                                                    $descriptions = json_decode($discussion->document_description) ?? [$discussion->document_description];
                                                @endphp
                                                <div class="flex flex-col space-y-1">
                                                    @foreach($paths as $index => $path)
                                                        @php
                                                            $extension = pathinfo($path, PATHINFO_EXTENSION);
                                                            $iconClass = 'fa-file';
                                                            $iconColor = 'text-blue-500';
                                                            
                                                            if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])) {
                                                                $iconClass = 'fa-file-image';
                                                                $iconColor = 'text-green-500';
                                                            } elseif ($extension === 'pdf') {
                                                                $iconClass = 'fa-file-pdf';
                                                                $iconColor = 'text-red-500';
                                                            } elseif (in_array($extension, ['doc', 'docx'])) {
                                                                $iconClass = 'fa-file-word';
                                                                $iconColor = 'text-blue-500';
                                                            }
                                                        @endphp
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas {{ $iconClass }} {{ $iconColor }}"></i>
                                                            <a href="{{ asset('storage/' . $path) }}" 
                                                               target="_blank"
                                                               class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                                                                {{ Str::limit($descriptions[$index] ?? basename($path), 30) }}
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-sm">Tidak ada dokumen</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <div class="flex items-center gap-2">
                                                <!-- Tombol Expand -->
                                                <button onclick="toggleDetails('discussion-{{ $discussion->id }}')" 
                                                        class="text-blue-600 hover:text-blue-800 focus:outline-none"
                                                        aria-expanded="false"
                                                        aria-controls="details-{{ $discussion->id }}"
                                                        title="Detail">
                                                    <i class="fas fa-chevron-down transition-transform duration-200" 
                                                       id="icon-discussion-{{ $discussion->id }}"></i>
                                                </button>

                                                <!-- Tombol Edit -->
                                                <a href="{{ route('admin.other-discussions.edit', $discussion->id) }}" 
                                                   class="text-yellow-600 hover:text-yellow-800 focus:outline-none"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <!-- Tombol Download -->
                                                <div class="relative" x-data="{ isOpen: false }">
                                                    <button @click="isOpen = !isOpen"
                                                            class="text-green-600 hover:text-green-800 focus:outline-none"
                                                            title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                    
                                                    <!-- Dropdown Menu -->
                                                    <div x-show="isOpen"
                                                         @click.away="isOpen = false"
                                                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-50 border border-gray-200">
                                                        <div class="py-1">
                                                            <!-- Print -->
                                                            <a href="{{ route('admin.other-discussions.print.single', $discussion->id) }}" 
                                                               target="_blank"
                                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                <i class="fas fa-print mr-2"></i>
                                                                Print
                                                            </a>
                                                            
                                                            <!-- Export PDF -->
                                                            <a href="{{ route('admin.other-discussions.export.single', ['id' => $discussion->id, 'format' => 'pdf']) }}" 
                                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                <i class="fas fa-file-pdf mr-2 text-red-600"></i>
                                                                Export PDF
                                                            </a>
                                                            
                                                            <!-- Export Excel -->
                                                            <a href="{{ route('admin.other-discussions.export.single', ['id' => $discussion->id, 'format' => 'xlsx']) }}" 
                                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                <i class="fas fa-file-excel mr-2 text-green-600"></i>
                                                                Export Excel
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Tombol Delete -->
                                                <button onclick="confirmDelete('{{ $discussion->id }}')"
                                                        class="text-red-600 hover:text-red-800 focus:outline-none"
                                                        title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Row Detail (Hidden by default) -->
                                    <tr id="discussion-{{ $discussion->id }}" class="hidden bg-gray-50">
                                        <td colspan="8" class="px-4 py-3">
                                            <div class="grid grid-cols-2 gap-4 p-4">
                                                <!-- Kolom Kiri -->
                                                <div>
                                                    <div class="mb-4">
                                                        <h4 class="font-semibold text-gray-700">Target</h4>
                                                        <p class="text-sm text-gray-600">{{ $discussion->target }}</p>
                                                    </div>
                                                    <div class="mb-4">
                                                        <h4 class="font-semibold text-gray-700">PIC</h4>
                                                        <p class="text-sm text-gray-600">{{ $discussion->pic }}</p>
                                                    </div>
                                                    <div class="mb-4">
                                                        <h4 class="font-semibold text-gray-700">Risk Level</h4>
                                                        <p class="text-sm text-gray-600">{{ $discussion->risk_level }}</p>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-semibold text-gray-700">Priority Level</h4>
                                                        <p class="text-sm text-gray-600">{{ $discussion->priority_level }}</p>
                                                    </div>
                                                </div>
                                                <!-- Kolom Kanan - Commitments -->
                                                <div>
                                                    <h4 class="font-semibold text-gray-700 mb-2">Commitments</h4>
                                                    @foreach($discussion->commitments as $commitment)
                                                    <div class="mb-3 p-3 bg-white rounded shadow-sm">
                                                        <p class="text-sm text-gray-600">{{ $commitment->description }}</p>
                                                        <div class="mt-2 flex justify-between items-center">
                                                            <span class="text-xs text-gray-500">
                                                                Deadline: {{ $commitment->deadline ? \Carbon\Carbon::parse($commitment->deadline)->format('d/m/Y') : '-' }}
                                                            </span>
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                                {{ $commitment->status === 'Open' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                                                {{ $commitment->status }}
                                                            </span>
                                                        </div>
                                                        <p class="text-xs text-gray-500 mt-1">PIC: {{ $commitment->pic }}</p>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="mt-4">
                                                <h4 class="font-semibold text-gray-700 mb-2">Dokumen Pendukung</h4>
                                                @if($discussion->document_path)
                                                    <div class="bg-white p-3 rounded-lg shadow-sm border">
                                                        @php
                                                            $paths = json_decode($discussion->document_path) ?? [$discussion->document_path];
                                                            $names = json_decode($discussion->document_description) ?? [$discussion->document_description];
                                                        @endphp
                                                        @foreach($paths as $index => $path)
                                                            @php
                                                                $extension = pathinfo($path, PATHINFO_EXTENSION);
                                                                $iconClass = 'fa-file';
                                                                $iconColor = 'text-blue-500';
                                                                
                                                                if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])) {
                                                                    $iconClass = 'fa-file-image';
                                                                    $iconColor = 'text-green-500';
                                                                } elseif (strtolower($extension) === 'pdf') {
                                                                    $iconClass = 'fa-file-pdf';
                                                                    $iconColor = 'text-red-500';
                                                                } elseif (in_array($extension, ['doc', 'docx'])) {
                                                                    $iconClass = 'fa-file-word';
                                                                    $iconColor = 'text-blue-500';
                                                                }
                                                            @endphp
                                                            <div class="flex items-center justify-between mb-2 last:mb-0">
                                                                <div class="flex items-center gap-2">
                                                                    <i class="fas {{ $iconClass }} {{ $iconColor }}"></i>
                                                                    <div>
                                                                        <p class="text-sm font-medium text-gray-700 truncate">
                                                                            {{ $names[$index] ?? basename($path) }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <a href="{{ asset('storage/' . $path) }}" 
                                                                   target="_blank"
                                                                   class="text-blue-600 hover:text-blue-800">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="text-gray-500 text-sm">Tidak ada dokumen pendukung</p>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                            Tidak ada data aktif
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    Menampilkan {{ $activeDiscussions->firstItem() ?? 0 }} 
                                    sampai {{ $activeDiscussions->lastItem() ?? 0 }} 
                                    dari {{ $activeDiscussions->total() }} data
                                </div>
                                <div>
                                    {{ $activeDiscussions->appends(request()->except('active_page'))->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Target Overdue Tab -->
                <div id="target-overdue-content" class="tab-content hidden">
                    <div class="overflow-x-auto shadow-md rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-[#0A749B]">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">No SR</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">No Pembahasan</th>
                                    <!-- Filter Unit -->
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">
                                        <div class="flex flex-col gap-2">
                                            <span>Unit</span>
                                            <form id="unitFilterForm" action="{{ route('admin.other-discussions.index') }}" method="GET">
                                                <input type="hidden" name="tab" value="{{ request('tab', 'target-overdue') }}">
                                                
                                                @foreach(request()->except(['unit', 'page', 'tab']) as $key => $value)
                                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                                @endforeach
                                                
                                                <select id="unit" name="unit" onchange="this.form.submit()" class="w-full px-2 py-1 text-gray-700 bg-white border border-gray-300 rounded-md text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                    <option value="">Semua Unit</option>
                                                    <!-- Unit Kendari -->
                                                    <optgroup label="UP KENDARI">
                                                        @foreach($powerPlants->where('unit_source', 'mysql')->sortBy('name') as $plant)
                                                            <option value="{{ $plant->name }}" {{ request('unit') == $plant->name ? 'selected' : '' }}>
                                                                {{ $plant->name }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                    
                                                    <!-- Unit Wua-Wua -->
                                                    <optgroup label="PLTD WUA-WUA">
                                                        @foreach($powerPlants->where('unit_source', 'mysql_wua_wua')->sortBy('name') as $plant)
                                                            <option value="{{ $plant->name }}" {{ request('unit') == $plant->name ? 'selected' : '' }}>
                                                                {{ $plant->name }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                    
                                                    <!-- Unit Poasia -->
                                                    <optgroup label="PLTD POASIA">
                                                        @foreach($powerPlants->where('unit_source', 'mysql_poasia')->sortBy('name') as $plant)
                                                            <option value="{{ $plant->name }}" {{ request('unit') == $plant->name ? 'selected' : '' }}>
                                                                {{ $plant->name }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                    
                                                    <!-- Unit Kolaka -->
                                                    <optgroup label="PLTD KOLAKA">
                                                        @foreach($powerPlants->where('unit_source', 'mysql_kolaka')->sortBy('name') as $plant)
                                                            <option value="{{ $plant->name }}" {{ request('unit') == $plant->name ? 'selected' : '' }}>
                                                                {{ $plant->name }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                    
                                                    <!-- Unit Bau-Bau -->
                                                    <optgroup label="PLTD BAU-BAU">
                                                        @foreach($powerPlants->where('unit_source', 'mysql_bau_bau')->sortBy('name') as $plant)
                                                            <option value="{{ $plant->name }}" {{ request('unit') == $plant->name ? 'selected' : '' }}>
                                                                {{ $plant->name }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                </select>
                                            </form>
                                        </div>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase w-[300px]">Topic</th>
                                    <!-- Filter Status -->
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">
                                        <div class="flex flex-col gap-2">
                                            <span>Status</span>
                                            <form id="statusFilterForm" action="{{ route('admin.other-discussions.index') }}" method="GET">
                                                @foreach(request()->except(['status', 'page']) as $key => $value)
                                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                                @endforeach
                                                <select id="status-filter" name="status" onchange="this.form.submit()" class="w-full px-2 py-1 text-gray-700 bg-white border border-gray-300 rounded-md text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                    <option value="">Semua Status</option>
                                                    <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                                                    <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                                                </select>
                                            </form>
                                        </div>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">Dokumen</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($targetOverdueDiscussions as $index => $discussion)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm">{{ $targetOverdueDiscussions->firstItem() + $index }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $discussion->sr_number }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ $discussion->no_pembahasan }}
                                        @if($discussion->created_at->diffInHours(now()) < 24)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">New</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ $discussion->unit }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="line-clamp-2">{{ $discussion->topic }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $discussion->status === 'Open' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $discussion->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($discussion->document_path)
                                            @php
                                                $paths = json_decode($discussion->document_path) ?? [$discussion->document_path];
                                                $descriptions = json_decode($discussion->document_description) ?? [$discussion->document_description];
                                            @endphp
                                            <div class="flex flex-col space-y-1">
                                                @foreach($paths as $index => $path)
                                                    @php
                                                        $extension = pathinfo($path, PATHINFO_EXTENSION);
                                                        $iconClass = 'fa-file';
                                                        $iconColor = 'text-blue-500';
                                                        
                                                        if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])) {
                                                            $iconClass = 'fa-file-image';
                                                            $iconColor = 'text-green-500';
                                                        } elseif ($extension === 'pdf') {
                                                            $iconClass = 'fa-file-pdf';
                                                            $iconColor = 'text-red-500';
                                                        } elseif (in_array($extension, ['doc', 'docx'])) {
                                                            $iconClass = 'fa-file-word';
                                                            $iconColor = 'text-blue-500';
                                                        }
                                                    @endphp
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas {{ $iconClass }} {{ $iconColor }}"></i>
                                                        <a href="{{ asset('storage/' . $path) }}" 
                                                           target="_blank"
                                                           class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                                                            {{ Str::limit($descriptions[$index] ?? basename($path), 30) }}
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-sm">Tidak ada dokumen</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="flex items-center gap-2">
                                            <button onclick="toggleDetails('overdue-{{ $discussion->id }}')" 
                                                    class="text-blue-600 hover:text-blue-800 focus:outline-none"
                                                    aria-expanded="false"
                                                    aria-controls="details-overdue-{{ $discussion->id }}"
                                                    title="Detail">
                                                <i class="fas fa-chevron-down transition-transform duration-200" 
                                                   id="icon-overdue-{{ $discussion->id }}"></i>
                                            </button>
                                            <a href="{{ route('admin.other-discussions.edit', $discussion->id) }}" 
                                               class="text-yellow-600 hover:text-yellow-800 focus:outline-none"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="confirmDelete('{{ $discussion->id }}')"
                                                    class="text-red-600 hover:text-red-800 focus:outline-none"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Detail Row -->
                                <tr id="overdue-{{ $discussion->id }}" class="hidden bg-gray-50">
                                    <td colspan="7" class="px-4 py-3">
                                        <div class="grid grid-cols-2 gap-4 p-4">
                                            <!-- Kolom Kiri -->
                                            <div>
                                                <div class="mb-4">
                                                    <h4 class="font-semibold text-gray-700">Target</h4>
                                                    <p class="text-sm text-gray-600">{{ $discussion->target }}</p>
                                                </div>
                                                <div class="mb-4">
                                                    <h4 class="font-semibold text-gray-700">PIC</h4>
                                                    <p class="text-sm text-gray-600">{{ $discussion->pic }}</p>
                                                </div>
                                                <div class="mb-4">
                                                    <h4 class="font-semibold text-gray-700">Risk Level</h4>
                                                    <p class="text-sm text-gray-600">{{ $discussion->risk_level }}</p>
                                                </div>
                                                <div>
                                                    <h4 class="font-semibold text-gray-700">Priority Level</h4>
                                                    <p class="text-sm text-gray-600">{{ $discussion->priority_level }}</p>
                                                </div>
                                            </div>
                                            <!-- Kolom Kanan - Commitments -->
                                            <div>
                                                <h4 class="font-semibold text-gray-700 mb-2">Commitments</h4>
                                                @foreach($discussion->commitments as $commitment)
                                                    <div class="mb-3 p-3 bg-white rounded shadow-sm">
                                                        <p class="text-sm text-gray-600">{{ $commitment->description }}</p>
                                                        <div class="mt-2 flex justify-between items-center">
                                                            <span class="text-xs text-gray-500">
                                                                Deadline: {{ $commitment->deadline ? \Carbon\Carbon::parse($commitment->deadline)->format('d/m/Y') : '-' }}
                                                            </span>
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $commitment->status === 'Open' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                                {{ $commitment->status }}
                                                            </span>
                                                        </div>
                                                        <p class="text-xs text-gray-500 mt-1">PIC: {{ $commitment->pic }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <h4 class="font-semibold text-gray-700 mb-2">Dokumen Pendukung</h4>
                                            @if($discussion->document_path)
                                                <div class="bg-white p-3 rounded-lg shadow-sm border">
                                                    @php
                                                        $paths = json_decode($discussion->document_path) ?? [$discussion->document_path];
                                                        $names = json_decode($discussion->document_description) ?? [$discussion->document_description];
                                                    @endphp
                                                    @foreach($paths as $index => $path)
                                                        @php
                                                            $extension = pathinfo($path, PATHINFO_EXTENSION);
                                                            $iconClass = 'fa-file';
                                                            $iconColor = 'text-blue-500';
                                                            
                                                            if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])) {
                                                                $iconClass = 'fa-file-image';
                                                                $iconColor = 'text-green-500';
                                                            } elseif (strtolower($extension) === 'pdf') {
                                                                $iconClass = 'fa-file-pdf';
                                                                $iconColor = 'text-red-500';
                                                            } elseif (in_array($extension, ['doc', 'docx'])) {
                                                                $iconClass = 'fa-file-word';
                                                                $iconColor = 'text-blue-500';
                                                            }
                                                        @endphp
                                                        <div class="flex items-center justify-between mb-2 last:mb-0">
                                                            <div class="flex items-center gap-2">
                                                                <i class="fas {{ $iconClass }} {{ $iconColor }}"></i>
                                                                <div>
                                                                    <p class="text-sm font-medium text-gray-700 truncate">
                                                                        {{ $names[$index] ?? basename($path) }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <a href="{{ asset('storage/' . $path) }}" 
                                                               target="_blank"
                                                               class="text-blue-600 hover:text-blue-800">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-gray-500 text-sm">Tidak ada dokumen pendukung</p>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-3 text-center text-gray-500">
                                        Tidak ada data yang melewati deadline
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Menampilkan {{ $targetOverdueDiscussions->firstItem() ?? 0 }} 
                                sampai {{ $targetOverdueDiscussions->lastItem() ?? 0 }} 
                                dari {{ $targetOverdueDiscussions->total() }} data
                            </div>
                            <div>
                                {{ $targetOverdueDiscussions->appends(request()->except('target_page'))->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Commitment Overdue Tab -->
                <div id="commitment-overdue-content" class="tab-content hidden">
                    <div class="overflow-x-auto shadow-md rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-[#0A749B]">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">No SR</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">No Pembahasan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">
                                        <div class="flex flex-col gap-2">
                                            <span>Unit</span>
                                            <form id="unitFilterForm" action="{{ route('admin.other-discussions.index') }}" method="GET">
                                                <!-- Filter unit yang sama seperti tab aktif -->
                                                <input type="hidden" name="tab" value="{{ request('tab', 'commitment-overdue') }}">
                                                
                                                @foreach(request()->except(['unit', 'page', 'tab']) as $key => $value)
                                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                                @endforeach
                                                
                                                <select id="unit" name="unit" onchange="this.form.submit()" class="w-full px-2 py-1 text-gray-700 bg-white border border-gray-300 rounded-md text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                    <option value="">Semua Unit</option>
                                                    <!-- Unit Kendari -->
                                                    <optgroup label="UP KENDARI">
                                                        @foreach($powerPlants->where('unit_source', 'mysql')->sortBy('name') as $plant)
                                                            <option value="{{ $plant->name }}" {{ request('unit') == $plant->name ? 'selected' : '' }}>
                                                                {{ $plant->name }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                    
                                                    <!-- Unit Wua-Wua -->
                                                    <optgroup label="PLTD WUA-WUA">
                                                        @foreach($powerPlants->where('unit_source', 'mysql_wua_wua')->sortBy('name') as $plant)
                                                            <option value="{{ $plant->name }}" {{ request('unit') == $plant->name ? 'selected' : '' }}>
                                                                {{ $plant->name }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                    
                                                    <!-- Unit Poasia -->
                                                    <optgroup label="PLTD POASIA">
                                                        @foreach($powerPlants->where('unit_source', 'mysql_poasia')->sortBy('name') as $plant)
                                                            <option value="{{ $plant->name }}" {{ request('unit') == $plant->name ? 'selected' : '' }}>
                                                                {{ $plant->name }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                    
                                                    <!-- Unit Kolaka -->
                                                    <optgroup label="PLTD KOLAKA">
                                                        @foreach($powerPlants->where('unit_source', 'mysql_kolaka')->sortBy('name') as $plant)
                                                            <option value="{{ $plant->name }}" {{ request('unit') == $plant->name ? 'selected' : '' }}>
                                                                {{ $plant->name }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                    
                                                    <!-- Unit Bau-Bau -->
                                                    <optgroup label="PLTD BAU-BAU">
                                                        @foreach($powerPlants->where('unit_source', 'mysql_bau_bau')->sortBy('name') as $plant)
                                                            <option value="{{ $plant->name }}" {{ request('unit') == $plant->name ? 'selected' : '' }}>
                                                                {{ $plant->name }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                </select>
                                            </form>
                                        </div>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase w-[300px]">Topic</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">
                                        <div class="flex flex-col gap-2">
                                            <span>Status</span>
                                            <form id="statusFilterForm" action="{{ route('admin.other-discussions.index') }}" method="GET">
                                                <!-- Filter status yang sama seperti tab aktif -->
                                                @foreach(request()->except(['status', 'page']) as $key => $value)
                                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                                @endforeach
                                                <select id="status-filter" name="status" onchange="this.form.submit()" class="w-full px-2 py-1 text-gray-700 bg-white border border-gray-300 rounded-md text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                    <option value="">Semua Status</option>
                                                    <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                                                    <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                                                </select>
                                            </form>
                                        </div>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">Dokumen</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($commitmentOverdueDiscussions as $index => $discussion)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm">{{ $commitmentOverdueDiscussions->firstItem() + $index }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $discussion->sr_number }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ $discussion->no_pembahasan }}
                                        @if($discussion->created_at->diffInHours(now()) < 24)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">New</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ $discussion->unit }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="line-clamp-2">{{ $discussion->topic }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $discussion->status === 'Open' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $discussion->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <!-- Dokumen section yang sama seperti tab aktif -->
                                        @if($discussion->document_path)
                                            @php
                                                $paths = json_decode($discussion->document_path) ?? [$discussion->document_path];
                                                $descriptions = json_decode($discussion->document_description) ?? [$discussion->document_description];
                                            @endphp
                                            <div class="flex flex-col space-y-1">
                                                @foreach($paths as $index => $path)
                                                    @php
                                                        $extension = pathinfo($path, PATHINFO_EXTENSION);
                                                        $iconClass = 'fa-file';
                                                        $iconColor = 'text-blue-500';
                                                        
                                                        if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])) {
                                                            $iconClass = 'fa-file-image';
                                                            $iconColor = 'text-green-500';
                                                        } elseif ($extension === 'pdf') {
                                                            $iconClass = 'fa-file-pdf';
                                                            $iconColor = 'text-red-500';
                                                        } elseif (in_array($extension, ['doc', 'docx'])) {
                                                            $iconClass = 'fa-file-word';
                                                            $iconColor = 'text-blue-500';
                                                        }
                                                    @endphp
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas {{ $iconClass }} {{ $iconColor }}"></i>
                                                        <a href="{{ asset('storage/' . $path) }}" 
                                                           target="_blank"
                                                           class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                                                            {{ Str::limit($descriptions[$index] ?? basename($path), 30) }}
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-sm">Tidak ada dokumen</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="flex items-center gap-2">
                                            <!-- Tombol Expand -->
                                            <button onclick="toggleDetails('commitment-{{ $discussion->id }}')" 
                                                    class="text-blue-600 hover:text-blue-800 focus:outline-none"
                                                    title="Detail">
                                                <i class="fas fa-chevron-down transition-transform duration-200" 
                                                   id="icon-commitment-{{ $discussion->id }}"></i>
                                            </button>

                                            <!-- Tombol Edit -->
                                            <a href="{{ route('admin.other-discussions.edit', $discussion->id) }}" 
                                               class="text-yellow-600 hover:text-yellow-800 focus:outline-none"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <!-- Tombol Delete -->
                                            <button onclick="confirmDelete('{{ $discussion->id }}')"
                                                    class="text-red-600 hover:text-red-800 focus:outline-none"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Detail Row -->
                                <tr id="commitment-{{ $discussion->id }}" class="hidden bg-gray-50">
                                    <td colspan="8" class="px-4 py-3">
                                        <div class="grid grid-cols-2 gap-4 p-4">
                                            <!-- Kolom Kiri -->
                                            <div>
                                                <div class="mb-4">
                                                    <h4 class="font-semibold text-gray-700">Target</h4>
                                                    <p class="text-sm text-gray-600">{{ $discussion->target }}</p>
                                                </div>
                                                <div class="mb-4">
                                                    <h4 class="font-semibold text-gray-700">PIC</h4>
                                                    <p class="text-sm text-gray-600">{{ $discussion->pic }}</p>
                                                </div>
                                                <div class="mb-4">
                                                    <h4 class="font-semibold text-gray-700">Risk Level</h4>
                                                    <p class="text-sm text-gray-600">{{ $discussion->risk_level }}</p>
                                                </div>
                                                <div>
                                                    <h4 class="font-semibold text-gray-700">Priority Level</h4>
                                                    <p class="text-sm text-gray-600">{{ $discussion->priority_level }}</p>
                                                </div>
                                            </div>
                                            <!-- Kolom Kanan - Commitments -->
                                            <div>
                                                <h4 class="font-semibold text-gray-700 mb-2">Commitments</h4>
                                                @foreach($discussion->commitments as $commitment)
                                                    <div class="mb-3 p-3 bg-white rounded shadow-sm">
                                                        <p class="text-sm text-gray-600">{{ $commitment->description }}</p>
                                                        <div class="mt-2 flex justify-between items-center">
                                                            <span class="text-xs text-gray-500">
                                                                Deadline: {{ $commitment->deadline ? \Carbon\Carbon::parse($commitment->deadline)->format('d/m/Y') : '-' }}
                                                            </span>
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                                {{ $commitment->status === 'Open' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                                                {{ $commitment->status }}
                                                            </span>
                                                        </div>
                                                        <p class="text-xs text-gray-500 mt-1">PIC: {{ $commitment->pic }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-3 text-center text-gray-500">
                                        Tidak ada diskusi dengan komitmen yang melewati deadline
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Menampilkan {{ $commitmentOverdueDiscussions->firstItem() ?? 0 }} 
                                sampai {{ $commitmentOverdueDiscussions->lastItem() ?? 0 }} 
                                dari {{ $commitmentOverdueDiscussions->total() }} data
                            </div>
                            <div>
                                {{ $commitmentOverdueDiscussions->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Closed Discussions Tab Content -->
                <div id="closed-content" class="tab-content hidden">
                    <div class="overflow-x-auto shadow-md rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-[#0A749B]">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">No SR</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">No Pembahasan</th>
                                    <!-- Filter Unit -->
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">
                                        <div class="flex flex-col gap-2">
                                            <span>Unit</span>
                                            <form id="unitFilterForm" action="{{ route('admin.other-discussions.index') }}" method="GET">
                                                <input type="hidden" name="tab" value="{{ request('tab', 'closed') }}">
                                                
                                                @foreach(request()->except(['unit', 'page', 'tab']) as $key => $value)
                                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                                @endforeach
                                                
                                                <select id="unit" name="unit" onchange="this.form.submit()" class="w-full px-2 py-1 text-gray-700 bg-white border border-gray-300 rounded-md text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                    <option value="">Semua Unit</option>
                                                    <!-- Unit Kendari -->
                                                    <optgroup label="UP KENDARI">
                                                        @foreach($powerPlants->where('unit_source', 'mysql')->sortBy('name') as $plant)
                                                            <option value="{{ $plant->name }}" {{ request('unit') == $plant->name ? 'selected' : '' }}>
                                                                {{ $plant->name }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                    
                                                    <!-- Unit Wua-Wua -->
                                                    <optgroup label="PLTD WUA-WUA">
                                                        @foreach($powerPlants->where('unit_source', 'mysql_wua_wua')->sortBy('name') as $plant)
                                                            <option value="{{ $plant->name }}" {{ request('unit') == $plant->name ? 'selected' : '' }}>
                                                                {{ $plant->name }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                    
                                                    <!-- Unit Poasia -->
                                                    <optgroup label="PLTD POASIA">
                                                        @foreach($powerPlants->where('unit_source', 'mysql_poasia')->sortBy('name') as $plant)
                                                            <option value="{{ $plant->name }}" {{ request('unit') == $plant->name ? 'selected' : '' }}>
                                                                {{ $plant->name }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                    
                                                    <!-- Unit Kolaka -->
                                                    <optgroup label="PLTD KOLAKA">
                                                        @foreach($powerPlants->where('unit_source', 'mysql_kolaka')->sortBy('name') as $plant)
                                                            <option value="{{ $plant->name }}" {{ request('unit') == $plant->name ? 'selected' : '' }}>
                                                                {{ $plant->name }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                    
                                                    <!-- Unit Bau-Bau -->
                                                    <optgroup label="PLTD BAU-BAU">
                                                        @foreach($powerPlants->where('unit_source', 'mysql_bau_bau')->sortBy('name') as $plant)
                                                            <option value="{{ $plant->name }}" {{ request('unit') == $plant->name ? 'selected' : '' }}>
                                                                {{ $plant->name }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                </select>
                                            </form>
                                        </div>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase w-[300px]">Topic</th>
                                    <!-- Filter Status -->
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">
                                        <div class="flex flex-col gap-2">
                                            <span>Status</span>
                                            <form id="statusFilterForm" action="{{ route('admin.other-discussions.index') }}" method="GET">
                                                @foreach(request()->except(['status', 'page']) as $key => $value)
                                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                                @endforeach
                                                <select id="status-filter" name="status" onchange="this.form.submit()" class="w-full px-2 py-1 text-gray-700 bg-white border border-gray-300 rounded-md text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                    <option value="">Semua Status</option>
                                                    <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                                                    <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                                                </select>
                                            </form>
                                        </div>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">Dokumen</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($closedDiscussions as $index => $discussion)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm">{{ $closedDiscussions->firstItem() + $index }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $discussion->sr_number }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ $discussion->no_pembahasan }}
                                        @if($discussion->created_at->diffInHours(now()) < 24)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">New</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ $discussion->unit }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="line-clamp-2">{{ $discussion->topic }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $discussion->status }}
                                        </span>
                                    </td>
                              
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($discussion->document_path)
                                            @php
                                                $paths = json_decode($discussion->document_path) ?? [$discussion->document_path];
                                                $descriptions = json_decode($discussion->document_description) ?? [$discussion->document_description];
                                            @endphp
                                            <div class="flex flex-col space-y-1">
                                                @foreach($paths as $index => $path)
                                                    @php
                                                        $extension = pathinfo($path, PATHINFO_EXTENSION);
                                                        $iconClass = 'fa-file';
                                                        $iconColor = 'text-blue-500';
                                                        
                                                        if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])) {
                                                            $iconClass = 'fa-file-image';
                                                            $iconColor = 'text-green-500';
                                                        } elseif ($extension === 'pdf') {
                                                            $iconClass = 'fa-file-pdf';
                                                            $iconColor = 'text-red-500';
                                                        } elseif (in_array($extension, ['doc', 'docx'])) {
                                                            $iconClass = 'fa-file-word';
                                                            $iconColor = 'text-blue-500';
                                                        }
                                                    @endphp
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas {{ $iconClass }} {{ $iconColor }}"></i>
                                                        <a href="{{ asset('storage/' . $path) }}" 
                                                           target="_blank"
                                                           class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                                                            {{ Str::limit($descriptions[$index] ?? basename($path), 30) }}
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-sm">Tidak ada dokumen</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="flex items-center gap-2">
                                            <button onclick="toggleDetails('closed-{{ $discussion->id }}')" 
                                                    class="text-blue-600 hover:text-blue-800 focus:outline-none"
                                                    aria-expanded="false"
                                                    aria-controls="details-closed-{{ $discussion->id }}"
                                                    title="Detail">
                                                <i class="fas fa-chevron-down transition-transform duration-200" 
                                                   id="icon-closed-{{ $discussion->id }}"></i>
                                            </button>
                                            
                                            <button onclick="confirmDelete('{{ $discussion->id }}')"
                                                    class="text-red-600 hover:text-red-800 focus:outline-none"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Detail Row -->
                                <tr id="closed-{{ $discussion->id }}" class="hidden bg-gray-50">
                                    <td colspan="8" class="px-4 py-3">
                                        <div class="grid grid-cols-2 gap-4 p-4">
                                            <!-- Kolom Kiri -->
                                            <div>
                                                <div class="mb-4">
                                                    <h4 class="font-semibold text-gray-700">Target</h4>
                                                    <p class="text-sm text-gray-600">{{ $discussion->target }}</p>
                                                </div>
                                                <div class="mb-4">
                                                    <h4 class="font-semibold text-gray-700">PIC</h4>
                                                    <p class="text-sm text-gray-600">{{ $discussion->pic }}</p>
                                                </div>
                                                <div class="mb-4">
                                                    <h4 class="font-semibold text-gray-700">Risk Level</h4>
                                                    <p class="text-sm text-gray-600">{{ $discussion->risk_level }}</p>
                                                </div>
                                                <div>
                                                    <h4 class="font-semibold text-gray-700">Priority Level</h4>
                                                    <p class="text-sm text-gray-600">{{ $discussion->priority_level }}</p>
                                                </div>
                                            </div>
                                            <!-- Kolom Kanan - Commitments -->
                                            <div>
                                                <h4 class="font-semibold text-gray-700 mb-2">Commitments</h4>
                                                @foreach($discussion->commitments as $commitment)
                                                    <div class="mb-3 p-3 bg-white rounded shadow-sm">
                                                        <p class="text-sm text-gray-600">{{ $commitment->description }}</p>
                                                        <div class="mt-2 flex justify-between items-center">
                                                            <span class="text-xs text-gray-500">
                                                                Deadline: {{ $commitment->deadline ? \Carbon\Carbon::parse($commitment->deadline)->format('d/m/Y') : '-' }}
                                                            </span>
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                                {{ $commitment->status }}
                                                            </span>
                                                        </div>
                                                        <p class="text-xs text-gray-500 mt-1">PIC: {{ $commitment->pic }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <h4 class="font-semibold text-gray-700 mb-2">Dokumen Pendukung</h4>
                                            @if($discussion->document_path)
                                                <div class="bg-white p-3 rounded-lg shadow-sm border">
                                                    @php
                                                        $paths = json_decode($discussion->document_path) ?? [$discussion->document_path];
                                                        $names = json_decode($discussion->document_description) ?? [$discussion->document_description];
                                                    @endphp
                                                    @foreach($paths as $index => $path)
                                                        @php
                                                            $extension = pathinfo($path, PATHINFO_EXTENSION);
                                                            $iconClass = 'fa-file';
                                                            $iconColor = 'text-blue-500';
                                                            
                                                            if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])) {
                                                                $iconClass = 'fa-file-image';
                                                                $iconColor = 'text-green-500';
                                                            } elseif (strtolower($extension) === 'pdf') {
                                                                $iconClass = 'fa-file-pdf';
                                                                $iconColor = 'text-red-500';
                                                            } elseif (in_array($extension, ['doc', 'docx'])) {
                                                                $iconClass = 'fa-file-word';
                                                                $iconColor = 'text-blue-500';
                                                            }
                                                        @endphp
                                                        <div class="flex items-center justify-between mb-2 last:mb-0">
                                                            <div class="flex items-center gap-2">
                                                                <i class="fas {{ $iconClass }} {{ $iconColor }}"></i>
                                                                <div>
                                                                    <p class="text-sm font-medium text-gray-700 truncate">
                                                                        {{ $names[$index] ?? basename($path) }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <a href="{{ asset('storage/' . $path) }}" 
                                                               target="_blank"
                                                               class="text-blue-600 hover:text-blue-800">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-gray-500 text-sm">Tidak ada dokumen pendukung</p>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-3 text-center text-gray-500">
                                        Tidak ada data yang selesai
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Menampilkan {{ $closedDiscussions->firstItem() ?? 0 }} 
                                sampai {{ $closedDiscussions->lastItem() ?? 0 }} 
                                dari {{ $closedDiscussions->total() }} data
                            </div>
                            <div>
                                {{ $closedDiscussions->appends(request()->except('closed_page'))->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
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

/* Animasi untuk fade in */
.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Animasi untuk rotate icon */
.rotate-180 {
    transform: rotate(180deg);
}

/* Transisi smooth untuk icon */
.fa-chevron-down {
    transition: transform 0.2s ease-in-out;
}

/* Tambahan style untuk action buttons */
.action-buttons {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.action-buttons button,
.action-buttons a {
    padding: 0.25rem;
    border-radius: 0.25rem;
    transition: all 0.2s;
}

.action-buttons button:hover,
.action-buttons a:hover {
    transform: scale(1.1);
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
            if (endDate.value && this.value > endDate.value) {
                alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir');
                this.value = '';
            }
        });

        endDate.addEventListener('change', function() {
            if (startDate.value && this.value < startDate.value) {
                alert('Tanggal akhir tidak boleh lebih kecil dari tanggal mulai');
                this.value = '';
            }
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

        // Auto submit form when filter changes
        const unitSourceSelect = document.getElementById('unit-source');
        if (unitSourceSelect) {
            unitSourceSelect.addEventListener('change', function() {
                this.form.submit();
            });
        }

        const statusSelect = document.getElementById('status');
        if (statusSelect) {
            statusSelect.addEventListener('change', function() {
                this.form.submit();
            });
        }

        const searchInput = document.getElementById('search');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.form.submit();
                }, 500);
            });
        }
    });
    function confirmDelete(id) {
        Swal.fire({
            title: 'Verifikasi Password',
            input: 'password',
            inputLabel: 'Masukkan password Anda untuk melanjutkan',
            inputPlaceholder: 'Password',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
            showLoaderOnConfirm: true,
            inputValidator: (value) => {
                if (!value) {
                    return 'Password harus diisi!';
                }
            },
            preConfirm: async (password) => {
                try {
                    // Gunakan route yang di-generate Laravel
                    const verifyUrl = "{{ route('admin.verify-password') }}";
                    const deleteUrl = "{{ route('admin.other-discussions.index') }}";
                    
                    console.log('Debug URLs:', {
                        verifyUrl,
                        deleteUrl,
                        currentOrigin: window.location.origin
                    });

                    // Verifikasi password
                    const verifyResponse = await fetch(verifyUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ password })
                    });

                    if (!verifyResponse.ok) {
                        const errorText = await verifyResponse.text();
                        console.error('Verify Response Error:', {
                            status: verifyResponse.status,
                            statusText: verifyResponse.statusText,
                            headers: Object.fromEntries(verifyResponse.headers.entries()),
                            body: errorText
                        });
                        throw new Error(`Verifikasi password gagal (${verifyResponse.status})`);
                    }

                    const verifyData = await verifyResponse.json();
                    
                    if (!verifyData.success) {
                        throw new Error(verifyData.message || 'Password tidak valid');
                    }

                    // Proses delete dengan URL yang benar
                    const deleteResponse = await fetch(`${deleteUrl}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (!deleteResponse.ok) {
                        const errorText = await deleteResponse.text();
                        console.error('Delete Response Error:', {
                            status: deleteResponse.status,
                            statusText: deleteResponse.statusText,
                            headers: Object.fromEntries(deleteResponse.headers.entries()),
                            body: errorText
                        });
                        throw new Error(`Gagal menghapus data (${deleteResponse.status})`);
                    }

                    const deleteData = await deleteResponse.json();
                    
                    if (!deleteData.success) {
                        throw new Error(deleteData.message || 'Gagal menghapus data');
                    }

                    return deleteData;

                } catch (error) {
                    console.error('Full Error Details:', {
                        message: error.message,
                        stack: error.stack,
                        timestamp: new Date().toISOString()
                    });
                    
                    Swal.showValidationMessage(`
                        Error: ${error.message}
                        ${error.stack ? `\nStack: ${error.stack}` : ''}
                    `);
                }
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed && result.value?.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.value.message || 'Data berhasil dihapus',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.reload();
                });
            }
        });
    }

    // Debug helper
    document.addEventListener('DOMContentLoaded', () => {
        console.log('Available Routes:', {
            verifyPassword: "{{ route('admin.verify-password') }}",
            discussionIndex: "{{ route('admin.other-discussions.index') }}",
            baseUrl: "{{ url('/') }}",
            currentUrl: window.location.href
        });
    });

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

    function toggleDetails(id) {
        const detailRow = document.getElementById(id);
        const icon = document.getElementById(`icon-${id}`);
        
        // Toggle visibility dengan animasi
        if (detailRow.classList.contains('hidden')) {
            detailRow.classList.remove('hidden');
            detailRow.classList.add('animate-fade-in');
            icon.classList.add('rotate-180');
        } else {
            detailRow.classList.add('hidden');
            detailRow.classList.remove('animate-fade-in');
            icon.classList.remove('rotate-180');
        }
    }

    function handlePrint() {
        // Dapatkan semua parameter filter
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const search = document.getElementById('search').value;
        const unit = document.getElementById('unit')?.value;
        
        // Buat URL dengan parameter filter
        let printUrl = "{{ route('admin.other-discussions.print') }}";
        const params = new URLSearchParams();
        
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (search) params.append('search', search);
        if (unit) params.append('unit', unit);
        
        // Tambahkan parameter ke URL
        if (params.toString()) {
            printUrl += '?' + params.toString();
        }
        
        // Buka tab baru dengan URL print
        const printWindow = window.open(printUrl, '_blank');
        
        // Fokus ke tab baru (opsional, tergantung browser)
        if (printWindow) {
            printWindow.focus();
        }
    }
</script>
@push('scripts')
@endpush
@endsection 