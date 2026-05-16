<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('admin.login.form'));

Route::prefix('admin')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    Route::middleware('admin')->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    });
});
