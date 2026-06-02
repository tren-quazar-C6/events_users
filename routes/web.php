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
use App\Services\EventService;
use App\Services\PurchaseFlowService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;


Route::get('/', function () {
    try {
        $events = Evento::with(['tipo', 'imagenes'])
            ->where('activo', true)
            ->whereNotNull('slug')
            ->where('slug', '<>', '')
            ->orderBy('fecha_evento')
            ->take(6)
            ->get();
    } catch (\Throwable $exception) {
        Log::warning('Home events unavailable from DB.', [
            'message' => $exception->getMessage(),
        ]);

        $events = collect();
    }

    return view('home', compact('events'));
})->name('home');
Route::get('/catalog', fn () => view('catalog'))->name('catalog');

Route::get('/events/{slug}', function ($slug) {
    $apiEvent = app(EventService::class)->findBySlug($slug);

    if ($apiEvent) {
        $showtime = $apiEvent['showtimes'][0] ?? ['date' => now()->toDateString(), 'time' => '00:00'];
        $date = Carbon::parse($showtime['date'].' '.$showtime['time']);

        $event = [
            'id'           => $apiEvent['id'],
            'slug'         => $apiEvent['slug'],
            'title'        => $apiEvent['title'],
            'category'     => $apiEvent['category'],
            'author'       => 'Tickify',
            'duration'     => 'Por confirmar',
            'synopsis'     => filled($apiEvent['synopsis']) ? [$apiEvent['synopsis']] : ['Información del evento por confirmar.'],
            'poster_color' => $apiEvent['poster_color'],
            'image_url'    => $apiEvent['image_url'],
            'price_from'   => $apiEvent['price_from'],
            'venue'        => 'Por confirmar',
            'city'         => '',
            'dates'        => [[
                'dow'   => $date->locale('es')->isoFormat('ddd'),
                'day'   => $date->day,
                'month' => $date->locale('es')->isoFormat('MMM'),
            ]],
            'times'        => [$date->format('H:i')],
            'price'        => $apiEvent['price_from'] > 0 ? number_format($apiEvent['price_from'], 0, ',', '.') : 'Por confirmar',
            'showtimes'    => $apiEvent['showtimes'],
        ];

        return view('events.show', compact('event'));
    }

    $evento = Evento::with('tipo')->where('slug', $slug)->where('activo', true)->firstOrFail();

    $event = [
        'id' => $evento->id,
        'slug' => $evento->slug,
        'title' => $evento->nombre_evento,
        'category' => $evento->tipo->nombre_tipo ?? '',
        'author' => $evento->author,
        'duration' => $evento->duration,
        'synopsis' => $evento->synopsis ?? [],
        'poster_color' => $evento->poster_color,
        'image_url' => null,
        'price_from' => $evento->price_from,
        'venue' => $evento->venue,
        'city' => $evento->city,
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
        $hasSalesTables = Schema::hasTable('ventas') && Schema::hasTable('tickets') && Schema::hasTable('estado_tickets');
        $cachedTickets = app(PurchaseFlowService::class)->ticketsForUser($user->id);
        $user           = Auth::user();
        $hasSalesTables = Schema::hasTable('VENTAS')
            && Schema::hasTable('TICKETS')
            && Schema::hasTable('ESTADO_TICKET');

        if (! $hasSalesTables) {
            $tickets = app(PurchaseFlowService::class)->ticketsForUser($user->id);

        if (! $hasSalesTables) {
            $tickets = app(PurchaseFlowService::class)->ticketsForUser($user->id);

        if ($hasSalesTables) {
            $estadoConfirmado = EstadoTicket::where('nombre_estado', 'CONFIRMADO')->value('id');
            $estadoUsado      = EstadoTicket::where('nombre_estado', 'USADO')->value('id');
            return view('dashboard.tickets', [
                'upcoming' => $tickets->filter(fn ($t) => $t->eventoAsiento?->evento?->fecha_evento?->isFuture())->values(),
                'past'     => $tickets->filter(fn ($t) => $t->eventoAsiento?->evento?->fecha_evento?->isPast())->values(),
            ]);
        }

        // Encontrar el id_usuario correspondiente en la tabla USUARIO del VPS
        $usuarioId = DB::table('USUARIO')->where('email', $user->email)->value('id_usuario');

        if (! $usuarioId) {
            return view('dashboard.tickets', ['upcoming' => collect(), 'past' => collect()]);
        }

            $ventaIds = Venta::where('user_id', $user->id)->pluck('id');
        $ventaIds = DB::table('VENTAS')->where('id_usuario', $usuarioId)->pluck('id_venta');

            $upcoming = Ticket::whereIn('venta_id', $ventaIds)
                ->where('estado_ticket_id', $estadoConfirmado)
                ->with(['venta', 'eventoAsiento.asiento.zona', 'eventoAsiento.evento'])
                ->latest()
                ->get();
        // Estado 2 = PAGADO (activo), Estado 3 = USADO (pasado)
        $upcoming = Ticket::whereIn('id_venta', $ventaIds)
            ->where('id_estado_ticket', 2)
            ->with(['eventoAsiento.evento', 'eventoAsiento.asiento', 'estadoTicket'])
            ->orderByDesc('fecha_generacion')
            ->get();

            $past = Ticket::whereIn('venta_id', $ventaIds)
                ->where('estado_ticket_id', $estadoUsado)
                ->with(['venta', 'eventoAsiento.asiento.zona', 'eventoAsiento.evento'])
                ->latest()
                ->get();
        }

        // Encontrar el id_usuario correspondiente en la tabla USUARIO del VPS
        $usuarioId = DB::table('USUARIO')->where('email', $user->email)->value('id_usuario');

        $past = $past
            ->concat($cachedTickets->filter(fn ($ticket) => $ticket->eventoAsiento?->evento?->fecha_evento?->isPast()))
            ->unique('codigo_unico')
            ->values();
        $past = Ticket::whereIn('id_venta', $ventaIds)
            ->where('id_estado_ticket', 3)
            ->with(['eventoAsiento.evento', 'eventoAsiento.asiento', 'estadoTicket'])
            ->orderByDesc('fecha_generacion')
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
        $apiEvent = app(EventService::class)->findBySlug($slug);

        if ($apiEvent) {
            $apiSeats = app(EventService::class)->seatsForEvent((int) $apiEvent['id']);

            $seatRows = $apiSeats
                ->groupBy('row')
                ->sortKeys()
                ->map(fn ($seats, $row) => [
                    'label' => $row,
                    'section' => $seats->first()['zone'],
                    'seats' => $seats->sortBy('number')->map(fn ($seat) => [
                        'id' => $seat['id'],
                        'n' => $seat['number'],
                        's' => match ($seat['status']) {
                            'DISPONIBLE' => 'a',
                            'RESERVADO', 'VENDIDO' => 'o',
                            'BLOQUEADO' => 'b',
                            default => 'a',
                        },
                        'precio' => $seat['price'],
                        'section' => $seat['zone'],
                    ])->values()->all(),
                ])->values()->all();

            $showtime = $apiEvent['showtimes'][0] ?? ['date' => now()->toDateString(), 'time' => '00:00'];
            $event = [
                'id' => $apiEvent['id'],
                'slug' => $apiEvent['slug'],
                'title' => $apiEvent['title'],
                'venue' => 'Por confirmar',
                'city' => '',
                'price_from' => $apiEvent['price_from'],
                'showtimes' => [$showtime],
            ];

            return view('events.seats', compact('event', 'seatRows'));
        }

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
