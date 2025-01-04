@extends('layouts.app')

@section('content')
<style>
    .tab-btn {
        transition: all 0.3s ease-in-out;
    }

    .tab-btn:hover {
        color: #1a56db;
    }

    .tab-content {
        transition: opacity 0.3s ease-in-out;
    }
    </style>
    

    <div class="flex h-screen bg-gray-50 overflow-auto">
        @include("components.sidebar")

        <div id="main-content" class="flex-1 overflow-auto">
            <header class="bg-white shadow-sm sticky top-0 z-10">
                <div class="flex justify-between items-center px-6 py-3">
                    <div class="flex items-center gap-x-3">
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
                        <!--  Menu Toggle Sidebar-->
                        <button id="desktop-menu-toggle"
                            class="hidden md:block relative items-center justify-center rounded-md text-gray-400 hover:bg-[#009BB9] p-2 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                            aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true" data-slot="icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800">Pengaturan</h1>
                    </div>

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

            <main class="px-6 py-8">
                <!-- Tab Navigation -->
                <div class="mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <button type="button" 
                                class="tab-btn flex items-center whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600" 
                                data-target="general-settings">
                                <i class="fas fa-cog mr-2"></i>Umum
                            </button>
                            <button type="button" 
                                class="tab-btn flex items-center whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                                data-target="notification-settings">
                                <i class="fas fa-bell mr-2"></i>Notifikasi
                            </button>
                            <button type="button" 
                                class="tab-btn flex items-center whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                                data-target="monitoring-settings">
                                <i class="fas fa-desktop mr-2"></i>Pemantauan
                            </button>
                            <button type="button" 
                                class="tab-btn flex items-center whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                                data-target="security-settings">
                                <i class="fas fa-shield-alt mr-2"></i>Keamanan
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Tab Contents -->
                <div class="tab-contents">
                    <!-- General Settings Tab -->
                    <div id="general-settings" class="tab-content">
                        @include('admin.settings.partials.general')
                    </div>

                    <!-- Notification Settings Tab -->
                    <div id="notification-settings" class="tab-content hidden">
                        @include('admin.settings.partials.notification')
                    </div>

                    <!-- Monitoring Settings Tab -->
                    <div id="monitoring-settings" class="tab-content hidden">
                        @include('admin.settings.partials.monitoring')
                    </div>

                    <!-- Security Settings Tab -->
                    <div id="security-settings" class="tab-content hidden">
                        @include('admin.settings.partials.security')
                    </div>
                </div>

                <!-- Floating Save Button -->
                <div class="fixed bottom-6 right-6">
                    <button type="submit" class="flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-lg transition-all">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </main>
        </div>
    </div>
    <script src="{{ asset('js/toggle.js ') }}"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi untuk mengaktifkan tab
            function activateTab(tabId) {
                // Sembunyikan semua konten tab
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });

                // Hapus kelas aktif dari semua tab button
                document.querySelectorAll('.tab-btn').forEach(btn => {
                    btn.classList.remove('border-blue-500', 'text-blue-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });

                // Tampilkan konten tab yang dipilih
                const selectedContent = document.getElementById(tabId);
                if (selectedContent) {
                    selectedContent.classList.remove('hidden');
                }

                // Aktifkan tab button yang dipilih
                const selectedTab = document.querySelector(`[data-target="${tabId}"]`);
                if (selectedTab) {
                    selectedTab.classList.remove('border-transparent', 'text-gray-500');
                    selectedTab.classList.add('border-blue-500', 'text-blue-600');
                }

                // Simpan tab aktif ke localStorage
                localStorage.setItem('activeSettingsTab', tabId);
            }

            // Tambahkan event listener ke semua tab button
            document.querySelectorAll('.tab-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const tabId = this.getAttribute('data-target');
                    activateTab(tabId);
                });
            });

            // Cek localStorage untuk tab yang terakhir aktif
            const savedTab = localStorage.getItem('activeSettingsTab');
            if (savedTab && document.getElementById(savedTab)) {
                activateTab(savedTab);
            } else {
                // Default ke tab general jika tidak ada tab yang tersimpan
                activateTab('general-settings');
            }
        });
    </script>


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
        @push('scripts')
    @endpush
    @endsection
