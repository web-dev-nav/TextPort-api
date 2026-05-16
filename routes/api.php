<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MessageController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json(['ok' => true]));

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('api.token')->group(function (): void {
    Route::post('/messages/sync', [MessageController::class, 'sync']);
    Route::post('/account/pause', [AccountController::class, 'pause']);
    Route::post('/account/resume', [AccountController::class, 'resume']);
    Route::get('/account/export', [AccountController::class, 'export']);
    Route::post('/account/delete', [AccountController::class, 'delete']);
});
