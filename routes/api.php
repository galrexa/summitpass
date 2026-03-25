<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MountainController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\CheckpointController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

Route::prefix('v1')->group(function () {
    
    // Authentication Routes (Public)
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        
        // Auth
        Route::get('/auth/profile', [AuthController::class, 'profile']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);
        Route::put('/auth/profile', [AuthController::class, 'updateProfile']);

        // Mountains (Public viewing)
        Route::get('/mountains', [MountainController::class, 'index']);
        Route::get('/mountains/{id}', [MountainController::class, 'show']);
        Route::get('/mountains/{mountainId}/basecamps', [MountainController::class, 'getBasecamps']);
        Route::get('/mountains/{mountainId}/checkpoints', [MountainController::class, 'getCheckpoints']);

        // Mountains Admin
        Route::post('/mountains', [MountainController::class, 'store']);
        Route::put('/mountains/{id}', [MountainController::class, 'update']);
        Route::delete('/mountains/{id}', [MountainController::class, 'destroy']);

        // Bookings
        Route::get('/bookings', [BookingController::class, 'index']);
        Route::get('/bookings/{id}', [BookingController::class, 'show']);
        Route::post('/bookings', [BookingController::class, 'store']);
        Route::delete('/bookings/{id}/cancel', [BookingController::class, 'cancel']);

        // Booking Helpers
        Route::get('/mountains/{mountainId}/available-dates', [BookingController::class, 'getAvailableDates']);
        Route::get('/mountains/{mountainId}/operators', [BookingController::class, 'getOperators']);

        // Trips (Active Trips)
        Route::get('/trips', [TripController::class, 'index']);
        Route::get('/trips/{id}', [TripController::class, 'show']);
        Route::get('/trips/{id}/checkpoints', [TripController::class, 'getCheckpoints']);
        Route::get('/trips/{id}/logs', [TripController::class, 'getCheckpointLogs']);

        // Checkpoints
        Route::post('/checkpoints/log', [CheckpointController::class, 'logCheckpoint']);
        Route::get('/checkpoints/log/{id}', [CheckpointController::class, 'show']);
    });
});

// Health check (for monitoring)
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now(),
    ]);
});