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
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\MeetingController;
use App\Http\Controllers\ScoreCardDailyController;
use App\Http\Controllers\WoBacklogController;
use App\Http\Controllers\DashboardPemantauanController;
use App\Http\Controllers\Admin\PowerPlantController;
use App\Http\Controllers\Admin\OtherDiscussionController;
use App\Http\Controllers\Admin\OverdueDiscussionController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\Admin\LaporanDeleteController;
use App\Http\Controllers\Admin\MachineStatusViewController;
use App\Http\Controllers\Admin\MachineStatusController;
use App\Http\Controllers\Admin\OtherDiscussionEditController;

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
    Route::get('/attendance/scan/{token}', [AttendanceController::class, 'showScanForm'])->name('attendance.scan');
    Route::post('/attendance/submit', [AttendanceController::class, 'submitAttendance'])->name('attendance.submit');
});

Route::prefix('attendance')->group(function () {
    Route::get('/scan/{token}', [AttendanceController::class, 'showScanForm'])
        ->name('attendance.scan-form')
        ->withoutMiddleware(['auth'])
        ->where('token', '.*');
    
    Route::post('/submit', [AttendanceController::class, 'submitAttendance'])
        ->name('attendance.submit')
        ->withoutMiddleware(['auth']);
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/dashboard/refresh', [DashboardController::class, 'refresh'])->name('dashboard.refresh');

    Route::prefix('machine-monitor')->group(function () {
        Route::get('/', [MachineMonitorController::class, 'index'])->name('machine-monitor');
        Route::get('/create', [MachineMonitorController::class, 'create'])->name('machine-monitor.create');
        Route::post('/store', [MachineMonitorController::class, 'store'])->name('machine-monitor.store');
        Route::get('/show', [MachineMonitorController::class, 'show'])->name('machine-monitor.show');
        Route::get('/{id}/edit', [MachineMonitorController::class, 'edit'])->name('machine-monitor.edit');
        Route::put('/{id}', [MachineMonitorController::class, 'update'])->name('machine-monitor.update');
        Route::delete('/{id}', [MachineMonitorController::class, 'destroy'])->name('machine-monitor.destroy');
    });

    Route::prefix('pembangkit')->group(function () {
        Route::get('/ready', [PembangkitController::class, 'ready'])->name('pembangkit.ready');
        Route::post('/save-status', [PembangkitController::class, 'saveStatus'])->name('pembangkit.save-status');
        Route::get('/get-status', [PembangkitController::class, 'getStatus'])->name('pembangkit.get-status');
        Route::get('/status-history', [PembangkitController::class, 'getStatusHistory'])->name('pembangkit.status-history');
        Route::get('/report', [PembangkitController::class, 'report'])->name('pembangkit.report');
        Route::get('/report/download', [PembangkitController::class, 'downloadReport'])->name('pembangkit.report.download');
        Route::get('/report/print', [PembangkitController::class, 'printReport'])->name('pembangkit.report.print');
    });

    Route::prefix('laporan')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/manage', [LaporanController::class, 'manage'])->name('laporan.manage');
        Route::get('/sr_wo', [LaporanController::class, 'srWo'])->name('laporan.sr_wo');
        Route::get('/sr_wo/closed', [LaporanController::class, 'srWoClosed'])->name('laporan.sr_wo_closed');
        Route::get('/sr_wo/closed/download', [LaporanController::class, 'downloadSrWoClosed'])->name('laporan.sr_wo.closed.download');
        Route::get('/sr_wo/closed/print', [LaporanController::class, 'printSrWoClosed'])->name('laporan.sr_wo.closed.print');
        Route::post('/store-sr', [LaporanController::class, 'storeSR'])->name('laporan.store-sr');
        Route::post('/store-wo', [LaporanController::class, 'storeWO'])->name('laporan.store-wo');
        

    });
   

    Route::prefix('daftar-hadir')->name('daftar_hadir.')->group(function () {
        Route::get('/', [DaftarHadirController::class, 'index'])->name('index');
        Route::post('/store-token', [DaftarHadirController::class, 'storeToken'])->name('store-token');
        Route::post('/admin/daftar-hadir/store-token', [DaftarHadirController::class, 'storeToken'])->name('admin.daftar_hadir.store_token');
    });

    Route::prefix('meetings')->group(function () {
        Route::get('/', [AdminMeetingController::class, 'index'])->name('meetings');    
        Route::get('/create', [AdminMeetingController::class, 'create'])->name('meetings.create');
        Route::post('/upload', [AdminMeetingController::class, 'upload'])->name('meetings.upload');
        Route::get('/print', [AdminMeetingController::class, 'print'])->name('meetings.print');
        Route::get('/{meeting}', [AdminMeetingController::class, 'show'])->name('meetings.show');
        Route::get('/export', [AdminMeetingController::class, 'export'])->name('meetings.export');
        Route::get('/user/daily-meeting', [UserController::class, 'dailyMeeting'])->name('user.daily-meeting');
        Route::get('/admin/score-card/data', [AdminMeetingController::class, 'getScoreCardData'])->name('admin.score-card.data');
        Route::get('/admin/score-card/download', [AdminMeetingController::class, 'downloadScoreCard']);
        Route::get('/admin/meetings/print', [AdminMeetingController::class, 'print'])
        ->name('admin.meetings.print');
        Route::get('/admin/meetings/download-pdf', [AdminMeetingController::class, 'downloadPDF'])->name('admin.meetings.download-pdf');
        Route::get('/admin/meetings/download-excel', [AdminMeetingController::class, 'downloadExcel'])->name('admin.meetings.download-excel');
        
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

    Route::get('/machine-monitor', [MachineMonitorController::class, 'index'])->name('machine-monitor');
});


