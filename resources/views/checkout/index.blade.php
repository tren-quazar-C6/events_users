@extends('layouts.app')

@section('title', 'Resumen de compra — Tickify')

@section('content')

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Encabezado --}}
    <div class="mb-10">
        <div class="mb-3">
            <span class="bg-secondary-container text-on-secondary-container px-3 py-1 rounded-full font-label-sm text-label-sm uppercase tracking-wider">
                Resumen de compra
            </span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-on-surface">Confirma tu pedido</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-2">Revisa los detalles antes de pagar</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

        {{-- Columna izquierda: detalles del evento + asientos --}}
        <div class="lg:col-span-3 space-y-6">

            {{-- Tarjeta del evento --}}
            <div class="bg-surface-container-low rounded-3xl p-6 border border-secondary-container/20 shadow-sm">
                <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider mb-1">Evento</p>
                <h2 class="font-headline-md text-headline-md text-on-surface">{{ $data['event_title'] }}</h2>
                <div class="mt-4 space-y-2 text-on-surface-variant font-body-md text-body-md">
                    <p class="flex items-center gap-2">
                        <span class="material-symbols-outlined" style="font-size:18px">calendar_today</span>
                        {{ $data['event_date'] }} · {{ $data['event_time'] }}h
                    </p>
                    <p class="flex items-center gap-2">
                        <span class="material-symbols-outlined" style="font-size:18px">location_on</span>
                        {{ $data['venue'] }}, {{ $data['city'] }}
                    </p>
                </div>
            </div>

            {{-- Asientos seleccionados --}}
            <div class="bg-surface-container-low rounded-3xl p-6 border border-secondary-container/20 shadow-sm">
                <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider mb-4">
                    Asientos ({{ count($data['seats']) }})
                </p>
                <div class="space-y-2">
                    @foreach ($data['seats'] as $seat)
                        <div class="flex items-center justify-between py-2 border-b border-secondary-container/20 last:border-0">
                            <span class="font-body-md text-body-md text-on-surface">
                                Fila {{ $seat['row'] }}, Asiento {{ $seat['num'] }}
                                <span class="text-on-surface-variant">· {{ $seat['section'] }}</span>
                            </span>
                            <span class="font-label-lg text-label-lg text-on-surface">
                                $ {{ number_format($data['price'], 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Form de pago mock --}}
            <div class="bg-surface-container-low rounded-3xl p-6 border border-secondary-container/20 shadow-sm">
                <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider mb-4">Datos de pago</p>
                <div class="space-y-4">
                    <div>
                        <label class="font-label-md text-label-md text-on-surface-variant block mb-1">Número de tarjeta</label>
                        <input type="text" placeholder="4242 4242 4242 4242" maxlength="19"
                               class="w-full bg-surface-container border border-secondary-container rounded-xl px-4 py-3 font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-primary/40">
                    </div>
                    <div>
                        <label class="font-label-md text-label-md text-on-surface-variant block mb-1">Nombre en la tarjeta</label>
                        <input type="text" placeholder="Tu nombre"
                               class="w-full bg-surface-container border border-secondary-container rounded-xl px-4 py-3 font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-primary/40">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="font-label-md text-label-md text-on-surface-variant block mb-1">Vencimiento</label>
                            <input type="text" placeholder="MM/AA" maxlength="5"
                                   class="w-full bg-surface-container border border-secondary-container rounded-xl px-4 py-3 font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-primary/40">
                        </div>
                        <div>
                            <label class="font-label-md text-label-md text-on-surface-variant block mb-1">CVV</label>
                            <input type="text" placeholder="123" maxlength="4"
                                   class="w-full bg-surface-container border border-secondary-container rounded-xl px-4 py-3 font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-primary/40">
                        </div>
                    </div>
                    <p class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1.5">
                        <span class="material-symbols-outlined" style="font-size:16px">info</span>
                        Entorno de pruebas — ningún cargo real será realizado
                    </p>
                </div>
            </div>

        </div>

        {{-- Columna derecha: resumen de precio + CTA --}}
        <div class="lg:col-span-2">
            <div class="sticky top-6 bg-surface-container-low rounded-3xl p-6 border border-secondary-container/20 shadow-sm space-y-4">
                <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Total a pagar</p>

                <div class="space-y-3">
                    <div class="flex justify-between font-body-md text-body-md text-on-surface-variant">
                        <span>{{ count($data['seats']) }} {{ count($data['seats']) === 1 ? 'entrada' : 'entradas' }}</span>
                        <span>$ {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-body-md text-body-md text-on-surface-variant">
                        <span>Cargo por servicio</span>
                        <span>$ {{ number_format($fee, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-headline-md text-headline-md text-primary pt-3 border-t border-secondary-container/20">
                        <span>Total</span>
                        <span>$ {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('checkout.confirm', $token) }}">
                    @csrf
                    <button type="submit"
                            class="w-full py-4 bg-primary text-on-primary font-label-lg text-label-lg rounded-full flex items-center justify-center gap-2 hover:scale-[1.02] shadow-lg shadow-primary/20 transition-all cursor-pointer">
                        Confirmar compra
                        <span class="material-symbols-outlined">check_circle</span>
                    </button>
                </form>

                <p class="text-center font-label-sm text-label-sm text-on-surface-variant flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined" style="font-size:16px">lock</span>
                    Pago 100% seguro
                </p>
            </div>
        </div>

    </div>
</div>

@endsection
