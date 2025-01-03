@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    <!-- Sidebar -->
    @include('components.sidebar')

    <!-- Main Content -->
    <div id="main-content" class="flex-1 main-content">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="flex justify-between items-center px-6 py-3">
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
                <h1 class="text-xl font-semibold text-gray-800">Dashboard Admin</h1>
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
        <div class="flex items-center pt-2">
            <x-admin-breadcrumb :breadcrumbs="[
                ['name' => 'Laporan SR/WO', 'url' => route('admin.laporan.sr_wo')],
                ['name' => 'Closed', 'url' => null]
            ]" />
        </div>

    <div class="flex-1 main-content px-4 py-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Daftar SR/WO Closed</h2>
                <div class="flex gap-4">
                    <!-- Tombol Kembali -->
                   
                    <!-- Tombol Download PDF -->
                    <a href="{{ route('admin.laporan.sr_wo.closed.download') }}" 
                       class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 flex items-center">
                        <i class="fas fa-download mr-2"></i>Download PDF
                    </a>
                    
                    <!-- Tombol Print -->
                    <button onclick="window.open('{{ route('admin.laporan.sr_wo.closed.print') }}', '_blank')"
                            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 flex items-center">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 border">
                        @foreach(App\Models\ServiceRequest::where('status', 'Closed')->get() as $index => $report)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap border">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap border">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    SR
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap border">{{ $report->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap border">{{ Carbon\Carbon::parse($report->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 border">{{ $report->description }}</td>
                            <td class="px-6 py-4 whitespace-nowrap border">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $report->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach

                        @foreach(App\Models\WorkOrder::where('status', 'Closed')->get() as $index => $report)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap border">{{ App\Models\ServiceRequest::where('status', 'Closed')->count() + $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap border">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    WO
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap border">{{ $report->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap border">{{ Carbon\Carbon::parse($report->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 border">{{ $report->description }}</td>
                            <td class="px-6 py-4 whitespace-nowrap border">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $report->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 