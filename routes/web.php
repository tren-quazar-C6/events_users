<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Services\EventService;

// ─── Públicas ───
Route::get('/', fn () => view('home'))->name('home');
Route::get('/catalog', fn () => view('catalog'))->name('catalog');

Route::get('/events/{slug}', function ($slug) {
    $event = app(EventService::class)->findBySlug($slug);

    abort_if(! $event, 404);

    return view('event-detail', compact('event'));
})->name('events.show');

// ─── Auth ───
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'auth'])->name('auth.attempt');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Protegidas ───
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard.index'))->name('dashboard');
    Route::get('/dashboard/tickets', fn () => view('dashboard.tickets'))->name('dashboard.tickets');
    Route::get('/dashboard/history', fn () => view('dashboard.history'))->name('dashboard.history');

    Route::get('/dashboard/profile',  [\App\Http\Controllers\ProfileController::class, 'show'])->name('dashboard.profile');
    Route::patch('/dashboard/profile', [\App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::patch('/dashboard/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/dashboard/favorites', function () {
        $slugs = auth()->user()->favoriteSlugs();

        $favorites = app(EventService::class)
            ->all()
            ->whereIn('slug', $slugs)
            ->values();

        return view('dashboard.favorites', compact('favorites'));
    })->name('dashboard.favorites');
});