// Tambahkan route group untuk profile
Route::middleware(['auth'])->group(function () {
    // Profile routes
    Route::get('/profile', [UserController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [UserController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [UserController::class, 'destroy'])->name('profile.destroy');
    Route::get('/generate-qrcode', [AttendanceController::class, 'generateQrCode'])->name('generate.qrcode');
    Route::post('/record-attendance', [AttendanceController::class, 'recordAttendance'])->name('record.attendance');
    Route::get('/daftar-hadir', [AttendanceController::class, 'index'])->name('admin.daftar_hadir.index');
    Route::get('/rekapitulasi', [AttendanceController::class, 'rekapitulasi'])->name('admin.daftar_hadir.rekapitulasi');
    
    // Tambahkan route untuk Zoom meeting
    Route::post('/create-zoom-meeting', [ScoreCardDailyController::class, 'createZoomMeeting'])
        ->name('admin.create-zoom-meeting');
});

// Tambahkan route untuk AJAX
Route::get('/admin/machine-monitor/operations', [MachineMonitorController::class, 'getMachineOperations'])
    ->name('admin.machine-monitor.operations');

// Route untuk attendance
Route::prefix('attendance')->group(function () {
    Route::get('/scan/{token}', [AttendanceController::class, 'showScanForm'])->name('attendance.scan-form');
    Route::post('/submit', [AttendanceController::class, 'submitAttendance'])->name('attendance.submit');
});

// Route untuk create Zoom Meeting
//  Route::post('/create-zoom-meeting', [ScoreCardDailyController::class, 'createMeeting'])->name('createZoomMeeting');

Route::post('/create-zoom-meeting', [ScoreCardDailyController::class, 'createZoomMeeting'])
    ->name('create.zoom.meeting')
    ->middleware('web');

Route::get('/admin/pembangkit/report', [PembangkitController::class, 'report'])->name('admin.pembangkit.report');
Route::get('/admin/pembangkit/downloadReport', [PembangkitController::class, 'downloadReport'])->name('admin.pembangkit.downloadReport');
Route::get('/admin/pembangkit/printReport', [PembangkitController::class, 'printReport'])->name('admin.pembangkit.printReport');

Route::get('/admin/machine-monitor/show-all', [MachineMonitorController::class, 'showAll'])
    ->name('admin.machine-monitor.show.all');

Route::middleware(['auth'])->group(function () {
    // Prefix admin untuk semua route admin
    Route::prefix('admin')->name('admin.')->group(function () {
        // Route untuk Zoom Meeting
        Route::post('/create-zoom-meeting', [ScoreCardDailyController::class, 'createZoomMeeting'])
            ->name('create-zoom-meeting');

        // Route untuk Daftar Hadir
        Route::prefix('daftar-hadir')->name('daftar_hadir.')->group(function () {
            Route::post('/store-token', [DaftarHadirController::class, 'storeToken'])
                ->name('store_token');
        });

        // Route Score Card lainnya
        Route::resource('score-card', ScoreCardDailyController::class);
    });
});

Route::prefix('admin')->middleware(['auth'])->group(function () {
    // Pastikan route ini ada dan benar
    Route::get('/score-card/data', [AdminMeetingController::class, 'getScoreCardData'])
        ->name('admin.score-card.data');
});

// Route untuk attendance (tanpa middleware auth)
Route::prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/scan/{token}', [AttendanceController::class, 'showScanForm'])
        ->name('scan-form')
        ->where('token', '.*')
        ->withoutMiddleware(['auth']);
        
    Route::post('/submit', [AttendanceController::class, 'submitAttendance'])
        ->name('submit')
        ->withoutMiddleware(['auth']);
});

