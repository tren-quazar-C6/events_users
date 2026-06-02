<?php

namespace App\Livewire;

use App\Services\FavoriteService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FavoriteButton extends Component
{
    public ?string $slug = null;
    public ?string $title = null;
    public ?string $category = null;
    public mixed $synopsis = null;
    public ?float $priceFrom = null;
    public ?string $posterColor = null;
    public ?string $imageUrl = null;
    public bool $isFavorited = false;

    public function mount(
        ?int $eventoId = null,
        ?string $slug = null,
        ?string $title = null,
        ?string $category = null,
        mixed $synopsis = null,
        ?float $priceFrom = null,
        ?string $posterColor = null,
        ?string $imageUrl = null,
    ): void
    {
        $this->slug = $slug;
        $this->title = $title;
        $this->category = $category;
        // Convert array synopsis to string (take first element)
        $this->synopsis = is_array($synopsis) ? ($synopsis[0] ?? null) : $synopsis;
        $this->priceFrom = $priceFrom;
        $this->posterColor = $posterColor;
        $this->imageUrl = $imageUrl;

        if (Auth::check() && filled($this->slug)) {
            $this->isFavorited = app(FavoriteService::class)->hasForUser(Auth::id(), $this->slug);
        }
    }

    public function toggle()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (blank($this->slug) || blank($this->title)) {
            return;
        }

        $this->isFavorited = app(FavoriteService::class)->toggleForUser(Auth::id(), [
            'slug' => $this->slug,
            'title' => $this->title,
            'category' => $this->category,
            'synopsis' => $this->synopsis,
            'price_from' => $this->priceFrom,
            'poster_color' => $this->posterColor,
            'image_url' => $this->imageUrl,
        ]);

        $this->dispatch('favorites-changed');
    }

    public function render()
    {
        return view('livewire.favorite-button');
    }
}
