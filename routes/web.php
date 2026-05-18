<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ─── Públicas ───
Route::get('/', fn () => view('home'))->name('home');
Route::get('/catalog', function () {
    $events = json_decode(file_get_contents(resource_path('mocks/events.json')), true);
    return view('catalog', compact('events'));
})->name('catalog');

Route::get('/events/{id}', function ($id) {
    $events = json_decode(file_get_contents(resource_path('mocks/events.json')), true);
    $event  = collect($events['upcoming'])->firstWhere('id', (int) $id);
    abort_if(!$event, 404);
    return view('events.show', compact('event'));
})->name('events.show');

Route::get('/events/{id}/seats', function ($id) {
    $events = json_decode(file_get_contents(resource_path('mocks/events.json')), true);
    $event  = collect($events['upcoming'])->firstWhere('id', (int) $id);
    abort_if(!$event, 404);
    return view('events.seats', compact('event'));
})->name('events.seats');

// ─── Auth ───
Route::get('/login',     [AuthController::class, 'login'])->name('login');
Route::post('/login',    [AuthController::class, 'auth'])->name('auth.attempt');
Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout',   [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Protegidas ───
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard.index'))->name('dashboard');

    Route::get('/dashboard/tickets', function () {
        $tickets = json_decode(file_get_contents(resource_path('mocks/tickets.json')), true);
        return view('dashboard.tickets', compact('tickets'));
    })->name('dashboard.tickets');
});