<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

class EventCatalog extends Component
{
    public string $search = '';
    public string $category = 'all';

    /**
     * Fuente de datos. Devuelve siempre una Collection lista para encadenar.
     *
     * - Cache::remember guarda el resultado decodificado por 5 minutos.
     *   El segundo argumento de json_decode (true) lo devuelve como ARRAY
     *   asociativo, no como stdClass. Los arrays se serializan/deserializan
     *   sin problemas (a diferencia de stdClass y Collection).
     *
     * - Cada vez que se llama, envolvemos el array crudo con collect() para
     *   poder usar ->filter(), ->pluck(), etc. encadenado. Es operación de
     *   memoria pura, despreciable.
     *
     * - #[Computed] memoiza el resultado DENTRO de un mismo request,
     *   así si se accede 3 veces solo se ejecuta una.
     */
    #[Computed]
    public function allEvents()
    {
        $raw = Cache::remember('events.all', now()->addMinutes(5), function () {
            return json_decode(
                file_get_contents(database_path('mocks/events.json')),
                true   // ← true = devuelve arrays asociativos, no stdClass
            );
        });

        return collect($raw);
    }

    #[Computed]
    public function filteredEvents()
    {
        return $this->allEvents
            ->when($this->category !== 'all',
                fn ($events) => $events->filter(fn ($e) => $e['category'] === $this->category)
            )
            ->when($this->search !== '',
                fn ($events) => $events->filter(fn ($e) => stripos($e['title'], $this->search) !== false)
            )
            ->values();
    }

    #[Computed]
    public function categories()
    {
        return $this->allEvents
            ->pluck('category')
            ->unique()
            ->sort()
            ->values();
    }

    public function render()
    {
        return view('livewire.event-catalog', [
            'events'     => $this->filteredEvents,
            'categories' => $this->categories,
        ]);
    }
}