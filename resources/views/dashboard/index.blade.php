@extends('layouts.dashboard')

@section('title', 'Mi Dashboard')

@section('dashboard-content')
@php
    use App\Models\Ticket;
    use App\Models\Venta;
    use App\Services\FavoriteService;
    use App\Services\PurchaseFlowService;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;

    $user = auth()->user();
    $hasSalesTables = Schema::hasTable('VENTAS') && Schema::hasTable('TICKETS');
    $upcomingTickets = collect();
    $dbTickets = collect();
    $cachedTickets = app(PurchaseFlowService::class)->ticketsForUser($user->id);

    if ($hasSalesTables) {
        $usuarioId = DB::table('USUARIO')->where('email', $user->email)->value('id_usuario');

        if ($usuarioId) {
            $ventaIds = Venta::where('id_usuario', $usuarioId)->pluck('id_venta');

            $upcomingTickets = Ticket::whereIn('id_venta', $ventaIds)
                ->where('id_estado_ticket', 2)
                ->with(['eventoAsiento.evento', 'eventoAsiento.asiento'])
                ->get()
                ->filter(fn ($t) => $t->eventoAsiento?->evento?->fecha_evento?->isFuture());

            $dbTickets = Ticket::whereIn('id_venta', $ventaIds)
                ->with(['eventoAsiento.evento', 'eventoAsiento.asiento'])
                ->get();
        }
    }

    $upcomingTickets = $upcomingTickets
        ->concat($cachedTickets->filter(fn ($t) => $t->eventoAsiento?->evento?->fecha_evento?->isFuture()))
        ->unique('codigo_unico')
        ->values();

    $totalTickets = $dbTickets
        ->concat($cachedTickets)
        ->unique('codigo_unico')
        ->count();

    $favoritesCount = app(FavoriteService::class)->countForUser($user->id);
    $events = app(\App\Services\EventService::class)->featured();
@endphp

    {{-- Saludo --}}
    <div class="mb-8">
        <h1 class="font-display text-4xl text-sage-dark">¡Hola, {{ auth()->user()->name }}!</h1>
        <p class="mt-2 text-sage-dark/70">Aquí está el resumen de tu actividad en Tickify.</p>
    </div>

    {{-- Stats rápidas --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-10">
        <div class="bg-white rounded-card shadow-soft p-5">
            <p class="text-sm text-sage-dark/60">Próximas funciones</p>
            <p class="font-display text-3xl text-sage-dark mt-1">{{ $upcomingTickets->count() }}</p>
        </div>
        <div class="bg-white rounded-card shadow-soft p-5">
            <p class="text-sm text-sage-dark/60">Entradas totales</p>
            <p class="font-display text-3xl text-sage-dark mt-1">{{ $totalTickets }}</p>
        </div>
        <div class="bg-white rounded-card shadow-soft p-5 col-span-2 md:col-span-1">
            <p class="text-sm text-sage-dark/60">Favoritos</p>
            <p class="font-display text-3xl text-sage-dark mt-1">{{ $favoritesCount }}</p>
        </div>
    </div>

    {{-- Próxima entrada --}}
    @if ($upcomingTickets->isNotEmpty())
        @php
            $next       = $upcomingTickets->sortBy(fn ($t) => $t->eventoAsiento->evento->fecha_evento)->first();
            $nextEvento = $next->eventoAsiento->evento;
            $nextAsiento= $next->eventoAsiento->asiento;
        @endphp
        <div class="bg-sage-dark text-cream rounded-card shadow-soft p-6 mb-10">
            <p class="text-cream/70 text-sm">Tu próxima función</p>
            <h2 class="font-display text-3xl mt-1">{{ $nextEvento->nombre_evento }}</h2>
            <p class="mt-2 text-cream/80">
                {{ $nextEvento->fecha_evento->translatedFormat('l j \d\e F') }}
                · {{ $nextEvento->fecha_evento->format('H:i') }}
                @if ($nextAsiento)
                    · Fila {{ $nextAsiento->fila }}, Asiento {{ $nextAsiento->numero }}
                @endif
            </p>
            <a href="{{ route('dashboard.tickets') }}"
               class="inline-block mt-4 bg-cream text-sage-dark px-4 py-2 rounded-btn font-semibold hover:bg-white transition">
                Ver entrada
            </a>
        </div>
    @endif

    {{-- Feed: nuevos eventos en cartelera --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-display text-2xl text-sage-dark">Nuevo en cartelera</h2>
            <a href="{{ route('catalog') }}" class="text-sage font-semibold hover:underline">Ver todo →</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse ($events as $event)
                @php $cardImage = $event['image_url'] ?? '/icons/icon-512.png'; @endphp
                <a href="{{ route('events.show', $event['slug']) }}"
                   class="bg-white rounded-card shadow-soft overflow-hidden hover:shadow-lg transition">
                    <div class="aspect-[4/3] bg-cover bg-center"
                         style="background-image: linear-gradient(rgba(45, 74, 62, .20), rgba(45, 74, 62, .20)), url('{{ $cardImage }}')"></div>
                    <div class="p-4">
                        <p class="text-xs text-sage-dark/60">{{ $event['category'] }}</p>
                        <p class="font-display text-lg text-sage-dark mt-1">{{ $event['title'] }}</p>
                    </div>
                </a>
            @empty
                <p class="col-span-3 text-sm text-sage-dark/60">No hay eventos disponibles.</p>
            @endforelse
        </div>
    </div>
@endsection
