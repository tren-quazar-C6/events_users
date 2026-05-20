<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\TicketController;

// ─── Públicas ───
Route::get('/', fn () => view('home'))->name('home');
Route::get('/catalog', fn () => view('catalog'))->name('catalog');

Route::get('/events/{slug}', function ($slug) {
    $events = json_decode(file_get_contents(database_path('mocks/events.json')), true);
    $event  = collect($events)->firstWhere('slug', $slug);
    abort_if(!$event, 404);
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
    Route::get('/dashboard', fn () => view('dashboard.index'))->name('dashboard');

    // ─── Dashboard sub-páginas ───
    Route::get('/dashboard/tickets', function () {
        $upcoming = Auth::user()->tickets()->where('status', 'confirmed')->with('purchase')->latest()->get();
        $past     = Auth::user()->tickets()->where('status', 'used')->with('purchase')->latest()->get();
        return view('dashboard.tickets', compact('upcoming', 'past'));
    })->name('dashboard.tickets');

    Route::get('/dashboard/history', fn () => view('dashboard.history'))->name('dashboard.history');

    Route::get('/dashboard/profile',          [ProfileController::class, 'show'])->name('dashboard.profile');
    Route::patch('/dashboard/profile',         [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::patch('/dashboard/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // ─── Mapa de asientos ───
    Route::get('/events/{slug}/seats', function ($slug) {
        $events = json_decode(file_get_contents(database_path('mocks/events.json')), true);
        $event  = collect($events)->firstWhere('slug', $slug);
        abort_if(!$event, 404);
        return view('events.seats', compact('event'));
    })->name('events.seats');

    // ─── Flujo de compra ───
    Route::post('/events/{slug}/checkout',         [PurchaseController::class, 'initCheckout'])->name('checkout.init');
    Route::get('/checkout/{token}',                [PurchaseController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/{token}/confirm',       [PurchaseController::class, 'confirmCheckout'])->name('checkout.confirm');
    Route::get('/purchase/{reference}/confirmation', [PurchaseController::class, 'confirmation'])->name('purchase.confirmation');

    // ─── QR de ticket ───
    Route::get('/tickets/{code}/qr', [TicketController::class, 'qr'])->name('tickets.qr');
});