// Route untuk admin (dengan middleware auth)
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Route untuk laporan
    Route::post('/laporan/sr/{id}/update-status', [LaporanController::class, 'updateSRStatus'])->name('laporan.update-sr-status');
    Route::post('/laporan/wo/{id}/update-status', [LaporanController::class, 'updateWOStatus'])->name('laporan.update-wo-status');
    
    // Route untuk daftar hadir
    Route::prefix('daftar-hadir')->name('daftar_hadir.')->group(function () {
        Route::get('/', [DaftarHadirController::class, 'index'])->name('index');
        Route::get('/generate-qr', [DaftarHadirController::class, 'generateQRCode'])->name('generate_qr');
        Route::post('/store-token', [DaftarHadirController::class, 'storeToken'])->name('store_token');
    });
});

Route::get('/attendance/scan/{token}', [AttendanceController::class, 'scan'])->name('attendance.scan');
Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');
Route::get('/attendance/generate-qr', [AttendanceController::class, 'generateQRCode'])->name('attendance.generate-qr');

Route::get('/attendance/success', [AttendanceController::class, 'success'])->name('attendance.success');

Route::get('/admin/laporan/create-sr', [LaporanController::class, 'createSR'])->name('admin.laporan.create-sr');

// Rute untuk halaman SR/WO
Route::get('/admin/laporan/sr-wo', [LaporanController::class, 'srWo'])->name('admin.laporan.sr_wo');

// Rute untuk halaman tambah SR
Route::get('/admin/laporan/create-sr', [LaporanController::class, 'createSR'])->name('admin.laporan.create-sr');

// Rute untuk menyimpan SR
Route::post('/admin/laporan/store-sr', [LaporanController::class, 'storeSR'])->name('admin.laporan.store-sr');

Route::resource('wo_backlog', WoBacklogController::class);

Route::post('/admin/laporan/store-wo-backlog', [LaporanController::class, 'storeWOBacklog'])->name('admin.laporan.store-wo-backlog');

Route::get('/admin/laporan/create-wo-backlog', [LaporanController::class, 'createWOBacklog'])->name('admin.laporan.create-wo-backlog');

Route::get('/admin/laporan/create-wo', [LaporanController::class, 'createWO'])->name('admin.laporan.create-wo');

Route::post('/admin/laporan/store-wo', [LaporanController::class, 'storeWO'])->name('admin.laporan.store-wo');

