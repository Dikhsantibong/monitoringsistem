<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
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
use App\Http\Controllers\Admin\MeetingController;
use App\Http\Controllers\ScoreCardDailyController;
use App\Http\Controllers\WoBacklogController;
use App\Http\Controllers\DashboardPemantauanController;

Route::get('/', [HomeController::class, 'index'])->name('homepage');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/scan/{token}', [AttendanceController::class, 'showScanForm'])
        ->name('scan-form')
        ->withoutMiddleware(['auth'])
        ->where('token', '.*');
    Route::post('/submit', [AttendanceController::class, 'submitAttendance'])
        ->name('submit')
        ->withoutMiddleware(['auth']);
    Route::get('/success', function () {
        return view('admin.daftar_hadir.success');
    })->name('success');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [UserController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [UserController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [UserController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/generate-qrcode', [AttendanceController::class, 'generateQrCode'])->name('generate.qrcode');
    Route::post('/record-attendance', [AttendanceController::class, 'recordAttendance'])->name('record.attendance');
    Route::get('/attendance/signature/{id}', [AttendanceController::class, 'showSignature'])->name('attendance.signature');
});

Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/daily-meeting', [UserController::class, 'dailyMeeting'])->name('daily.meeting');
    Route::get('/monitoring', [UserController::class, 'monitoring'])->name('monitoring');
    Route::get('/documentation', [UserController::class, 'documentation'])->name('documentation');
    Route::get('/support', [UserController::class, 'support'])->name('support');
    Route::get('/user/machine-monitor', [UserController::class, 'machineMonitor'])->name('user.machine.monitor');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/refresh', [DashboardController::class, 'refresh'])->name('dashboard.refresh');
    
    Route::prefix('activities')->name('activities.')->group(function () {
        Route::get('/', [ActivityController::class, 'index'])->name('index');
        Route::get('/export', [ActivityController::class, 'export'])->name('export');
    });

    Route::prefix('machine-monitor')->group(function () {
        Route::get('/', [MachineMonitorController::class, 'index'])->name('machine-monitor');
        Route::get('/create', [MachineMonitorController::class, 'create'])->name('machine-monitor.create');
        Route::post('/store', [MachineMonitorController::class, 'store'])->name('machine-monitor.store');
        Route::get('/show', [MachineMonitorController::class, 'show'])->name('machine-monitor.show');
        Route::get('/{id}/edit', [MachineMonitorController::class, 'edit'])->name('machine-monitor.edit');
        Route::put('/{id}', [MachineMonitorController::class, 'update'])
            ->name('machine-monitor.update');
        Route::delete('/{id}', [MachineMonitorController::class, 'destroy'])->name('machine-monitor.destroy');
        Route::get('/show-all', [MachineMonitorController::class, 'showAll'])->name('machine-monitor.show.all');
    });

    Route::prefix('pembangkit')->name('pembangkit.')->group(function () {
        Route::get('/ready', [PembangkitController::class, 'ready'])->name('ready');
        Route::post('/save-status', [PembangkitController::class, 'saveStatus'])->name('save-status');
        Route::get('/get-status', [PembangkitController::class, 'getStatus'])->name('get-status');
        Route::get('/status-history', [PembangkitController::class, 'getStatusHistory'])->name('status-history');
        Route::get('/report', [PembangkitController::class, 'report'])->name('report');
        Route::get('/report/download', [PembangkitController::class, 'downloadReport'])->name('downloadReport');
        Route::get('/report/print', [PembangkitController::class, 'printReport'])->name('printReport');
    });

    Route::prefix('laporan')->group(function () {
        Route::get('/sr_wo', [LaporanController::class, 'srWo'])->name('laporan.sr_wo');
        Route::get('/sr_wo/closed', [LaporanController::class, 'srWoClosed'])->name('laporan.sr_wo_closed');
        Route::get('/sr_wo/closed/download', [LaporanController::class, 'downloadSrWoClosed'])->name('laporan.sr_wo.closed.download');
        Route::get('/sr_wo/closed/print', [LaporanController::class, 'printSrWoClosed'])->name('laporan.sr_wo.closed.print');
        Route::post('/store-sr', [LaporanController::class, 'storeSR'])->name('laporan.store-sr');
        Route::post('/store-wo', [LaporanController::class, 'storeWO'])->name('laporan.store-wo');
        Route::post('/sr/{id}/update-status', [LaporanController::class, 'updateSRStatus'])->name('laporan.update-sr-status');
        Route::post('/wo/{id}/update-status', [LaporanController::class, 'updateWOStatus'])->name('laporan.update-wo-status');
        Route::get('/create-sr', [LaporanController::class, 'createSR'])->name('laporan.create-sr');
        Route::get('/create-wo', [LaporanController::class, 'createWO'])->name('laporan.create-wo');
        
        Route::get('/create-wo-backlog', [LaporanController::class, 'createWOBacklog'])->name('laporan.create-wo-backlog');
        Route::post('/store-wo-backlog', [LaporanController::class, 'storeWOBacklog'])->name('laporan.store-wo-backlog');
        Route::get('/wo-backlog/{id}/edit', [LaporanController::class, 'editWoBacklog'])->name('laporan.edit-wo-backlog');
        Route::put('/wo-backlog/{id}', [LaporanController::class, 'updateWoBacklog'])->name('laporan.update-wo-backlog');
        Route::post('/wo-backlog/{id}/status', [LaporanController::class, 'updateBacklogStatus'])->name('laporan.update-backlog-status');
    });

    Route::prefix('meetings')->group(function () {
        Route::get('/', [AdminMeetingController::class, 'index'])->name('meetings');    
        Route::get('/create', [AdminMeetingController::class, 'create'])->name('meetings.create');
        Route::post('/upload', [AdminMeetingController::class, 'upload'])->name('meetings.upload');
        Route::get('/{meeting}', [AdminMeetingController::class, 'show'])->name('meetings.show');
        Route::get('/export', [AdminMeetingController::class, 'export'])->name('meetings.export');
        Route::get('/meeting-details', [AdminMeetingController::class, 'print'])->name('meetings.meeting-details');
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('users');
        Route::get('/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/store', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::get('/search', [AdminUserController::class, 'search'])->name('users.search');
    });

    Route::resource('score-card', ScoreCardDailyController::class);

    Route::prefix('daftar-hadir')->name('daftar_hadir.')->group(function () {
        Route::get('/', [DaftarHadirController::class, 'index'])->name('index');
        Route::get('/create', [DaftarHadirController::class, 'create'])->name('create');
        Route::post('/store', [DaftarHadirController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [DaftarHadirController::class, 'edit'])->name('edit');
        Route::put('/{id}', [DaftarHadirController::class, 'update'])->name('update');
        Route::delete('/{id}', [DaftarHadirController::class, 'destroy'])->name('destroy');
        Route::get('/export', [DaftarHadirController::class, 'export'])->name('export');
        Route::get('/print', [DaftarHadirController::class, 'print'])->name('print');
        Route::get('/rekapitulasi', [DaftarHadirController::class, 'rekapitulasi'])->name('rekapitulasi');
    });

    Route::prefix('settings')->group(function () {
        Route::get('/', [AdminSettingController::class, 'index'])->name('settings');
        Route::post('/update', [AdminSettingController::class, 'update'])->name('settings.update');
    });
});

Route::get('/dashboard-pemantauan', [DashboardPemantauanController::class, 'index'])
    ->name('dashboard.pemantauan');


    