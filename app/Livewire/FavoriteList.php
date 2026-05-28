<?php

namespace App\Livewire;

use App\Services\FavoriteService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class FavoriteList extends Component
{
    #[On('favorites-changed')]
    public function refresh(): void {}

    public function render()
    {
        if (! Auth::check()) {
            return view('livewire.favorite-list', ['eventos' => collect()]);
        }

        $eventos = app(FavoriteService::class)->allForUser(Auth::id());

        return view('livewire.favorite-list', compact('eventos'));
    }
}
