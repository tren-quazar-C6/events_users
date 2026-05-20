@extends('layouts.dashboard')

@section('title', 'Historial')

@section('dashboard-content')
    @php
        $tickets = collect(json_decode(file_get_contents(database_path('mocks/my-tickets.json'))));
        $past = $tickets
            ->filter(fn ($t) => \Carbon\Carbon::parse($t->date)->isPast())
            ->sortByDesc('date')
            ->values();
    @endphp

    <h1 class="font-display text-4xl text-sage-dark mb-6">Historial de compras</h1>

    @if ($past->isEmpty())
        <div class="bg-white rounded-card shadow-soft p-12 text-center">
            <p class="text-sage-dark/70">Aún no tienes compras pasadas en tu historial.</p>
        </div>
    @else
        <div class="bg-white rounded-card shadow-soft overflow-hidden">
            <table class="w-full">
                <thead class="bg-cream text-left text-xs font-semibold uppercase tracking-wide text-sage-dark/60">
                    <tr>
                        <th class="px-6 py-3">Evento</th>
                        <th class="px-6 py-3">Fecha</th>
                        <th class="px-6 py-3">Código</th>
                        <th class="px-6 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sage-dark/10">
                    @foreach ($past as $ticket)
                        <tr class="hover:bg-cream/50 transition">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-sage-dark">{{ $ticket->event_title }}</p>
                                <p class="text-xs text-sage-dark/60">{{ $ticket->seat }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ \Carbon\Carbon::parse($ticket->date)->translatedFormat('j M Y') }}
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-sage-dark/70">{{ $ticket->code }}</td>
                            <td class="px-6 py-4 text-right font-semibold">${{ number_format($ticket->price, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection