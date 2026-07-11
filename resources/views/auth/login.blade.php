@extends('layouts.app')

@section('content')
<div class="min-h-screen flex w-full m-0 p-0 overflow-hidden">
    <!-- Left Section -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/bg.jpg') }}');">
        <!-- Overlay Gradient to match the dark blueish tint in the screenshot -->
        <div class="absolute inset-0 bg-blue-900/40 mix-blend-multiply"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-blue-950/90 via-blue-900/30 to-transparent"></div>
        
        <!-- Top left logo box -->
        <div class="absolute top-8 left-8 bg-white px-4 py-2 rounded-sm shadow-md">
            <img src="{{ asset('logo/navlogo.png') }}" alt="Logo" class="h-10 object-contain">
        </div>
        
        <!-- Top right text -->
        <div class="absolute top-10 right-8 text-white font-semibold tracking-wider text-sm opacity-90">
            MONITORING DAILY UP KENDARI
        </div>

        <!-- Bottom text -->
        <div class="absolute bottom-16 left-12 text-white z-10">
            <h1 class="text-4xl font-bold mb-4 leading-tight">Monitoring Daily<br>UP Kendari</h1>
            <div class="flex items-center gap-4">
                <div class="h-px w-10 bg-blue-400"></div>
                <p class="text-white/90 text-lg font-medium tracking-wide">PLN Nusantara Power — Unit Pembangkitan Kendari</p>
            </div>
        </div>
    </div>

    <!-- Right Section -->
    <div class="w-full lg:w-1/2 flex flex-col justify-center items-center bg-white p-8 lg:p-24 relative min-h-screen">
        <div class="w-full max-w-sm">
            <!-- Mobile logo -->
            <div class="lg:hidden mb-6 bg-white px-4 py-2 inline-block rounded shadow">
                <img src="{{ asset('logo/navlogo.png') }}" alt="Logo" class="h-10 object-contain">
            </div>

            <h2 class="text-3xl font-bold text-gray-900 mb-2 leading-tight text-center" style="font-family: 'Inter', sans-serif;">Monitoring Daily<br>UP Kendari</h2>
            <p class="text-gray-500 mb-8 text-sm text-center">Masukkan email dan password Anda untuk melanjutkan</p>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-800 mb-1.5">Email</label>
                    <input id="email" type="email" name="email" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors @error('email') border-red-500 @enderror" placeholder="nama@plnnusantarapower.co.id" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-medium text-gray-800">Password</label>
                        {{-- <a href="#" class="text-sm text-gray-500 hover:text-blue-600 transition-colors">Lupa password?</a> --}}
                    </div>
                    <div class="relative">
                        <input id="password" type="password" name="password" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors pr-12 @error('password') border-red-500 @enderror" placeholder="Password" required>
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 focus:outline-none">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Unit Selection -->
                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-800 mb-1.5">Pilih Unit</label>
                    
                    @php
                        $units = [
                            'mysql' => 'UP Kendari',
                            'mysql_bau_bau' => 'ULPLTD Bau-Bau',
                            'mysql_kolaka' => 'ULPLTD Kolaka',
                            'mysql_poasia' => 'ULPLTD Poasia',
                            'mysql_wua_wua' => 'ULPLTD Wua-Wua'
                        ];
                        $currentUnit = $selectedUnit ?? 'mysql';
                        $currentLabel = $units[$currentUnit] ?? 'UP Kendari';
                    @endphp

                    <div x-data="{ 
                            open: false, 
                            selected: '{{ $currentUnit }}', 
                            label: '{{ $currentLabel }}',
                            options: [
                                { value: 'mysql', text: 'UP Kendari' },
                                { value: 'mysql_bau_bau', text: 'ULPLTD Bau-Bau' },
                                { value: 'mysql_kolaka', text: 'ULPLTD Kolaka' },
                                { value: 'mysql_poasia', text: 'ULPLTD Poasia' },
                                { value: 'mysql_wua_wua', text: 'ULPLTD Wua-Wua' }
                            ]
                        }" 
                        class="relative"
                        @click.away="open = false">
                        
                        <!-- Hidden input to hold the actual value for the form submission -->
                        <input type="hidden" name="unit" x-model="selected" id="unit">
                        
                        <!-- Custom Select Button -->
                        <button type="button" 
                                @click="open = !open" 
                                class="w-full flex justify-between items-center px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white transition-colors"
                                :class="{ 'ring-2 ring-blue-500 border-blue-500': open }">
                            <span x-text="label" class="text-gray-700 text-sm"></span>
                            <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300" :class="open ? 'transform rotate-180' : ''"></i>
                        </button>
                        
                        <!-- Dropdown Options Panel -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-[-10px] scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-[-10px] scale-95"
                             class="absolute z-50 w-full mt-1.5 bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden" 
                             style="display: none;">
                            <ul class="max-h-80 overflow-y-auto py-1">
                                <template x-for="option in options" :key="option.value">
                                    <li @click="selected = option.value; label = option.text; open = false" 
                                        class="px-4 py-2.5 cursor-pointer transition-colors text-sm flex items-center justify-between"
                                        :class="selected === option.value ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-gray-50'">
                                        <span x-text="option.text"></span>
                                        <i x-show="selected === option.value" class="fas fa-check text-blue-600"></i>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center pt-1">
                    <input id="remember" type="checkbox" name="remember" class="h-4 w-4 text-blue-500 focus:ring-blue-500 border-gray-300 rounded cursor-pointer" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                        Ingat saya
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2.5 px-4 rounded-lg transition-colors duration-200 mt-4 shadow-md">
                    Masuk
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-16 text-[11px] text-gray-400">
                © {{ date('Y') }} PLN Nusantara Power. Seluruh Hak Cipta Dilindungi.
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    
    body {
        margin: 0;
        padding: 0;
        font-family: 'Inter', sans-serif;
        background-color: #fff;
    }

    /* Override any layout padding from app.blade.php if present */
    main {
        margin-top: 0 !important;
    }
</style>
@endsection

@section('scripts')
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (sessionStorage.getItem('redirectAfterLogin')) {
            Swal.fire({
                icon: 'info',
                title: 'Login Diperlukan',
                text: 'Silakan login untuk melanjutkan.',
                timer: 2500,
                showConfirmButton: false
            });
        }
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: '{{ session("error") }}',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Coba Lagi',
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session("success") }}',
                confirmButtonColor: '#28a745',
                confirmButtonText: 'OK',
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: 'Email atau password yang Anda masukkan salah!',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Coba Lagi',
                timer: 3000,
                timerProgressBar: true
            });
        @endif
    });
</script>
@endsection
