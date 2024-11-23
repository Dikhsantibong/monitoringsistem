<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\MeetingController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminMeetingController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\MachineMonitorController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/daily-meeting', [MeetingController::class, 'index'])->name('daily.meeting');
Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring');
Route::get('/documentation', [DocumentationController::class, 'index'])->name('documentation');
Route::get('/support', [SupportController::class, 'index'])->name('support');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/refresh', [DashboardController::class, 'refresh'])->name('dashboard.refresh');
    
    Route::get('/machine-monitor', [MachineMonitorController::class, 'index'])->name('machine-monitor');
    Route::post('/machine-monitor/issues', [MachineMonitorController::class, 'storeIssue'])->name('machine-monitor.store-issue');
    Route::put('/machine-monitor/machines/{machine}/status', [MachineMonitorController::class, 'updateMachineStatus'])->name('machine-monitor.update-status');
    Route::put('/machine-monitor/machines/{machine}/metrics', [MachineMonitorController::class, 'updateMetrics'])->name('machine-monitor.update-metrics');
    
    Route::get('/users', [AdminUserController::class, 'index'])->name('users');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    
    Route::get('/meetings', [AdminMeetingController::class, 'index'])->name('meetings');
    Route::get('/meetings/create', [AdminMeetingController::class, 'create'])->name('meetings.create');
    Route::post('/meetings', [AdminMeetingController::class, 'store'])->name('meetings.store');
    Route::get('/meetings/{meeting}/edit', [AdminMeetingController::class, 'edit'])->name('meetings.edit');
    Route::put('/meetings/{meeting}', [AdminMeetingController::class, 'update'])->name('meetings.update');
    Route::delete('/meetings/{meeting}', [AdminMeetingController::class, 'destroy'])->name('meetings.destroy');
    
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/regenerate-api-key', [AdminSettingController::class, 'regenerateApiKey'])
         ->name('settings.regenerate-api-key');
    
    Route::get('/activities/export', [ActivityController::class, 'export'])->name('activities.export');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'index'])->name('user.dashboard');
});

Route::get('/test', function () {
    return 'Test Route';
});
