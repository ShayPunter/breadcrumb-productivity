<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\ProductivitySessionController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\UserSettingsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [MarketingController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('stats', [StatsController::class, 'index'])->name('stats');

    // Productivity sessions API
    Route::post('sessions', [ProductivitySessionController::class, 'store'])->name('sessions.store');
    Route::get('sessions', [ProductivitySessionController::class, 'index'])->name('sessions.index');

    // User settings
    Route::get('settings/preferences', [UserSettingsController::class, 'edit'])->name('settings.preferences');
    Route::put('settings/preferences', [UserSettingsController::class, 'update'])->name('settings.preferences.update');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
