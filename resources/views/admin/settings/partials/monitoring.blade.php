<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center mb-6">
        <i class="fas fa-desktop text-2xl text-gray-500 mr-3"></i>
        <h2 class="text-xl font-semibold text-gray-800">Pengaturan Pemantauan</h2>
    </div>

    <div class="space-y-6">
        <!-- System Monitoring -->
        <div class="border-b pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Pemantauan Sistem</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <label for="system_monitoring" class="font-medium text-gray-700">Pemantauan Sistem</label>
                        <p class="text-sm text-gray-500">Pantau penggunaan CPU, memori, dan penyimpanan</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="system_monitoring" id="system_monitoring" class="sr-only peer"
                            {{ isset($settings['system_monitoring']) && $settings['system_monitoring'] ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Interval Pemantauan (menit)
                        </label>
                        <input type="number" name="monitoring_interval" min="1" max="60"
                            value="{{ old('monitoring_interval', $settings['monitoring_interval'] ?? '5') }}"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Batas Penggunaan CPU (%)
                        </label>
                        <input type="number" name="cpu_threshold" min="1" max="100"
                            value="{{ old('cpu_threshold', $settings['cpu_threshold'] ?? '80') }}"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Batas Penggunaan RAM (%)
                        </label>
                        <input type="number" name="ram_threshold" min="1" max="100"
                            value="{{ old('ram_threshold', $settings['ram_threshold'] ?? '80') }}"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Batas Penggunaan Disk (%)
                        </label>
                        <input type="number" name="disk_threshold" min="1" max="100"
                            value="{{ old('disk_threshold', $settings['disk_threshold'] ?? '90') }}"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Monitoring -->
        <div class="border-b pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Pemantauan Kinerja</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <label for="performance_monitoring" class="font-medium text-gray-700">Pemantauan Kinerja</label>
                        <p class="text-sm text-gray-500">Pantau waktu respons dan kinerja aplikasi</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="performance_monitoring" id="performance_monitoring" class="sr-only peer"
                            {{ isset($settings['performance_monitoring']) && $settings['performance_monitoring'] ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Batas Waktu Respons (ms)
                    </label>
                    <input type="number" name="response_time_threshold" min="100" max="10000"
                        value="{{ old('response_time_threshold', $settings['response_time_threshold'] ?? '1000') }}"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- Log Monitoring -->
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Pemantauan Log</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <label for="log_monitoring" class="font-medium text-gray-700">Pemantauan Log</label>
                        <p class="text-sm text-gray-500">Pantau dan analisis log sistem</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="log_monitoring" id="log_monitoring" class="sr-only peer"
                            {{ isset($settings['log_monitoring']) && $settings['log_monitoring'] ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Level Log Minimum
                    </label>
                    <select name="log_level" 
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="debug" {{ (old('log_level', $settings['log_level'] ?? '') == 'debug') ? 'selected' : '' }}>Debug</option>
                        <option value="info" {{ (old('log_level', $settings['log_level'] ?? '') == 'info') ? 'selected' : '' }}>Info</option>
                        <option value="warning" {{ (old('log_level', $settings['log_level'] ?? '') == 'warning') ? 'selected' : '' }}>Warning</option>
                        <option value="error" {{ (old('log_level', $settings['log_level'] ?? '') == 'error') ? 'selected' : '' }}>Error</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Retensi Log (hari)
                    </label>
                    <input type="number" name="log_retention" min="1" max="365"
                        value="{{ old('log_retention', $settings['log_retention'] ?? '30') }}"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>
            </div>
        </div>
    </div>
</div> 