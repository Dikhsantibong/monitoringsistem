<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center mb-6">
        <i class="fas fa-shield-alt text-2xl text-gray-500 mr-3"></i>
        <h2 class="text-xl font-semibold text-gray-800">Pengaturan Keamanan</h2>
    </div>

    <div class="space-y-6">
        <!-- Password Policy -->
        <div class="border-b pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Kebijakan Password</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <label for="strong_password" class="font-medium text-gray-700">Password Kuat</label>
                        <p class="text-sm text-gray-500">Wajib menggunakan kombinasi huruf, angka, dan simbol</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="strong_password" id="strong_password" class="sr-only peer"
                            {{ isset($settings['strong_password']) && $settings['strong_password'] ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Panjang Minimum Password
                    </label>
                    <input type="number" name="min_password_length" min="8" max="32"
                        value="{{ old('min_password_length', $settings['min_password_length'] ?? '8') }}"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Masa Berlaku Password (hari)
                    </label>
                    <input type="number" name="password_expiry_days" min="0" max="365"
                        value="{{ old('password_expiry_days', $settings['password_expiry_days'] ?? '90') }}"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Masukkan 0 untuk menonaktifkan kebijakan ini</p>
                </div>
            </div>
        </div>

        <!-- Login Security -->
        <div class="border-b pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Keamanan Login</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <label for="two_factor_auth" class="font-medium text-gray-700">Autentikasi Dua Faktor</label>
                        <p class="text-sm text-gray-500">Wajibkan verifikasi tambahan saat login</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="two_factor_auth" id="two_factor_auth" class="sr-only peer"
                            {{ isset($settings['two_factor_auth']) && $settings['two_factor_auth'] ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Batas Percobaan Login
                    </label>
                    <input type="number" name="max_login_attempts" min="3" max="10"
                        value="{{ old('max_login_attempts', $settings['max_login_attempts'] ?? '5') }}"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Waktu Blokir (menit)
                    </label>
                    <input type="number" name="lockout_duration" min="5" max="1440"
                        value="{{ old('lockout_duration', $settings['lockout_duration'] ?? '30') }}"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- Session Security -->
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Keamanan Sesi</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Batas Waktu Sesi (menit)
                    </label>
                    <input type="number" name="session_timeout" min="5" max="1440"
                        value="{{ old('session_timeout', $settings['session_timeout'] ?? '120') }}"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <label for="force_logout_all" class="font-medium text-gray-700">Paksa Logout Semua Perangkat</label>
                        <p class="text-sm text-gray-500">Logout dari semua sesi aktif saat mengubah password</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="force_logout_all" id="force_logout_all" class="sr-only peer"
                            {{ isset($settings['force_logout_all']) && $settings['force_logout_all'] ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div> 