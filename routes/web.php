<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Route Halaman Depan (Landing Page)
Route::get('/', function () {
    // Jika user sudah login, arahkan ke dashboard. Jika belum, tampilkan landing page.
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('landing'); // Memanggil file resources/views/landing.blade.php
})->name('home');

// Route Dashboard (Dilindungi login)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::patch('/actuator/toggle', [DashboardController::class, 'toggleActuator'])->name('actuator.toggle');
    // TAMBAHKAN BARIS INI UNTUK AI CHAT
    Route::post('/ai/chat', [DashboardController::class, 'aiChat'])->name('ai.chat');
});

require __DIR__.'/auth.php';