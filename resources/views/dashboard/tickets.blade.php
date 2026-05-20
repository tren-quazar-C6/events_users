@extends('layouts.dashboard')

@section('title', 'Mis Tickets')

@section('dashboard-content')
<div
    x-data="{
        tab: 'upcoming',
        showQR: false,
        ticket: null,
        open(t) { this.ticket = t; this.showQR = true; },
        close() { this.showQR = false; this.ticket = null; }
    }"
>

    {{-- ── Encabezado + tabs ───────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
        <div>
            <h1 class="font-display text-4xl text-sage-dark mb-1">Mis tickets</h1>
            <p class="text-sage-dark/70">Gestiona tus próximas funciones y revisa tu historial.</p>
        </div>

        <div class="flex gap-1 p-1 bg-white rounded-card shadow-soft self-start">
            <button
                @click="tab = 'upcoming'"
                :class="tab === 'upcoming'
                    ? 'bg-sage text-white shadow-sm'
                    : 'text-sage-dark/60 hover:bg-cream'"
                class="px-6 py-2 rounded-btn text-sm font-semibold transition-all">
                Próximos
                @if($upcoming->count() > 0)
                    <span class="ml-1.5 bg-white/20 text-xs px-2 py-0.5 rounded-full">{{ $upcoming->count() }}</span>
                @endif
            </button>
            <button
                @click="tab = 'past'"
                :class="tab === 'past'
                    ? 'bg-sage text-white shadow-sm'
                    : 'text-sage-dark/60 hover:bg-cream'"
                class="px-6 py-2 rounded-btn text-sm font-semibold transition-all">
                Pasados
            </button>
        </div>
    </div>

    {{-- ── PRÓXIMOS ─────────────────────────────────────────── --}}
    <div x-show="tab === 'upcoming'" class="grid grid-cols-1 md:grid-cols-2 gap-6">

        @forelse ($upcoming as $ticket)
        <article class="bg-white rounded-card shadow-soft overflow-hidden hover:-translate-y-1 transition-all duration-300">

            <div class="h-40 bg-sage-light flex items-center justify-center relative">
                <svg class="w-12 h-12 text-sage/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
                <span class="absolute top-3 left-3 flex items-center gap-1.5 bg-white text-sage text-xs font-semibold px-3 py-1 rounded-full shadow-soft">
                    <span class="w-1.5 h-1.5 rounded-full bg-sage inline-block animate-pulse"></span>
                    Activo
                </span>
            </div>

            <div class="p-5">
                <h3 class="font-display text-xl text-sage-dark mb-1 leading-snug line-clamp-2">
                    {{ $ticket->event_title }}
                </h3>
                <p class="text-xs text-sage-dark/50 mb-4 tracking-widest font-mono">
                    {{ $ticket->unique_code }}
                </p>

                <div class="space-y-1.5 mb-5 text-sm text-sage-dark/70">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-sage shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ $ticket->event_date }} · {{ $ticket->event_time }}h
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-sage shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $ticket->venue }}
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-sage shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Fila {{ $ticket->seat_row }}, Asiento {{ $ticket->seat_number }}
                        <span class="text-sage-dark/40">· {{ $ticket->seat_section }}</span>
                    </div>
                </div>

                <button
                    @click="open({{ Js::from([
                        'code'    => $ticket->unique_code,
                        'title'   => $ticket->event_title,
                        'date'    => $ticket->event_date,
                        'time'    => $ticket->event_time,
                        'venue'   => $ticket->venue,
                        'row'     => $ticket->seat_row,
                        'number'  => $ticket->seat_number,
                        'section' => $ticket->seat_section,
                        'status'  => $ticket->status,
                        'qr_url'  => route('tickets.qr', $ticket->unique_code),
                    ]) }})"
                    class="w-full py-2.5 bg-sage text-white text-sm font-semibold rounded-btn flex items-center justify-center gap-2 hover:bg-sage-dark transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.24M16.24 12l1.41 1.41M12 16.24V12m0 4.24L10.59 14.83"/></svg>
                    Ver QR
                </button>
            </div>
        </article>
        @empty
        <div class="col-span-full py-16 flex flex-col items-center text-sage-dark/40">
            <svg class="w-14 h-14 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
            <p class="text-sm">No tienes tickets próximos.</p>
        </div>
        @endforelse

        {{-- Tarjeta descubrir --}}
        <div class="bg-cream rounded-card border-2 border-dashed border-sage/20 flex flex-col items-center justify-center p-8 text-center min-h-[320px]">
            <div class="w-14 h-14 bg-white rounded-full shadow-soft flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-sage" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <h4 class="font-display text-xl text-sage-dark mb-2">¿Buscas algo nuevo?</h4>
            <p class="text-sm text-sage-dark/60 mb-6 max-w-[200px]">Explora las mejores obras y funciones disponibles.</p>
            <a href="{{ route('catalog') }}"
               class="px-6 py-2 border-2 border-sage text-sage text-sm font-semibold rounded-btn hover:bg-sage hover:text-white transition-all">
                Explorar funciones
            </a>
        </div>
    </div>

    {{-- ── PASADOS ──────────────────────────────────────────── --}}
    <div x-show="tab === 'past'" x-cloak>

        @if ($past->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($past as $ticket)
            <article class="bg-white rounded-card shadow-soft overflow-hidden opacity-70">
                <div class="h-40 bg-sage-light/30 flex items-center justify-center relative grayscale">
                    <svg class="w-12 h-12 text-sage-dark/20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    <span class="absolute top-3 left-3 flex items-center gap-1.5 bg-white/80 text-sage-dark/60 text-xs font-medium px-3 py-1 rounded-full">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Usado
                    </span>
                </div>
                <div class="p-5">
                    <h3 class="font-display text-xl text-sage-dark mb-1 leading-snug line-clamp-2">{{ $ticket->event_title }}</h3>
                    <p class="text-xs text-sage-dark/50 mb-4 tracking-widest font-mono">{{ $ticket->unique_code }}</p>
                    <div class="space-y-1.5 mb-5 text-sm text-sage-dark/50">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $ticket->event_date }} · {{ $ticket->event_time }}h
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $ticket->venue }}
                        </div>
                    </div>
                    <button
                        @click="open({{ Js::from([
                            'code'    => $ticket->unique_code,
                            'title'   => $ticket->event_title,
                            'date'    => $ticket->event_date,
                            'time'    => $ticket->event_time,
                            'venue'   => $ticket->venue,
                            'row'     => $ticket->seat_row,
                            'number'  => $ticket->seat_number,
                            'section' => $ticket->seat_section,
                            'status'  => $ticket->status,
                            'qr_url'  => route('tickets.qr', $ticket->unique_code),
                        ]) }})"
                        class="w-full py-2.5 bg-cream text-sage-dark/60 text-sm font-semibold rounded-btn flex items-center justify-center gap-2 hover:bg-sage-light transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.24M16.24 12l1.41 1.41M12 16.24V12m0 4.24L10.59 14.83"/></svg>
                        Ver QR
                    </button>
                </div>
            </article>
            @endforeach
        </div>
        @else
        <div class="rounded-card bg-cream p-16 flex flex-col items-center text-center border border-sage/10">
            <svg class="w-16 h-16 text-sage/20 mb-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
            <h3 class="font-display text-2xl text-sage-dark mb-2">No tienes tickets pasados</h3>
            <p class="text-sm text-sage-dark/60 max-w-xs">Tu historial de funciones aparecerá aquí una vez que hayas disfrutado de tus espectáculos.</p>
            <a href="{{ route('catalog') }}" class="mt-8 px-8 py-2.5 bg-sage text-white text-sm font-semibold rounded-btn hover:bg-sage-dark transition-all">
                Ver recomendaciones
            </a>
        </div>
        @endif
    </div>

    {{-- ── MODAL QR ─────────────────────────────────────────── --}}
    <div
        x-show="showQR"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @keydown.escape.window="close()"
        @click.self="close()"
        class="fixed inset-0 z-50 bg-sage-dark/50 backdrop-blur-sm flex items-center justify-center p-4">

        <div
            x-show="showQR"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-card shadow-soft w-full max-w-sm overflow-hidden">

            <div class="flex items-start justify-between p-6 border-b border-sage/10">
                <div>
                    <p class="text-xs text-sage-dark/50 uppercase tracking-wider mb-1">Entrada</p>
                    <h2 class="font-display text-xl text-sage-dark leading-snug" x-text="ticket?.title"></h2>
                </div>
                <button @click="close()" class="text-sage-dark/40 hover:text-sage-dark transition-colors ml-4 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 flex flex-col items-center gap-5">
                <div class="bg-cream p-4 rounded-card border border-sage/10">
                    <template x-if="ticket">
                        <img :src="ticket.qr_url" :alt="'QR ' + ticket.code" class="w-44 h-44">
                    </template>
                </div>

                <div class="flex items-center gap-3 flex-wrap justify-center">
                    <template x-if="ticket?.status === 'confirmed'">
                        <span class="flex items-center gap-1.5 bg-sage-light text-sage-dark text-xs font-semibold px-4 py-1.5 rounded-full">
                            <span class="w-2 h-2 rounded-full bg-sage inline-block animate-pulse"></span>
                            Activo
                        </span>
                    </template>
                    <template x-if="ticket?.status === 'used'">
                        <span class="flex items-center gap-1.5 bg-cream text-sage-dark/60 text-xs font-medium px-4 py-1.5 rounded-full border border-sage/10">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Usado
                        </span>
                    </template>
                    <span class="font-mono text-sm text-sage tracking-widest font-semibold" x-text="ticket?.code"></span>
                </div>

                <div class="w-full space-y-2 text-sm text-sage-dark/70">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-sage shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span x-text="ticket?.date + ' · ' + ticket?.time + 'h'"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-sage shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span x-text="ticket?.venue"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-sage shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="'Fila ' + ticket?.row + ', Asiento ' + ticket?.number + ' · ' + ticket?.section"></span>
                    </div>
                </div>
            </div>

            <div class="px-6 pb-6">
                <button @click="close()"
                        class="w-full py-3 bg-cream text-sage-dark text-sm font-semibold rounded-btn hover:bg-sage-light transition-all cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
