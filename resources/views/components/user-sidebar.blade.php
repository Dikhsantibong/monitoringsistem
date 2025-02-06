<aside id="mobile-menu"
    class="fixed z-20 transform overflow-hidden transition-transform duration-300 md:relative md:translate-x-0 h-screen w-[280px] bg-transparent shadow-md text-white hidden md:block p-3">
    <!-- Container untuk background dengan padding -->
    <div class="bg-[#0A749B] rounded-2xl h-full px-4 py-6">
        <!-- Logo section -->
        <div class="flex items-center justify-between mb-8">
            <img src="{{ asset('logo/navlogo.png') }}" alt="Logo Aplikasi Rapat Harian" class="w-40">
            <button id="menu-toggle-close"
                class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#0A749B] hover:text-white focus:outline-none">
                <span class="sr-only">Open main menu</span>
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Navigation dengan style yang lebih modern -->
        <nav class="space-y-2">
            <a href="{{ route('user.dashboard') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('user.dashboard') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-home w-6 h-6"></i>
                <span class="ml-3 text-base">Dashboard</span>
            </a>

            <a href="{{ route('user.machine.monitor') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('user.machine.monitor') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-cogs w-6 h-6"></i>
                <span class="ml-3 text-base">Machine Monitor</span>
            </a>

            <a href="{{ route('daily.meeting') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('daily.meeting') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-users w-6 h-6"></i>
                <span class="ml-3 text-base">Daily Meeting</span>
            </a>

            <a href="{{ route('monitoring') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('monitoring') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-chart-line w-6 h-6"></i>
                <span class="ml-3 text-base">Monitoring</span>
            </a>

            <a href="{{ route('documentation') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('documentation') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-book w-6 h-6"></i>
                <span class="ml-3 text-base">Documentation</span>
            </a>

            <a href="{{ route('support') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('support') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-headset w-6 h-6"></i>
                <span class="ml-3 text-base">Support</span>
            </a>
        </nav>
    </div>
</aside> 