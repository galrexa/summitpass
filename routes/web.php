<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Pendaki\PendakiController;
use App\Http\Controllers\Pendaki\BookingController as PendakiBookingController;
use App\Http\Controllers\Admin\AdminWebController;
use App\Http\Controllers\Admin\MountainWebController;
use App\Http\Controllers\Admin\BookingWebController;
use App\Http\Controllers\Admin\PaymentWebController;
use App\Http\Controllers\Admin\UserWebController;
use App\Http\Controllers\Admin\MonitoringWebController;
use App\Http\Controllers\Admin\SettingsWebController;
use App\Http\Controllers\Admin\SimulateScanController;
use App\Http\Controllers\Admin\TrekkingMapController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'webLogin']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'webRegister']);
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
});

Route::post('/logout', [AuthController::class, 'webLogout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard.index'))->name('dashboard');
    Route::get('/home', fn () => view('dashboard.index'))->name('home');
    Route::get('/profile/setup', [ProfileController::class, 'showSetup'])->name('profile.setup');
    Route::post('/profile/setup', [ProfileController::class, 'saveSetup'])->name('profile.setup.save');

    Route::prefix('my')->name('pendaki.')->middleware('role:pendaki')->group(function () {
        Route::get('/bookings',           [PendakiBookingController::class, 'index'])->name('bookings');
        Route::get('/bookings/create',    [PendakiBookingController::class, 'create'])->name('bookings.create');
        Route::post('/bookings',          [PendakiBookingController::class, 'store'])->name('bookings.store');
        Route::get('/bookings/{id}',      [PendakiBookingController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{id}/pay', [PendakiBookingController::class, 'simulatePay'])->name('bookings.pay');
        Route::get('/jejak-summit',       [PendakiController::class, 'jejakSummit'])->name('jejak-summit');
        Route::get('/trekking-log',       [PendakiController::class, 'trekkingLog'])->name('trekking-log');
        Route::get('/pass',               [PendakiController::class, 'myPass'])->name('my-pass');
        Route::get('/profile',            [PendakiController::class, 'profile'])->name('profile');
        Route::get('/settings',           [PendakiController::class, 'settings'])->name('settings');
    });

    Route::get('/api/mountains/{mountainId}/trails', [PendakiBookingController::class, 'trails'])
        ->name('api.mountains.trails');
});

// Admin & Pengelola
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,pengelola_tn'])->group(function () {
    Route::get('/', [AdminWebController::class, 'dashboard'])->name('dashboard');

    // Mountains — create must be before {id} to avoid conflict
    Route::get('/mountains',           [MountainWebController::class, 'index'])->name('mountains.index');
    Route::get('/mountains/create',    [MountainWebController::class, 'create'])->name('mountains.create');
    Route::post('/mountains',          [MountainWebController::class, 'store'])->name('mountains.store');
    Route::get('/mountains/{id}',      [MountainWebController::class, 'show'])->name('mountains.show');
    Route::get('/mountains/{id}/edit', [MountainWebController::class, 'edit'])->name('mountains.edit');
    Route::put('/mountains/{id}',      [MountainWebController::class, 'update'])->name('mountains.update');
    Route::delete('/mountains/{id}',   [MountainWebController::class, 'destroy'])->name('mountains.destroy');
    Route::post('/mountains/{mountainId}/trails', [MountainWebController::class, 'storeTrail'])->name('mountains.trails.store');
    Route::put('/mountains/{mountainId}/trails/{trailId}', [MountainWebController::class, 'updateTrail'])->name('mountains.trails.update');
    Route::delete('/mountains/{mountainId}/trails/{trailId}', [MountainWebController::class, 'destroyTrail'])->name('mountains.trails.destroy');
    Route::post('/mountains/{mountainId}/trails/{trailId}/checkpoints', [MountainWebController::class, 'storeCheckpoint'])->name('mountains.checkpoints.store');
    Route::put('/mountains/{mountainId}/trails/{trailId}/checkpoints/{checkpointId}', [MountainWebController::class, 'updateCheckpoint'])->name('mountains.checkpoints.update');
    Route::delete('/mountains/{mountainId}/trails/{trailId}/checkpoints/{checkpointId}', [MountainWebController::class, 'destroyCheckpoint'])->name('mountains.checkpoints.destroy');

    // Bookings
    Route::get('/bookings',                       [BookingWebController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}',                  [BookingWebController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{id}/cancel',          [BookingWebController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{id}/confirm-payment', [BookingWebController::class, 'confirmPayment'])->name('bookings.confirm-payment');

    // Payments
    Route::get('/payments', [PaymentWebController::class, 'index'])->name('payments.index');

    // Monitoring & Map
    Route::get('/monitoring',                          [MonitoringWebController::class, 'index'])->name('monitoring.index');
    Route::get('/trekking-map',                        [TrekkingMapController::class, 'index'])->name('trekking-map.index');
    Route::get('/trekking-map/data',                   [TrekkingMapController::class, 'data'])->name('trekking-map.data');
    Route::get('/trekking-map/trails/{mountainId}',    [TrekkingMapController::class, 'trails'])->name('trekking-map.trails');

    // Simulate scan — accessible by admin & pengelola_tn
    Route::get('/simulate/scan',     [SimulateScanController::class, 'index'])->name('simulate.scan');
    Route::post('/simulate/resolve', [SimulateScanController::class, 'resolve'])->name('simulate.resolve');
    Route::post('/simulate/record',  [SimulateScanController::class, 'record'])->name('simulate.record');

    // Anomaly check — accessible by admin & pengelola_tn
    Route::post('/settings/run-anomaly-check', [SettingsWebController::class, 'runAnomalyCheck'])->name('settings.run-anomaly-check');

    // Admin-only
    Route::middleware('role:admin')->group(function () {
        Route::get('/users',             [UserWebController::class, 'index'])->name('users.index');
        Route::get('/users/create',      [UserWebController::class, 'create'])->name('users.create');
        Route::post('/users',            [UserWebController::class, 'store'])->name('users.store');
        Route::get('/users/{id}',        [UserWebController::class, 'show'])->name('users.show');
        Route::patch('/users/{id}/role', [UserWebController::class, 'updateRole'])->name('users.update-role');
        Route::delete('/users/{id}',     [UserWebController::class, 'destroy'])->name('users.destroy');

        Route::get('/settings',                    [SettingsWebController::class, 'index'])->name('settings.index');
        Route::post('/settings/{key}',             [SettingsWebController::class, 'update'])->name('settings.update');
    });
});
