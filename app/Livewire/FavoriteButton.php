<?php

namespace App\Livewire;

use App\Models\Favorite;
use Livewire\Component;

class FavoriteButton extends Component
{
    // El slug del evento al que pertenece este botón.
    // Se pasa desde la vista padre: <livewire:favorite-button :slug="..." />
    public string $slug;

    // Estado local del botón
    public bool $isFavorite = false;

    /**
     * mount() corre UNA vez al montar el componente.
     * Aquí preguntamos a la BD si ya es favorito.
     */
    public function mount(string $slug)
    {
        $this->slug = $slug;

        if (auth()->check()) {
            $this->isFavorite = auth()->user()
                ->favorites()
                ->where('event_slug', $slug)
                ->exists();
        }
    }

    /**
     * Toggle del favorito. Se llama con wire:click="toggle".
     */
    public function toggle()
    {
        // Si no está logueado, lo mandamos a login.
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($this->isFavorite) {
            // Ya era favorito → quitarlo
            $user->favorites()->where('event_slug', $this->slug)->delete();
            $this->isFavorite = false;
        } else {
            // No era favorito → agregarlo
            // firstOrCreate evita duplicados aunque haya doble clic rápido.
            Favorite::firstOrCreate([
                'user_id'    => $user->id,
                'event_slug' => $this->slug,
            ]);
            $this->isFavorite = true;
        }
    }

    public function render()
    {
        return view('livewire.favorite-button');
    }
}