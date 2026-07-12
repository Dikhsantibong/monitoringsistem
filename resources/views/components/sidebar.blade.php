<aside id="mobile-menu"
    class="sidebar-container fixed z-20 transform overflow-hidden transition-all duration-300 md:relative md:translate-x-0 h-screen w-[280px] bg-transparent shadow-md text-white hidden md:block p-3 text-sm">
    <!-- Container untuk background dengan padding dan SCROLL -->
    <div class="bg-[#0A749B] rounded-2xl h-full px-4 py-6 flex flex-col overflow-y-auto overflow-x-hidden scrollbar-thin scrollbar-thumb-[#e5e5e5]/40 scrollbar-track-[#0A749B]/20 transition-all duration-300" id="sidebar-inner">
        <!-- Logo section -->
        <div class="flex items-center justify-between mb-8 flex-shrink-0" id="sidebar-logo-wrapper">
            <img src="{{ asset('logo/navlogo.png') }}" alt="Logo Aplikasi Rapat Harian" class="w-40 sidebar-logo-full transition-opacity duration-300">
            <!-- Small logo for collapsed state (can just be a small icon or text, here we use an icon) -->
            <i class="fas fa-bolt text-2xl text-white hidden sidebar-logo-icon mx-auto"></i>
            
            <button id="menu-toggle-close"
                class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#0A749B] hover:text-white focus:outline-none">
                <span class="sr-only">Open main menu</span>
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Navigation dengan style yang lebih modern -->
        <nav class="space-y-2 flex-grow sidebar-nav">
            <a href="{{ route('admin.dashboard') }}"
                class="nav-item flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm transition-all duration-200" title="Dashboard">
                <div class="icon-wrapper w-6 h-6 flex justify-center items-center"><i class="fas fa-home"></i></div>
                <span class="ml-3 text-sm sidebar-text whitespace-nowrap">Dashboard</span>
            </a>

            <a href="{{ route('admin.machine-status.view') }}"
                class="nav-item flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.machine-status.view') || request()->routeIs('admin.machine-status.*') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm transition-all duration-200" title="Kesiapan Pembangkit">
                <div class="icon-wrapper w-6 h-6 flex justify-center items-center"><i class="fas fa-check"></i></div>
                <span class="ml-3 text-sm sidebar-text whitespace-nowrap">Kesiapan Pembangkit</span>
            </a>
            <a href="{{ route('kalender.pemeliharaan') }}"
                class="nav-item flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('kalender.pemeliharaan') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm transition-all duration-200" title="Kalender Pemeliharaan">
                <div class="icon-wrapper w-6 h-6 flex justify-center items-center"><i class="fas fa-calendar-alt"></i></div>
                <span class="ml-3 text-sm sidebar-text whitespace-nowrap">Kalender Pemeliharaan</span>
            </a>

            <a href="{{ route('admin.maximo.index') }}"
                class="nav-item flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.maximo.*') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm transition-all duration-200" title="Laporan SR/WO (Maximo Akses)">
                <div class="icon-wrapper w-6 h-6 flex justify-center items-center"><i class="fas fa-file-alt"></i></div>
                <span class="ml-3 text-sm sidebar-text whitespace-nowrap">Laporan SR/WO (Maximo)</span>
            </a>

            <a href="{{ route('admin.other-discussions.index') }}"
                class="nav-item flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.other-discussions.*') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm transition-all duration-200" title="Pembahasan Lain-lain{{ session('unit') === 'mysql' ? ' dan Weekly' : '' }}">
                <div class="icon-wrapper w-6 h-6 flex justify-center items-center"><i class="fas fa-comments"></i></div>
                <span class="ml-3 text-sm sidebar-text whitespace-nowrap">Pembahasan Lain-lain</span>
            </a>

            <!-- Data Master Dropdown Menu -->
            @if (Auth::check() && Auth::user()->email === 'admin@upkendari.com')
            <div class="relative group nav-dropdown-container">
                <button type="button" id="data-master-dropdown"
                    class="nav-item flex items-center w-full px-3 py-2.5 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.power-plants.index') || request()->routeIs('admin.machine-monitor.show') || request()->routeIs('admin.users') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm" title="Data Master">
                    <div class="icon-wrapper w-6 h-6 flex justify-center items-center"><i class="fas fa-database"></i></div>
                    <span class="ml-3 text-sm flex-1 text-left sidebar-text whitespace-nowrap">Data Master</span>
                    <i class="fas fa-chevron-down ml-auto transition-transform duration-200 sidebar-text" id="data-master-chevron"></i>
                </button>
                <div id="data-master-submenu"
                    class="max-h-0 overflow-hidden transition-all duration-300 bg-[#086283] rounded-lg mt-1 ml-2 mr-2 sidebar-submenu"
                    style="box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
                    <a href="{{ route('admin.power-plants.index') }}"
                        class="flex items-center px-4 py-2 rounded-lg {{ request()->routeIs('admin.power-plants.index') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/20' }} text-sm transition-colors" title="Unit Pembangkit">
                        <i class="fas fa-bolt w-5 h-5 text-center"></i>
                        <span class="ml-3 text-sm sidebar-text whitespace-nowrap">Unit Pembangkit</span>
                    </a>
                    <a href="{{ route('admin.machine-monitor.show', 1) }}"
                        class="flex items-center px-4 py-2 rounded-lg {{ request()->routeIs('admin.machine-monitor.show') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/20' }} text-sm transition-colors" title="Detail Mesin">
                        <i class="fas fa-cogs w-5 h-5 text-center"></i>
                        <span class="ml-3 text-sm sidebar-text whitespace-nowrap">Detail Mesin</span>
                    </a>
                    <a href="{{ route('admin.users') }}"
                        class="flex items-center px-4 py-2 rounded-lg {{ request()->routeIs('admin.users') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/20' }} text-sm transition-colors" title="Manajemen Pengguna">
                        <i class="fas fa-users w-5 h-5 text-center"></i>
                        <span class="ml-3 text-sm sidebar-text whitespace-nowrap">Manajemen Pengguna</span>
                    </a>
                    <a href="{{ route('admin.material-master.index') }}"
                        class="flex items-center px-4 py-2 rounded-lg {{ request()->routeIs('admin.material-master.index') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/20' }} text-sm transition-colors" title="Material Master">
                        <i class="fas fa-cogs w-5 h-5 text-center"></i>
                        <span class="ml-3 text-sm sidebar-text whitespace-nowrap">Material Master</span>
                    </a>
                </div>
            </div>
            @endif

            <a href="{{ route('admin.attendance.qr') }}"
                class="nav-item flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.attendance.qr') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm transition-all duration-200" title="Daftar Hadir">
                <div class="icon-wrapper w-6 h-6 flex justify-center items-center"><i class="fas fa-qrcode"></i></div>
                <span class="ml-3 text-sm sidebar-text whitespace-nowrap">Daftar Hadir</span>
            </a>

            <a href="{{ route('admin.score-card.index') }}"
                class="nav-item flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.score-card.*') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm transition-all duration-200" title="Score Card Daily">
                <div class="icon-wrapper w-6 h-6 flex justify-center items-center"><i class="fas fa-clipboard-list"></i></div>
                <span class="ml-3 text-sm sidebar-text whitespace-nowrap">Score Card Daily</span>
            </a>
           
            <a href="{{ route('admin.meetings') }}"
                class="nav-item flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.meetings') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm transition-all duration-200" title="Laporan Rapat">
                <div class="icon-wrapper w-6 h-6 flex justify-center items-center"><i class="fas fa-chart-bar"></i></div>
                <span class="ml-3 text-sm sidebar-text whitespace-nowrap">Laporan Rapat</span>
            </a>

            @if (Auth::check() && Auth::user()->role === 'super_admin')
            <a href="{{ route('admin.settings') }}"
                class="nav-item flex items-center px-3 py-2.5 rounded-lg {{ request()->request->routeIs('admin.settings') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }} text-sm transition-all duration-200" title="Pengaturan">
                <div class="icon-wrapper w-6 h-6 flex justify-center items-center"><i class="fas fa-cog"></i></div>
                <span class="ml-3 text-sm sidebar-text whitespace-nowrap">Pengaturan</span>
            </a>
            @endif
        </nav>

        <!-- Bottom Section: Logout -->
        <div class="mt-2 flex-shrink-0">
            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf
                <button type="button" 
                    onclick="confirmLogout()"
                    class="nav-item flex items-center w-full px-3 py-2.5 rounded-lg text-white bg-red-400 hover:bg-red-700 transition-colors duration-200 text-sm" title="Logout">
                    <div class="icon-wrapper w-6 h-6 flex justify-center items-center"><i class="fas fa-sign-out-alt"></i></div>
                    <span class="ml-3 text-sm sidebar-text whitespace-nowrap">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>

