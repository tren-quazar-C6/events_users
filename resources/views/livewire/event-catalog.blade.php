<div class="max-w-7xl mx-auto px-4 py-12">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="font-display text-5xl text-sage-dark">Cartelera</h1>
        <p class="mt-2 text-sage-dark/70">{{ count($events) }} {{ count($events) === 1 ? 'evento' : 'eventos' }} encontrados</p>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-card shadow-soft p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Buscador --}}
            <div>
                <label for="search" class="block text-sm font-semibold mb-2">Buscar por nombre</label>
                <input id="search" type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Ej: Hamlet, Chicago…"
                       class="w-full px-4 py-3 rounded-btn border border-sage-dark/20 bg-white focus:outline-none focus:ring-2 focus:ring-sage/20 focus:border-sage transition" />
            </div>

            {{-- Categoría --}}
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

    {{-- Grid de eventos --}}
    @if (count($events) === 0)
        {{-- Empty state --}}
        <div class="bg-white rounded-card shadow-soft p-12 text-center">
            <p class="font-display text-2xl text-sage-dark">Sin resultados</p>
            <p class="mt-2 text-sage-dark/70">Intenta con otra búsqueda o cambia la categoría.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($events as $event)
                <div class="bg-white rounded-card shadow-soft overflow-hidden hover:shadow-lg transition group">

                <div class="relative aspect-[4/3] flex items-center justify-center"
                    style="background-color: {{ $event['poster_color'] }}">

                    <div class="absolute top-3 right-3 z-10">
                        <livewire:favorite-button :slug="$event['slug']" :key="'fav-'.$event['slug']" />
                    </div>

                    <a href="{{ route('events.show', $event['slug']) }}" class="absolute inset-0 flex items-center justify-center">
                        <span class="font-display text-4xl text-white/90 px-6 text-center">
                            {{ $event['title'] }}
                        </span>
                    </a>
                </div>

                <div class="p-5">
                    <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-sage-light text-sage-dark">
                        {{ $event['category'] }}
                    </span>
                    <a href="{{ route('events.show', $event['slug']) }}">
                        <h3 class="font-display text-xl text-sage-dark mt-2 group-hover:text-sage transition">
                            {{ $event['title'] }}
                        </h3>
                    </a>
                    <p class="text-sm font-semibold text-sage mt-3">
                        Desde ${{ number_format($event['price_from'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>