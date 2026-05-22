<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FavoriteButton extends Component
{
    public int $eventId;
    public bool $isFavorited = false;

    public function mount(int $eventId): void
    {
        $this->eventId   = $eventId;
        $this->isFavorited = Auth::user()->hasFavorited($eventId);
    }

    public function toggle(): void
    {
        $user = Auth::user();

        if ($this->isFavorited) {
            $user->favorites()->where('event_id', $this->eventId)->delete();
        } else {
            $user->favorites()->create(['event_id' => $this->eventId]);
        }

        $this->isFavorited = ! $this->isFavorited;
        $this->dispatch('favorites-changed');
    }

    public function render()
    {
        return view('livewire.favorite-button');
    }
}
