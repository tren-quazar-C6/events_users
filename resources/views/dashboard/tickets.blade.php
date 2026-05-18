@extends('layouts.dashboard')

@section('title', 'Mis entradas')

@section('dashboard-content')
    @php
        $tickets = collect(json_decode(file_get_contents(database_path('mocks/my-tickets.json'))));
        $upcoming = $tickets->filter(fn ($t) => \Carbon\Carbon::parse($t->date)->isFuture())->values();
    @endphp

    <h1 class="font-display text-4xl text-sage-dark mb-6">Mis entradas</h1>

    @if ($upcoming->isEmpty())
        <div class="bg-white rounded-card shadow-soft p-12 text-center">
            <p class="font-display text-2xl text-sage-dark">No tienes entradas próximas</p>
            <p class="mt-2 text-sage-dark/70">Explora la cartelera y reserva tu próxima función.</p>
            <a href="{{ route('catalog') }}" class="inline-block mt-4 bg-sage text-white px-6 py-3 rounded-btn font-semibold hover:bg-sage-dark transition">
                Ver cartelera
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($upcoming as $ticket)
                <div class="bg-white rounded-card shadow-soft overflow-hidden">
                    <div class="grid md:grid-cols-[1fr_auto]">
                        <div class="p-6">
                            <p class="text-xs font-semibold text-sage uppercase tracking-wide">Próxima función</p>
                            <h2 class="font-display text-2xl text-sage-dark mt-1">{{ $ticket->event_title }}</h2>
                            <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-sage-dark/60">Fecha</p>
                                    <p class="font-semibold">{{ \Carbon\Carbon::parse($ticket->date)->translatedFormat('j \\d\\e F, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sage-dark/60">Hora</p>
                                    <p class="font-semibold">{{ $ticket->time }}</p>
                                </div>
                                <div>
                                    <p class="text-sage-dark/60">Asiento</p>
                                    <p class="font-semibold">{{ $ticket->seat }}</p>
                                </div>
                                <div>
                                    <p class="text-sage-dark/60">Código</p>
                                    <p class="font-mono text-xs">{{ $ticket->code }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- QR placeholder --}}
                        <div class="bg-cream p-6 flex items-center justify-center border-t md:border-t-0 md:border-l border-sage-dark/10">
                            <div class="w-32 h-32 bg-white border-2 border-sage-dark/30 rounded-btn flex items-center justify-center">
                                <div class="grid grid-cols-8 gap-0.5 p-2">
                                    @for ($i = 0; $i < 64; $i++)
                                        <div class="w-2 h-2 {{ rand(0,1) ? 'bg-sage-dark' : 'bg-white' }}"></div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection