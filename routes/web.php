<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\TicketController;
use App\Models\EstadoTicket;
use App\Models\Evento;
use App\Models\EventoAsiento;
use App\Models\Ticket;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', fn () => view('home'))->name('home');
Route::get('/catalog', fn () => view('catalog'))->name('catalog');

Route::get('/events/{slug}', function ($slug) {
    $evento = Evento::with('tipo')->where('slug', $slug)->where('activo', true)->firstOrFail();

    $event = [
        'id'           => $evento->id,
        'slug'         => $evento->slug,
        'title'        => $evento->nombre_evento,
        'category'     => $evento->tipo->nombre_tipo ?? '',
        'author'       => $evento->author,
        'duration'     => $evento->duration,
        'synopsis'     => $evento->synopsis ?? [],
        'poster_color' => $evento->poster_color,
        'price_from'   => $evento->price_from,
        'venue'        => $evento->venue,
        'city'         => $evento->city,
        'dates'        => [[
            'dow'   => Carbon::parse($evento->fecha_evento)->locale('es')->isoFormat('ddd'),
            'day'   => Carbon::parse($evento->fecha_evento)->day,
            'month' => Carbon::parse($evento->fecha_evento)->locale('es')->isoFormat('MMM'),
        ]],
        'times'  => [Carbon::parse($evento->fecha_evento)->format('H:i')],
        'price'  => number_format($evento->price_from, 0, ',', '.'),
        'showtimes' => [[
            'date' => Carbon::parse($evento->fecha_evento)->format('Y-m-d'),
            'time' => Carbon::parse($evento->fecha_evento)->format('H:i'),
        ]],
    ];

    return view('events.show', compact('event'));
})->name('events.show');

// ─── Auth ────────────────────────────────────────────────────────────────────
Route::get('/login',     [AuthController::class, 'login'])->name('login');
Route::post('/login',    [AuthController::class, 'auth'])->name('auth.attempt');
Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout',   [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Protegidas ──────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard.index'))->name('dashboard');

    // ─── Dashboard sub-páginas ───────────────────────────────────────────
    Route::get('/dashboard/tickets', function () {
        $user = Auth::user();

        $estadoConfirmado = EstadoTicket::where('nombre_estado', 'CONFIRMADO')->value('id');
        $estadoUsado      = EstadoTicket::where('nombre_estado', 'USADO')->value('id');

        $ventaIds = Venta::where('user_id', $user->id)->pluck('id');

        $upcoming = Ticket::whereIn('venta_id', $ventaIds)
            ->where('estado_ticket_id', $estadoConfirmado)
            ->with(['venta', 'eventoAsiento.asiento.zona', 'eventoAsiento.evento'])
            ->latest()
            ->get();

        $past = Ticket::whereIn('venta_id', $ventaIds)
            ->where('estado_ticket_id', $estadoUsado)
            ->with(['venta', 'eventoAsiento.asiento.zona', 'eventoAsiento.evento'])
            ->latest()
            ->get();

        return view('dashboard.tickets', compact('upcoming', 'past'));
    })->name('dashboard.tickets');

    Route::get('/dashboard/history',   fn () => view('dashboard.history'))->name('dashboard.history');
    Route::get('/dashboard/favorites', fn () => view('dashboard.favorites'))->name('dashboard.favorites');

    Route::get('/dashboard/profile',           [ProfileController::class, 'show'])->name('dashboard.profile');
    Route::patch('/dashboard/profile',          [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::patch('/dashboard/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // ─── Mapa de asientos ────────────────────────────────────────────────
    Route::get('/events/{slug}/seats', function ($slug) {
        $evento = Evento::where('slug', $slug)->where('activo', true)->firstOrFail();

        $eventoAsientos = EventoAsiento::where('evento_id', $evento->id)
            ->with(['asiento.zona'])
            ->get();

        // Liberar reservas expiradas antes de mostrar el mapa
        $eventoAsientos->each(fn ($ea) => $ea->isDisponible());
        $eventoAsientos = EventoAsiento::where('evento_id', $evento->id)
            ->with(['asiento.zona'])
            ->get();

        $seatRows = $eventoAsientos
            ->groupBy(fn ($ea) => $ea->asiento->fila)
            ->sortKeys()
            ->map(fn ($seats, $fila) => [
                'label'   => $fila,
                'section' => $seats->first()->asiento->zona->nombre_zona,
                'seats'   => $seats->sortBy('asiento.numero')->map(fn ($ea) => [
                    'id'      => $ea->id,
                    'n'       => $ea->asiento->numero,
                    's'       => match ($ea->estado) {
                        'DISPONIBLE' => 'a',
                        'RESERVADO', 'VENDIDO' => 'o',
                        'BLOQUEADO' => 'b',
                        default     => 'a',
                    },
                    'precio'  => (float) $ea->precio,
                    'section' => $ea->asiento->zona->nombre_zona,
                ])->values()->all(),
            ])->values()->all();

        $event = [
            'id'        => $evento->id,
            'slug'      => $evento->slug,
            'title'     => $evento->nombre_evento,
            'venue'     => $evento->venue,
            'city'      => $evento->city,
            'price_from'=> $evento->price_from,
            'showtimes' => [[
                'date' => Carbon::parse($evento->fecha_evento)->format('Y-m-d'),
                'time' => Carbon::parse($evento->fecha_evento)->format('H:i'),
            ]],
        ];

        return view('events.seats', compact('event', 'seatRows'));
    })->name('events.seats');

    // ─── Flujo de compra ─────────────────────────────────────────────────
    Route::post('/events/{slug}/checkout',          [PurchaseController::class, 'initCheckout'])->name('checkout.init');
    Route::get('/checkout/{token}',                 [PurchaseController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/{token}/confirm',        [PurchaseController::class, 'confirmCheckout'])->name('checkout.confirm');
    Route::get('/purchase/{reference}/confirmation', [PurchaseController::class, 'confirmation'])->name('purchase.confirmation');

    // ─── QR de ticket ────────────────────────────────────────────────────
    Route::get('/tickets/{code}/qr', [TicketController::class, 'qr'])->name('tickets.qr');
});
