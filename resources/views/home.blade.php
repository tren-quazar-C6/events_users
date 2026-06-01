@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    {{-- HERO --}}
    <section class="bg-cream py-20 px-4">
        <div class="max-w-5xl mx-auto text-center">
            <h1 class="font-display text-6xl md:text-7xl text-sage-dark leading-tight">
                Vive el teatro<br/>
                <span class="text-sage">como nunca antes</span>
            </h1>
            <p class="mt-6 text-lg text-sage-dark/70 max-w-2xl mx-auto">
                Descubre obras, compra tu entrada en segundos y guarda tus tickets en el teléfono.
                Tickify es tu boletería de bolsillo.
            </p>
            <div class="mt-8 flex gap-4 justify-center flex-wrap">
                <a href="{{ route('catalog') }}" class="bg-sage text-white px-8 py-4 rounded-btn font-semibold hover:bg-sage-dark transition">
                    Ver cartelera
                </a>
                @guest
                    <a href="{{ route('register') }}" class="bg-white text-sage-dark border border-sage-dark/20 px-8 py-4 rounded-btn font-semibold hover:bg-sage-light transition">
                        Crear cuenta gratis
                    </a>
                @endguest
            </div>
        </div>
    </section>

    {{-- DESTACADOS --}}
    <section class="py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="font-display text-4xl text-sage-dark mb-8">Esta semana en cartelera</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse ($events->take(3) as $event)
                    @php
                        $image = $event->imagenes->firstWhere('principal', true)
                            ?? $event->imagenes->firstWhere('activo', true)
                            ?? $event->imagenes->first();
                        $imageUrl = null;

                        if ($image?->ruta_url) {
                            $imageUrl = \Illuminate\Support\Str::startsWith($image->ruta_url, ['http://', 'https://', '/'])
                                ? $image->ruta_url
                                : \Illuminate\Support\Facades\Storage::url($image->ruta_url);
                        }
                        $imageUrl = $imageUrl ?: '/icons/icon-512.png';
                    @endphp
                    <div class="bg-white rounded-card shadow-soft overflow-hidden">
                        <div class="aspect-[4/3] bg-sage-light bg-cover bg-center"
                             style="background-image: linear-gradient(rgba(45, 74, 62, .20), rgba(45, 74, 62, .20)), url('{{ $imageUrl }}');"></div>
                        <div class="p-6">
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-sage-light text-sage-dark">{{ $event->tipo->nombre_tipo ?? 'Evento' }}</span>
                            <h3 class="font-display text-2xl text-sage-dark mt-2">{{ $event->nombre_evento }}</h3>
                            <p class="text-sage-dark/70 text-sm mt-2">
                                {{ \Illuminate\Support\Str::limit(implode(' ', (array) ($event->synopsis ?? [])), 120) ?: 'Próximamente más información de esta obra.' }}
                            </p>
                            <p class="text-sage-dark/60 text-xs mt-2">{{ $event->fecha_evento?->translatedFormat('l j \\d\\e F · H:i') }}</p>
                            @if (filled($event->slug))
                                <div class="mt-4 flex items-center gap-4">
                                    <a href="{{ route('events.show', $event->slug) }}" class="text-sage font-semibold hover:underline">Ver detalles →</a>
                                    @auth
                                        <a href="{{ route('events.seats', $event->slug) }}" class="text-sage-dark font-semibold hover:underline">Comprar</a>
                                    @else
                                        <a href="{{ route('login') }}" class="text-sage-dark font-semibold hover:underline">Comprar</a>
                                    @endauth
                                </div>
                            @else
                                <p class="mt-4 text-sage-dark/60 text-sm">Evento sin URL pública todavía.</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-3 bg-white rounded-card shadow-soft p-6 text-sage-dark/70">
                        Aún no hay eventos activos en cartelera.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- CTA categorías --}}
    <section class="bg-sage-light py-16 px-4">
        <div class="max-w-5xl mx-auto text-center">
            <h2 class="font-display text-4xl text-sage-dark">Encuentra tu próxima función</h2>
            <p class="mt-3 text-sage-dark/70">Drama, comedia, infantil, musical y más.</p>
            <a href="{{ route('catalog') }}" class="inline-block mt-6 bg-sage-dark text-cream px-8 py-3 rounded-btn font-semibold hover:bg-sage transition">
                Explorar catálogo
            </a>
        </div>
    </section>
@endsection
