<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MessageController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json(['ok' => true]));
Route::get('/health/db', function () {
    try {
        DB::select('SELECT 1');
        return response()->json([
            'ok' => true,
            'database' => config('database.connections.mysql.database'),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'ok' => false,
            'error' => 'Database connection failed',
            'message' => $e->getMessage(),
        ], 500);
    }
});

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/activate', [AuthController::class, 'activate']);

Route::middleware('api.token')->group(function (): void {
    Route::post('/messages/sync', [MessageController::class, 'sync']);
    Route::post('/account/pause', [AccountController::class, 'pause']);
    Route::post('/account/resume', [AccountController::class, 'resume']);
    Route::get('/account/export', [AccountController::class, 'export']);
    Route::post('/account/delete', [AccountController::class, 'delete']);
});
