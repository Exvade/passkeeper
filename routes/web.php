<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialAuthController; // Pakai controller yang benar
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PinController;
use App\Http\Controllers\AuthController; // Jika logout masih pakai ini

// 1. HALAMAN DEPAN (LOGIN)
Route::get('/', function () {
    return view('login'); // Pastikan file 'login.blade.php' ada
})->name('login');

// 2. AUTH GOOGLE (Login & Register)
Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirectToProvider'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleProviderCallback']);

// 3. LOGOUT
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');


// --- AREA YANG BUTUH LOGIN ---
Route::middleware('auth')->group(function () {

    // A. ROUTE PIN (TIDAK BOLEH KENA MIDDLEWARE PIN)
    // Supaya tidak looping (redirect berulang-ulang)
    
    // Verifikasi PIN (Lock Screen)
    Route::get('/locked', [PinController::class, 'showVerifyForm'])->name('pin.verify');
    Route::post('/locked', [PinController::class, 'verify'])->name('pin.check');

    // Buat PIN Baru (Setup)
    Route::get('/setup-pin', [PinController::class, 'showSetupForm'])->name('pin.setup'); // Konsisten pakai .setup
    Route::post('/setup-pin', [PinController::class, 'setup'])->name('pin.store');


    // B. ROUTE DASHBOARD (DILINDUNGI PIN)
    // Hanya bisa diakses kalau sudah login DAN sudah masukkan PIN
    Route::middleware('pin.verified')->group(function () {

        Route::get('/dashboard', [PasswordController::class, 'index'])->name('dashboard');

        // CRUD Password
        Route::post('/passwords', [PasswordController::class, 'store'])->name('passwords.store');
        Route::put('/passwords/{id}', [PasswordController::class, 'update'])->name('passwords.update');
        Route::delete('/passwords/{id}', [PasswordController::class, 'destroy'])->name('passwords.destroy');
        Route::get('/passwords/{id}/decrypt', [PasswordController::class, 'decrypt'])->name('passwords.decrypt');
        Route::patch('/passwords/{id}/favorite', [PasswordController::class, 'toggleFavorite'])->name('passwords.favorite');
        Route::get('/export', [PasswordController::class, 'export'])->name('passwords.export');

        // Hapus Akun Google (Unlink)
        Route::delete('/social-accounts/{id}', [SocialAuthController::class, 'destroy'])->name('social-accounts.destroy');
    });

});