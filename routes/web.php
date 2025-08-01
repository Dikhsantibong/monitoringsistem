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
use App\Http\Controllers\Admin\PasswordVerificationController;
use App\Http\Controllers\NotulenController;
use App\Http\Controllers\Api\NotulenAttendanceController;
use App\Http\Controllers\Api\NotulenDraftController;
use App\Http\Controllers\Api\NotulenFileController;
use App\Http\Controllers\KinerjaPemeliharaanController;

Route::get('/', [HomeController::class, 'index'])->name('homepage');

// Update notulen routes to use controller
Route::get('/notulen', [NotulenController::class, 'form'])->name('notulen.form');
Route::get('/notulen/search', [NotulenController::class, 'search'])->name('notulen.search');
Route::get('/notulen/{notulen}/edit', [NotulenController::class, 'edit'])->name('notulen.edit');
Route::put('/notulen/{notulen}', [NotulenController::class, 'update'])->name('notulen.update');
Route::get('/notulen/create', [NotulenController::class, 'create'])->name('notulen.create');
Route::post('/notulen', [NotulenController::class, 'store'])->name('notulen.store');
Route::get('/notulen/{notulen}', [NotulenController::class, 'show'])->name('notulen.show');
Route::get('/notulen/{notulen}/print-pdf', [NotulenController::class, 'printPdf'])->name('notulen.print-pdf');
Route::get('/notulen/{notulen}/download-zip', [NotulenController::class, 'downloadZip'])->name('notulen.download-zip');


// Remove incorrect draft routes
// Route::get('/notulen/drafts', [NotulenDraftController::class, 'save'])->name('notulen.drafts');
// Route::get('/notulen/drafts/list', [NotulenDraftController::class, 'list'])->name('notulen.drafts.list');

