<aside id="mobile-menu"
    class="fixed z-20 transform overflow-hidden transition-transform duration-300 md:relative md:translate-x-0 h-screen w-[280px] bg-transparent shadow-md text-white hidden md:block p-3">
    <div class="bg-[#0A749B] rounded-2xl h-full px-4 py-6 flex flex-col overflow-y-auto scrollbar-thin scrollbar-thumb-[#e5e5e5]/40 scrollbar-track-[#0A749B]/20">
        <!-- Logo section -->
        <div class="flex items-center justify-between mb-8">
            <img src="{{ asset('logo/navlogo.png') }}" alt="Logo Aplikasi Pemeliharaan" class="w-40">
            <button id="menu-toggle-close"
                class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#0A749B] hover:text-white focus:outline-none">
                <span class="sr-only">Open main menu</span>
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <!-- Navigation -->
        <nav class="space-y-2 flex-grow">
            <a href="{{ route('pemeliharaan.dashboard') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('pemeliharaan.dashboard') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-tools w-6 h-6"></i>
                <span class="ml-3 text-base">Dashboard Pemeliharaan</span>
            </a>
            <a href="{{ route('pemeliharaan.calendar') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('pemeliharaan.calendar') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-calendar-alt w-6 h-6"></i>
                <span class="ml-3 text-base">Kalender Pemeliharaan</span>
            </a>

            <a href="{{ route('pemeliharaan.labor-saya') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('pemeliharaan.labor-saya') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-user w-6 h-6"></i>
                <span class="ml-3 text-base">Work Order</span>
            </a>
            <a href="{{ route('pemeliharaan.wo-wmatl.index') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('pemeliharaan.wo-wmatl.*') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-boxes w-6 h-6"></i>
                <span class="ml-3 text-base">WO Material (WMATL)</span>
            </a>
            <a href="{{ route('pemeliharaan.jobcard') }}"
            class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('pemeliharaan.jobcard') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
            <i class="fas fa-file-alt w-6 h-6"></i>
            <span class="ml-3 text-base">Jobcard</span>
        </a>
        <a href="{{ route('pemeliharaan.katalog.index') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('pemeliharaan.katalog.*') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-file-signature w-6 h-6"></i>
                <span class="ml-3 text-base">Pendaftaran Katalog</span>
            </a>
            <a href="{{ route('pemeliharaan.pengajuan-material.index') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('pemeliharaan.pengajuan-material.*') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-file-upload w-6 h-6"></i>
                <span class="ml-3 text-base">Pengajuan Material</span>
            </a>
            
           
            <a href="{{ route('pemeliharaan.master-labor') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('pemeliharaan.master-labor') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-database w-6 h-6"></i>
                <span class="ml-3 text-base">Team Pemeliharaan</span>
            </a> 
           
           
            <a href="{{ route('pemeliharaan.support') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('pemeliharaan.support') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-headset w-6 h-6"></i>
                <span class="ml-3 text-base">Support</span>
            </a>
            
        </nav>

        <!-- Bottom Section: Logout -->
        <div class="mt-4 pt-4 border-t border-white/10 flex-shrink-0">
            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf
                <button type="button" 
                    onclick="confirmLogout()"
                    class="flex items-center w-full px-3 py-2.5 rounded-lg text-white bg-red-400 hover:bg-red-700 transition-colors duration-200 text-sm font-bold">
                    <i class="fas fa-sign-out-alt w-6 h-6"></i>
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