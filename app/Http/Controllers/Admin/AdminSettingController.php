<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminSettingController extends Controller
{
    public function index()
    {
        // Ambil settings dari database atau file konfigurasi
        $settings = [
            'company_name' => config('app.name'),
            'contact_email' => config('mail.from.address'),
            'email_notifications' => true,
            'maintenance_alerts' => true,
            'refresh_interval' => 30,
            'alert_threshold' => 80,
            'maintenance_interval' => 30,
            'maintenance_window' => 'morning',
            'api_key' => config('services.machine_monitor.api_key'),
            'webhook_url' => config('services.machine_monitor.webhook_url'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'email_notifications' => 'boolean',
            'maintenance_alerts' => 'boolean',
            'refresh_interval' => 'required|integer|min:10|max:300',
            'alert_threshold' => 'required|integer|min:0|max:100',
            'maintenance_interval' => 'required|integer|min:1|max:365',
            'maintenance_window' => 'required|in:morning,afternoon,night',
            'webhook_url' => 'nullable|url',
        ]);

        // Simpan settings ke database atau file konfigurasi
        // ... implementasi penyimpanan settings ...

        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully');
    }

    public function regenerateApiKey()
    {
        $newApiKey = Str::random(32);

        // Simpan API key baru ke database atau file konfigurasi
        // ... implementasi penyimpanan API key ...

        return response()->json([
            'success' => true,
            'message' => 'API key regenerated successfully'
        ]);
    }
} 