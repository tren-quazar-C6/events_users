@extends('layouts.dashboard')

@section('title', 'Mi Dashboard')

@section('dashboard-content')
@php
    use App\Models\EstadoTicket;
    use App\Models\Ticket;
    use App\Models\Venta;

    $user         = auth()->user();
    $ventaIds     = Venta::where('user_id', $user->id)->pluck('id');
    $confirmadoId = EstadoTicket::where('nombre_estado', 'CONFIRMADO')->value('id');

    $upcomingTickets = Ticket::whereIn('venta_id', $ventaIds)
        ->where('estado_ticket_id', $confirmadoId)
        ->with(['eventoAsiento.evento', 'eventoAsiento.asiento'])
        ->get()
        ->filter(fn ($t) => $t->eventoAsiento?->evento?->fecha_evento?->isFuture());

    $totalTickets = Ticket::whereIn('venta_id', $ventaIds)->count();
    $events       = app(\App\Services\EventService::class)->featured();
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
            <p class="font-display text-3xl text-sage-dark mt-1">{{ $user->favoritos()->count() }}</p>
        </div>
    </div>

    {{-- Próxima entrada --}}
    @if ($upcomingTickets->isNotEmpty())
        @php $next = $upcomingTickets->first(); $nextEvento = $next->eventoAsiento->evento; $nextAsiento = $next->eventoAsiento->asiento; @endphp
        <div class="bg-sage-dark text-cream rounded-card shadow-soft p-6 mb-10">
            <p class="text-cream/70 text-sm">Tu próxima función</p>
            <h2 class="font-display text-3xl mt-1">{{ $nextEvento->nombre_evento }}</h2>
            <p class="mt-2 text-cream/80">
                {{ $nextEvento->fecha_evento->translatedFormat('l j \d\e F') }}
                · {{ $nextEvento->fecha_evento->format('H:i') }}
                @if ($nextAsiento) · Fila {{ $nextAsiento->fila }}, Asiento {{ $nextAsiento->numero }} @endif
            </p>
            <a href="{{ route('dashboard.tickets') }}" class="inline-block mt-4 bg-cream text-sage-dark px-4 py-2 rounded-btn font-semibold hover:bg-white transition">
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
            @foreach ($events as $event)
                <a href="{{ route('events.show', $event['slug']) }}" class="bg-white rounded-card shadow-soft overflow-hidden hover:shadow-lg transition">
                    <div class="aspect-[4/3]" style="background-color: {{ $event['poster_color'] }}"></div>
                    <div class="p-4">
                        <p class="text-xs text-sage-dark/60">{{ $event['category'] }}</p>
                        <p class="font-display text-lg text-sage-dark mt-1">{{ $event['title'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection