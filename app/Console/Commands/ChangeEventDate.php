<?php

namespace App\Console\Commands;

use App\Mail\EventDateChanged;
use App\Models\Favorite;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class ChangeEventDate extends Command
{
    protected $signature   = 'events:change-date {slug} {oldDate} {newDate}';
    protected $description = 'Actualiza la fecha de un evento en el JSON y notifica a quienes lo tienen como favorito';

    public function handle(): int
    {
        $slug    = $this->argument('slug');
        $oldDate = $this->argument('oldDate');
        $newDate = $this->argument('newDate');

        $path   = database_path('mocks/events.json');
        $events = json_decode(file_get_contents($path), true);

        $index = collect($events)->search(fn ($e) => $e['slug'] === $slug);

        if ($index === false) {
            $this->error("No se encontró el evento con slug: {$slug}");
            return self::FAILURE;
        }

        $updated = false;
        foreach ($events[$index]['showtimes'] as &$showtime) {
            if ($showtime['date'] === $oldDate) {
                $showtime['date'] = $newDate;
                $updated = true;
            }
        }
        unset($showtime);

        if (! $updated) {
            $this->error("No se encontraron funciones con fecha {$oldDate} para el evento '{$slug}'.");
            return self::FAILURE;
        }

        file_put_contents($path, json_encode($events, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        Cache::forget('events.all');

        $this->info("Fecha actualizada: {$oldDate} → {$newDate}");

        $event     = $events[$index];
        $favorites = Favorite::where('event_id', $event['id'])->with('user')->get();

        foreach ($favorites as $favorite) {
            Mail::to($favorite->user)->send(new EventDateChanged($favorite->user, $event, $oldDate, $newDate));
        }

        $count = $favorites->count();
        $this->info("{$count} " . ($count === 1 ? 'correo encolado' : 'correos encolados') . ".");

        return self::SUCCESS;
    }
}