<style>
    /* CSS untuk transisi collapse sidebar */
    .sidebar-collapsed {
        width: 100px !important;
    }
    .sidebar-collapsed #sidebar-inner {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        align-items: center;
    }
    .sidebar-collapsed .sidebar-text,
    .sidebar-collapsed .sidebar-logo-full {
        opacity: 0;
        visibility: hidden;
        display: none;
        width: 0;
    }
    .sidebar-collapsed .sidebar-logo-icon {
        display: block !important;
        margin-bottom: 1rem;
    }
    .sidebar-collapsed .nav-item {
        justify-content: center;
        padding-left: 0;
        padding-right: 0;
        width: 44px;
        height: 44px;
        margin: 0 auto;
        border-radius: 50%;
    }
    .sidebar-collapsed .icon-wrapper {
        margin: 0;
    }
    .sidebar-collapsed .sidebar-submenu {
        position: absolute;
        left: 100%;
        top: 0;
        min-width: 200px;
        background-color: #0A749B;
        z-index: 50;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-radius: 0.5rem;
        opacity: 0;
        pointer-events: none;
        max-height: none !important;
        transform: translateX(10px);
        transition: all 0.3s ease;
    }
    .sidebar-collapsed .nav-dropdown-container:hover .sidebar-submenu {
        opacity: 1;
        pointer-events: auto;
        transform: translateX(0);
    }
    .sidebar-collapsed .sidebar-submenu .sidebar-text {
        opacity: 1;
        visibility: visible;
        display: block;
        width: auto;
    }
    .sidebar-logo-icon {
        display: none;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdownButton = document.getElementById('data-master-dropdown');
    const submenu = document.getElementById('data-master-submenu');
    const chevron = document.getElementById('data-master-chevron');
    let isOpen = false;

    if (dropdownButton && submenu) {
        dropdownButton.addEventListener('click', function(e) {
            e.preventDefault();
            // Jika sedang collapse, klik tidak membuka accordion tapi menu hover yang jalan
            if (document.getElementById('mobile-menu').classList.contains('sidebar-collapsed')) {
                return;
            }
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
            if(!document.getElementById('mobile-menu').classList.contains('sidebar-collapsed')) {
                submenu.style.maxHeight = submenu.scrollHeight + 'px';
                if(chevron) chevron.classList.add('rotate-180');
                isOpen = true;
            }
        @endif
    }

    // Toggle logic
    const desktopToggle = document.getElementById('desktop-menu-toggle');
    const sidebar = document.getElementById('mobile-menu');
    
    // Set state default from localStorage if exists
    if(localStorage.getItem('sidebar-collapsed') === 'true') {
        sidebar.classList.add('sidebar-collapsed');
        if (submenu) {
            submenu.style.maxHeight = 'none'; // reset for hover mode
        }
    }

    if(desktopToggle && sidebar) {
        desktopToggle.addEventListener('click', function() {
            sidebar.classList.toggle('sidebar-collapsed');
            const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebar-collapsed', isCollapsed);
            
            if (isCollapsed) {
                if(submenu) {
                    submenu.style.maxHeight = 'none';
                    if(chevron) chevron.classList.remove('rotate-180');
                    // do not change isOpen here
                }
            } else {
                if(submenu) {
                    if(isOpen) {
                        submenu.style.maxHeight = submenu.scrollHeight + 'px';
                        if(chevron) chevron.classList.add('rotate-180');
                    } else {
                        submenu.style.maxHeight = '0';
                        if(chevron) chevron.classList.remove('rotate-180');
                    }
                }
            }
        });
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
