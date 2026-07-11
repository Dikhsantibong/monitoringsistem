<!-- Navbar Container -->
<div class="fixed top-0 left-0 right-0 z-50 px-4 pt-6 sm:px-6 lg:px-8">
    <nav class="mx-auto flex w-full max-w-screen-2xl items-center justify-between rounded-2xl !bg-white/90 backdrop-blur-md border border-white/20 px-6 py-4 shadow-lg dark:!bg-zinc-950/90 dark:border-zinc-800">
        <!-- Left: Logo -->
        <div class="flex items-center gap-4">
            <a href="#" class="flex items-center">
                <img src="{{ asset('logo/navlogo.png') }}" alt="Logo" class="h-10 object-contain">
            </a>
        </div>

        <!-- Center: Menus -->
        <div class="hidden lg:flex items-center gap-6 xl:gap-8">
            <a href="{{ route('homepage') }}" class="text-xs xl:text-sm font-bold transition-colors hover:text-blue-600 dark:hover:text-blue-400 {{ request()->routeIs('homepage') ? '!text-blue-600 dark:!text-blue-400' : '!text-slate-800 dark:!text-gray-200' }}" style="text-decoration: none;">
                HOME
            </a>
            <a href="{{ route('kinerja.pemeliharaan') }}" class="text-xs xl:text-sm font-bold transition-colors hover:text-blue-600 dark:hover:text-blue-400 {{ request()->routeIs('kinerja.pemeliharaan') ? '!text-blue-600 dark:!text-blue-400' : '!text-slate-800 dark:!text-gray-200' }}" style="text-decoration: none;">
                KINERJA PEMELIHARAAN
            </a>
            <a href="{{ route('weekly-meeting.index') }}" class="text-xs xl:text-sm font-bold transition-colors hover:text-blue-600 dark:hover:text-blue-400 {{ request()->routeIs('weekly-meeting.*') ? '!text-blue-600 dark:!text-blue-400' : '!text-slate-800 dark:!text-gray-200' }}" style="text-decoration: none;">
                WEEKLY MEETING
            </a>
            <a href="{{ route('peta-kesehatan-unit') }}" class="text-xs xl:text-sm font-bold transition-colors hover:text-blue-600 dark:hover:text-blue-400 {{ request()->routeIs('peta-kesehatan-unit') ? '!text-blue-600 dark:!text-blue-400' : '!text-slate-800 dark:!text-gray-200' }} text-center" style="max-width: 120px; line-height: 1.2; text-decoration: none;">
                EQUIPMENT GANGGUAN BERULANG
            </a>
            <a href="{{ route('calendar.index') }}" class="text-xs xl:text-sm font-bold transition-colors hover:text-blue-600 dark:hover:text-blue-400 {{ request()->routeIs('calendar.*') ? '!text-blue-600 dark:!text-blue-400' : '!text-slate-800 dark:!text-gray-200' }}" style="text-decoration: none;">
                KALENDER
            </a>
            <a href="{{ route('notulen.form') }}" class="text-xs xl:text-sm font-bold transition-colors hover:text-blue-600 dark:hover:text-blue-400 {{ request()->routeIs('notulen.*') ? '!text-blue-600 dark:!text-blue-400' : '!text-slate-800 dark:!text-gray-200' }}" style="text-decoration: none;">
                NOTULEN
            </a>
            <a href="{{ route('monitoring-mesin') }}" class="text-xs xl:text-sm font-bold transition-colors hover:text-blue-600 dark:hover:text-blue-400 {{ request()->routeIs('monitoring-mesin') ? '!text-blue-600 dark:!text-blue-400' : '!text-slate-800 dark:!text-gray-200' }}" style="text-decoration: none;">
                MONITORING MESIN
            </a>
        </div>

        <!-- Right: Login Button & Mobile Toggle -->
        <div class="flex items-center gap-4">
            <a href="{{ route('login') }}" class="hidden md:inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 bg-blue-600 text-white hover:bg-blue-700 h-10 px-4 py-2">
                <i class="fas fa-user mr-2"></i> Log in
            </a>

            <!-- Menu Mobile Toggle -->
            <button id="mobile-menu-button" class="lg:hidden text-gray-700 dark:text-gray-300 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                </svg>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden lg:hidden mx-auto mt-2 w-full max-w-7xl rounded-2xl bg-white px-6 py-4 shadow-md dark:bg-zinc-950">
        <ul class="space-y-4">
            <li>
                <a href="{{ route('homepage') }}" class="block text-sm font-bold !text-slate-800 hover:!text-blue-600 dark:!text-gray-200 dark:hover:!text-blue-400" style="text-decoration: none;">HOME</a>
            </li>
            <li>
                <a href="{{ route('kinerja.pemeliharaan') }}" class="block text-sm font-bold !text-slate-800 hover:!text-blue-600 dark:!text-gray-200 dark:hover:!text-blue-400" style="text-decoration: none;">KINERJA PEMELIHARAAN</a>
            </li>
            <li>
                <a href="{{ route('weekly-meeting.index') }}" class="block text-sm font-bold !text-slate-800 hover:!text-blue-600 dark:!text-gray-200 dark:hover:!text-blue-400" style="text-decoration: none;">WEEKLY MEETING</a>
            </li>
            <li>
                <a href="{{ route('peta-kesehatan-unit') }}" class="block text-sm font-bold !text-slate-800 hover:!text-blue-600 dark:!text-gray-200 dark:hover:!text-blue-400" style="text-decoration: none;">EQUIPMENT GANGGUAN BERULANG</a>
            </li>
            <li>
                <a href="{{ route('calendar.index') }}" class="block text-sm font-bold !text-slate-800 hover:!text-blue-600 dark:!text-gray-200 dark:hover:!text-blue-400" style="text-decoration: none;">KALENDER PEMELIHARAAN</a>
            </li>
            <li>
                <a href="{{ route('notulen.form') }}" class="block text-sm font-bold !text-slate-800 hover:!text-blue-600 dark:!text-gray-200 dark:hover:!text-blue-400" style="text-decoration: none;">NOTULEN</a>
            </li>
            <li>
                <a href="{{ route('monitoring-mesin') }}" class="block text-sm font-bold !text-slate-800 hover:!text-blue-600 dark:!text-gray-200 dark:hover:!text-blue-400" style="text-decoration: none;">MONITORING MESIN</a>
            </li>
            <li class="pt-2 border-t border-gray-200 dark:border-gray-700 md:hidden">
                <a href="{{ route('login') }}" class="block text-sm font-bold !text-blue-600 hover:!text-blue-700 dark:!text-blue-400 dark:hover:!text-blue-300" style="text-decoration: none;">
                    <i class="fas fa-user mr-2"></i> Log in
                </a>
            </li>
        </ul>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mobileBtn = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
    
        mobileBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    });
</script>
    