// Add API routes for drafts
Route::prefix('api')->group(function () {
    Route::get('/notulen-draft/list', [NotulenDraftController::class, 'list']);
    Route::post('/notulen-draft/save', [NotulenDraftController::class, 'save']);
    Route::get('/notulen-draft/load/{tempNotulenId}', [NotulenDraftController::class, 'load']);
    Route::delete('/notulen-draft/delete/{tempNotulenId}', [NotulenDraftController::class, 'delete']);
});


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
        Route::post('/upload-image', [PembangkitController::class, 'uploadImage'])->name('admin.pembangkit.upload-image');
        Route::delete('/delete-image', [PembangkitController::class, 'deleteImage'])->name('admin.pembangkit.delete-image');
        Route::get('/get-images/{machineId}', [PembangkitController::class, 'getImages'])->name('admin.pembangkit.get-images');
        Route::get('/get-last-dmn/{machineId}', [PembangkitController::class, 'getLastDmn'])->name('admin.pembangkit.get-last-dmn');
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
        Route::get('/print', [AttendanceController::class, 'printView'])
            ->name('admin.daftar_hadir.print');
    });

    Route::prefix('meetings')->group(function () {
        Route::get('/', [AdminMeetingController::class, 'index'])->name('meetings');
        Route::get('/create', [AdminMeetingController::class, 'create'])->name('meetings.create');
        Route::post('/upload', [AdminMeetingController::class, 'upload'])->name('meetings.upload');
        Route::get('/print', [AdminMeetingController::class, 'print'])->name('meetings.print');
        Route::get('/download-pdf', [AdminMeetingController::class, 'downloadPDF'])->name('meetings.download-pdf');
        Route::get('/{meeting}', [AdminMeetingController::class, 'show'])->name('meetings.show');
        Route::get('/export', [AdminMeetingController::class, 'export'])->name('meetings.export');
        Route::get('/user/daily-meeting', [UserController::class, 'dailyMeeting'])->name('user.daily-meeting');
        Route::get('/admin/score-card/data', [AdminMeetingController::class, 'getScoreCardData'])->name('admin.score-card.data');
        Route::get('/admin/score-card/download', [AdminMeetingController::class, 'downloadScoreCard']);
        Route::get('/admin/meetings/print', [AdminMeetingController::class, 'print'])
        ->name('admin.meetings.print');
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

    // Route untuk attendance
    Route::get('/attendance/error', function() {
        return view('admin.daftar_hadir.error');
    })->name('attendance.error');

    Route::get('/attendance/success', function() {
        return view('admin.daftar_hadir.success');
    })->name('attendance.success');
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

        // Route untuk laporan
        Route::get('/laporan/sr-wo', [LaporanController::class, 'srWo'])->name('laporan.sr_wo');
        Route::get('/laporan/create-sr', [LaporanController::class, 'createSR'])->name('laporan.create-sr');
        Route::get('/laporan/create-wo', [LaporanController::class, 'createWO'])->name('laporan.create-wo');
        Route::post('/laporan/store-sr', [LaporanController::class, 'storeSR'])->name('laporan.store-sr');
        Route::post('/laporan/store-wo', [LaporanController::class, 'storeWO'])->name('laporan.store-wo');

        // Route untuk update status
        Route::post('/laporan/update-sr-status/{id}', [LaporanController::class, 'updateSRStatus'])
            ->name('laporan.update-sr-status');
        Route::post('/laporan/update-wo-status/{id}', [LaporanController::class, 'updateWOStatus'])
            ->name('laporan.update-wo-status');

        // Route untuk WO Backlog
        Route::get('/laporan/create-wo-backlog', [LaporanController::class, 'createWOBacklog'])
            ->name('laporan.create-wo-backlog');
        Route::post('/laporan/store-wo-backlog', [LaporanController::class, 'storeWOBacklog'])
            ->name('laporan.store-wo-backlog');
        Route::get('/laporan/edit-wo-backlog/{id}', [LaporanController::class, 'editWoBacklog'])
            ->name('laporan.edit-wo-backlog');
        Route::put('/laporan/wo-backlog/{id}', [LaporanController::class, 'updateWoBacklog'])
            ->name('laporan.update-wo-backlog');
        Route::post('/laporan/update-backlog-status/{id}', [LaporanController::class, 'updateBacklogStatus'])
            ->name('laporan.update-backlog-status');
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
    Route::post('/laporan/update-sr-status/{id}', [LaporanController::class, 'updateSRStatus'])
        ->name('admin.laporan.update-sr-status');
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

Route::get('/accumulation-data/{markerId}', [HomeController::class, 'getAccumulationData'])->name('getAccumulationData');
Route::get('/get-engine-issues/{markerId}', [HomeController::class, 'getEngineIssues'])->name('getEngineIssues');

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

Route::prefix('admin')->middleware(['auth'])->group(function () {
    // Pembangkit routes
    Route::prefix('pembangkit')->name('pembangkit.')->group(function () {
        Route::get('/ready', [PembangkitController::class, 'ready'])->name('ready');
        Route::post('/save-status', [PembangkitController::class, 'saveStatus'])->name('save-status');
        Route::get('/get-status', [PembangkitController::class, 'getStatus'])->name('get-status');
        Route::get('/status-history', [PembangkitController::class, 'getStatusHistory'])->name('status-history');
        Route::get('/report', [PembangkitController::class, 'report'])->name('report');
        Route::get('/report/download', [PembangkitController::class, 'downloadReport'])->name('report.download');
        Route::get('/report/print', [PembangkitController::class, 'printReport'])->name('report.print');

        // Image routes
        Route::post('/upload-image', [PembangkitController::class, 'uploadImage'])->name('upload-image');
        Route::delete('/delete-image/{machineId}', [PembangkitController::class, 'deleteImage'])->name('delete-image');
        Route::get('/check-image/{machineId}', [PembangkitController::class, 'checkImage'])->name('check-image');
    });
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

Route::get('/get-plant-chart-data/{plantId}', [HomeController::class, 'getPlantChartData'])->name('plant.chart.data');

Route::get('other-discussions/{id}/download-document',
    [OtherDiscussionController::class, 'downloadDocument'])
    ->name('admin.other-discussions.download-document');

Route::post('/other-discussions/{id}/remove-file', [OtherDiscussionEditController::class, 'removeFile'])
    ->name('admin.other-discussions.remove-file');

Route::middleware(['auth', 'web'])->prefix('admin')->group(function () {
    // Verifikasi password
    Route::post('/verify-password', [PasswordVerificationController::class, 'verify'])
        ->name('verify-password');

    // Route lainnya...
    Route::delete('/other-discussions/{discussion}/remove-file/{index}',
        [OtherDiscussionController::class, 'removeFile']);
    Route::delete('/other-discussions/{discussion}/commitments/{commitment}',
        [OtherDiscussionController::class, 'removeCommitment']);
});

// Verifikasi password
Route::post('/admin/verify-password', [App\Http\Controllers\Admin\PasswordVerificationController::class, 'verify'])
    ->name('admin.verify-password');

// Hapus file
Route::delete('/admin/other-discussions/{discussion}/remove-file/{index}', [App\Http\Controllers\Admin\OtherDiscussionController::class, 'removeFile'])
    ->name('admin.other-discussions.remove-file');

// Route untuk hapus commitment
Route::delete('/admin/other-discussions/{discussion}/commitments/{commitment}',
    [App\Http\Controllers\Admin\OtherDiscussionController::class, 'removeCommitment'])
    ->name('admin.other-discussions.remove-commitment');

// Tambahkan di akhir file, sebelum route fallback
Route::middleware(['auth'])->group(function () {
    // Update status routes
    Route::post('/admin/laporan/update-sr-status/{id}', [App\Http\Controllers\Admin\LaporanController::class, 'updateSRStatus'])
        ->name('admin.laporan.update-sr-status');
    Route::post('/admin/laporan/update-wo-status/{id}', [App\Http\Controllers\Admin\LaporanController::class, 'updateWOStatus'])
        ->name('admin.laporan.update-wo-status');
});

// Route fallback untuk debugging
Route::fallback(function () {
    return response()->json(['error' => 'Route tidak ditemukan'], 404);
});

Route::middleware(['auth', 'web'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::post('/verify-password', [PasswordVerificationController::class, 'verify'])
            ->name('verify-password');
        Route::resource('other-discussions', OtherDiscussionController::class);
    });
});

Route::post('/admin/laporan/verify-delete', [LaporanController::class, 'verifyPasswordAndDelete'])
    ->name('admin.laporan.verify-delete');

// Tambahkan route berikut di dalam group admin
Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {
    // ... existing routes ...

    // Routes untuk handle image
    Route::post('/pembangkit/upload-image', [PembangkitController::class, 'uploadImage'])->name('admin.pembangkit.upload-image');
    Route::delete('/pembangkit/delete-image', [PembangkitController::class, 'deleteImage'])->name('admin.pembangkit.delete-image');
    Route::get('/pembangkit/get-images/{machineId}', [PembangkitController::class, 'getImages'])->name('admin.pembangkit.get-images');
});

Route::post('/admin/laporan/move-to-backlog/{id}', [LaporanController::class, 'moveToBacklog'])
    ->name('admin.laporan.move-to-backlog');

Route::get('/admin/laporan/edit-wo/{id}', [LaporanController::class, 'editWO'])
    ->name('admin.laporan.edit-wo');
Route::post('/admin/laporan/update-wo/{id}', [LaporanController::class, 'updateWO'])
    ->name('admin.laporan.update-wo');

    Route::get('/admin/laporan/download-document/{id}', [LaporanController::class, 'downloadDocument'])
        ->name('admin.laporan.download-document');

Route::prefix('admin/daftar-hadir')->group(function () {
    Route::get('/export-excel', [AttendanceController::class, 'exportExcel'])
        ->name('admin.daftar_hadir.export-excel');
    Route::get('/export-pdf', [AttendanceController::class, 'exportPDF'])
        ->name('admin.daftar_hadir.export-pdf');
});

Route::prefix('admin')->middleware(['auth'])->group(function () {
    // Route yang sudah ada
    Route::get('/daftar-hadir/rekapitulasi', [AttendanceController::class, 'rekapitulasi'])
        ->name('admin.daftar_hadir.rekapitulasi');

    // Tambahkan route untuk export dan print
    Route::get('/daftar-hadir/export-excel', [AttendanceController::class, 'exportExcel'])
        ->name('admin.daftar_hadir.export-excel');

    Route::get('/daftar-hadir/export-pdf', [AttendanceController::class, 'exportPDF'])
        ->name('admin.daftar_hadir.export-pdf');

    // Route baru untuk print
    Route::get('/daftar-hadir/print', [AttendanceController::class, 'printView'])
        ->name('admin.daftar_hadir.print');
});

Route::get('/admin/laporan/print/{type}', [LaporanController::class, 'print'])
    ->name('admin.laporan.print')
    ->middleware(['auth']);

Route::post('/admin/daftar-hadir/backdate', [AttendanceController::class, 'storeBackdate'])
    ->name('admin.daftar_hadir.backdate');

Route::get('/admin/laporan/download-backlog-document/{no_wo}', [LaporanController::class, 'downloadBacklogDocument'])
    ->name('admin.laporan.download-backlog-document');

Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        // ... existing routes ...

        // Daftar Hadir routes
        Route::prefix('daftar-hadir')->name('daftar_hadir.')->group(function () {
            Route::get('/rekapitulasi', [AttendanceController::class, 'rekapitulasi'])->name('rekapitulasi');
            Route::post('/generate-backdate-token', [AttendanceController::class, 'generateBackdateToken'])->name('generate-backdate-token');
            Route::get('/export-excel', [AttendanceController::class, 'exportExcel'])->name('export-excel');
            Route::get('/export-pdf', [AttendanceController::class, 'exportPDF'])->name('export-pdf');
            Route::get('/print', [AttendanceController::class, 'printView'])->name('print');
        });
    });
});



