<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\TicketController;
use App\Models\Evento;
use App\Models\EventoAsiento;
use App\Models\Ticket;
use App\Models\Venta;
use App\Services\EventService;
use App\Services\PurchaseFlowService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;


Route::get('/', function () {
    try {
        $events = Evento::with('tipo')
            ->where('activo', true)
            ->where('publicado', true)
            ->orderBy('fecha_evento')
            ->take(6)
            ->get();

        $minPrecios = DB::table('EVENTO_ZONA')
            ->whereIn('id_evento', $events->pluck('id_evento'))
            ->select('id_evento', DB::raw('MIN(precio) as min_precio'))
            ->groupBy('id_evento')
            ->pluck('min_precio', 'id_evento');

        $events->each(fn ($e) => $e->price_from = (int) ($minPrecios[$e->id_evento] ?? 0));
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
    // Primero intentar desde la API
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
            'venue'        => $apiEvent['venue'] ?? 'Teatro Quasar',
            'city'         => $apiEvent['city'] ?? 'Medellín',
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

    // Fallback a BD local
    $eventos = Evento::with('tipo')->where('activo', true)->get();
    $evento = $eventos->first(fn ($e) => \Illuminate\Support\Str::slug($e->nombre_evento) === $slug);

    if ($evento) {
        $priceFromDesc = $evento->price_from_description;

        $event = [
            'id'           => $evento->id_evento,
            'slug'         => \Illuminate\Support\Str::slug($evento->nombre_evento),
            'title'        => $evento->nombre_evento,
            'category'     => $evento->tipo->nombre_tipo ?? '',
            'author'       => 'Tickify',
            'duration'     => 'Por confirmar',
            'synopsis'     => filled($evento->descripcion) ? [$evento->descripcion] : ['Información del evento por confirmar.'],
            'poster_color' => $evento->poster_color ?? '#7BB394',
            'image_url'    => $evento->ruta_url,
            'price_from'   => $priceFromDesc > 0 ? $priceFromDesc : 0,
            'venue'        => 'Teatro Quasar',
            'city'         => 'Medellín',
            'dates'        => [[
                'dow'   => Carbon::parse($evento->fecha_evento)->locale('es')->isoFormat('ddd'),
                'day'   => Carbon::parse($evento->fecha_evento)->day,
                'month' => Carbon::parse($evento->fecha_evento)->locale('es')->isoFormat('MMM'),
            ]],
            'times'        => [Carbon::parse($evento->fecha_evento)->format('H:i')],
            'price'        => $priceFromDesc > 0 ? number_format($priceFromDesc, 0, ',', '.') : 'Por confirmar',
            'showtimes'    => [[
                'date' => Carbon::parse($evento->fecha_evento)->format('Y-m-d'),
                'time' => Carbon::parse($evento->fecha_evento)->format('H:i'),
            ]],
        ];

        return view('events.show', compact('event'));
    }

    abort(404, 'Evento no encontrado');
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
        $hasSalesTables = Schema::hasTable('VENTAS') && Schema::hasTable('TICKETS');
        $cachedTickets = app(PurchaseFlowService::class)->ticketsForUser($user->id);

        $upcoming = collect();
        $past = collect();

        if ($hasSalesTables) {
            $usuarioId = DB::table('USUARIO')->where('email', $user->email)->value('id_usuario');

            if ($usuarioId) {
                $ventaIds = Venta::where('id_usuario', $usuarioId)->pluck('id_venta');

                $upcoming = Ticket::whereIn('id_venta', $ventaIds)
                    ->where('id_estado_ticket', 2)
                    ->with(['estadoTicket', 'eventoAsiento.asiento.zona', 'eventoAsiento.evento'])
                    ->latest('fecha_generacion')
                    ->get();

                $past = Ticket::whereIn('id_venta', $ventaIds)
                    ->where('id_estado_ticket', 3)
                    ->with(['estadoTicket', 'eventoAsiento.asiento.zona', 'eventoAsiento.evento'])
                    ->latest('fecha_generacion')
                    ->get();
            }
        }

        $upcoming = $upcoming
            ->concat($cachedTickets->filter(fn ($ticket) => $ticket->eventoAsiento?->evento?->fecha_evento?->isFuture()))
            ->unique('codigo_unico')
            ->values();

        $past = $past
            ->concat($cachedTickets->filter(fn ($ticket) => $ticket->eventoAsiento?->evento?->fecha_evento?->isPast()))
            ->unique('codigo_unico')
            ->values();

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

            if ($apiSeats->isNotEmpty()) {
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
        }

        // Fallback a BD local: buscar por slug generado desde nombre_evento
        $eventos = Evento::where('activo', true)->get();
        $evento = $eventos->first(fn ($e) => \Illuminate\Support\Str::slug($e->nombre_evento) === $slug);

        if (!$evento) {
            abort(404, 'Evento no encontrado');
        }

        $eventoAsientos = EventoAsiento::where('id_evento', $evento->id_evento)
            ->with(['asiento.zona'])
            ->get();

        // Liberar reservas expiradas antes de mostrar el mapa
        $eventoAsientos->each(fn ($ea) => $ea->isDisponible());
        $eventoAsientos = EventoAsiento::where('id_evento', $evento->id_evento)
            ->with(['asiento.zona'])
            ->get();

        // Obtener precios desde EVENTO_ZONA via JOIN
        $precioMap = DB::table('EVENTO_ASIENTO as ea')
            ->join('ASIENTOS as a', 'a.id_asiento', '=', 'ea.id_asiento')
            ->join('EVENTO_ZONA as ez', function ($j) use ($evento) {
                $j->on('ez.id_evento', '=', 'ea.id_evento')
                  ->on('ez.id_zona', '=', 'a.id_zona');
            })
            ->where('ea.id_evento', $evento->id_evento)
            ->pluck('ez.precio', 'ea.id_evento_asiento');

        $seatRows = $eventoAsientos
            ->groupBy(fn ($ea) => $ea->asiento->fila)
            ->sortKeys()
            ->map(fn ($seats, $fila) => [
                'label'   => $fila,
                'section' => $seats->first()->asiento->zona->nombre_zona,
                'seats'   => $seats->sortBy('asiento.numero')->map(fn ($ea) => [
                    'id'      => $ea->id_evento_asiento,
                    'n'       => $ea->asiento->numero,
                    's'       => match ($ea->estado) {
                        'DISPONIBLE' => 'a',
                        'RESERVADO', 'VENDIDO' => 'o',
                        'BLOQUEADO' => 'b',
                        default     => 'a',
                    },
                    'precio'  => (float) ($precioMap[$ea->id_evento_asiento] ?? 0),
                    'section' => $ea->asiento->zona->nombre_zona,
                ])->values()->all(),
            ])->values()->all();

        $minPrecios = DB::table('EVENTO_ZONA')
            ->where('id_evento', $evento->id_evento)
            ->select(DB::raw('MIN(precio) as min_precio'))
            ->value('min_precio');

        $event = [
            'id'        => $evento->id_evento,
            'slug'      => \Illuminate\Support\Str::slug($evento->nombre_evento),
            'title'     => $evento->nombre_evento,
            'venue'     => $evento->venue_name,
            'city'      => $evento->city_name,
            'price_from'=> (int) ($minPrecios ?? 0),
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
