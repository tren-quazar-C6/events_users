@extends('layouts.app')

@section('title', 'Mis Tickets')

@section('content')
<div
    x-data="{
        tab: 'upcoming',
        showQR: false,
        ticket: null,
        open(t) { this.ticket = t; this.showQR = true; },
        close() { this.showQR = false; this.ticket = null; }
    }"
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16 py-12"
>

    {{-- ── Encabezado + tabs ───────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
        <div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface mb-1">Mis tickets</h1>
            <p class="font-body-md text-body-md text-on-surface-variant">
                Gestiona tus próximas funciones y revisa tu historial.
            </p>
        </div>

        <div class="flex gap-1 p-1 bg-surface-container-low rounded-xl self-start">
            <button
                @click="tab = 'upcoming'"
                :class="tab === 'upcoming'
                    ? 'bg-primary-container text-on-primary-container shadow-sm'
                    : 'text-on-surface-variant hover:bg-surface-container-high'"
                class="px-6 py-2 rounded-lg font-label-lg text-label-lg transition-all">
                Próximos
                @if($upcoming->count() > 0)
                    <span class="ml-1.5 bg-primary text-on-primary text-xs px-2 py-0.5 rounded-full">{{ $upcoming->count() }}</span>
                @endif
            </button>
            <button
                @click="tab = 'past'"
                :class="tab === 'past'
                    ? 'bg-primary-container text-on-primary-container shadow-sm'
                    : 'text-on-surface-variant hover:bg-surface-container-high'"
                class="px-6 py-2 rounded-lg font-label-lg text-label-lg transition-all">
                Pasados
            </button>
        </div>
    </div>

    {{-- ── PRÓXIMOS ─────────────────────────────────────────── --}}
    <div x-show="tab === 'upcoming'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        @forelse ($upcoming as $ticket)
        <article class="group bg-surface-container-lowest rounded-xl overflow-hidden shadow-md shadow-on-surface/5 border border-surface-container hover:-translate-y-1 transition-all duration-300">

            <div class="h-48 bg-secondary-container flex items-center justify-center relative">
                <span class="material-symbols-outlined text-primary/30" style="font-size: 56px">theater_comedy</span>
                <span class="absolute top-4 left-4 flex items-center gap-1.5 bg-tertiary-container text-on-tertiary-container px-3 py-1 rounded-full font-label-sm text-label-sm">
                    <span class="w-2 h-2 rounded-full bg-tertiary inline-block animate-pulse"></span>
                    Activo
                </span>
            </div>

            <div class="p-6">
                <h3 class="font-headline-sm text-headline-sm text-on-surface mb-1 leading-snug line-clamp-2">
                    {{ $ticket->event_title }}
                </h3>
                <p class="font-label-sm text-label-sm text-on-surface-variant mb-4 tracking-widest font-mono">
                    {{ $ticket->unique_code }}
                </p>

                <div class="space-y-2 mb-6">
                    <div class="flex items-center gap-2 text-on-surface-variant font-body-md text-body-md">
                        <span class="material-symbols-outlined text-primary" style="font-size:18px">calendar_today</span>
                        {{ $ticket->event_date }} · {{ $ticket->event_time }}h
                    </div>
                    <div class="flex items-center gap-2 text-on-surface-variant font-body-md text-body-md">
                        <span class="material-symbols-outlined text-primary" style="font-size:18px">location_on</span>
                        {{ $ticket->venue }}
                    </div>
                    <div class="flex items-center gap-2 text-on-surface-variant font-body-md text-body-md">
                        <span class="material-symbols-outlined text-primary" style="font-size:18px">event_seat</span>
                        Fila {{ $ticket->seat_row }}, Asiento {{ $ticket->seat_number }}
                        <span class="text-on-surface-variant/50">· {{ $ticket->seat_section }}</span>
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
                    class="w-full py-3 bg-primary-container text-on-primary-container font-label-lg text-label-lg rounded-lg flex items-center justify-center gap-2 hover:brightness-95 transition-all shadow-sm cursor-pointer">
                    <span class="material-symbols-outlined" style="font-size:18px">qr_code_2</span>
                    Ver QR
                </button>
            </div>
        </article>
        @empty
        <div class="col-span-full py-16 flex flex-col items-center text-on-surface-variant">
            <span class="material-symbols-outlined" style="font-size: 56px; opacity: 0.3">confirmation_number</span>
            <p class="mt-3 font-body-md text-body-md">No tienes tickets próximos.</p>
        </div>
        @endforelse

        {{-- Tarjeta descubrir --}}
        <div class="bg-surface-container-low/50 rounded-xl border-2 border-dashed border-outline-variant flex flex-col items-center justify-center p-8 text-center min-h-[380px]">
            <div class="w-16 h-16 bg-surface-container-high rounded-full flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-outline" style="font-size: 32px">add_circle</span>
            </div>
            <h4 class="font-headline-md text-headline-md text-on-surface mb-2">¿Buscas algo nuevo?</h4>
            <p class="font-body-md text-body-md text-on-surface-variant mb-6 max-w-[220px]">
                Explora las mejores obras y funciones disponibles en cartelera.
            </p>
            <a href="{{ route('catalog') }}"
               class="px-8 py-2 border-2 border-primary text-primary font-label-lg text-label-lg rounded-lg hover:bg-primary hover:text-on-primary transition-all">
                Explorar funciones
            </a>
        </div>
    </div>

    {{-- ── PASADOS ──────────────────────────────────────────── --}}
    <div x-show="tab === 'past'" x-cloak>

        @if ($past->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($past as $ticket)
            <article class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-md shadow-on-surface/5 border border-surface-container opacity-70">

                <div class="h-48 bg-surface-container-high flex items-center justify-center relative grayscale">
                    <span class="material-symbols-outlined text-outline/40" style="font-size: 56px">theater_comedy</span>
                    <span class="absolute top-4 left-4 flex items-center gap-1.5 bg-surface-container text-on-surface-variant px-3 py-1 rounded-full font-label-sm text-label-sm">
                        <span class="material-symbols-outlined" style="font-size:14px">check_circle</span>
                        Usado
                    </span>
                </div>

                <div class="p-6">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface mb-1 leading-snug line-clamp-2">
                        {{ $ticket->event_title }}
                    </h3>
                    <p class="font-label-sm text-label-sm text-on-surface-variant mb-4 tracking-widest font-mono">
                        {{ $ticket->unique_code }}
                    </p>

                    <div class="space-y-2 mb-6">
                        <div class="flex items-center gap-2 text-on-surface-variant/60 font-body-md text-body-md">
                            <span class="material-symbols-outlined" style="font-size:18px">calendar_today</span>
                            {{ $ticket->event_date }} · {{ $ticket->event_time }}h
                        </div>
                        <div class="flex items-center gap-2 text-on-surface-variant/60 font-body-md text-body-md">
                            <span class="material-symbols-outlined" style="font-size:18px">location_on</span>
                            {{ $ticket->venue }}
                        </div>
                        <div class="flex items-center gap-2 text-on-surface-variant/60 font-body-md text-body-md">
                            <span class="material-symbols-outlined" style="font-size:18px">event_seat</span>
                            Fila {{ $ticket->seat_row }}, Asiento {{ $ticket->seat_number }}
                            <span class="text-on-surface-variant/40">· {{ $ticket->seat_section }}</span>
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
                        class="w-full py-3 bg-surface-container text-on-surface-variant font-label-lg text-label-lg rounded-lg flex items-center justify-center gap-2 hover:bg-surface-container-high transition-all cursor-pointer">
                        <span class="material-symbols-outlined" style="font-size:18px">qr_code_2</span>
                        Ver QR
                    </button>
                </div>
            </article>
            @endforeach
        </div>

        @else
        <div class="rounded-3xl px-8 py-20 flex flex-col items-center justify-center text-center border border-surface-container bg-surface-container-low">
            <div class="relative mb-8">
                <span class="material-symbols-outlined text-primary/20" style="font-size: 80px">confirmation_number</span>
                <span class="material-symbols-outlined text-primary/40 absolute -bottom-2 -right-2" style="font-size: 40px">history</span>
            </div>
            <h3 class="font-headline-md text-headline-md text-on-surface mb-3">No tienes tickets pasados</h3>
            <p class="font-body-md text-body-md text-on-surface-variant max-w-md">
                Tu historial de funciones aparecerá aquí una vez que hayas disfrutado de tus espectáculos.
            </p>
            <a href="{{ route('catalog') }}"
               class="mt-10 px-8 py-3 bg-secondary text-on-secondary font-label-lg text-label-lg rounded-lg hover:brightness-110 transition-all">
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
        class="fixed inset-0 z-50 bg-on-surface/50 backdrop-blur-sm flex items-center justify-center p-4">

        <div
            x-show="showQR"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-surface-container-lowest rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden">

            <div class="flex items-start justify-between p-6 border-b border-secondary-container/20">
                <div>
                    <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider mb-1">Entrada</p>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface leading-snug" x-text="ticket?.title"></h2>
                </div>
                <button @click="close()" class="text-on-surface-variant hover:text-on-surface transition-colors ml-4 mt-0.5 cursor-pointer">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="p-6 flex flex-col items-center gap-5">

                <div class="bg-white p-4 rounded-2xl shadow-sm border border-secondary-container/20">
                    <template x-if="ticket">
                        <img :src="ticket.qr_url" :alt="'QR ' + ticket.code" class="w-44 h-44">
                    </template>
                </div>

                <div class="flex items-center gap-3 flex-wrap justify-center">
                    <template x-if="ticket?.status === 'confirmed'">
                        <span class="flex items-center gap-1.5 bg-tertiary-container text-on-tertiary-container px-4 py-1.5 rounded-full font-label-md text-label-md">
                            <span class="w-2 h-2 rounded-full bg-tertiary inline-block animate-pulse"></span>
                            Activo
                        </span>
                    </template>
                    <template x-if="ticket?.status === 'used'">
                        <span class="flex items-center gap-1.5 bg-surface-container text-on-surface-variant px-4 py-1.5 rounded-full font-label-md text-label-md">
                            <span class="material-symbols-outlined" style="font-size:14px">check_circle</span>
                            Usado
                        </span>
                    </template>
                    <span class="font-mono font-label-lg text-label-lg text-primary tracking-widest" x-text="ticket?.code"></span>
                </div>

                <div class="w-full space-y-2 text-on-surface-variant font-body-md text-body-md">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary" style="font-size:16px">calendar_today</span>
                        <span x-text="ticket?.date + ' · ' + ticket?.time + 'h'"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary" style="font-size:16px">location_on</span>
                        <span x-text="ticket?.venue"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary" style="font-size:16px">event_seat</span>
                        <span x-text="'Fila ' + ticket?.row + ', Asiento ' + ticket?.number + ' · ' + ticket?.section"></span>
                    </div>
                </div>
            </div>

            <div class="px-6 pb-6">
                <button @click="close()"
                        class="w-full py-3 bg-surface-container text-on-surface font-label-lg text-label-lg rounded-xl hover:bg-surface-container-high transition-all cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
