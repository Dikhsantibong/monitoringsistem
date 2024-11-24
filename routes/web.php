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

    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

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
        Route::get('/meetings/{meeting}', [AdminMeetingController::class, 'show'])->name('meetings.show');
        Route::get('/meetings/export', [AdminMeetingController::class, 'export'])->name('meetings.export');
        
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/regenerate-api-key', [AdminSettingController::class, 'regenerateApiKey'])
            ->name('settings.regenerate-api-key');
        
        Route::get('/activities/export', [ActivityController::class, 'export'])->name('activities.export');
        
        Route::post('/machine-monitor/machines', [MachineMonitorController::class, 'storeMachine'])
            ->name('machine-monitor.store-machine');
        Route::get('/machine-monitor/machines/{machine}', [MachineMonitorController::class, 'showMachine'])
            ->name('machine-monitor.show-machine');
        Route::put('/machine-monitor/machines/{machine}', [MachineMonitorController::class, 'updateMachine'])
            ->name('machine-monitor.update-machine');
        Route::delete('/machine-monitor/machines/{machine}', [MachineMonitorController::class, 'destroyMachine'])
            ->name('machine-monitor.destroy-machine');
    });

    Route::get('/test', function () {
        return 'Test Route';
    });
