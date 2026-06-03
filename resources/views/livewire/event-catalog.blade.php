<div class="max-w-7xl mx-auto px-4 py-12">

    <div class="mb-8">
        <h1 class="font-display text-5xl text-sage-dark">Cartelera</h1>
        <p class="mt-2 text-sage-dark/70">{{ count($events) }} {{ count($events) === 1 ? 'evento' : 'eventos' }} encontrados</p>
    </div>

    <div class="bg-white rounded-card shadow-soft p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="search" class="block text-sm font-semibold mb-2">Buscar por nombre</label>
                <input id="search" type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Ej: Hamlet, Chicago..."
                       class="w-full px-4 py-3 rounded-btn border border-sage-dark/20 bg-white focus:outline-none focus:ring-2 focus:ring-sage/20 focus:border-sage transition" />
            </div>

            <div>
                <label for="category" class="block text-sm font-semibold mb-2">Categoría</label>
                <select id="category"
                        wire:model.live="category"
                        class="w-full px-4 py-3 rounded-btn border border-sage-dark/20 bg-white focus:outline-none focus:ring-2 focus:ring-sage/20 focus:border-sage transition">
                    <option value="all">Todas las categorías</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @if (count($events) === 0)
        <div class="bg-white rounded-card shadow-soft p-12 text-center">
            <p class="font-display text-2xl text-sage-dark">Sin resultados</p>
            <p class="mt-2 text-sage-dark/70">Intenta con otra búsqueda o cambia la categoría.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($events as $event)
                @php
                    $lowestPrice = $event['price_from'] ?? null;
                    $cardImage = $event['image_url'] ?? '/icons/icon-512.png';
                @endphp

                <div class="relative">
                    <a href="{{ route('events.show', $event['slug']) }}"
                       class="bg-white rounded-card shadow-soft overflow-hidden hover:-translate-y-1 transition-all duration-300 block">
                        <div class="aspect-[4/3] flex items-center justify-center bg-cover bg-center"
                             style="background-image: linear-gradient(rgba(45, 74, 62, .25), rgba(45, 74, 62, .25)), url('{{ $cardImage }}')">
                            <span class="font-display text-4xl text-white/90 px-6 text-center drop-shadow">
                                {{ $event['title'] }}
                            </span>
                        </div>
                        <div class="p-5">
                            <span class="text-xs font-semibold uppercase tracking-wide text-sage bg-sage-light px-2 py-0.5 rounded-full">
                                {{ $event['category'] }}
                            </span>
                            <h3 class="font-display text-xl text-sage-dark mt-2 mb-1 leading-snug line-clamp-2">
                                {{ $event['title'] }}
                            </h3>
                            <div class="mt-3">
                                <p class="text-xs text-sage-dark/60">Fecha del evento</p>
                                <p class="text-sm font-semibold text-sage-dark">
                                    @if (! empty($event['display_date']))
                                        {{ $event['display_date']->locale('es')->translatedFormat('j M Y · H:i') }}
                                    @else
                                        Por confirmar
                                    @endif
                                </p>
                            </div>
                            <div class="mt-3">
                                <p class="text-xs text-sage-dark/60">Precio desde</p>
                                <p class="text-sm font-semibold text-sage">
                                    @if ($lowestPrice > 0)
                                        ${{ number_format($lowestPrice, 0, ',', '.') }}
                                    @else
                                        Por confirmar
                                    @endif
                                </p>
                            </div>
                        </div>
                    </a>

                    @auth
                        <div class="absolute top-2 right-2 z-10">
                            <livewire:favorite-button
                                :slug="$event['slug']"
                                :title="$event['title']"
                                :category="$event['category']"
                                :synopsis="$event['synopsis'] ?? null"
                                :priceFrom="$event['price_from'] ?? 0"
                                :posterColor="$event['poster_color']"
                                :imageUrl="$event['image_url'] ?? null"
                                :key="'fav-catalog-'.$event['slug']"
                            />
                        </div>
                    @endauth
                </div>
            @endforeach
        </div>
    @endif
</div>