Route::get('/monitoring-data/{period}', [HomeController::class, 'getMonitoringData'])
    ->name('monitoring.data')
    ->where('period', 'daily|weekly|monthly');

Route::post('/attendance/scan-qr', [AttendanceController::class, 'scanQR'])->name('attendance.scan-qr');

Route::get('/admin/meetings/_table', [MeetingController::class, 'table'])
    ->name('admin.meetings._table');

Route::prefix('admin/machine-status')->name('admin.machine-status.')->middleware(['auth'])->group(function () {
    Route::get('/', [MachineStatusController::class, 'view'])->name('view');
    Route::get('/{machineId}/edit/{logId}', [MachineStatusController::class, 'edit'])->name('edit');
    Route::put('/{machineId}/update/{logId}', [MachineStatusController::class, 'update'])->name('update');
    Route::delete('/{machineId}/destroy/{logId}', [MachineStatusController::class, 'destroy'])->name('destroy');
});

// Notulen Attendance Routes
Route::get('/notulen-attendance/{tempNotulenId}', [App\Http\Controllers\Api\NotulenAttendanceController::class, 'showAttendanceForm'])->name('notulen.attendance.form');
Route::post('/api/notulen-attendance', [App\Http\Controllers\Api\NotulenAttendanceController::class, 'store'])->name('notulen.attendance.store');
Route::get('/notulen/late-attendance/{notulen}', [App\Http\Controllers\Api\NotulenAttendanceController::class, 'showLateAttendanceForm'])->name('notulen.late-attendance.form');
Route::post('public/api/notulen/{notulen}/late-attendance', [App\Http\Controllers\Api\NotulenAttendanceController::class, 'storeLateAttendance'])->name('notulen.late-attendance.store');
Route::get('/notulen-documentation/{tempNotulenId}', [App\Http\Controllers\Api\NotulenDocumentationController::class, 'showDocumentationForm'])->name('notulen.documentation.form');
Route::post('public/api/notulen-documentation', [App\Http\Controllers\Api\NotulenDocumentationController::class, 'store'])->name('notulen.documentation.store');
Route::post('public/api/notulen-documentation/{id}', [App\Http\Controllers\Api\NotulenDocumentationController::class, 'store'])->name('notulen.documentation.store');
Route::post('public/api/notulen-file', [App\Http\Controllers\Api\NotulenFileController::class, 'store'])->name('notulen.file.store');

