<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MountainController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TrekkingLogController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QrPassController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\AdminUserController;

/*
|--------------------------------------------------------------------------
| API Routes - SummitPass v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ── Auth (Public) ────────────────────────────────────────────────────
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login',    [AuthController::class, 'login']);

    // ── Protected ────────────────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::get('/auth/profile',  [AuthController::class, 'profile']);
        Route::post('/auth/logout',  [AuthController::class, 'logout']);
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);
        Route::put('/auth/profile',  [AuthController::class, 'updateProfile']);

        // ── Mountains (semua role) ────────────────────────────────────────
        Route::get('/mountains',      [MountainController::class, 'index']);
        Route::get('/mountains/{id}', [MountainController::class, 'show']);

        // Jalur & pos per gunung
        Route::get('/mountains/{mountainId}/trails',                                   [MountainController::class, 'getTrails']);
        Route::get('/mountains/{mountainId}/trails/{trailId}/checkpoints',             [MountainController::class, 'getCheckpoints']);
        Route::get('/mountains/{mountainId}/trails/{trailId}/available-dates',         [BookingController::class, 'getAvailableDates']);

        // ── Bookings (SIMAKSI Digital) ────────────────────────────────────
        Route::get('/bookings',              [BookingController::class, 'index']);
        Route::get('/bookings/{id}',         [BookingController::class, 'show']);
        Route::post('/bookings',             [BookingController::class, 'store']);
        Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);

        // ── Payment (Langkah 5 — dummy) ───────────────────────────────────
        Route::post('/bookings/{id}/payment',         [PaymentController::class, 'initiate']);
        Route::post('/bookings/{id}/payment/confirm', [PaymentController::class, 'confirmDummy']);
        Route::get('/bookings/{id}/payment',          [PaymentController::class, 'status']);

        // ── QR Pass (Langkah 4 — klaim & tampil QR) ──────────────────────
        Route::post('/qr-passes/claim',       [QrPassController::class, 'claim']);
        Route::get('/qr-passes',              [QrPassController::class, 'myPasses']);
        Route::get('/qr-passes/{qrToken}',    [QrPassController::class, 'show']);

        // ── Admin only ────────────────────────────────────────────────────
        Route::middleware('role:admin')->group(function () {

            // User Management
            Route::get('/admin/users',             [AdminUserController::class, 'index']);
            Route::post('/admin/users',            [AdminUserController::class, 'store']);
            Route::get('/admin/users/{id}',        [AdminUserController::class, 'show']);
            Route::patch('/admin/users/{id}/role', [AdminUserController::class, 'updateRole']);
            Route::delete('/admin/users/{id}',     [AdminUserController::class, 'destroy']);

            // System Settings & Anomaly
            Route::get('/admin/settings',          [SystemSettingController::class, 'index']);
            Route::put('/admin/settings/{key}',    [SystemSettingController::class, 'update']);
            Route::post('/admin/anomaly/run',      [SystemSettingController::class, 'runAnomalyCheck']);

            // Mountain management (hanya admin)
            Route::post('/mountains',              [MountainController::class, 'store']);
            Route::put('/mountains/{id}',          [MountainController::class, 'update']);
            Route::delete('/mountains/{id}',       [MountainController::class, 'destroy']);
        });

        // ── Trekking Log (Scan QR di pos) ────────────────────────────────
        Route::post('/trekking/scan',              [TrekkingLogController::class, 'scan']);
        Route::get('/trekking/history/{qrToken}',  [TrekkingLogController::class, 'history']);
        Route::get('/trekking/logs/{id}',          [TrekkingLogController::class, 'show']);
    });
});

// Health check
Route::get('/health', fn () => response()->json(['status' => 'healthy', 'timestamp' => now()]));
