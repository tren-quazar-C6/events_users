@extends('layouts.dashboard')

@section('title', 'Mis favoritos')

@section('dashboard-content')
    <h1 class="font-display text-4xl text-sage-dark mb-6">Mis favoritos</h1>

    @if ($favorites->isEmpty())
        <div class="bg-white rounded-card shadow-soft p-12 text-center">
            <p class="font-display text-2xl text-sage-dark">Aún no tienes favoritos</p>
            <p class="mt-2 text-sage-dark/70">Explora la cartelera y marca los eventos que te interesen con el corazón.</p>
            <a href="{{ route('catalog') }}" class="inline-block mt-4 bg-sage text-white px-6 py-3 rounded-btn font-semibold hover:bg-sage-dark transition">
                Ver cartelera
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($favorites as $event)
                <div class="bg-white rounded-card shadow-soft overflow-hidden hover:shadow-lg transition">
                    <div class="relative aspect-[4/3] flex items-center justify-center"
                         style="background-color: {{ $event['poster_color'] }}">
                        <div class="absolute top-3 right-3 z-10">
                            <livewire:favorite-button :slug="$event['slug']" :key="'fav-'.$event['slug']" />
                        </div>
                        <a href="{{ route('events.show', $event['slug']) }}" class="absolute inset-0 flex items-center justify-center">
                            <span class="font-display text-3xl text-white/90 px-6 text-center">{{ $event['title'] }}</span>
                        </a>
                    </div>
                    <div class="p-5">
                        <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-sage-light text-sage-dark">{{ $event['category'] }}</span>
                        <a href="{{ route('events.show', $event['slug']) }}">
                            <h3 class="font-display text-xl text-sage-dark mt-2">{{ $event['title'] }}</h3>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection