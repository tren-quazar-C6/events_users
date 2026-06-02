@extends('layouts.app')

@section('title', $event['title'])

@section('content')

{{-- ── HERO — split layout ──────────────────────────────────── --}}
<section class="relative w-full overflow-hidden bg-surface-container-low">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row min-h-[600px]">

        {{-- Columna texto --}}
        <div class="w-full md:w-1/2 px-4 sm:px-10 lg:px-16 py-16 flex flex-col justify-center relative z-10">

            <a href="{{ route('catalog') }}"
               class="inline-flex items-center gap-1 text-on-surface-variant font-label-lg text-label-lg hover:text-primary transition-colors mb-6 w-fit">
                <span class="material-symbols-outlined" style="font-size: 18px">arrow_back</span>
                Volver a cartelera
            </a>

            <div class="inline-flex items-center px-4 py-1.5 rounded-full bg-secondary-container text-on-secondary-container font-label-lg text-label-lg mb-6 w-fit">
                {{ $event['category'] }}
            </div>

            <h1 class="font-display text-display text-on-surface mb-4 leading-none">
                {{ $event['title'] }}
            </h1>

            @auth
            <div class="mb-6">
                <livewire:favorite-button
                    :slug="$event['slug']"
                    :title="$event['title']"
                    :category="$event['category']"
                    :synopsis="$event['synopsis'] ?? null"
                    :priceFrom="$event['price_from'] ?? 0"
                    :posterColor="$event['poster_color']"
                    :imageUrl="$event['image_url'] ?? null"
                    :key="'fav-event-'.$event['slug']"
                />
            </div>
            @endauth

            <div class="flex flex-wrap gap-6 mb-8 text-on-surface-variant">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">schedule</span>
                    <span class="font-body-md text-body-md">{{ $event['duration'] }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">location_on</span>
                    <span class="font-body-md text-body-md">{{ $event['venue'] }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">theater_comedy</span>
                    <span class="font-body-md text-body-md">{{ $event['author'] }}</span>
                </div>
            </div>

            <p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed max-w-xl">
                {{ $event['synopsis'][0] ?? '' }}
            </p>
        </div>

        {{-- Columna imagen --}}
        <div class="w-full md:w-1/2 relative min-h-[400px] bg-secondary-container flex items-center justify-center">
            <span class="material-symbols-outlined text-primary/20" style="font-size: 140px">curtains</span>
            <div class="absolute inset-0 bg-gradient-to-r from-surface-container-low via-transparent to-transparent hidden md:block"></div>
        </div>

    </div>
</section>

{{-- ── CONTENIDO + SIDEBAR ─────────────────────────────────── --}}
<div x-data="{ selectedDate: 0, selectedTime: 0 }"
     class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16 py-16 grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- ── Columna izquierda ────────────────────────────────── --}}
    <div class="lg:col-span-8 space-y-16">

        {{-- Sinopsis --}}
        <section>
            <h2 class="font-headline-md text-headline-md text-primary mb-6">Sinopsis</h2>
            <div class="space-y-4 max-w-3xl">
                @foreach ($event['synopsis'] as $paragraph)
                <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">{{ $paragraph }}</p>
                @endforeach
            </div>
        </section>

        {{-- Galería --}}
        <section>
            <h2 class="font-headline-md text-headline-md text-primary mb-6">Galería de producción</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                {{-- Foto grande --}}
                <div class="col-span-2 row-span-2 overflow-hidden rounded-xl bg-surface-container shadow-sm border border-secondary-container/20 min-h-[200px] flex items-center justify-center group">
                    <span class="material-symbols-outlined text-outline/30 group-hover:scale-110 transition-transform duration-500" style="font-size: 64px">photo_camera</span>
                </div>

                {{-- Foto ancha --}}
                <div class="col-span-2 overflow-hidden rounded-xl bg-surface-container-high shadow-sm border border-secondary-container/20 min-h-[96px] flex items-center justify-center group">
                    <span class="material-symbols-outlined text-outline/30 group-hover:scale-110 transition-transform duration-500" style="font-size: 48px">theater_comedy</span>
                </div>

                {{-- Foto pequeña 1 --}}
                <div class="overflow-hidden rounded-xl bg-secondary-container/40 shadow-sm border border-secondary-container/20 min-h-[96px] flex items-center justify-center group">
                    <span class="material-symbols-outlined text-outline/30 group-hover:scale-110 transition-transform duration-500" style="font-size: 40px">spotlight</span>
                </div>

                {{-- Foto pequeña 2 --}}
                <div class="overflow-hidden rounded-xl bg-surface-container shadow-sm border border-secondary-container/20 min-h-[96px] flex items-center justify-center group">
                    <span class="material-symbols-outlined text-outline/30 group-hover:scale-110 transition-transform duration-500" style="font-size: 40px">stage</span>
                </div>

            </div>
        </section>

    </div>

    {{-- ── Sidebar reserva ─────────────────────────────────── --}}
    <aside class="lg:col-span-4">
        <div class="sticky top-28 bg-surface-container-lowest p-8 rounded-xl shadow-lg shadow-primary/5 border border-secondary-container/30 space-y-6">

            <h3 class="font-headline-md text-headline-md text-on-surface">Seleccionar función</h3>

            {{-- Selector de fecha --}}
            <div class="grid grid-cols-3 gap-3">
                @foreach ($event['dates'] as $i => $date)
                <button
                    @click="selectedDate = {{ $i }}"
                    :class="selectedDate === {{ $i }}
                        ? 'border-primary bg-secondary-container/20 text-primary'
                        : 'border-transparent bg-surface-container text-on-surface-variant hover:border-secondary-container'"
                    class="flex flex-col items-center justify-center p-3 rounded-lg border-2 transition-all duration-200"
                >
                    <span class="font-label-sm text-label-sm uppercase">{{ $date['dow'] }}</span>
                    <span class="font-headline-md text-headline-md leading-none">{{ $date['day'] }}</span>
                    <span class="font-label-sm text-label-sm">{{ $date['month'] }}</span>
                </button>
                @endforeach
            </div>

            {{-- Selector de horario --}}
            <div class="space-y-3">
                <label class="font-label-lg text-label-lg text-on-surface">Horarios disponibles</label>
                <div class="flex flex-wrap gap-2">
                    @foreach ($event['times'] as $i => $time)
                    <button
                        @click="selectedTime = {{ $i }}"
                        :class="selectedTime === {{ $i }}
                            ? 'border-primary bg-primary text-on-primary'
                            : 'border-secondary-container bg-surface text-on-secondary-container hover:bg-secondary-container/20'"
                        class="px-5 py-2 rounded-full border font-label-lg text-label-lg transition-all"
                    >
                        {{ $time }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Precio --}}
            <div class="py-4 border-y border-secondary-container/30 flex justify-between items-center">
                <span class="font-label-lg text-label-lg text-on-surface-variant">Desde</span>
                <span class="font-headline-md text-headline-md text-primary">$ {{ $event['price'] }}</span>
            </div>

            {{-- CTA compra --}}
            <a href="{{ route('events.seats', $event['slug']) }}"
               class="w-full bg-primary-container text-on-primary-container py-4 rounded-xl font-label-lg text-label-lg font-bold hover:brightness-95 active:scale-95 transition-all shadow-md shadow-primary/10 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">confirmation_number</span>
                Comprar entradas
            </a>

            <p class="text-center font-label-sm text-label-sm text-on-surface-variant">
                Quedan pocas entradas para esta función
            </p>

        </div>
    </aside>

</div>

@endsection
