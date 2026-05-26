<?php

namespace App\Livewire;

use App\Models\Evento;
use App\Models\TipoEvento;
use Livewire\Attributes\Computed;
use Livewire\Component;

class EventCatalog extends Component
{
    public string $search   = '';
    public string $category = 'all';

    #[Computed]
    public function filteredEvents()
    {
        return Evento::query()
            ->where('publicado', true)
            ->where('activo', true)
            ->when($this->category !== 'all',
                fn ($q) => $q->whereHas('tipo', fn ($t) => $t->where('nombre_tipo', $this->category))
            )
            ->when($this->search !== '',
                fn ($q) => $q->where('nombre_evento', 'like', '%' . $this->search . '%')
            )
            ->with('tipo')
            ->withMin([
                'eventoAsientos as available_price_from' => fn ($q) => $q->where('estado', 'DISPONIBLE'),
            ], 'precio')
            ->orderBy('fecha_evento')
            ->get();
    }

    #[Computed]
    public function categories()
    {
        return TipoEvento::whereHas('eventos', fn ($q) => $q->where('publicado', true)->where('activo', true))
            ->orderBy('nombre_tipo')
            ->pluck('nombre_tipo');
    }

    public function render()
    {
        return view('livewire.event-catalog', [
            'events'     => $this->filteredEvents,
            'categories' => $this->categories,
        ]);
    }
}
