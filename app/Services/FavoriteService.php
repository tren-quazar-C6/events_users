<?php

namespace App\Services;

use App\Models\Favorito;
use Illuminate\Support\Collection;

class FavoriteService
{
    public function allForUser(int $userId): Collection
    {
        $eventoIds = Favorito::where('user_id', $userId)->pluck('evento_id');

        return app(EventService::class)->all()
            ->filter(fn(array $event) => $eventoIds->contains($event['id']))
            ->values();
    }

    public function hasForUser(int $userId, int $eventoId): bool
    {
        return Favorito::where('user_id', $userId)->where('evento_id', $eventoId)->exists();
    }

    public function toggleForUser(int $userId, int $eventoId): bool
    {
        $existing = Favorito::where('user_id', $userId)->where('evento_id', $eventoId)->first();

        if ($existing) {
            $existing->delete();
            return false;
        }

        Favorito::create(['user_id' => $userId, 'evento_id' => $eventoId]);
        return true;
    }
}
