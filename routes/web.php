<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ─── Públicas ───
Route::get('/', function () {
    return view('welcome');
});

// ─── Auth ───
// GET muestra el form. POST procesa las credenciales.
// El name('login') es crítico: route('login') lo usa, y el middleware 'auth'
// redirige aquí cuando alguien intenta entrar sin sesión.
Route::get('/login',  [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'auth'])->name('auth.attempt');

// Logout siempre va por POST (nunca GET) para evitar CSRF.
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ─── Protegidas (requieren sesión activa) ───
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});