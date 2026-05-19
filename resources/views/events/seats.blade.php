@extends('layouts.app')

@section('title', 'Seleccionar asientos — ' . $event['title'])

@section('content')

{{-- ── SHOW INFO HEADER ──────────────────────────────────────────── --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16 pt-12 pb-8 border-b border-secondary-container/30">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">

        <div>
            <a href="{{ route('events.show', $event['id']) }}"
               class="inline-flex items-center gap-1 text-on-surface-variant font-label-lg text-label-lg hover:text-primary transition-colors mb-3 w-fit">
                <span class="material-symbols-outlined" style="font-size: 18px">arrow_back</span>
                Volver al evento
            </a>
            <div class="mb-3">
                <span class="bg-secondary-container text-on-secondary-container px-3 py-1 rounded-full font-label-sm text-label-sm uppercase tracking-wider">
                    Selección de Asientos
                </span>
            </div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface">{{ $event['title'] }}</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-2 flex flex-wrap items-center gap-2">
                <span class="material-symbols-outlined" style="font-size: 20px">calendar_today</span>
                {{ $event['dates'][0]['dow'] }} {{ $event['dates'][0]['day'] }} de {{ $event['dates'][0]['month'] }} · {{ $event['times'][0] }}h
                <span class="text-outline-variant mx-1">|</span>
                <span class="material-symbols-outlined" style="font-size: 20px">location_on</span>
                {{ $event['venue'] }}
            </p>
        </div>

        {{-- Leyenda --}}
        <div class="flex flex-wrap items-center gap-5 bg-surface-container-low px-6 py-4 rounded-xl shadow-sm shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded bg-primary-container shadow-sm"></div>
                <span class="font-label-lg text-label-lg text-on-surface-variant">Disponible</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded bg-tertiary-container shadow-sm"></div>
                <span class="font-label-lg text-label-lg text-on-surface-variant">Seleccionado</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded bg-secondary-fixed-dim shadow-sm"></div>
                <span class="font-label-lg text-label-lg text-on-surface-variant">Ocupado</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded bg-surface-dim shadow-sm"></div>
                <span class="font-label-lg text-label-lg text-on-surface-variant">Bloqueado</span>
            </div>
        </div>

    </div>
</section>

{{-- ── SEAT MAP + SIDEBAR ──────────────────────────────────────────── --}}
<div x-data="{
        rows: [
            { label: 'A', section: 'Platea Central', seats: [
                {n:1,s:'a'},{n:2,s:'a'},{n:3,s:'o'},{n:4,s:'o'},null,
                {n:5,s:'sel'},{n:6,s:'sel'},{n:7,s:'a'},{n:8,s:'a'},null,
                {n:9,s:'o'},{n:10,s:'o'},{n:11,s:'a'},{n:12,s:'a'}
            ]},
            { label: 'B', section: 'Platea Central', seats: [
                {n:1,s:'a'},{n:2,s:'o'},{n:3,s:'o'},{n:4,s:'a'},null,
                {n:5,s:'a'},{n:6,s:'a'},{n:7,s:'a'},{n:8,s:'a'},null,
                {n:9,s:'b'},{n:10,s:'b'},{n:11,s:'a'},{n:12,s:'a'}
            ]},
            { label: 'C', section: 'Platea Central', seats: [
                {n:1,s:'a'},{n:2,s:'a'},{n:3,s:'a'},{n:4,s:'a'},null,
                {n:5,s:'a'},{n:6,s:'a'},{n:7,s:'a'},{n:8,s:'a'},null,
                {n:9,s:'a'},{n:10,s:'a'},{n:11,s:'a'},{n:12,s:'a'}
            ]},
            { label: 'D', section: 'Platea Central', seats: [
                {n:1,s:'a'},{n:2,s:'a'},{n:3,s:'a'},{n:4,s:'a'},null,
                {n:5,s:'a'},{n:6,s:'a'},{n:7,s:'a'},{n:8,s:'a'},null,
                {n:9,s:'a'},{n:10,s:'a'},{n:11,s:'a'},{n:12,s:'a'}
            ]},
            { label: 'E', section: 'Platea Alta', seats: [
                {n:1,s:'o'},{n:2,s:'o'},{n:3,s:'o'},{n:4,s:'o'},null,
                {n:5,s:'a'},{n:6,s:'a'},{n:7,s:'a'},{n:8,s:'a'},null,
                {n:9,s:'o'},{n:10,s:'o'},{n:11,s:'o'},{n:12,s:'o'}
            ]}
        ],
        pricePerSeat: {{ (int) str_replace('.', '', $event['price']) }},
        fee: 3500,
        toggleSeat(rowIdx, seatIdx) {
            const seat = this.rows[rowIdx].seats[seatIdx];
            if (!seat || seat.s === 'o' || seat.s === 'b') return;
            seat.s = seat.s === 'sel' ? 'a' : 'sel';
        },
        seatClass(seat) {
            if (seat === null) return 'w-4';
            const base = 'w-8 h-8 rounded shadow-sm transition-transform ';
            if (seat.s === 'a')   return base + 'bg-primary-container hover:scale-110 cursor-pointer';
            if (seat.s === 'sel') return base + 'bg-tertiary-container ring-2 ring-tertiary ring-offset-2 hover:scale-110 cursor-pointer shadow-md';
            if (seat.s === 'o')   return base + 'bg-secondary-fixed-dim opacity-60 cursor-not-allowed';
            return base + 'bg-surface-dim cursor-not-allowed';
        },
        get selected() {
            const result = [];
            this.rows.forEach(row => {
                row.seats.forEach(seat => {
                    if (seat && seat.s === 'sel') result.push({ row: row.label, num: seat.n, section: row.section });
                });
            });
            return result;
        },
        get subtotal() { return this.selected.length * this.pricePerSeat; },
        get total() { return this.subtotal + (this.selected.length > 0 ? this.fee : 0); },
        fmt(n) { return '$ ' + n.toLocaleString('es-CO'); }
     }"
     class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16 py-12 grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">

    {{-- ── Mapa de asientos ──────────────────────────────────────── --}}
    <div class="lg:col-span-8 bg-surface-container-lowest rounded-3xl p-8 md:p-12 shadow-sm border border-secondary-container/20 overflow-hidden">

        {{-- Escenario --}}
        <div class="relative w-full mb-16 text-center">
            <div class="stage-curve w-3/4 mx-auto h-2 bg-gradient-to-b from-primary/20 to-transparent shadow-[0_15px_30px_-5px_rgba(50,105,78,0.15)]"></div>
            <span class="font-label-lg text-label-lg text-primary uppercase tracking-[0.3em] mt-5 inline-block">Escenario</span>
        </div>

        {{--
            Tailwind scanner — clases dinámicas generadas por seatClass():
            bg-primary-container bg-tertiary-container bg-secondary-fixed-dim bg-surface-dim
            ring-2 ring-tertiary ring-offset-2 opacity-60 shadow-md
            hover:scale-110 cursor-pointer cursor-not-allowed
        --}}

        {{-- Grid de asientos --}}
        <div class="seat-grid flex flex-col gap-3 items-center">
            <template x-for="(row, rowIdx) in rows" :key="row.label">
                <div class="flex gap-1.5 items-center">
                    <span class="w-6 text-right font-label-sm text-label-sm text-outline pr-1" x-text="row.label"></span>
                    <div class="flex gap-1.5 items-center">
                        <template x-for="(seat, seatIdx) in row.seats" :key="seatIdx">
                            <div
                                :class="seatClass(seat)"
                                @click="seat !== null && toggleSeat(rowIdx, seatIdx)">
                            </div>
                        </template>
                    </div>
                    <span class="w-6 text-left font-label-sm text-label-sm text-outline pl-1" x-text="row.label"></span>
                </div>
            </template>
        </div>

        {{-- Pie del mapa --}}
        <div class="mt-12 flex flex-col sm:flex-row justify-between items-center gap-4 text-on-surface-variant font-label-sm text-label-sm border-t border-secondary-container/20 pt-8">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined" style="font-size: 18px">info</span>
                <span>Haz clic en un asiento para seleccionarlo</span>
            </div>
            <div class="flex items-center gap-6">
                <span class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined" style="font-size: 18px">accessible</span>
                    Zona preferencial
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined" style="font-size: 18px">visibility</span>
                    Visión excelente
                </span>
            </div>
        </div>
    </div>

    {{-- ── Sidebar pedido ──────────────────────────────────────────── --}}
    <aside class="lg:col-span-4 sticky top-28">
        <div class="bg-surface-container-low rounded-3xl p-8 shadow-md border border-secondary-container/30">
            <h2 class="font-headline-md text-headline-md text-on-surface mb-6">Tu pedido</h2>

            {{-- Lista de asientos seleccionados --}}
            <div class="space-y-3 mb-8 min-h-[80px]">
                <template x-if="selected.length === 0">
                    <p class="font-body-md text-body-md text-on-surface-variant text-center py-6">
                        Selecciona tus asientos en el mapa
                    </p>
                </template>
                <template x-for="seat in selected" :key="seat.row + seat.num">
                    <div class="flex justify-between items-center bg-surface-container-highest/50 p-4 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-tertiary-container flex items-center justify-center text-on-tertiary-container shrink-0">
                                <span class="material-symbols-outlined" style="font-size: 20px">event_seat</span>
                            </div>
                            <div>
                                <p class="font-label-lg text-label-lg text-on-surface"
                                   x-text="'Fila ' + seat.row + ', Asiento ' + String(seat.num).padStart(2, '0')"></p>
                                <p class="font-label-sm text-label-sm text-on-surface-variant" x-text="seat.section"></p>
                            </div>
                        </div>
                        <span class="font-label-lg text-label-lg text-on-surface shrink-0" x-text="fmt(pricePerSeat)"></span>
                    </div>
                </template>
            </div>

            {{-- Desglose de precio --}}
            <div class="space-y-2 border-t border-secondary-container/30 pt-6 mb-6">
                <div class="flex justify-between font-body-md text-body-md text-on-surface-variant">
                    <span x-text="'Subtotal (' + selected.length + ' ' + (selected.length === 1 ? 'entrada' : 'entradas') + ')'">Subtotal</span>
                    <span x-text="fmt(subtotal)"></span>
                </div>
                <template x-if="selected.length > 0">
                    <div class="flex justify-between font-body-md text-body-md text-on-surface-variant">
                        <span>Cargo por servicio</span>
                        <span x-text="fmt(fee)"></span>
                    </div>
                </template>
                <div class="flex justify-between font-headline-md text-headline-md text-primary pt-3">
                    <span>Total</span>
                    <span x-text="fmt(total)"></span>
                </div>
            </div>

            {{-- CTA — form oculto que serializa los asientos seleccionados --}}
            <form method="POST" action="{{ route('checkout.init', $event['id']) }}"
                  @submit.prevent="
                      $el.querySelector('[name=seats]').value = JSON.stringify(
                          selected.map(s => ({ row: s.row, num: s.num, section: s.section }))
                      );
                      $el.submit();
                  ">
                @csrf
                <input type="hidden" name="seats">
                <input type="hidden" name="event_title" value="{{ $event['title'] }}">
                <input type="hidden" name="event_date"  value="{{ $event['dates'][0]['day'] }} de {{ $event['dates'][0]['month'] }} de {{ $event['dates'][0]['year'] ?? date('Y') }}">
                <input type="hidden" name="event_time"  value="{{ $event['times'][0] }}">
                <input type="hidden" name="venue"       value="{{ $event['venue'] }}">
                <input type="hidden" name="city"        value="{{ $event['city'] }}">
                <input type="hidden" name="price"       value="{{ (int) str_replace('.', '', $event['price']) }}">

                <button
                    type="submit"
                    :disabled="selected.length === 0"
                    :class="selected.length > 0
                        ? 'hover:scale-[1.02] shadow-lg shadow-primary/20 cursor-pointer'
                        : 'opacity-40 cursor-not-allowed'"
                    class="w-full py-4 bg-primary text-on-primary font-label-lg text-label-lg rounded-full flex items-center justify-center gap-2 transition-all">
                    Continuar
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </form>

            <p class="text-center font-label-sm text-label-sm text-on-surface-variant mt-4 flex items-center justify-center gap-1.5">
                <span class="material-symbols-outlined" style="font-size: 16px">lock</span>
                Pago 100% seguro
            </p>
        </div>

        {{-- Imagen teatro --}}
        <div class="mt-6 rounded-3xl overflow-hidden shadow-sm border border-secondary-container/20 aspect-video relative group bg-secondary-container flex items-center justify-center">
            <span class="material-symbols-outlined text-primary/20 group-hover:scale-110 transition-transform duration-700" style="font-size: 80px">theater_comedy</span>
            <div class="absolute inset-0 bg-gradient-to-t from-on-surface/60 to-transparent flex flex-col justify-end p-6">
                <p class="text-white font-label-lg text-label-lg">Visión desde el escenario</p>
            </div>
        </div>
    </aside>

</div>

@endsection
