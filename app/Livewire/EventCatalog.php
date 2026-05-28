<?php

namespace App\Livewire;

use App\Services\EventService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

class EventCatalog extends Component
{
    public string $search   = '';
    public string $category = 'all';

    #[Computed]
    public function allEvents(): Collection
    {
        $events = Cache::remember('events.api.all', now()->addMinutes(2), function () {
            return app(EventService::class)->all()->all();
        });

        return collect($events);
    }

    #[Computed]
    public function filteredEvents(): Collection
    {
        return $this->allEvents
            ->when($this->category !== 'all',
                fn ($events) => $events->filter(fn ($event) => $event['category'] === $this->category)
            )
            ->when($this->search !== '',
                fn ($events) => $events->filter(fn ($event) => str_contains(
                    mb_strtolower($event['title']),
                    mb_strtolower($this->search)
                ))
            )
            ->values();
    }

    #[Computed]
    public function categories(): Collection
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
