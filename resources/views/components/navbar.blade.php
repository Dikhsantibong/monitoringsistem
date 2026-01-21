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
                        <li><a href="{{ route('homepage') }}" class="nav-link">Home</a></li>
                        <li><a href="{{ route('weekly-meeting.index') }}" class="nav-link">Weekly Meeting</a></li>
                        <li><a href="{{ route('kinerja.pemeliharaan') }}" class="nav-link">Kinerja Pemeliharaan</a></li>
                        <li><a href="{{ route('calendar.index') }}" class="nav-link">Kalender Pemeliharaan</a></li>
                        <li><a href="https://sites.google.com/view/pemeliharaan-upkendari" class="nav-link" target="_blank">Bid. Pemeliharaan</a></li>
                        <li><a href="{{ route('notulen.form') }}" class="nav-link">Notulen</a></li>
                        <!-- Dropdown Menu Lainnya -->
                        {{-- <li class="relative group">
                            <button class="nav-link flex items-center focus:outline-none" id="menu-lainnya-btn">
                                Menu Lainnya
                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div class="absolute left-0 mt-2 w-56 bg-white rounded shadow-lg z-50 hidden group-hover:block group-focus:block" id="menu-lainnya-dropdown">
                                <a href="{{ route('calendar.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-100"><i class="fas fa-calendar-alt mr-1"></i> Kalender</a>
                                <a href="{{ route('kinerja.pemeliharaan') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-100"><i class="fas fa-chart-line mr-1"></i> Kinerja Pemeliharaan</a>
                                <a href="{{ route('weekly-meeting.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-100">Weekly Meeting</a>
                            </div>
                        </li> --}}
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
                <ul class="space-y-4 pt-4">
                    <li>
                        <a href="{{ route('homepage') }}" class="nav-link-mobile">Home</a>
                    </li>

                    <li>
                        <a href="{{ route('weekly-meeting.index') }}" class="nav-link-mobile">
                            Weekly Meeting
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('kinerja.pemeliharaan') }}" class="nav-link-mobile">
                            Kinerja Pemeliharaan
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('calendar.index') }}" class="nav-link-mobile">
                            Kalender Pemeliharaan
                        </a>
                    </li>

                    <li>
                        <a href="https://sites.google.com/view/pemeliharaan-upkendari"
                        target="_blank"
                        class="nav-link-mobile">
                            Bid. Pemeliharaan
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('notulen.form') }}" class="nav-link-mobile">
                            Notulen
                        </a>
                    </li>

                    <!-- Login -->
                    <li class="pt-2 border-t border-gray-200">
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
    document.addEventListener('DOMContentLoaded', function () {
        const mobileBtn = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
    
        mobileBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    });
</script>
    