<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class FavoriteService
{
    public function allForUser(int $userId): Collection
    {
        return collect(Cache::get($this->key($userId), []))->values();
    }

    public function countForUser(int $userId): int
    {
        return $this->allForUser($userId)->count();
    }

    public function hasForUser(int $userId, string $slug): bool
    {
        return $this->allForUser($userId)->contains(fn (array $favorite) => ($favorite['slug'] ?? null) === $slug);
    }

    public function toggleForUser(int $userId, array $event): bool
    {
        $favorites = $this->allForUser($userId);
        $slug = $event['slug'] ?? null;

        if (blank($slug)) {
            return false;
        }

        if ($this->hasForUser($userId, $slug)) {
            $favorites = $favorites
                ->reject(fn (array $favorite) => ($favorite['slug'] ?? null) === $slug)
                ->values();

            Cache::forever($this->key($userId), $favorites->all());

            return false;
        }

        $favorites->push([
            'slug' => $slug,
            'title' => $event['title'] ?? 'Evento',
            'category' => $event['category'] ?? 'General',
            'synopsis' => $event['synopsis'] ?? '',
            'poster_color' => $event['poster_color'] ?? '#7BB394',
            'image_url' => $event['image_url'] ?? null,
            'price_from' => (float) ($event['price_from'] ?? 0),
        ]);

        Cache::forever($this->key($userId), $favorites->unique('slug')->values()->all());

        return true;
    }

    private function key(int $userId): string
    {
        return "favorites:user:{$userId}";
    }
}
