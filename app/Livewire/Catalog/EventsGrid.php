<?php

namespace App\Livewire\Catalog;

use Livewire\Component;

class EventsGrid extends Component
{
    public string $search = '';

    public string $category = '';

    public function render()
    {
        $events = collect(require resource_path('mock/events.php'));

        // filtro búsqueda
        if ($this->search) {
            $events = $events->filter(function ($event) {
                return str_contains(
                    strtolower($event['title']),
                    strtolower($this->search)
                );
            });
        }

        // filtro categoría
        if ($this->category) {
            $events = $events->where('category', $this->category);
        }

        return view('livewire.catalog.events-grid', [
            'events' => $events,
        ]);
    }
}