Route::get('/attendance/signature/{id}', [AttendanceController::class, 'showSignature'])->name('attendance.signature');



Route::get('/dashboard-pemantauan', [DashboardPemantauanController::class, 'index'])
    ->name('dashboard.pemantauan');

Route::get('/admin/laporan/wo-backlog/{id}/edit', [LaporanController::class, 'editWoBacklog'])->name('admin.laporan.edit-wo-backlog');
Route::put('/admin/laporan/wo-backlog/{id}', [LaporanController::class, 'updateWoBacklog'])->name('admin.laporan.update-wo-backlog');

Route::post('/admin/laporan/wo-backlog/{id}/status', [LaporanController::class, 'updateBacklogStatus'])->name('admin.laporan.update-backlog-status');

Route::delete('/admin/machine-monitor/{id}', [MachineMonitorController::class, 'destroy'])
    ->name('admin.machine-monitor.destroy');

Route::get('/accumulation-data/{markerId}', [HomeController::class, 'getAccumulationData']);

Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        // Power Plants routes
        Route::get('/power-plants', [PowerPlantController::class, 'index'])->name('power-plants.index');
        Route::get('/power-plants/{id}/edit', [PowerPlantController::class, 'edit'])->name('power-plants.edit');
        Route::put('/power-plants/{id}', [PowerPlantController::class, 'update'])->name('power-plants.update');
        Route::delete('/power-plants/{id}', [PowerPlantController::class, 'destroy'])->name('power-plants.destroy');
        Route::get('/power-plants/create', [PowerPlantController::class, 'create'])->name('power-plants.create');
        Route::post('/power-plants', [PowerPlantController::class, 'store'])->name('power-plants.store');
    });
});

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});

// Other Discussions Routes
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    Route::get('/other-discussions', [OtherDiscussionController::class, 'index'])
        ->name('other-discussions.index');
    Route::get('/other-discussions/create', [OtherDiscussionController::class, 'create'])
        ->name('other-discussions.create');
    Route::post('/other-discussions', [OtherDiscussionController::class, 'store'])
        ->name('other-discussions.store');
    Route::get('/other-discussions/{id}/edit', [OtherDiscussionController::class, 'edit'])
        ->name('other-discussions.edit');
    Route::put('/other-discussions/{id}', [OtherDiscussionEditController::class, 'update'])
        ->name('admin.other-discussions.update');
    Route::delete('/other-discussions/{id}', [OtherDiscussionController::class, 'destroy'])
        ->name('other-discussions.destroy');
    Route::post('/other-discussions/{id}/update-status', [OtherDiscussionController::class, 'updateStatus'])
        ->name('other-discussions.update-status');
});

// Route untuk overdue discussions
Route::delete('/admin/overdue-discussions/{discussion}', [OverdueDiscussionController::class, 'destroy'])
     ->name('admin.overdue-discussions.destroy');
Route::post('/admin/overdue-discussions/{discussion}/update-status', [OverdueDiscussionController::class, 'updateStatus'])
     ->name('admin.overdue-discussions.update-status');

Route::post('/admin/peserta/update', [PesertaController::class, 'update'])->name('admin.peserta.update');

Route::prefix('admin/laporan')->group(function () {
    // Route yang sudah ada
    Route::get('/sr-wo', [LaporanController::class, 'srWo'])->name('admin.laporan.sr_wo');
    
    // Route baru untuk halaman manage
    Route::get('/manage', [LaporanController::class, 'manage'])->name('admin.laporan.manage');
    
    // Route untuk delete
    Route::delete('/sr/{id}', [LaporanController::class, 'destroySR'])->name('admin.laporan.sr.destroy');
    Route::delete('/wo/{id}', [LaporanController::class, 'destroyWO'])->name('admin.laporan.wo.destroy');
    Route::delete('/backlog/{id}', [LaporanController::class, 'destroyBacklog'])->name('admin.laporan.backlog.destroy');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/manage', [LaporanController::class, 'manage'])->name('manage');
        // Perbaikan route delete
        Route::delete('/delete/{type}/{id}', [LaporanDeleteController::class, 'destroy'])
            ->name('delete')
            ->where(['type' => 'sr|wo|backlog']);
    });
});

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    // ... route lainnya ...
    
    // Route khusus untuk delete
    Route::delete('/laporan/delete/{type}/{id}', [LaporanDeleteController::class, 'destroy'])
        ->name('laporan.delete')
        ->where(['type' => 'sr|wo|backlog']);
});

