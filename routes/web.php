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

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::resource('/machines', MachineController::class)->middleware('admin');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'index'])->name('user.dashboard');
});

Route::get('/test', function () {
    return 'Test Route';
});
