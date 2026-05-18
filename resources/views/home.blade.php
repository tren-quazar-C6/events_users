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

    {{-- DESTACADOS placeholder --}}
    <section class="py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="font-display text-4xl text-sage-dark mb-8">Esta semana en cartelera</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @for ($i = 1; $i <= 3; $i++)
                    <div class="bg-white rounded-card shadow-soft overflow-hidden">
                        <div class="aspect-[4/3] bg-sage-light"></div>
                        <div class="p-6">
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-sage-light text-sage-dark">Drama</span>
                            <h3 class="font-display text-2xl text-sage-dark mt-2">Próxima obra {{ $i }}</h3>
                            <p class="text-sage-dark/70 text-sm mt-2">Una breve descripción de la obra que va aquí cuando conectemos el catálogo.</p>
                            <a href="{{ route('catalog') }}" class="inline-block mt-4 text-sage font-semibold hover:underline">Ver detalles →</a>
                        </div>
                    </div>
                @endfor
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