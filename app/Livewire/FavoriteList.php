<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;

class FavoriteList extends Component
{
    #[On('favorites-changed')]
    public function refresh(): void {}

    public function render()
    {
        $allEvents = collect(Cache::remember('events.all', now()->addMinutes(5), function () {
            return json_decode(file_get_contents(database_path('mocks/events.json')), true);
        }));

        $favoriteIds = Auth::user()->favorites()->pluck('event_id')->toArray();

        $events = $allEvents->whereIn('id', $favoriteIds)->values();

        return view('livewire.favorite-list', compact('events'));
    }
}
