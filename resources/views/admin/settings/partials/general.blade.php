<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center mb-6">
        <i class="fas fa-cog text-2xl text-gray-500 mr-3"></i>
        <h2 class="text-xl font-semibold text-gray-800">Informasi Aplikasi</h2>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Nama Aplikasi
            </label>
            <input type="text" name="app_name"
                value="{{ old('app_name', $settings['app_name'] ?? '') }}"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Versi Aplikasi
            </label>
            <input type="text" name="app_version" 
                value="{{ old('app_version', $settings['app_version'] ?? '') }}"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Deskripsi Aplikasi
            </label>
            <textarea name="app_description" rows="3"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">{{ old('app_description', $settings['app_description'] ?? '') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Email Administrator
            </label>
            <input type="email" name="admin_email"
                value="{{ old('admin_email', $settings['admin_email'] ?? '') }}"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Zona Waktu Default
            </label>
            <select name="timezone" 
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                <option value="Asia/Jakarta" {{ (old('timezone', $settings['timezone'] ?? '') == 'Asia/Jakarta') ? 'selected' : '' }}>
                    Asia/Jakarta (WIB)
                </option>
                <option value="Asia/Makassar" {{ (old('timezone', $settings['timezone'] ?? '') == 'Asia/Makassar') ? 'selected' : '' }}>
                    Asia/Makassar (WITA)
                </option>
                <option value="Asia/Jayapura" {{ (old('timezone', $settings['timezone'] ?? '') == 'Asia/Jayapura') ? 'selected' : '' }}>
                    Asia/Jayapura (WIT)
                </option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Bahasa Default
            </label>
            <select name="default_language" 
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                <option value="id" {{ (old('default_language', $settings['default_language'] ?? '') == 'id') ? 'selected' : '' }}>
                    Bahasa Indonesia
                </option>
                <option value="en" {{ (old('default_language', $settings['default_language'] ?? '') == 'en') ? 'selected' : '' }}>
                    English
                </option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Mode Maintenance
            </label>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="maintenance_mode" class="sr-only peer" 
                    {{ isset($settings['maintenance_mode']) && $settings['maintenance_mode'] ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-sm font-medium text-gray-700">Aktifkan Mode Maintenance</span>
            </label>
        </div>
    </div>
</div> 