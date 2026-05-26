@extends('layouts.app')

@section('title', 'Compra confirmada · ' . $venta->referencia_interna . ' — Tickify')

@section('content')

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Banner de éxito --}}
    <div class="text-center mb-12">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-tertiary-container mb-6">
            <span class="material-symbols-outlined text-tertiary" style="font-size:44px">check_circle</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-on-surface">¡Compra confirmada!</h1>
        <p class="font-body-lg text-body-lg text-on-surface-variant mt-2">
            Referencia: <span class="font-label-lg text-label-lg text-primary">{{ $venta->referencia_interna }}</span>
        </p>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">
            Enviamos tus entradas a <strong>{{ Auth::user()->email }}</strong>
        </p>
    </div>

    {{-- Tarjetas de ticket --}}
    <div class="space-y-6">
        @foreach ($venta->tickets as $ticket)
            @php
                $ea     = $ticket->eventoAsiento;
                $evento = $ea->evento;
                $asiento= $ea->asiento;
            @endphp
            <div class="bg-surface-container-low rounded-3xl border border-secondary-container/20 shadow-sm overflow-hidden">
                <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-6 items-center">

                    {{-- Datos del ticket --}}
                    <div class="sm:col-span-2 space-y-3">
                        <div>
                            <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider mb-1">Evento</p>
                            <p class="font-headline-sm text-headline-sm text-on-surface">{{ $evento->nombre_evento }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-on-surface-variant font-body-md text-body-md">
                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined" style="font-size:16px">calendar_today</span>
                                {{ $evento->fecha_evento->translatedFormat('j \\d\\e F \\d\\e Y') }}
                            </p>
                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined" style="font-size:16px">schedule</span>
                                {{ $evento->fecha_evento->format('H:i') }}h
                            </p>
                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined" style="font-size:16px">location_on</span>
                                {{ $evento->venue }}
                            </p>
                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined" style="font-size:16px">event_seat</span>
                                Fila {{ $asiento->fila }}, Asiento {{ $asiento->numero }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3 pt-2">
                            <span class="bg-primary-container text-on-primary-container px-4 py-1.5 rounded-full font-label-md text-label-md tracking-widest">
                                {{ $ticket->codigo_unico }}
                            </span>
                            <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $asiento->zona->nombre_zona }}</span>
                        </div>
                    </div>

                    {{-- QR code --}}
                    <div class="flex justify-center sm:justify-end">
                        <div class="bg-white p-3 rounded-2xl shadow-sm border border-secondary-container/20">
                            <img src="{{ route('tickets.qr', $ticket->codigo_unico) }}"
                                 alt="QR {{ $ticket->codigo_unico }}"
                                 class="w-32 h-32">
                        </div>
                    </div>

                </div>
            </div>
        @endforeach
    </div>

    {{-- CTAs --}}
    <div class="flex flex-col sm:flex-row gap-4 mt-10 justify-center">
        <a href="{{ route('dashboard.tickets') }}"
           class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-primary text-on-primary font-label-lg text-label-lg rounded-full hover:scale-[1.02] transition-all shadow-md">
            <span class="material-symbols-outlined">confirmation_number</span>
            Ver todas mis entradas
        </a>
        <a href="{{ route('catalog') }}"
           class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-surface-container-highest text-on-surface font-label-lg text-label-lg rounded-full hover:bg-secondary-container transition-all">
            <span class="material-symbols-outlined">explore</span>
            Seguir explorando
        </a>
    </div>

</div>

@endsection
