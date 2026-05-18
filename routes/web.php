<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ─── Públicas ───
Route::get('/', fn () => view('home'))->name('home');
//Route::get('/catalog', fn () => view('catalog'))->name('catalog');
Route::get('/catalog', function () { $events = collect(require resource_path('mock/events.php')); return view('catalog', compact('events')); })->name('catalog');
Route::get('/events/{slug}', function ($slug) {
    // Lee el JSON y busca el evento por slug
    $events = json_decode(file_get_contents(database_path('mocks/events.json')));
    $event = collect($events)->firstWhere('slug', $slug);

    abort_if(! $event, 404);

    return view('event-detail', compact('event'));
})->name('events.show');

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