<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PinController;


// Halaman Depan (Login)
Route::get('/', function () {
    return view('login');
})->name('login');

// Route Google
Route::get('/auth/google', [AuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'callback'])->name('google.callback');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard (Protected)
Route::middleware('auth')->group(function () {

    // 1. Route PIN (Tidak kena middleware PIN, supaya ga looping)
    Route::get('/locked', [PinController::class, 'showVerifyForm'])->name('pin.verify');
    Route::post('/locked', [PinController::class, 'verify'])->name('pin.check');

    Route::get('/setup-pin', [PinController::class, 'showSetupForm'])->name('pin.create');
    Route::post('/setup-pin', [PinController::class, 'setup'])->name('pin.store');

    // 2. Route Dashboard & Password (DILINDUNGI PIN)
    Route::middleware(\App\Http\Middleware\EnsurePinIsVerified::class)->group(function () {

        Route::get('/dashboard', [PasswordController::class, 'index'])->name('dashboard');

        // ... Semua route password lainnya (store, delete, export, dll) masukkan ke sini ...
        Route::post('/passwords', [PasswordController::class, 'store'])->name('passwords.store');
        Route::put('/passwords/{id}', [PasswordController::class, 'update'])->name('passwords.update');
        Route::delete('/passwords/{id}', [PasswordController::class, 'destroy'])->name('passwords.destroy');
        Route::get('/passwords/{id}/decrypt', [PasswordController::class, 'decrypt'])->name('passwords.decrypt');
        Route::patch('/passwords/{id}/favorite', [PasswordController::class, 'toggleFavorite'])->name('passwords.favorite');
        Route::get('/export', [PasswordController::class, 'export'])->name('passwords.export');

        // Route Link Akun (Nanti kita buat)
        // Route::post('/link-account', ...);
    });

});