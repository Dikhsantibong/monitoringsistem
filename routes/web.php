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
    // use App\Http\Controllers\Admin\MeetingController;
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
 

    Route::get('/', function () {
        return view('homepage');
    });

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/profile', [UserController::class, 'showProfile'])->name('user.profile');
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('user.profile.update');

    Route::middleware(['auth', 'user'])->group(function () {
        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
        Route::get('/daily-meeting', [UserController::class, 'dailyMeeting'])->name('daily.meeting');
        Route::get('/monitoring', [UserController::class, 'monitoring'])->name('monitoring');
        Route::get('/documentation', [UserController::class, 'documentation'])->name('documentation');
        Route::get('/support', [UserController::class, 'support'])->name('support');
        Route::get('/user/profile', [UserController::class, 'profile'])->name('user.profile')->middleware('auth');
        Route::get('/user/machine-monitor', [UserController::class, 'machineMonitor'])->name('user.machine.monitor')->middleware('auth');
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
        
        Route::prefix('users')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->name('users');
            Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
            Route::put('/{user}', [AdminUserController::class, 'update'])->name('users.update');
            Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
            Route::get('/create', [AdminUserController::class, 'create'])->name('users.create');
            Route::post('/store', [AdminUserController::class, 'store'])->name('users.store');
            Route::get('/{user}/delete', [AdminUserController::class, 'delete'])->name('users.delete');
        });
        
        Route::prefix('meetings')->group(function () {
            Route::get('/', [AdminMeetingController::class, 'index'])->name('meetings');
            Route::get('/{meeting}', [AdminMeetingController::class, 'show'])->name('meetings.show');
            Route::get('/export', [AdminMeetingController::class, 'export'])->name('meetings.export');
            Route::post('/upload', [AdminMeetingController::class, 'upload'])->name('admin.meetings.upload');
        });
        
        Route::prefix('activities')->group(function () {
            Route::get('/export', [ActivityController::class, 'export'])->name('activities.export');
        });
        
        Route::prefix('settings')->group(function () {
            Route::get('/', [AdminSettingController::class, 'index'])->name('settings');
            Route::post('/', [AdminSettingController::class, 'update'])->name('settings.update');
            Route::post('/regenerate-api-key', [AdminSettingController::class, 'regenerateApiKey'])->name('settings.regenerate-api-key');
        });
    });

    Route::get('/test', function () {
        return 'Test Route';
    });

    Route::get('/admin/pembangkit/ready', [PembangkitController::class, 'ready'])->name('admin.pembangkit.ready');
    Route::get('/admin/laporan/sr_wo', [LaporanController::class, 'srWo'])->name('admin.laporan.sr_wo');
    Route::get('/admin/daftar-hadir', [DaftarHadirController::class, 'index'])->name('admin.daftar_hadir.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/admin/pembangkit/ready', [PowerPlantController::class, 'ready'])->name('admin.pembangkit.ready');
    Route::get('/admin/daftar_hadir', [AttendanceController::class, 'index'])->name('admin.daftar_hadir.index');
    Route::post('/admin/meetings/upload', [MeetingController::class, 'upload'])->name('admin.meetings.upload');