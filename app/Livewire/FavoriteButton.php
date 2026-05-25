<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FavoriteButton extends Component
{
    public int  $eventoId;
    public bool $isFavorited = false;

    public function mount(int $eventoId): void
    {
        $this->eventoId    = $eventoId;
        $this->isFavorited = Auth::user()->hasFavorited($eventoId);
    }

    public function toggle(): void
    {
        $user = Auth::user();

        if ($this->isFavorited) {
            $user->favoritos()->where('evento_id', $this->eventoId)->delete();
        } else {
            $user->favoritos()->create(['evento_id' => $this->eventoId]);
        }

        $this->isFavorited = !$this->isFavorited;
        $this->dispatch('favorites-changed');
    }

    public function render()
    {
        return view('livewire.favorite-button');
    }
}
