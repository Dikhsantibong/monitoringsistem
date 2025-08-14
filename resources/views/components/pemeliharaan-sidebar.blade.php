<aside id="mobile-menu"
    class="fixed z-20 transform overflow-hidden transition-transform duration-300 md:relative md:translate-x-0 h-screen w-[280px] bg-transparent shadow-md text-white hidden md:block p-3">
    <div class="bg-[#0A749B] rounded-2xl h-full px-4 py-6">
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
        <nav class="space-y-2">
            <a href="{{ route('pemeliharaan.dashboard') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('pemeliharaan.dashboard') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-tools w-6 h-6"></i>
                <span class="ml-3 text-base">Dashboard Pemeliharaan</span>
            </a>

            <a href="{{ route('pemeliharaan.labor-saya') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('pemeliharaan.labor-saya') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-user w-6 h-6"></i>
                <span class="ml-3 text-base">Work Order</span>
            </a>
            <a href="{{ route('pemeliharaan.jobcard') }}"
            class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('pemeliharaan.jobcard') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
            <i class="fas fa-file-alt w-6 h-6"></i>
            <span class="ml-3 text-base">Jobcard</span>
        </a>
           
            <a href="{{ route('pemeliharaan.master-labor') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('pemeliharaan.master-labor') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-database w-6 h-6"></i>
                <span class="ml-3 text-base">Team Pemeliharaan</span>
            </a>
           
           
            <a href="{{ route('support') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('support') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-headset w-6 h-6"></i>
                <span class="ml-3 text-base">Support</span>
            </a>
            
        </nav>
    </div>
</aside>