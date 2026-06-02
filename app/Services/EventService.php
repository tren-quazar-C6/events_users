<?php

namespace App\Services;

use App\Models\Evento;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class EventService
{
    public function all(): Collection
    {
        return collect($this->fetchEvents())
            ->map(fn (array $event) => $this->normalizeEvent($event))
            ->filter(fn (array $event) => filled($event['title']))
            ->values();
    }

    public function featured(int $limit = 3): Collection
    {
        return $this->all()->take($limit);
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->all()->firstWhere('slug', $slug);
    }

    public function seatsForEvent(int $eventId): Collection
    {
        $baseUrl = config('services.events_api.url');

        if (blank($baseUrl)) {
            return collect();
        }

        try {
            $response = Http::baseUrl($baseUrl)
                ->timeout(config('services.events_api.timeout', 5))
                ->acceptJson()
                ->get("/api/eventos/{$eventId}/asientos")
                ->throw();

            return collect($this->extractList($response->json()))
                ->map(fn (array $seat) => $this->normalizeSeat($seat))
                ->values();
        } catch (\Throwable $exception) {
            Log::warning('Events API seats unavailable.', [
                'event_id' => $eventId,
                'message' => $exception->getMessage(),
            ]);

            return collect();
        }
    }

    private function fetchEvents(): array
    {
        if (Schema::hasTable('EVENTOS')) {
            return $this->fetchFromDB();
        }

        $baseUrl = config('services.events_api.url');

        if (blank($baseUrl)) {
            return $this->mockEvents();
        }

        try {
            $response = Http::baseUrl($baseUrl)
                ->timeout(config('services.events_api.timeout', 5))
                ->acceptJson()
                ->get('/api/eventos')
                ->throw();

            return $this->extractList($response->json());
        } catch (\Throwable $exception) {
            Log::warning('Events API unavailable; using local mock events.', [
                'message' => $exception->getMessage(),
            ]);

            return $this->mockEvents();
        }
    }

    private function fetchFromDB(): array
    {
        try {
            return Evento::with('tipo')
                ->where('activo', true)
                ->where('publicado', true)
                ->get()
                ->map(function (Evento $evento): array {
                    return [
                        'id'              => $evento->getKey(),
                        'slug'            => $evento->slug,
                        'title'           => $evento->nombre_evento,
                        'category'        => $evento->tipo?->nombre_tipo ?? 'General',
                        'synopsis'        => $evento->synopsis ?? [],
                        'poster_color'    => $evento->poster_color ?? '#7BB394',
                        'image_url'       => $evento->ruta_url,
                        'price_from'      => (int) $evento->price_from,
                        'showtimes'       => $evento->fecha_evento
                            ? [['date' => $evento->fecha_evento->format('Y-m-d'), 'time' => $evento->fecha_evento->format('H:i')]]
                            : [],
                        'venue'           => $evento->venue ?? '',
                        'city'            => $evento->city ?? '',
                        'author'          => $evento->author ?? '',
                        'duration'        => $evento->duration ?? '',
                        'available_seats' => 0,
                    ];
                })
                ->all();
        } catch (\Throwable $e) {
            Log::warning('DB eventos fetch failed; falling back.', ['message' => $e->getMessage()]);
            return [];
        }
    }

    private function extractList(mixed $payload): array
    {
        if (! is_array($payload)) {
            return [];
        }

        foreach (['data', 'events', 'eventos', 'items', 'results'] as $key) {
            if (isset($payload[$key]) && is_array($payload[$key])) {
                return $payload[$key];
            }
        }

        return array_is_list($payload) ? $payload : [];
    }

    private function normalizeEvent(array $event): array
    {
        $title = data_get($event, 'title')
            ?? data_get($event, 'titulo')
            ?? data_get($event, 'nombreEvento')
            ?? data_get($event, 'nombre')
            ?? data_get($event, 'name')
            ?? '';

        $slug = data_get($event, 'slug') ?: Str::slug($title);

        return [
            'id' => data_get($event, 'id')
                ?? data_get($event, 'idEvento')
                ?? data_get($event, 'eventoId'),
            'slug' => $slug,
            'title' => $title,
            'category' => data_get($event, 'category')
                ?? data_get($event, 'categoria')
                ?? data_get($event, 'tipoEvento')
                ?? data_get($event, 'tipo')
                ?? 'General',
            'synopsis' => data_get($event, 'synopsis')
                ?? data_get($event, 'sinopsis')
                ?? data_get($event, 'descripcion')
                ?? data_get($event, 'description')
                ?? '',
            'poster_color' => data_get($event, 'poster_color')
                ?? data_get($event, 'posterColor')
                ?? data_get($event, 'color')
                ?? '#7BB394',
            'image_url' => data_get($event, 'image_url')
                ?? data_get($event, 'imageUrl')
                ?? data_get($event, 'imagenPrincipal')
                ?? data_get($event, 'imagen_principal'),
            'price_from' => (int) (data_get($event, 'price_from')
                ?? data_get($event, 'priceFrom')
                ?? data_get($event, 'precioDesde')
                ?? data_get($event, 'precio')
                ?? data_get($event, 'price')
                ?? 0),
            'available_seats' => (int) (data_get($event, 'available_seats')
                ?? data_get($event, 'asientosDisponibles')
                ?? 0),
            'showtimes' => $this->normalizeShowtimes(
                data_get($event, 'showtimes')
                ?? data_get($event, 'funciones')
                ?? data_get($event, 'horarios')
                ?? $this->showtimesFromEventDate(data_get($event, 'fechaEvento'))
                ?? []
            ),
        ];
    }

    private function showtimesFromEventDate(?string $eventDate): array
    {
        if (blank($eventDate)) {
            return [];
        }

        return [[
            'date' => substr($eventDate, 0, 10),
            'time' => substr($eventDate, 11, 5),
        ]];
    }

    private function normalizeShowtimes(mixed $showtimes): array
    {
        if (! is_array($showtimes)) {
            return [];
        }

        return collect($showtimes)
            ->map(fn ($showtime) => is_array($showtime) ? [
                'date' => data_get($showtime, 'date')
                    ?? data_get($showtime, 'fecha')
                    ?? data_get($showtime, 'day'),
                'time' => data_get($showtime, 'time')
                    ?? data_get($showtime, 'hora')
                    ?? data_get($showtime, 'hour'),
            ] : null)
            ->filter(fn ($showtime) => is_array($showtime) && filled($showtime['date']) && filled($showtime['time']))
            ->values()
            ->all();
    }

    private function normalizeSeat(array $seat): array
    {
        return [
            'id' => data_get($seat, 'idEventoAsiento') ?? data_get($seat, 'id'),
            'seat_id' => data_get($seat, 'idAsiento') ?? data_get($seat, 'seat_id'),
            'code' => data_get($seat, 'codigoAsiento') ?? data_get($seat, 'code'),
            'row' => data_get($seat, 'fila') ?? data_get($seat, 'row'),
            'number' => data_get($seat, 'numero') ?? data_get($seat, 'number'),
            'zone' => data_get($seat, 'zona') ?? data_get($seat, 'zone') ?? 'General',
            'zone_color' => data_get($seat, 'colorZona') ?? data_get($seat, 'zone_color'),
            'price' => (float) (data_get($seat, 'precio') ?? data_get($seat, 'price') ?? 0),
            'status' => data_get($seat, 'estado') ?? data_get($seat, 'status') ?? 'DISPONIBLE',
        ];
    }

    private function mockEvents(): array
    {
        return json_decode(file_get_contents(database_path('mocks/events.json')), true) ?? [];
    }
}
