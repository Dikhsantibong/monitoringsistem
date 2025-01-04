<div id="notification-settings" class="tab-content hidden">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center mb-6">
            <i class="fas fa-bell text-2xl text-gray-500 mr-3"></i>
            <h2 class="text-xl font-semibold text-gray-800">Pengaturan Notifikasi</h2>
        </div>

        <div class="space-y-6">
            <!-- Email Notifications Section -->
            <div class="border-b pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Notifikasi Email</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <label for="email_notifications" class="font-medium text-gray-700">Notifikasi Email</label>
                            <p class="text-sm text-gray-500">Terima pemberitahuan melalui email</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_notifications" id="email_notifications" class="sr-only peer" 
                                {{ isset($settings['email_notifications']) && $settings['email_notifications'] ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <label for="maintenance_alerts" class="font-medium text-gray-700">Peringatan Pemeliharaan</label>
                            <p class="text-sm text-gray-500">Dapatkan pemberitahuan saat sistem dalam pemeliharaan</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="maintenance_alerts" id="maintenance_alerts" class="sr-only peer"
                                {{ isset($settings['maintenance_alerts']) && $settings['maintenance_alerts'] ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- System Notifications Section -->
            <div class="border-b pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Notifikasi Sistem</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <label for="system_alerts" class="font-medium text-gray-700">Peringatan Sistem</label>
                            <p class="text-sm text-gray-500">Notifikasi tentang status dan kinerja sistem</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="system_alerts" id="system_alerts" class="sr-only peer"
                                {{ isset($settings['system_alerts']) && $settings['system_alerts'] ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <label for="security_alerts" class="font-medium text-gray-700">Peringatan Keamanan</label>
                            <p class="text-sm text-gray-500">Notifikasi tentang masalah keamanan</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="security_alerts" id="security_alerts" class="sr-only peer"
                                {{ isset($settings['security_alerts']) && $settings['security_alerts'] ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Notification Frequency -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Frekuensi Notifikasi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Frekuensi Pengiriman
                        </label>
                        <select name="notification_frequency" 
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                            <option value="realtime" {{ (old('notification_frequency', $settings['notification_frequency'] ?? '') == 'realtime') ? 'selected' : '' }}>
                                Realtime
                            </option>
                            <option value="daily" {{ (old('notification_frequency', $settings['notification_frequency'] ?? '') == 'daily') ? 'selected' : '' }}>
                                Harian
                            </option>
                            <option value="weekly" {{ (old('notification_frequency', $settings['notification_frequency'] ?? '') == 'weekly') ? 'selected' : '' }}>
                                Mingguan
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Waktu Pengiriman (untuk notifikasi terjadwal)
                        </label>
                        <input type="time" name="notification_time"
                            value="{{ old('notification_time', $settings['notification_time'] ?? '09:00') }}"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 