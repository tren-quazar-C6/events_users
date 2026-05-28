<div class="max-w-7xl mx-auto">

    @if ($eventos->isEmpty())
        <div class="bg-cream rounded-card border-2 border-dashed border-sage/20 flex flex-col items-center justify-center p-16 text-center">
            <div class="w-16 h-16 bg-white rounded-full shadow-soft flex items-center justify-center mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-sage/40" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
            </div>
            <h3 class="font-display text-2xl text-sage-dark mb-2">Aún no tienes favoritos</h3>
            <p class="text-sm text-sage-dark/60 max-w-xs mb-8">Guarda los eventos que te interesan para encontrarlos fácilmente después.</p>
            <a href="{{ route('catalog') }}"
               class="px-8 py-2.5 bg-sage text-white text-sm font-semibold rounded-btn hover:bg-sage-dark transition-all">
                Explorar cartelera
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($eventos as $evento)
                <div class="relative">
                    <a href="{{ route('events.show', $evento['slug']) }}"
                       class="bg-white rounded-card shadow-soft overflow-hidden hover:-translate-y-1 transition-all duration-300 block">
                        <div class="aspect-[4/3] flex items-center justify-center bg-cover bg-center"
                             style="{{ filled($evento['image_url'] ?? null) ? 'background-image: linear-gradient(rgba(45, 74, 62, .25), rgba(45, 74, 62, .25)), url('.$evento['image_url'].')' : 'background-color: '.($evento['poster_color'] ?? '#7BB394') }}">
                            <span class="font-display text-4xl text-white/90 px-6 text-center">
                                {{ $evento['title'] }}
                            </span>
                        </div>
                        <div class="p-5">
                            <span class="text-xs font-semibold uppercase tracking-wide text-sage bg-sage-light px-2 py-0.5 rounded-full">
                                {{ $evento['category'] }}
                            </span>
                            <h3 class="font-display text-xl text-sage-dark mt-2 mb-1 leading-snug line-clamp-2">
                                {{ $evento['title'] }}
                            </h3>
                            <p class="text-sm text-sage-dark/60">
                                Desde ${{ number_format($evento['price_from'] ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                    </a>
                    <div class="absolute top-2 right-2 z-10">
                        <livewire:favorite-button
                            :slug="$evento['slug']"
                            :title="$evento['title']"
                            :category="$evento['category']"
                            :synopsis="$evento['synopsis'] ?? null"
                            :priceFrom="$evento['price_from'] ?? 0"
                            :posterColor="$evento['poster_color'] ?? '#7BB394'"
                            :imageUrl="$evento['image_url'] ?? null"
                            :key="'fav-list-'.$evento['slug']"
                        />
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
