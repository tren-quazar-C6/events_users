@extends('layouts.app')

@section('title', 'Mis Tickets')


@section('content')
<div x-data="{ tab: 'upcoming' }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16 py-12">

    {{-- Encabezado + toggle --}}
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
                class="px-6 py-2 rounded-lg font-label-lg text-label-lg transition-all"
            >
                Próximos
            </button>
            <button
                @click="tab = 'past'"
                :class="tab === 'past'
                    ? 'bg-primary-container text-on-primary-container shadow-sm'
                    : 'text-on-surface-variant hover:bg-surface-container-high'"
                class="px-6 py-2 rounded-lg font-label-lg text-label-lg transition-all"
            >
                Pasados
            </button>
        </div>
    </div>

    {{-- ── PRÓXIMOS ─────────────────────────────────────────── --}}
    <div x-show="tab === 'upcoming'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        @forelse ($tickets['upcoming'] as $ticket)
        <article class="group bg-surface-container-lowest rounded-xl overflow-hidden shadow-md shadow-on-surface/5 border border-surface-container hover:-translate-y-1 transition-all duration-300">

            {{-- Imagen / placeholder --}}
            <div class="relative h-48 overflow-hidden">
                @if (!empty($ticket['image']))
                    <img
                        src="{{ $ticket['image'] }}"
                        alt="{{ $ticket['event_title'] }}"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                    />
                @else
                    <div class="w-full h-full bg-secondary-container flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary/30" style="font-size: 56px">
                            theater_comedy
                        </span>
                    </div>
                @endif

                <span class="absolute top-4 left-4 bg-secondary-container text-on-secondary-container px-3 py-1 rounded-full font-label-sm text-label-sm">
                    {{ $ticket['category'] }}
                </span>
            </div>

            {{-- Contenido --}}
            <div class="p-6">
                <h3 class="font-headline-md text-headline-md text-on-surface mb-4 leading-snug">
                    {{ $ticket['event_title'] }}
                </h3>

                <div class="space-y-3 mb-6">
                    <div class="flex items-center gap-3 text-on-surface-variant">
                        <span class="material-symbols-outlined text-primary" style="font-size: 18px">calendar_today</span>
                        <span class="font-body-md text-body-md">{{ $ticket['date_label'] }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-on-surface-variant">
                        <span class="material-symbols-outlined text-primary" style="font-size: 18px">location_on</span>
                        <span class="font-body-md text-body-md">{{ $ticket['venue'] }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-on-surface-variant">
                        <span class="material-symbols-outlined text-primary" style="font-size: 18px">event_seat</span>
                        <span class="font-body-md text-body-md">{{ $ticket['seat'] }}</span>
                    </div>
                </div>

                <button class="w-full py-3 bg-primary-container text-on-primary-container font-label-lg text-label-lg rounded-lg flex items-center justify-center gap-2 hover:brightness-95 transition-all shadow-sm">
                    <span class="material-symbols-outlined" style="font-size: 18px">qr_code_2</span>
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

        @if (count($tickets['past']) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($tickets['past'] as $ticket)
            <article class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-md shadow-on-surface/5 border border-surface-container opacity-70">

                <div class="relative h-48 overflow-hidden grayscale">
                    @if (!empty($ticket['image']))
                        <img src="{{ $ticket['image'] }}" alt="{{ $ticket['event_title'] }}" class="w-full h-full object-cover"/>
                    @else
                        <div class="w-full h-full bg-surface-container-high flex items-center justify-center">
                            <span class="material-symbols-outlined text-outline/40" style="font-size: 56px">theater_comedy</span>
                        </div>
                    @endif

                    <span class="absolute top-4 left-4 bg-secondary-container text-on-secondary-container px-3 py-1 rounded-full font-label-sm text-label-sm">
                        {{ $ticket['category'] }}
                    </span>
                </div>

                <div class="p-6">
                    <h3 class="font-headline-md text-headline-md text-on-surface mb-4 leading-snug">
                        {{ $ticket['event_title'] }}
                    </h3>

                    <div class="space-y-3 mb-6">
                        <div class="flex items-center gap-3 text-on-surface-variant/60">
                            <span class="material-symbols-outlined" style="font-size: 18px">calendar_today</span>
                            <span class="font-body-md text-body-md">{{ $ticket['date_label'] }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-on-surface-variant/60">
                            <span class="material-symbols-outlined" style="font-size: 18px">location_on</span>
                            <span class="font-body-md text-body-md">{{ $ticket['venue'] }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-on-surface-variant/60">
                            <span class="material-symbols-outlined" style="font-size: 18px">event_seat</span>
                            <span class="font-body-md text-body-md">{{ $ticket['seat'] }}</span>
                        </div>
                    </div>

                    <div class="w-full py-3 bg-surface-container text-on-surface-variant font-label-lg text-label-lg rounded-lg flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined" style="font-size: 18px">history</span>
                        Función finalizada
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        @else
        {{-- Estado vacío --}}
        <div class="rounded-3xl px-8 py-20 flex flex-col items-center justify-center text-center border border-surface-container bg-surface-container-low spotlight-glow">
            <div class="relative mb-8">
                <span class="material-symbols-outlined text-primary/20" style="font-size: 80px">confirmation_number</span>
                <span class="material-symbols-outlined text-primary/40 absolute -bottom-2 -right-2" style="font-size: 40px">history</span>
            </div>
            <h3 class="font-headline-md text-headline-md text-on-surface mb-3">No tienes tickets pasados</h3>
            <p class="font-body-md text-body-md text-on-surface-variant max-w-md">
                Tu historial de funciones aparecerá aquí una vez que hayas disfrutado de tus espectáculos. ¡El telón está a punto de subir!
            </p>
            <div class="mt-10">
                <a href="{{ route('catalog') }}"
                   class="px-8 py-3 bg-secondary text-on-secondary font-label-lg text-label-lg rounded-lg hover:brightness-110 transition-all">
                    Ver recomendaciones
                </a>
            </div>
        </div>
        @endif

    </div>

</div>
@endsection
