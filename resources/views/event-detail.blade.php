@extends('layouts.app')

@section('title', $event['title'])

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-12">

        {{-- Volver --}}
        <a href="{{ route('catalog') }}" class="inline-flex items-center gap-1 text-sage hover:text-sage-dark transition mb-6">
            ← Volver a la cartelera
        </a>

        <div class="bg-white rounded-card shadow-soft overflow-hidden">

            {{-- Hero del evento --}}
            <div class="aspect-[16/9] md:aspect-[21/9] flex items-center justify-center"
                 style="background-color: {{ $event['poster_color'] }}">
                <h1 class="font-display text-6xl md:text-7xl text-white/95 px-8 text-center">
                    {{ $event['title'] }}
                </h1>
            </div>

            <div class="p-8 md:p-12 grid md:grid-cols-3 gap-8">

                {{-- Info principal --}}
                <div class="md:col-span-2">
                    <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-sage-light text-sage-dark">
                        {{ $event['category'] }}
                    </span>
                    <h2 class="font-display text-3xl text-sage-dark mt-4">Sinopsis</h2>
                    <p class="mt-3 text-sage-dark/80 leading-relaxed">{{ $event['synopsis'] }}</p>

                    <h2 class="font-display text-2xl text-sage-dark mt-8">Funciones disponibles</h2>
                    <div class="mt-4 space-y-2">
                        @forelse ($event['showtimes'] as $showtime)
                            <div class="flex justify-between items-center p-4 bg-cream rounded-btn">
                                <div>
                                    <p class="font-semibold">{{ \Carbon\Carbon::parse($showtime['date'])->translatedFormat('l j \\d\\e F') }}</p>
                                    <p class="text-sm text-sage-dark/70">{{ $showtime['time'] }}</p>
                                </div>
                                <span class="text-sm font-semibold text-sage">Disponible</span>
                            </div>
                        @empty
                            <p class="text-sage-dark/70">No hay funciones disponibles por ahora.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Sidebar de compra --}}
                <aside class="md:col-span-1">
                    <div class="bg-sage-light rounded-card p-6 sticky top-24">
                        <p class="text-sm text-sage-dark/70">Desde</p>
                        <p class="font-display text-4xl text-sage-dark">
                            ${{ number_format($event['price_from'], 0, ',', '.') }}
                        </p>

                        @auth
                            <button class="w-full mt-6 bg-sage text-white font-semibold py-3 rounded-btn hover:bg-sage-dark transition">
                                Comprar entradas
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="block w-full mt-6 bg-sage text-white text-center font-semibold py-3 rounded-btn hover:bg-sage-dark transition">
                                Inicia sesión para comprar
                            </a>
                        @endauth
                    </div>
                    <livewire:favorite-button :slug="$event['slug']" />
                </aside>

            </div>
        </div>
    </div>
@endsection
