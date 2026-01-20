<aside id="mobile-menu"
    class="fixed z-20 transform overflow-hidden transition-transform duration-300 md:relative md:translate-x-0 h-screen w-[280px] bg-transparent shadow-md text-white hidden md:block p-3 text-sm">
    <!-- Container untuk background dengan padding dan SCROLL -->
    <div class="bg-[#0A749B] rounded-2xl h-full px-4 py-6 flex flex-col overflow-y-auto scrollbar-thin scrollbar-thumb-[#e5e5e5]/40 scrollbar-track-[#0A749B]/20">
        <!-- Logo section -->
        <div class="flex items-center justify-between mb-8 flex-shrink-0">
            <img src="{{ asset('logo/navlogo.png') }}" alt="Logo Aplikasi Rapat Harian" class="w-40">
            <button id="menu-toggle-close"
                class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#0A749B] hover:text-white focus:outline-none">
                <span class="sr-only">Open main menu</span>
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Navigation dengan style yang lebih modern -->
        <nav class="space-y-2 flex-grow">
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm">
                <i class="fas fa-home w-6 h-6"></i>
                <span class="ml-3 text-sm">Dashboard</span>
            </a>

            <a href="{{ route('admin.machine-status.view') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.machine-status.view') || request()->routeIs('admin.machine-status.*') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm">
                <i class="fas fa-check w-6 h-6"></i>
                <span class="ml-3 text-sm">Kesiapan Pembangkit</span>
            </a>
            <a href="{{ route('kalender.pemeliharaan') }}"
            class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('kalender.pemeliharaan') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm">
            <i class="fas fa-calendar-alt w-6 h-6"></i>
            <span class="ml-3 text-sm">Kalender Pemeliharaan</span>
        </a>

            {{-- <a href="{{ route('admin.laporan.sr_wo') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.laporan.sr_wo') || request()->routeIs('admin.laporan.manage') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm">
                <i class="fas fa-file-alt w-6 h-6"></i>
                <span class="ml-3 text-sm">Laporan SR/WO</span>
            </a> --}}

            <a href="{{ route('admin.maximo.index') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.maximo.*') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm">
                <i class="fas fa-file-alt w-6 h-6"></i>
                <span class="ml-3 text-sm">Laporan SR/WO (Maximo Akses)</span>
            </a>



            <a href="{{ route('admin.other-discussions.index') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.other-discussions.*') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm">
                <i class="fas fa-comments w-6 h-6"></i>
                <span class="ml-3 text-sm">Pembahasan Lain-lain</span>
            </a>

            <!-- Data Master Dropdown Menu -->
            @if (Auth::check() && Auth::user()->email === 'admin@upkendari.com')
            <div class="relative group">
                <button type="button" id="data-master-dropdown"
                    class="flex items-center w-full px-3 py-2.5 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.power-plants.index') || request()->routeIs('admin.machine-monitor.show') || request()->routeIs('admin.users') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm">
                    <i class="fas fa-database w-6 h-6"></i>
                    <span class="ml-3 text-sm flex-1 text-left">Data Master</span>
                    <i class="fas fa-chevron-down ml-auto transition-transform duration-200" id="data-master-chevron"></i>
                </button>
                <div id="data-master-submenu"
                    class="max-h-0 overflow-hidden transition-all duration-300 bg-[#0A749B] rounded-lg mt-1 ml-2"
                    style="box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <a href="{{ route('admin.power-plants.index') }}"
                        class="flex items-center px-5 py-2 rounded-lg {{ request()->routeIs('admin.power-plants.index') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/20' }} text-sm">
                        <i class="fas fa-bolt w-5 h-5"></i>
                        <span class="ml-3 text-sm">Unit Pembangkit</span>
                    </a>
                    <a href="{{ route('admin.machine-monitor.show', 1) }}"
                        class="flex items-center px-5 py-2 rounded-lg {{ request()->routeIs('admin.machine-monitor.show') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/20' }} text-sm">
                        <i class="fas fa-cogs w-5 h-5"></i>
                        <span class="ml-3 text-sm">Detail Mesin</span>
                    </a>
                    <a href="{{ route('admin.users') }}"
                        class="flex items-center px-5 py-2 rounded-lg {{ request()->routeIs('admin.users') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/20' }} text-sm">
                        <i class="fas fa-users w-5 h-5"></i>
                        <span class="ml-3 text-sm">Manajemen Pengguna</span>
                    </a>
                    <a href="{{ route('admin.material-master.index') }}"
                        class="flex items-center px-5 py-2 rounded-lg {{ request()->routeIs('admin.material-master.index') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/20' }} text-sm">
                        <i class="fas fa-cogs w-5 h-5"></i>
                        <span class="ml-3 text-sm">Material Master</span>
                    </a>
                </div>
            </div>
            @endif
            <!-- End Data Master Dropdown -->

            @if (Auth::check() && Auth::user()->email === 'admin@upkendari.com')
            <a href="{{ route('admin.machine-monitor') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.machine-monitor') || request()->routeIs('admin.machine-monitor.show') || request()->routeIs('admin.power-plants.index') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm">
                <i class="fas fa-desktop w-6 h-6"></i>
                <span class="ml-3 text-sm">Monitor Mesin</span>
            </a>
            @endif

            <a href="{{ route('admin.daftar_hadir.index') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.daftar_hadir.index') || request()->routeIs('admin.daftar_hadir.rekapitulasi') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm">
                <i class="fas fa-list w-6 h-6"></i>
                <span class="ml-3 text-sm">Daftar Hadir</span>
            </a>

            <a href="{{ route('admin.attendance.qr') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.attendance.qr') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm">
                <i class="fas fa-qrcode w-6 h-6"></i>
                <span class="ml-3 text-sm">QR Code</span>
            </a>

            <a href="{{ route('admin.score-card.index') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.score-card.*') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm">
                <i class="fas fa-clipboard-list w-6 h-6"></i>
                <span class="ml-3 text-sm">Score Card Daily</span>
            </a>
           
            {{-- Manajemen Pengguna sudah ada di Data Master --}}
           

            <a href="{{ route('admin.meetings') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.meetings') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm">
                <i class="fas fa-chart-bar w-6 h-6"></i>
                <span class="ml-3 text-sm">Laporan Rapat</span>
            </a>

            @if (Auth::check() && Auth::user()->role === 'super_admin')
            <a href="{{ route('admin.settings') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.settings') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm">
                <i class="fas fa-cog w-6 h-6"></i>
                <span class="ml-3 text-sm">Pengaturan</span>
            </a>
            @endif
        </nav>

        <!-- Bottom Section: Logout -->
        <div class="mt-2 flex-shrink-0">
            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf
                <button type="button" 
                    onclick="confirmLogout()"
                    class="flex items-center w-full px-3 py-2.5 rounded-lg text-white bg-red-400 hover:bg-red-700 transition-colors duration-200 text-sm">
                    <i class="fas fa-sign-out-alt w-6 h-6"></i>
                    <span class="ml-3 text-sm">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdownButton = document.getElementById('data-master-dropdown');
    const submenu = document.getElementById('data-master-submenu');
    const chevron = document.getElementById('data-master-chevron');
    let isOpen = false;

    if (dropdownButton && submenu) {
        dropdownButton.addEventListener('click', function(e) {
            e.preventDefault();
            isOpen = !isOpen;
            if (isOpen) {
                submenu.style.maxHeight = submenu.scrollHeight + 'px';
                chevron.classList.add('rotate-180');
            } else {
                submenu.style.maxHeight = '0';
                chevron.classList.remove('rotate-180');
            }
        });

        // Auto open if one of the submenu is active
        @if(request()->routeIs('admin.power-plants.index') || request()->routeIs('admin.machine-monitor.show') || request()->routeIs('admin.users'))
            submenu.style.maxHeight = submenu.scrollHeight + 'px';
            chevron.classList.add('rotate-180');
            isOpen = true;
        @endif
    }
});

function confirmLogout() {
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
}
</script>
