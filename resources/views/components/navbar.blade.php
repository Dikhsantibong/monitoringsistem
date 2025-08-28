 <!-- Navbar -->
 <nav class="fixed w-full top-0 z-50">
    <div class="nav-background ">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="#" class="flex items-center">
                        <img src="{{ asset('logo/navlogo.png') }}" alt="Logo" class="h-8">
                    </a>
                </div>

                <!-- Menu Desktop -->
                <div class="hidden md:flex items-center ">
                    <ul class="flex space-x-8">
                        <li><a href="#" class="nav-link">Home</a></li>
                        <li><a href="#map" class="nav-link">Peta Pembangkit</a></li>
                        <li><a href="#live-data" class="nav-link">Live Data</a></li>
                        <li><a href="{{ route('dashboard.pemantauan') }}" class="nav-link">Dashboard Pemantauan</a></li>
                        <li><a href="https://sites.google.com/view/pemeliharaan-upkendari" class="nav-link" target="_blank">Bid. Pemeliharaan</a></li>
                        <li><a href="{{ route('notulen.form') }}" class="nav-link">Notulen</a></li>
                        <!-- Dropdown Menu Lainnya -->
                        <li class="relative group">
                            <button class="nav-link flex items-center focus:outline-none" id="menu-lainnya-btn">
                                Menu Lainnya
                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div class="absolute left-0 mt-2 w-56 bg-white rounded shadow-lg z-50 hidden group-hover:block group-focus:block" id="menu-lainnya-dropdown">
                                <a href="{{ route('calendar.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-100"><i class="fas fa-calendar-alt mr-1"></i> Kalender</a>
                                <a href="{{ route('kinerja.pemeliharaan') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-100"><i class="fas fa-chart-line mr-1"></i> Kinerja Pemeliharaan</a>
                                <a href="{{ route('weekly-meeting.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-100">Weekly Meeting</a>
                            </div>
                        </li>
                        <!-- Login button -->
                        <li>
                            <a href="{{ route('login') }}" class="login-button">
                                <i class="fas fa-user mr-2"></i> Login
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Menu Mobile -->
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <ul class="space-y-4">
                    <li><a href="#" class="nav-link-mobile">Home</a></li>
                    <li><a href="#map" class="nav-link-mobile">Peta Pembangkit</a></li>
                    <li><a href="#live-data" class="nav-link-mobile">Live Data Unit Operasional</a></li>
                    <li><a href="{{ route('dashboard.pemantauan') }}" class="nav-link-mobile">Dashboard Pemantauan</a></li>
                    <li><a href="https://sites.google.com/view/pemeliharaan-upkendari" class="nav-link-mobile" target="_blank">Bid. Pemeliharaan</a></li>
                    <li><a href="{{ route('notulen.form') }}" class="nav-link-mobile">Notulen</a></li>
                    <li><a href="{{ route('calendar.index') }}" class="nav-link-mobile">
                        <i class="fas fa-calendar-alt mr-1"></i> Calendar
                    </a></li>
                    <li>
                        <a href="{{ route('kinerja.pemeliharaan') }}" class="nav-link-mobile">
                            <i class="fas fa-chart-line mr-1"></i> Kinerja Pemeliharaan
                        </a>
                    </li>
                    <li><a href="{{ route('weekly-meeting.index') }}" class="nav-link-mobile">Weekly Meeting</a></li>
                    <!-- Login button in mobile -->
                    <li>
                        <a href="{{ route('login') }}" class="nav-link-mobile login-mobile">
                            <i class="fas fa-user mr-2"></i> Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<script>
    // Dropdown show/hide for accessibility (optional, fallback for keyboard)
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('menu-lainnya-btn');
        const dropdown = document.getElementById('menu-lainnya-dropdown');
        if(btn && dropdown) {
            btn.addEventListener('focus', () => dropdown.classList.remove('hidden'));
            btn.addEventListener('blur', () => setTimeout(() => dropdown.classList.add('hidden'), 200));
        }
    });
</script>