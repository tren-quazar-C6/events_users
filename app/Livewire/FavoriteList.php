<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class FavoriteList extends Component
{
    #[On('favorites-changed')]
    public function refresh(): void {}

    public function render()
    {
        $eventos = Auth::user()
            ->favoritos()
            ->with('evento')
            ->get()
            ->pluck('evento')
            ->filter();

        return view('livewire.favorite-list', compact('eventos'));
    }
}
