<div class="space-y-8">

    {{-- filtros --}}
    <div class="flex flex-col md:flex-row gap-4">

        {{-- buscar --}}
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Buscar evento..."
            class="w-full md:w-80 rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-sage"
        >

        {{-- categorías --}}
        <select
            wire:model.live="category"
            class="rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-sage"
        >
            <option value="">Todas las categorías</option>
            <option value="Música">Música</option>
            <option value="Tecnología">Tecnología</option>
            <option value="Entretenimiento">Entretenimiento</option>
        </select>

    </div>

    {{-- loading --}}
    <div wire:loading class="text-slate-500">
        Cargando eventos...
    </div>

    {{-- grid --}}
    <div
        wire:loading.remove
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
    >

        @forelse ($events as $event)

            <x-card class="h-full">

                <img
                    src="{{ $event['banner'] }}"
                    alt="{{ $event['title'] }}"
                    class="w-full h-48 object-cover"
                >

                <div class="p-5 space-y-3">

                    <span class="text-xs font-semibold text-sage uppercase">
                        {{ $event['category'] }}
                    </span>

                    <h3 class="text-xl font-bold text-slate-800">
                        {{ $event['title'] }}
                    </h3>

                    <p class="text-sm text-slate-500">
                        {{ $event['date'] }} · {{ $event['city'] }}
                    </p>

                    <div class="flex justify-between items-center pt-3">

                        <span class="font-bold text-sage-dark">
                            ${{ number_format($event['price']) }}
                        </span>

                        <a
                            href="#"
                            class="px-4 py-2 rounded-xl bg-sage text-white text-sm font-medium hover:bg-sage-dark transition"
                        >
                            Ver evento
                        </a>

                    </div>

                </div>

            </x-card>

        @empty

            <div class="col-span-full">

                <x-card class="text-center py-12">

                    <p class="text-lg font-semibold text-slate-700">
                        No se encontraron eventos
                    </p>

                    <p class="text-slate-500 mt-2">
                        Intenta cambiar los filtros.
                    </p>

                </x-card>

            </div>

        @endforelse

    </div>

</div>
