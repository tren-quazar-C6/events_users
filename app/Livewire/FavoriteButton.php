<?php

namespace App\Livewire;

use App\Services\FavoriteService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FavoriteButton extends Component
{
    public ?int $eventoId = null;
    public ?string $slug = null;
    public ?string $title = null;
    public ?string $category = null;
    public ?string $synopsis = null;
    public ?float $priceFrom = null;
    public ?string $posterColor = null;
    public ?string $imageUrl = null;
    public bool $isFavorited = false;

    public function mount(
        ?int $eventoId = null,
        ?string $slug = null,
        ?string $title = null,
        ?string $category = null,
        ?string $synopsis = null,
        ?float $priceFrom = null,
        ?string $posterColor = null,
        ?string $imageUrl = null,
    ): void {
        $this->eventoId    = $eventoId;
        $this->slug        = $slug;
        $this->title       = $title;
        $this->category    = $category;
        $this->synopsis    = $synopsis;
        $this->priceFrom   = $priceFrom;
        $this->posterColor = $posterColor;
        $this->imageUrl    = $imageUrl;

        if (Auth::check() && $this->eventoId !== null) {
            $this->isFavorited = app(FavoriteService::class)->hasForUser(Auth::id(), $this->eventoId);
        }
    }

    public function toggle(): void
    {
        if (! Auth::check()) {
            $this->redirect(route('login'));
            return;
        }

        if ($this->eventoId === null) {
            return;
        }

        $this->isFavorited = app(FavoriteService::class)->toggleForUser(Auth::id(), $this->eventoId);

        $this->dispatch('favorites-changed');
    }

    public function render()
    {
        return view('livewire.favorite-button');
    }
}