Route::get('/get-mothballed-machines', [DashboardPemantauanController::class, 'getMothballedMachines']);

Route::get('/get-maintenance-machines', [DashboardPemantauanController::class, 'getMaintenanceMachines']);

Route::prefix('admin/pembangkit')->middleware(['auth'])->group(function () {
    Route::post('/save-status', [PembangkitController::class, 'saveStatus'])
        ->name('admin.pembangkit.save-status');
});

Route::post('/admin/pembangkit/reset-status', [PembangkitController::class, 'resetStatus'])
    ->name('admin.pembangkit.reset-status');

Route::get('/admin/pembangkit/ready', [PembangkitController::class, 'ready'])->name('admin.pembangkit.ready');

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Route yang sudah ada
    Route::get('/pembangkit/ready', [PembangkitController::class, 'ready'])->name('pembangkit.ready');
    Route::post('/pembangkit/save-status', [PembangkitController::class, 'saveStatus'])->name('pembangkit.save-status');
    Route::get('/pembangkit/get-status', [PembangkitController::class, 'getStatus'])->name('pembangkit.get-status');
    
    // Tambahkan route baru untuk search
    Route::get('/pembangkit/search', [PembangkitController::class, 'searchMachines'])->name('pembangkit.search');
});

Route::middleware(['auth', 'admin'])->group(function () {
    // ... other admin routes ...
    
    // Backlog routes
    Route::get('/admin/laporan/create-backlog', [App\Http\Controllers\Admin\LaporanController::class, 'createBacklog'])
        ->name('admin.laporan.create-backlog');
    Route::post('/admin/laporan/store-backlog', [App\Http\Controllers\Admin\LaporanController::class, 'storeBacklog'])
        ->name('admin.laporan.store-backlog');
});

Route::middleware(['auth'])->group(function () {
    // ... route lainnya ...
    
    Route::get('/admin/machine-status/view', [MachineStatusViewController::class, 'index'])
         ->name('admin.machine-status.view');
});

Route::get('/admin/machine-status/view', [MachineStatusController::class, 'view'])
    ->name('admin.machine-status.view')
    ->middleware(['auth', 'json.response']);

Route::delete('/admin/discussions/{id}', [OtherDiscussionController::class, 'destroy'])
    ->name('admin.discussions.destroy');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('other-discussions', OtherDiscussionController::class)
        ->names([
            'index' => 'other-discussions.index',
            'create' => 'other-discussions.create',
            'store' => 'other-discussions.store',
            'edit' => 'other-discussions.edit',
            'update' => 'other-discussions.update',
            'destroy' => 'other-discussions.destroy'
        ]);
        
    // Tambahkan route untuk print dan export
    Route::get('other-discussions/print', [OtherDiscussionController::class, 'print'])
        ->name('other-discussions.print');
    
    Route::get('other-discussions/export/{format}', [OtherDiscussionController::class, 'export'])
        ->name('other-discussions.export')
        ->where('format', 'xlsx|pdf');
        
    Route::post('other-discussions/update-status', [OtherDiscussionController::class, 'updateStatus'])
        ->name('other-discussions.update-status');
});

