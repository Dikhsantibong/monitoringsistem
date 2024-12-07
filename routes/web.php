<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminMeetingController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\MachineMonitorController;
use App\Http\Controllers\Admin\UserMachineMonitorController;
use App\Http\Controllers\Admin\PembangkitController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\DaftarHadirController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PowerPlantController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Admin\MeetingController;
use App\Http\Controllers\ScoreCardDailyController;

Route::get('/', [HomeController::class, 'index'])->name('homepage');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/profile', [UserController::class, 'showProfile'])->name('user.profile');
Route::post('/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');

Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/daily-meeting', [UserController::class, 'dailyMeeting'])->name('daily.meeting');
    Route::get('/monitoring', [UserController::class, 'monitoring'])->name('monitoring');
    Route::get('/documentation', [UserController::class, 'documentation'])->name('documentation');
    Route::get('/support', [UserController::class, 'support'])->name('support');
    Route::get('/user/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::get('/user/machine-monitor', [UserController::class, 'machineMonitor'])->name('user.machine.monitor');
    Route::get('/attendance/check', [AttendanceController::class, 'check'])->name('attendance.check');
    Route::get('/attendance/record', [AttendanceController::class, 'record'])->name('attendance.record');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/refresh', [DashboardController::class, 'refresh'])->name('dashboard.refresh');

    Route::prefix('machine-monitor')->group(function () {
        Route::get('/', [MachineMonitorController::class, 'index'])->name('machine-monitor');
        Route::get('/create', [MachineMonitorController::class, 'create'])->name('machine-monitor.create');
        Route::post('/store', [MachineMonitorController::class, 'store'])->name('machine-monitor.store');
        Route::get('/{machine}', [MachineMonitorController::class, 'show'])->name('machine-monitor.show');
        Route::delete('/{machine}', [MachineMonitorController::class, 'destroy'])->name('machine-monitor.destroy');
    });

    Route::prefix('pembangkit')->group(function () {
        Route::get('/ready', [PembangkitController::class, 'ready'])->name('pembangkit.ready');
        Route::post('/save-status', [PembangkitController::class, 'saveStatus'])->name('pembangkit.save-status');
        Route::get('/get-status', [PembangkitController::class, 'getStatus'])->name('pembangkit.get-status');
        Route::get('/status-history', [PembangkitController::class, 'getStatusHistory'])->name('pembangkit.status-history');
    });

    Route::prefix('laporan')->group(function () {
        Route::get('/sr_wo', [LaporanController::class, 'srWo'])->name('laporan.sr_wo');
        Route::post('/store-sr', [LaporanController::class, 'storeSR'])->name('laporan.store-sr');
        Route::post('/store-wo', [LaporanController::class, 'storeWO'])->name('laporan.store-wo');
    });

    Route::prefix('daftar-hadir')->group(function () {
        Route::get('/', [DaftarHadirController::class, 'index'])->name('daftar_hadir.index');
    });

    Route::prefix('meetings')->group(function () {
        Route::get('/', [AdminMeetingController::class, 'index'])->name('meetings');
        Route::get('/create', [AdminMeetingController::class, 'create'])->name('meetings.create');
        Route::post('/upload', [AdminMeetingController::class, 'upload'])->name('meetings.upload');
        Route::get('/{meeting}', [AdminMeetingController::class, 'show'])->name('meetings.show');
        Route::get('/export', [AdminMeetingController::class, 'export'])->name('meetings.export');
        Route::get('/user/daily-meeting', [UserController::class, 'dailyMeeting'])->name('user.daily-meeting');
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('users');
        Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::get('/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/store', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/{user}/delete', [AdminUserController::class, 'delete'])->name('users.delete');
    });

    Route::prefix('activities')->group(function () {
        Route::get('/export', [ActivityController::class, 'export'])->name('activities.export');
    });

    Route::prefix('settings')->group(function () {
        Route::get('/', [AdminSettingController::class, 'index'])->name('settings');
        Route::post('/', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::post('/regenerate-api-key', [AdminSettingController::class, 'regenerateApiKey'])->name('settings.regenerate-api-key');
    });

    Route::prefix('score-card')->group(function () {
        Route::resource('score-card', ScoreCardDailyController::class);
    });
});

// Rute untuk menampilkan detail berita
Route::get('/news/{id}', [NewsController::class, 'show'])->name('news.show');

// Rute untuk mengirim formulir kontak
Route::post('/contact/submit', [ContactController::class, 'submit'])->name('contact.submit');

// Rute untuk menampilkan detail blog
Route::get('/blog/{id}', [BlogController::class, 'show'])->name('blog.show');

// Tambahkan route group untuk profile
Route::middleware(['auth'])->group(function () {
    // Profile routes
    Route::get('/profile', [UserController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [UserController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [UserController::class, 'destroy'])->name('profile.destroy');
    Route::get('/generate-qrcode', [AttendanceController::class, 'generateQrCode'])->name('generate.qrcode');
    Route::post('/record-attendance', [AttendanceController::class, 'recordAttendance'])->name('record.attendance');
    Route::get('/daftar-hadir', [AttendanceController::class, 'index'])->name('admin.daftar_hadir.index');
});

// Tambahkan route untuk AJAX
Route::get('/admin/machine-monitor/operations', [MachineMonitorController::class, 'getMachineOperations'])
    ->name('admin.machine-monitor.operations');