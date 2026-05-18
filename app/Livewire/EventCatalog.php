<?php

namespace App\Livewire;

use Livewire\Component;

class EventCatalog extends Component
{
    public string $search = '';
    public string $category = 'all';

    public function render()
    {
        // 1. LEER el JSON con los eventos
        //    file_get_contents lee el archivo entero como string.
        //    json_decode lo convierte a un array de objetos.
        //    database_path() es un helper de Laravel que apunta a /database/...
        $allEvents = json_decode(file_get_contents(database_path('mocks/events.json')));

        // 2. FILTRAR usando Collections (la clase mágica de Laravel para arrays)
        //    collect() envuelve el array y nos da métodos encadenables.
        $filtered = collect($allEvents)
            ->when($this->category !== 'all', function ($events) {
                // Solo filtra si categoría != 'all'
                return $events->filter(fn ($e) => $e->category === $this->category);
            })
            ->when($this->search !== '', function ($events) {
                // Solo filtra si hay texto en el buscador
                return $events->filter(fn ($e) => stripos($e->title, $this->search) !== false);
            })
            ->values(); // resetea las keys del array

        // 3. LISTA DE CATEGORÍAS únicas, ordenadas
        $categories = collect($allEvents)
            ->pluck('category')   // saca solo el campo 'category' de cada evento
            ->unique()            // elimina duplicados
            ->sort()              // orden alfabético
            ->values();           // resetea keys

        return view('livewire.event-catalog', [
            'events'     => $filtered,
            'categories' => $categories,
        ]);
    }
}