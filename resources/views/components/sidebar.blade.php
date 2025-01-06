<aside id="mobile-menu"
class="fixed z-20 transform overflow-hidden transition-transform duration-300 md:relative md:translate-x-0 h-screen w-64 bg-[#0A749B] shadow-md text-white hidden md:block md:shadow-lg">
<div class="p-4 flex items-center gap-3">
    <img src="{{ asset('logo/navlogo.png') }}" alt="Logo Aplikasi Rapat Harian" class="w-40 h-15">
    <!-- Mobile Menu Toggle -->
    <button id="menu-toggle-close"
        class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#009BB9] hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
        aria-controls="mobile-menu" aria-expanded="false">
        <span class="sr-only">Open main menu</span>
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>
<nav class="mt-4">
    <a href="{{ route('admin.dashboard') }}"
        class="flex items-center px-4 py-3 rounded mb-2 {{ request()->routeIs('admin.dashboard') ? 'bg-[#F3F3F3] text-black shadow' : 'text-white hover:text-black hover:bg-[#F3F3F3] hover:rounded hover:shadow' }}">
        <i class="fas fa-home mr-3"></i>
        <span>Dashboard</span>
    </a>
    <a href="{{ route('admin.pembangkit.ready') }}"
        class="flex items-center px-4 py-3 rounded mb-2 {{ request()->routeIs('admin.pembangkit.ready') ? 'bg-[#F3F3F3] text-black shadow' : 'text-white hover:text-black hover:bg-[#F3F3F3] hover:rounded hover:shadow' }}">
        <i class="fas fa-check mr-3"></i>
        <span>Kesiapan Pembangkit</span>
    </a>
    <a href="{{ route('admin.laporan.sr_wo') }}"
        class="flex items-center px-4 py-3 rounded mb-2 {{ request()->routeIs('admin.laporan.sr_wo') ? 'bg-[#F3F3F3] text-black shadow' : 'text-white hover:text-black hover:bg-[#F3F3F3] hover:rounded hover:shadow' }}">
        <i class="fas fa-file-alt mr-3"></i>
        <span>Laporan SR/WO</span>
    </a>
    <a href="{{ route('admin.other-discussions.index') }}"
    class="flex items-center px-4 py-3 rounded mb-2 {{ request()->routeIs('admin.other-discussions.*') ? 'bg-[#F3F3F3] text-black shadow' : 'text-white hover:text-black hover:bg-[#F3F3F3] hover:rounded hover:shadow' }}">
    <i class="fas fa-comments mr-3"></i>
    <span>Pembahasan Lain-lain</span>
    </a>
    <a href="{{ route('admin.machine-monitor') }}"
        class="flex items-center px-4 py-3 rounded mb-2 {{ request()->routeIs('admin.machine-monitor') ? 'bg-[#F3F3F3] text-black shadow' : 'text-white hover:text-black hover:bg-[#F3F3F3] hover:rounded hover:shadow' }}">
        <i class="fas fa-cogs mr-3"></i>
        <span>Monitor Mesin</span>
       
    </a>
  
    <a href="{{ route('admin.daftar_hadir.index') }}"
        class="flex items-center px-4 py-3 rounded mb-2 {{ request()->routeIs('admin.daftar_hadir.index') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3] hover:rounded' }}">
        <i class="fas fa-list mr-3"></i>
        <span>Daftar Hadir</span>
    </a>
    <a href="{{ route('admin.score-card.index') }}"
        class="flex items-center px-4 py-3 rounded mb-2 {{ request()->routeIs('admin.score-card.*') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3] hover:rounded' }}">
        <i class="fas fa-clipboard-list mr-3"></i>
        <span>Score Card Daily</span>
    </a>
    <a href="{{ route('admin.users') }}"
        class="flex items-center px-4 py-3 rounded mb-2 {{ request()->routeIs('admin.users') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3] hover:rounded' }}">
        <i class="fas fa-users mr-3"></i>
        <span>Manajemen Pengguna</span>
    </a>
    <a href="{{ route('admin.meetings') }}"
        class="flex items-center px-4 py-3 rounded mb-2 {{ request()->routeIs('admin.meetings') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3] hover:rounded' }}">
        <i class="fas fa-chart-bar mr-3"></i>
        <span>Laporan Rapat</span>
    </a>
    <a href="{{ route('admin.settings') }}"
        class="flex items-center px-4 py-3 rounded mb-2 {{ request()->routeIs('admin.settings') ? 'bg-[#F3F3F3] text-black' : 'text-white hover:text-black hover:bg-[#F3F3F3] hover:rounded' }}">
        <i class="fas fa-cog mr-3"></i>
        <span>Pengaturan</span>
    </a>
   
</nav>
</aside>
{{-- 
<script>
// Fungsi untuk toggle dropdown
document.addEventListener('DOMContentLoaded', function() {
    const dropdownButton = document.querySelector('#machine-monitor-dropdown');
    const submenu = document.querySelector('#machine-monitor-submenu');
    let isOpen = false;

    dropdownButton.addEventListener('click', function(e) {
        e.preventDefault();
        isOpen = !isOpen;
        
        if (isOpen) {
            submenu.style.maxHeight = submenu.scrollHeight + 'px';
            dropdownButton.classList.add('rotate-180');
        } else {
            submenu.style.maxHeight = '0';
            dropdownButton.classList.remove('rotate-180');
        }
    });
});
</script> --}}
