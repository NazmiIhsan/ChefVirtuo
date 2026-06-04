<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/auth/firebase/session', [AuthController::class, 'storeSession'])->name('auth.firebase.session');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('lecturer')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
