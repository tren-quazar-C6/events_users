<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ─── Públicas ───
Route::get('/', fn () => view('home'))->name('home');
//Route::get('/catalog', fn () => view('catalog'))->name('catalog');
Route::get('/catalog', function () { $events = collect(require resource_path('mock/events.php')); return view('catalog', compact('events')); })->name('catalog');

// ─── Auth ───
Route::get('/login',     [AuthController::class, 'login'])->name('login');
Route::post('/login',    [AuthController::class, 'auth'])->name('auth.attempt');
Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout',   [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Protegidas ───
Route::middleware('auth')->group(function () {
    Route::get('/dashboard',         fn () => view('dashboard.index'))->name('dashboard');
    Route::get('/dashboard/tickets', fn () => view('dashboard.tickets'))->name('dashboard.tickets');
});