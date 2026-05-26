<?php

namespace App\Livewire;

use App\Models\Favorito;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FavoriteButton extends Component
{
    public int $eventoId;
    public bool $isFavorited = false;

    public function mount(int $eventoId): void
    {
        $this->eventoId = $eventoId;

        if (Auth::check()) {
            $this->isFavorited = Auth::user()
                ->favoritos()
                ->where('evento_id', $eventoId)
                ->exists();
        }
    }

    public function toggle()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($this->isFavorited) {
            $user->favoritos()->where('evento_id', $this->eventoId)->delete();
            $this->isFavorited = false;
        } else {
            Favorito::firstOrCreate([
                'user_id' => $user->id,
                'evento_id' => $this->eventoId,
            ]);
            $this->isFavorited = true;
        }

        $this->dispatch('favorites-changed');
    }

    public function render()
    {
        return view('livewire.favorite-button');
    }
}
