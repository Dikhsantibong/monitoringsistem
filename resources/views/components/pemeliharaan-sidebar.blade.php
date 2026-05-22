<aside id="mobile-menu"
    class="fixed z-20 transform overflow-hidden transition-transform duration-300 md:relative md:translate-x-0 h-screen w-[280px] bg-transparent shadow-md text-slate-300 hidden md:block p-3">
    <div class="bg-slate-900 rounded-2xl border border-slate-800 h-full px-4 py-6 flex flex-col overflow-y-auto scrollbar-thin scrollbar-thumb-slate-700 scrollbar-track-transparent shadow-2xl">
        <!-- Logo section -->
        <div class="flex items-center justify-between mb-8">
            <img src="{{ asset('logo/navlogo.png') }}" alt="Logo Aplikasi Pemeliharaan" class="w-40">
            <button id="menu-toggle-close"
                class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#0A749B] hover:text-white focus:outline-none">
                <span class="sr-only">Open main menu</span>
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>


        @php
            $childUnits = \App\Helpers\PemeliharaanLocationHelper::getChildUnits();
        @endphp

        @if(!empty($childUnits))
        <!-- Unit Filter -->
        <div class="mb-6 px-1">
            <label for="unit-filter" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Filter Unit</label>
            <div class="relative">
                <select id="unit-filter" onchange="applyUnitFilter(this.value)" class="block w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 appearance-none outline-none transition-colors cursor-pointer">
                    <option value="">Semua Unit (Area Induk)</option>
                    @foreach($childUnits as $prefix => $name)
                        <option value="{{ $prefix }}" {{ request('unit_prefix') == $prefix ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                    <i class="fas fa-chevron-down text-[10px]"></i>
                </div>
            </div>
        </div>
        <script>
            function applyUnitFilter(val) {
                const url = new URL(window.location.href);
                if (val) {
                    url.searchParams.set('unit_prefix', val);
                } else {
                    url.searchParams.delete('unit_prefix');
                }
                // Reset page pagination if exists when filtering
                if (url.searchParams.has('page')) {
                    url.searchParams.delete('page');
                }
                window.location.href = url.toString();
            }
        </script>
        @endif

        <!-- Navigation -->
        <nav class="space-y-1.5 flex-grow">
            <a href="{{ route('pemeliharaan.dashboard') }}"
                class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('pemeliharaan.dashboard') ? 'bg-blue-600/10 text-blue-400 font-semibold ring-1 ring-blue-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800' }}">
                <i class="fas fa-tools w-5 h-5 flex items-center justify-center"></i>
                <span class="ml-3 text-[15px]">Dashboard</span>
            </a>
            <a href="{{ route('pemeliharaan.calendar') }}"
                class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('pemeliharaan.calendar') ? 'bg-blue-600/10 text-blue-400 font-semibold ring-1 ring-blue-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800' }}">
                <i class="fas fa-calendar-alt w-5 h-5 flex items-center justify-center"></i>
                <span class="ml-3 text-[15px]">Kalender</span>
            </a>

            <a href="{{ route('pemeliharaan.labor-saya') }}"
                class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('pemeliharaan.labor-saya') ? 'bg-blue-600/10 text-blue-400 font-semibold ring-1 ring-blue-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800' }}">
                <i class="fas fa-file-invoice w-5 h-5 flex items-center justify-center"></i>
                <span class="ml-3 text-[15px]">Work Order</span>
            </a>
            <a href="{{ route('pemeliharaan.wo-wmatl.index') }}"
                class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('pemeliharaan.wo-wmatl.*') ? 'bg-blue-600/10 text-blue-400 font-semibold ring-1 ring-blue-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800' }}">
                <i class="fas fa-boxes w-5 h-5 flex items-center justify-center"></i>
                <span class="ml-3 text-[15px]">WO Material (WMATL)</span>
            </a>
            <a href="{{ route('pemeliharaan.jobcard') }}"
            class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('pemeliharaan.jobcard') ? 'bg-blue-600/10 text-blue-400 font-semibold ring-1 ring-blue-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800' }}">
            <i class="fas fa-clipboard-list w-5 h-5 flex items-center justify-center"></i>
            <span class="ml-3 text-[15px]">Jobcard</span>
            </a>
            
            <div class="pt-4 pb-2">
                <p class="px-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Katalog & Master</p>
            </div>

            <a href="{{ route('pemeliharaan.katalog.index') }}"
                class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('pemeliharaan.katalog.*') ? 'bg-blue-600/10 text-blue-400 font-semibold ring-1 ring-blue-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800' }}">
                <i class="fas fa-book w-5 h-5 flex items-center justify-center"></i>
                <span class="ml-3 text-[15px]">Katalog</span>
            </a>
            <a href="{{ route('pemeliharaan.pengajuan-material.index') }}"
                class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('pemeliharaan.pengajuan-material.*') ? 'bg-blue-600/10 text-blue-400 font-semibold ring-1 ring-blue-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800' }}">
                <i class="fas fa-box-open w-5 h-5 flex items-center justify-center"></i>
                <span class="ml-3 text-[15px]">Pengajuan Material</span>
            </a>
            <a href="{{ route('pemeliharaan.master-labor') }}"
                class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('pemeliharaan.master-labor') ? 'bg-blue-600/10 text-blue-400 font-semibold ring-1 ring-blue-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800' }}">
                <i class="fas fa-users-cog w-5 h-5 flex items-center justify-center"></i>
                <span class="ml-3 text-[15px]">Team Pemeliharaan</span>
            </a> 
           
            <a href="{{ route('pemeliharaan.support') }}"
                class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 mt-4 {{ request()->routeIs('pemeliharaan.support') ? 'bg-blue-600/10 text-blue-400 font-semibold ring-1 ring-blue-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800' }}">
                <i class="fas fa-life-ring w-5 h-5 flex items-center justify-center"></i>
                <span class="ml-3 text-[15px]">Support</span>
            </a>
            
        </nav>

        <!-- Bottom Section: Logout -->
        <div class="mt-4 pt-4 border-t border-slate-800 flex-shrink-0">
            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf
                <button type="button" 
                    onclick="confirmLogout()"
                    class="flex items-center w-full px-3 py-2.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-400/10 transition-colors duration-200 text-[15px] font-medium">
                    <i class="fas fa-sign-out-alt w-5 h-5 flex items-center justify-center"></i>
                    <span class="ml-3">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>

<script>
function confirmLogout() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Konfirmasi Logout',
            text: "Apakah anda yakin untuk logout?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    } else {
        if (confirm('Apakah anda yakin untuk logout?')) {
            document.getElementById('logout-form').submit();
        }
    }
}
</script>