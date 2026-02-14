@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    @include('components.pemeliharaan-sidebar')

    <div id="main-content" class="flex-1 overflow-auto">
        <!-- Header -->
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
					<h1 class="text-xl font-semibold text-gray-800">Support</h1>
				</div>
				<div class="flex items-center gap-x-4 relative">
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

        <!-- Main -->
        <main class="px-6 pt-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Jobcard (Generated)</h1>
                    <div class="text-sm text-gray-500">Total:
                        <b>{{ number_format($workOrders->total()) }}</b> dokumen
                    </div>
                </div>
                <form method="GET" action="{{ route('pemeliharaan.jobcard') }}"
                    class="flex items-center gap-2 w-full md:w-auto">
                    <div class="flex-1 md:w-72 relative">
                        <input type="text" name="q" value="{{ $q ?? '' }}"
                            placeholder="Cari WO (id, deskripsi, type, priority)"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <button type="submit"
                        class="bg-blue-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-blue-700 transition">Cari</button>
                    @if(!empty($q))
                        <a href="{{ route('pemeliharaan.jobcard') }}"
                            class="px-3 py-2 rounded-lg border text-sm hover:bg-gray-50 transition">Reset</a>
                    @endif
                </form>
            </div>

            <div class="bg-white rounded shadow p-4 overflow-x-auto">
                @if($workOrders->isEmpty())
                    <div class="text-gray-500 text-sm">Tidak ada dokumen jobcard yang sudah di-generate.</div>
                @else
                    <table class="min-w-full table-fixed divide-y divide-gray-200 border whitespace-nowrap">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">WO ID</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Deskripsi</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Location</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workOrders as $wo)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 border border-gray-200 text-center">{{ $loop->iteration + ($workOrders->currentPage() - 1) * $workOrders->perPage() }}</td>
                                    <td class="px-4 py-2 border border-gray-200">{{ $wo->wonum }}</td>
                                    <td class="px-4 py-2 border border-gray-200 max-w-[200px] overflow-hidden text-ellipsis whitespace-nowrap">{{ $wo->description }}</td>
                                    <td class="px-4 py-2 border border-gray-200 text-center">
                                        <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded text-xs font-medium border border-blue-100">
                                            {{ $wo->location }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 border border-gray-200 text-center">
                                        <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs">{{ $wo->status }}</span>
                                    </td>
                                    <td class="px-4 py-2 border border-gray-200 text-center">
                                        <a href="{{ asset('storage/' . $wo->jobcard_path) }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs hover:bg-blue-700 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                                            </svg>
                                            Unduh
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4 flex justify-end">
                        {{ $workOrders->links() }}
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
        var dropdown = document.getElementById('dropdown');
        var btn = document.getElementById('dropdownToggle');
        if (dropdown && !dropdown.classList.contains('hidden') && !btn.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>
@endsection
