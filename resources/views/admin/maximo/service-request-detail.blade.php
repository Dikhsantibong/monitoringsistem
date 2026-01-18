@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.sidebar')

    <div id="main-content" class="flex-1 overflow-auto">
        <header class="bg-white shadow-sm sticky top-0">
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
                    <button id="desktop-menu-toggle"
                        class="hidden md:block relative items-center justify-center rounded-md text-gray-400 hover:bg-[#009BB9] p-2 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800">Service Request Detail</h1>
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

        <main class="px-6 mt-4">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="mb-4">
                    <div class="text-sm text-gray-500">TICKETID</div>
                    <div class="text-lg font-semibold text-gray-800">{{ $sr['ticketid'] ?? '-' }}</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border rounded p-4">
                        <div class="text-sm text-gray-500 mb-2">Info</div>
                        <dl class="text-sm space-y-2">
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">Status</dt><dd class="font-medium text-gray-800">{{ $sr['status'] ?? '-' }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">Status Date</dt><dd class="font-medium text-gray-800">{{ $sr['statusdate'] ?? '-' }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">Reported By</dt><dd class="font-medium text-gray-800">{{ $sr['reportedby'] ?? '-' }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">Report Date</dt><dd class="font-medium text-gray-800">{{ $sr['reportdate'] ?? '-' }}</dd></div>
                        </dl>
                    </div>

                    <div class="border rounded p-4">
                        <div class="text-sm text-gray-500 mb-2">Asset & Lokasi</div>
                        <dl class="text-sm space-y-2">
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">Asset</dt><dd class="font-medium text-gray-800">{{ $sr['assetnum'] ?? '-' }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">Location</dt><dd class="font-medium text-gray-800">{{ $sr['location'] ?? '-' }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">Site</dt><dd class="font-medium text-gray-800">{{ $sr['siteid'] ?? '-' }}</dd></div>
                        </dl>
                    </div>
                </div>

                <div class="mt-4 border rounded p-4">
                    <div class="text-sm text-gray-500 mb-2">Description</div>
                    <div class="text-sm text-gray-800 whitespace-pre-wrap break-words">{{ $sr['description'] ?? '-' }}</div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

