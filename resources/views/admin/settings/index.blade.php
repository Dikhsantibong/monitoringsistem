@extends('layouts.app')

@section('content')
    <div class="flex h-screen bg-gray-50 overflow-auto">
        <!-- Sidebar -->
        <aside id="mobile-menu"
            class="fixed z-20 overflow-hidden transform transition-transform duration-300 md:relative md:translate-x-0 h-screen w-64 bg-[#0A749B] shadow-md text-white hidden md:block md:shadow-lg">
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
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-home mr-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.pembangkit.ready') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.pembangkit.ready') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-check mr-3"></i>
                    <span>Kesiapan Pembangkit</span>
                </a>
                <a href="{{ route('admin.laporan.sr_wo') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.laporan.sr_wo') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-file-alt mr-3"></i>
                    <span>Laporan SR/WO</span>
                </a>
                <a href="{{ route('admin.machine-monitor') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.machine-monitor') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-cogs mr-3"></i>
                    <span>Monitor Mesin</span>
                </a>
                <a href="{{ route('admin.daftar_hadir.index') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.daftar_hadir.index') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-list mr-3"></i>
                    <span>Daftar Hadir</span>
                </a>
                <a href="{{ route('admin.score-card.index') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.score-card.*') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-clipboard-list mr-3"></i>
                    <span>Score Card Daily</span>
                </a>
                <a href="{{ route('admin.users') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.users') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-users mr-3"></i>
                    <span>Manajemen Pengguna</span>
                </a>
                <a href="{{ route('admin.meetings') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.meetings') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-chart-bar mr-3"></i>
                    <span>Laporan Rapat</span>
                </a>
                <a href="{{ route('admin.settings') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('admin.settings') ? 'bg-[#F3F3F3] text-black' : 'text-white  hover:bg-[#F3F3F3]' }}">
                    <i class="fas fa-cog mr-3"></i>
                    <span>Pengaturan</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div id="main-content" class="flex-1 overflow-auto">
            <header class="bg-white shadow-sm">
                <div class="flex justify-between items-center px-6 py-2">
                    <!-- Mobile Menu Toggle -->
                    <button id="mobile-menu-toggle"
                        class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#009BB9] hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800">Pengaturan</h1>
                    <div class="relative">
                        <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                            <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                                class="w-7 h-7 rounded-full mr-2">
                            <span class="text-gray-700 text-sm">{{ Auth::user()->name }}</span>
                            <i class="fas fa-caret-down ml-2 text-gray-600"></i>
                        </button>
                        <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">

                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </header>
            <div class="flex items-center pt-2">
                <x-admin-breadcrumb :breadcrumbs="[['name' => 'Pengaturan', 'url' => null]]" />
            </div>
            <main class="px-6">
                <!-- Pengaturan Umum -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Pengaturan Umum</h2>
                        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('POST')

                            <!-- Informasi Perusahaan -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Nama Perusahaan
                                    </label>
                                    <input type="text" name="company_name"
                                        value="{{ old('company_name', $settings['company_name'] ?? '') }}"
                                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Email Kontak
                                    </label>
                                    <input type="email" name="contact_email"
                                        value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                </div>
                            </div>

                            <!-- Pengaturan Notifikasi -->
                            <div class="border-t pt-6">
                                <h3 class="text-md font-medium text-gray-700 mb-4">Pengaturan Notifikasi</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="email_notifications" id="email_notifications"
                                            {{ isset($settings['email_notifications']) && $settings['email_notifications'] ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600 rounded border-gray-300">
                                        <label for="email_notifications" class="ml-2 text-sm text-gray-700">
                                            Aktifkan Notifikasi Email
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="maintenance_alerts" id="maintenance_alerts"
                                            {{ isset($settings['maintenance_alerts']) && $settings['maintenance_alerts'] ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600 rounded border-gray-300">
                                        <label for="maintenance_alerts" class="ml-2 text-sm text-gray-700">
                                            Aktifkan Notifikasi Pemeliharaan
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Pengaturan Pemantauan Mesin -->
                            <div class="border-t pt-6">
                                <h3 class="text-md font-medium text-gray-700 mb-4">Pengaturan Pemantauan Mesin</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Interval Pembaruan Data (detik)
                                        </label>
                                        <input type="number" name="refresh_interval"
                                            value="{{ old('refresh_interval', $settings['refresh_interval'] ?? 30) }}"
                                            min="10" max="300"
                                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Ambang Batas Peringatan (%)
                                        </label>
                                        <input type="number" name="alert_threshold"
                                            value="{{ old('alert_threshold', $settings['alert_threshold'] ?? 80) }}"
                                            min="0" max="100"
                                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Jadwal Pemeliharaan -->
                            <div class="border-t pt-6">
                                <h3 class="text-md font-medium text-gray-700 mb-4">Jadwal Pemeliharaan</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Interval Pemeliharaan Rutin (hari)
                                        </label>
                                        <input type="number" name="maintenance_interval"
                                            value="{{ old('maintenance_interval', $settings['maintenance_interval'] ?? 30) }}"
                                            min="1" max="365"
                                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Jendela Waktu Pemeliharaan
                                        </label>
                                        <select name="maintenance_window"
                                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                            <option value="morning"
                                                {{ isset($settings['maintenance_window']) && $settings['maintenance_window'] === 'morning' ? 'selected' : '' }}>
                                                Pagi (6 AM - 12 PM)
                                            </option>
                                            <option value="afternoon"
                                                {{ isset($settings['maintenance_window']) && $settings['maintenance_window'] === 'afternoon' ? 'selected' : '' }}>
                                                Siang (12 PM - 6 PM)
                                            </option>
                                            <option value="night"
                                                {{ isset($settings['maintenance_window']) && $settings['maintenance_window'] === 'night' ? 'selected' : '' }}>
                                                Malam (6 PM - 12 AM)
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Simpan -->
                            <div class="flex justify-end pt-6 border-t">
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                    Simpan Pengaturan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Pengaturan API -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Pengaturan API</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Kunci API
                                </label>
                                <div class="flex">
                                    <input type="text" readonly
                                        value="{{ $settings['api_key'] ?? 'Tidak ada kunci API yang dihasilkan' }}"
                                        class="flex-1 px-3 py-2 border rounded-l-lg bg-gray-50">
                                    <button type="button" onclick="regenerateApiKey()"
                                        class="px-4 py-2 bg-gray-500 text-white rounded-r-lg hover:bg-gray-600">
                                        Regenerasi
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    URL Webhook
                                </label>
                                <input type="url" name="webhook_url"
                                    value="{{ old('webhook_url', $settings['webhook_url'] ?? '') }}"
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="{{ asset('js/toggle.js') }}"></script>
@endsection

@push('scripts')
    <script>
        function regenerateApiKey() {
            if (confirm(
                    'Apakah Anda yakin ingin menghasilkan kembali kunci API? Ini akan membuat kunci yang ada tidak valid.'
                )) {
                // Lakukan panggilan AJAX untuk menghasilkan kembali kunci API
                fetch('{{ route('admin.settings.regenerate-api-key') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Muat ulang halaman untuk menampilkan kunci API baru
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Gagal menghasilkan kembali kunci API. Silakan coba lagi.');
                    });
            }
        }

        // Tampilkan pesan sukses jika ada
        @if (session('success'))
            Swal.fire({
                title: 'Sukses!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        @endif
    </script>
@endpush
