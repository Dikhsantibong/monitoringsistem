<aside id="mobile-menu"
    class="fixed z-20 transform overflow-hidden transition-transform duration-300 md:relative md:translate-x-0 h-screen w-[280px] bg-transparent shadow-md text-white hidden md:block p-3">
    <div class="bg-[#0A749B] rounded-2xl h-full px-4 py-6">
        <!-- Logo section -->
        <div class="flex items-center justify-between mb-8">
            <img src="{{ asset('logo/navlogo.png') }}" alt="Logo Inventory" class="w-40">
        </div>
        <!-- Navigation -->
        <nav class="space-y-2">
            <a href="{{ route('inventory.dashboard') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('inventory.dashboard') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-warehouse w-6 h-6"></i>
                <span class="ml-3 text-base">Dashboard Inventory</span>
            </a>
            <a href="{{ route('inventory.material.index') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('inventory.material.index') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-boxes w-6 h-6"></i>
                <span class="ml-3 text-base">Data Material</span>
            </a>
            <a href="{{ route('inventory.katalog.index') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('inventory.katalog.index') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-file-signature w-6 h-6"></i>
                <span class="ml-3 text-base">Data Pengajuan Katalog</span>
            </a>
            <a href="{{ route('inventory.pengajuan-material.index') }}"
                class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('inventory.pengajuan-material.index') ? 'bg-white/10 text-white font-medium' : 'text-gray-100 hover:bg-white/10' }}">
                <i class="fas fa-file-upload w-6 h-6"></i>
                <span class="ml-3 text-base">DataPengajuan Material</span>
            </a>
            
        </nav>
    </div>
</aside>