Route::delete('public/api/notulen-file/{id}', [App\Http\Controllers\Api\NotulenFileController::class, 'destroy']);
Route::delete('/api/notulen-documentation/{id}', [App\Http\Controllers\Api\NotulenDocumentationController::class, 'destroy']);
Route::delete('/api/notulen-file/{id}', [App\Http\Controllers\Api\NotulenFileController::class, 'destroy']);

Route::post('/api/notulen-documentation', [App\Http\Controllers\Api\NotulenDocumentationController::class, 'store'])->name('notulen.documentation.store');
Route::post('/api/notulen-documentation/{id}', [App\Http\Controllers\Api\NotulenDocumentationController::class, 'store'])->name('notulen.documentation.store');
Route::post('/api/notulen-file', [App\Http\Controllers\Api\NotulenFileController::class, 'store'])->name('notulen.file.store');
Route::delete('/api/notulen-file/{id}', [App\Http\Controllers\Api\NotulenFileController::class, 'destroy']);

Route::post('/api/notulen-draft/save', [App\Http\Controllers\Api\NotulenDraftController::class, 'save'])->name('notulen.draft.save');
Route::get('/api/notulen-draft/load/{tempNotulenId}', [App\Http\Controllers\Api\NotulenDraftController::class, 'load'])->name('notulen.draft.load');
Route::delete('/api/notulen-draft/delete/{tempNotulenId}', [App\Http\Controllers\Api\NotulenDraftController::class, 'delete'])->name('notulen.draft.delete');
Route::post('/api/notulen-file', [App\Http\Controllers\Api\NotulenFileController::class, 'store'])->name('notulen.file.store');


Route::get('/calendar', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');

// Route::post('public/api/notulen-documentation', [App\Http\Controllers\Api\NotulenDocumentationController::class, 'store'])->name('notulen.documentation.store');
// Route::post('public/api/notulen-documentation/{id}', [App\Http\Controllers\Api\NotulenDocumentationController::class, 'store'])->name('notulen.documentation.store');

// Add new routes for edit mode
Route::post('/api/notulen/{notulen}/documentation', [App\Http\Controllers\Api\NotulenDocumentationController::class, 'storeForExisting'])->name('notulen.documentation.store-existing');
Route::post('/api/notulen/{notulen}/file', [App\Http\Controllers\Api\NotulenFileController::class, 'storeForExisting'])->name('notulen.file.store-existing');

Route::post('/api/notulen-paste-image', [NotulenController::class, 'uploadPastedImage'])
    ->name('notulen.paste-image');

Route::get('/kinerja-pemeliharaan', [KinerjaPemeliharaanController::class, 'index'])->name('kinerja.pemeliharaan');