Route::delete('/admin/overdue-discussions/{id}', [OtherDiscussionController::class, 'destroyOverdue'])
    ->name('admin.overdue-discussions.destroy');

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin'], 'as' => 'admin.'], function () {
    Route::get('machine-status/view', [MachineStatusController::class, 'view'])->name('machine-status.view');
});

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Route yang sudah ada
    Route::delete('/overdue-discussions/{id}', [OverdueDiscussionController::class, 'destroy'])
        ->name('overdue-discussions.destroy');
    Route::post('/overdue-discussions/{id}/update-status', [OverdueDiscussionController::class, 'updateStatus'])
        ->name('overdue-discussions.update-status');
    
    // Tambahkan route baru untuk pengecekan overdue
    Route::post('/overdue-discussions/check', [OverdueDiscussionController::class, 'checkAndMoveOverdue'])
        ->name('overdue-discussions.check')
        ->middleware('web');
});

Route::post('/other-discussions/update-status', [OtherDiscussionController::class, 'updateStatus'])
    ->name('admin.other-discussions.update-status');

Route::get('/admin/other-discussions/{id}/edit', [OtherDiscussionEditController::class, 'edit'])
    ->name('admin.other-discussions.edit');

Route::put('/admin/other-discussions/{id}', [OtherDiscussionEditController::class, 'update'])
    ->name('admin.other-discussions.update');

Route::resource('other-discussions', OtherDiscussionController::class)
    ->except(['edit', 'update']);

Route::post('other-discussions/update-status', [OtherDiscussionController::class, 'updateStatus'])
    ->name('admin.other-discussions.update-status');

Route::group(['prefix' => 'admin', 'middleware' => ['auth'], 'as' => 'admin.'], function () {
    // Pastikan namespace lengkap
    Route::get('other-discussions/{id}/edit', [OtherDiscussionEditController::class, 'edit'])
        ->name('other-discussions.edit');
    Route::put('other-discussions/{id}', [OtherDiscussionEditController::class, 'update'])
        ->name('other-discussions.update');
});

// Tambahkan route fallback untuk debugging
Route::fallback(function () {
    return response()->json(['error' => 'Route tidak ditemukan'], 404);
});

Route::get('/generate-no-pembahasan', [App\Http\Controllers\Admin\OtherDiscussionController::class, 'generateNoPembahasan'])->name('generate.no-pembahasan');

// Tambahkan route untuk generate nomor pembahasan
Route::post('/admin/other-discussions/generate-no-pembahasan', [App\Http\Controllers\Admin\OtherDiscussionController::class, 'generateNoPembahasan'])
    ->name('admin.other-discussions.generate-no-pembahasan')
    ->middleware(['auth']);

Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {
    // Other routes...
    Route::post('/other-discussions/generate-no-pembahasan', [
        App\Http\Controllers\Admin\OtherDiscussionController::class, 
        'generateNoPembahasan'
    ])->name('admin.other-discussions.generate-no-pembahasan');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/public/attendance/generate-qr', [AttendanceController::class, 'generateQRCode'])
        ->name('attendance.generate-qr');
    Route::get('/public/attendance/scan/{token}', [AttendanceController::class, 'scan'])
        ->name('attendance.scan');
});

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // ... route lainnya ...
    
    Route::get('other-discussions/show', [OtherDiscussionController::class, 'show'])
        ->name('other-discussions.show');
    
    Route::get('other-discussions/{id}', [OtherDiscussionController::class, 'show_single'])
        ->name('other-discussions.show_single');
});

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('other-discussions/export/xlsx', [OtherDiscussionController::class, 'exportExcel'])
        ->name('other-discussions.export.xlsx');
});

Route::get('/admin/other-discussions/{id}/print', [OtherDiscussionController::class, 'printSingle'])->name('admin.other-discussions.print.single');
Route::get('/admin/other-discussions/{id}/export/{format}', [OtherDiscussionController::class, 'exportSingle'])->name('admin.other-discussions.export.single');

Route::get('/admin/pembangkit/load-data', [PembangkitController::class, 'loadData'])
    ->name('admin.pembangkit.load-data');

Route::get('/admin/pembangkit/get-status', [PembangkitController::class, 'getStatus'])
    ->name('admin.pembangkit.get-status');



    
        
