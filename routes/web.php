<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordController;


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
    Route::get('/dashboard', [PasswordController::class, 'index'])->name('dashboard');
    Route::post('/passwords', [PasswordController::class, 'store'])->name('passwords.store');
    Route::get('/passwords/{id}/decrypt', [PasswordController::class, 'decrypt'])->name('passwords.decrypt');
    Route::delete('/passwords/{id}', [PasswordController::class, 'destroy'])->name('passwords.destroy');
});