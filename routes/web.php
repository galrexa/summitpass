<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminWebController;
use App\Http\Controllers\Admin\MountainWebController;
use App\Http\Controllers\Admin\BookingWebController;
use App\Http\Controllers\Admin\PaymentWebController;
use App\Http\Controllers\Admin\UserWebController;
use App\Http\Controllers\Admin\MonitoringWebController;
use App\Http\Controllers\Admin\SettingsWebController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Auth (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'webLogin']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'webRegister']);

    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
});

// Logout
Route::post('/logout', [AuthController::class, 'webLogout'])->name('logout')->middleware('auth');

// Protected
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard.index'))->name('dashboard');
    Route::get('/home', fn () => view('dashboard.index'))->name('home');
});

// Admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,pengelola_tn'])->group(function () {
    Route::get('/', [AdminWebController::class, 'dashboard'])->name('dashboard');

    // Gunung & Jalur
    Route::get('/mountains', [MountainWebController::class, 'index'])->name('mountains.index');
    Route::get('/mountains/create', [MountainWebController::class, 'create'])->name('mountains.create');
    Route::post('/mountains', [MountainWebController::class, 'store'])->name('mountains.store');
    Route::get('/mountains/{id}', [MountainWebController::class, 'show'])->name('mountains.show');
    Route::get('/mountains/{id}/edit', [MountainWebController::class, 'edit'])->name('mountains.edit');
    Route::put('/mountains/{id}', [MountainWebController::class, 'update'])->name('mountains.update');
    Route::delete('/mountains/{id}', [MountainWebController::class, 'destroy'])->name('mountains.destroy');
    // Trail
    Route::post('/mountains/{mountainId}/trails', [MountainWebController::class, 'storeTrail'])->name('mountains.trails.store');
    Route::put('/mountains/{mountainId}/trails/{trailId}', [MountainWebController::class, 'updateTrail'])->name('mountains.trails.update');
    Route::delete('/mountains/{mountainId}/trails/{trailId}', [MountainWebController::class, 'destroyTrail'])->name('mountains.trails.destroy');
    // Checkpoint
    Route::post('/mountains/{mountainId}/trails/{trailId}/checkpoints', [MountainWebController::class, 'storeCheckpoint'])->name('mountains.checkpoints.store');
    Route::delete('/mountains/{mountainId}/trails/{trailId}/checkpoints/{checkpointId}', [MountainWebController::class, 'destroyCheckpoint'])->name('mountains.checkpoints.destroy');

    // Booking
    Route::get('/bookings', [BookingWebController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}', [BookingWebController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{id}/cancel', [BookingWebController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{id}/confirm-payment', [BookingWebController::class, 'confirmPayment'])->name('bookings.confirm-payment');

    // Pembayaran
    Route::get('/payments', [PaymentWebController::class, 'index'])->name('payments.index');

    // Pengguna
    Route::get('/users', [UserWebController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserWebController::class, 'create'])->name('users.create');
    Route::post('/users', [UserWebController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [UserWebController::class, 'show'])->name('users.show');
    Route::patch('/users/{id}/role', [UserWebController::class, 'updateRole'])->name('users.update-role');
    Route::delete('/users/{id}', [UserWebController::class, 'destroy'])->name('users.destroy');

    // Monitoring
    Route::get('/monitoring', [MonitoringWebController::class, 'index'])->name('monitoring.index');

    // Settings
    Route::get('/settings', [SettingsWebController::class, 'index'])->name('settings.index');
    Route::post('/settings/run-anomaly-check', [SettingsWebController::class, 'runAnomalyCheck'])->name('settings.run-anomaly-check');
    Route::post('/settings/{key}', [SettingsWebController::class, 'update'])->name('settings.update');
});
