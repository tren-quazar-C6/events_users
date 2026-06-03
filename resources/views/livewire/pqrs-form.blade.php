<div class="space-y-8">
    <div class="bg-white rounded-card shadow-soft p-6">
        <div class="max-w-2xl">
            <p class="text-xs font-semibold uppercase tracking-wide text-sage-dark/50">PQRS</p>
            <h1 class="font-display text-4xl text-sage-dark mt-2">Crear solicitud</h1>
            <p class="mt-3 text-sage-dark/70">
                Envíanos tu petición, queja, reclamo o sugerencia y la revisaremos desde soporte.
            </p>
        </div>

        <form wire:submit.prevent="save" class="mt-8 grid gap-5 max-w-2xl">
            <div>
                <label for="tipo" class="block text-sm font-semibold text-sage-dark mb-2">Tipo</label>
                <select id="tipo" wire:model.live="tipo" class="w-full px-4 py-3 rounded-btn border border-sage-dark/20 bg-white focus:outline-none focus:ring-2 focus:ring-sage/20 focus:border-sage transition">
                    <option value="PREGUNTA">Pregunta</option>
                    <option value="QUEJA">Queja</option>
                    <option value="RECLAMO">Reclamo</option>
                    <option value="SUGERENCIA">Sugerencia</option>
                </select>
                @error('tipo') <p class="mt-1 text-sm text-coral">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="asunto" class="block text-sm font-semibold text-sage-dark mb-2">Asunto</label>
                <input id="asunto" type="text" wire:model.live="asunto" placeholder="Ej: Error en mi compra" class="w-full px-4 py-3 rounded-btn border border-sage-dark/20 bg-white focus:outline-none focus:ring-2 focus:ring-sage/20 focus:border-sage transition" />
                @error('asunto') <p class="mt-1 text-sm text-coral">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="mensaje" class="block text-sm font-semibold text-sage-dark mb-2">Mensaje</label>
                <textarea id="mensaje" wire:model.live="mensaje" rows="7" placeholder="Cuéntanos qué pasó y qué necesitas." class="w-full px-4 py-3 rounded-btn border border-sage-dark/20 bg-white focus:outline-none focus:ring-2 focus:ring-sage/20 focus:border-sage transition"></textarea>
                @error('mensaje') <p class="mt-1 text-sm text-coral">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                <p class="text-sm text-sage-dark/60">Te responderemos en el menor tiempo posible.</p>
                <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-50 cursor-not-allowed" class="inline-flex items-center justify-center px-6 py-3 rounded-btn bg-sage text-white font-semibold hover:bg-sage-dark transition">
                    <span wire:loading.remove>Enviar PQRS</span>
                    <span wire:loading>Enviando...</span>
                </button>
            </div>
        </form>
    </div>

    <div class="bg-sage-light/50 rounded-card p-6 border border-sage/20">
        <h2 class="font-display text-2xl text-sage-dark">Antes de enviar</h2>
        <div class="mt-3 grid gap-3 text-sm text-sage-dark/70">
            <p>- Incluye referencias de tu compra o evento si aplica.</p>
            <p>- Si adjuntas contexto claro, podemos resolverlo más rápido.</p>
            <p>- Guarda el asunto para identificar tu solicitud fácilmente.</p>
        </div>
    </div>

    @if ($historial->count())
        <div class="bg-white rounded-card shadow-soft p-6">
            <h2 class="font-display text-2xl text-sage-dark">Mis solicitudes</h2>
            <div class="mt-6 space-y-4">
                @foreach ($historial as $pqrs)
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-4 border border-sage-dark/10 rounded-btn hover:bg-sage-light/30 transition">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-3 mb-2">
                                @php
                                    $tipoClasses = match($pqrs->tipo) {
                                        'PREGUNTA' => 'bg-blue-100 text-blue-700',
                                        'QUEJA' => 'bg-orange-100 text-orange-700',
                                        'RECLAMO' => 'bg-red-100 text-red-700',
                                        'SUGERENCIA' => 'bg-green-100 text-green-700',
                                        default => 'bg-sage-light text-sage-dark',
                                    };
                                    $estadoClasses = match($pqrs->estado) {
                                        'ABIERTO' => 'bg-blue-50 text-blue-600',
                                        'EN_PROCESO' => 'bg-yellow-50 text-yellow-600',
                                        'RESPONDIDO' => 'bg-green-50 text-green-600',
                                        'CERRADO' => 'bg-sage-light text-sage-dark/70',
                                        default => 'bg-sage-light text-sage-dark',
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $tipoClasses }}">
                                    {{ $pqrs->tipo }}
                                </span>
                                <span class="px-2 py-1 rounded text-xs font-semibold border {{ $estadoClasses }}">
                                    {{ str_replace('_', ' ', $pqrs->estado) }}
                                </span>
                            </div>
                            <h3 class="font-semibold text-sage-dark">{{ $pqrs->asunto }}</h3>
                            <p class="text-xs text-sage-dark/60 mt-1">
                                {{ $pqrs->fecha_creacion->format('d M Y · H:i') }}
                            </p>
                        </div>
                        <a href="#" class="text-sage font-semibold text-sm hover:underline">Ver detalles</a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
