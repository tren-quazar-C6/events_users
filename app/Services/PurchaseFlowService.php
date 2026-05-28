<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;

class PurchaseFlowService
{
    public function buildCheckout(string $token, array $event, Collection $selectedSeats): void
    {
        session(["checkout:{$token}" => [
            'event' => $event,
            'seats' => $selectedSeats->values()->all(),
            'expires_at' => now()->addMinutes(15)->toIso8601String(),
        ]]);
    }

    public function getCheckout(string $token): ?array
    {
        $checkout = session("checkout:{$token}");

        if (! is_array($checkout)) {
            return null;
        }

        if (filled($checkout['expires_at'] ?? null) && Carbon::parse($checkout['expires_at'])->isPast()) {
            session()->forget("checkout:{$token}");

            return null;
        }

        return $checkout;
    }

    public function forgetCheckout(string $token): void
    {
        session()->forget("checkout:{$token}");
    }

    public function createPurchase(int $userId, string $userName, string $userEmail, array $event, Collection $selectedSeats): Fluent
    {
        $reference = 'ORD-' . strtoupper(Str::random(6));
        $subtotal = (float) $selectedSeats->sum('price');
        $fee = 3500.0;
        $eventDate = $this->eventDate($event);

        $purchase = [
            'user_id' => $userId,
            'user' => [
                'name' => $userName,
                'email' => $userEmail,
            ],
            'referencia_interna' => $reference,
            'subtotal' => $subtotal,
            'cargo_servicio' => $fee,
            'total' => $subtotal + $fee,
            'tickets' => $selectedSeats->values()->map(function (array $seat) use ($event, $eventDate) {
                return [
                    'codigo_unico' => strtoupper(Str::random(10)),
                    'qr_token' => (string) Str::uuid(),
                    'estadoTicket' => [
                        'nombre_estado' => 'CONFIRMADO',
                    ],
                    'eventoAsiento' => [
                        'precio' => (float) ($seat['price'] ?? 0),
                        'evento' => [
                            'slug' => $event['slug'],
                            'nombre_evento' => $event['title'],
                            'poster_color' => $event['poster_color'] ?? '#7BB394',
                            'venue' => $event['venue'] ?? 'Por confirmar',
                            'city' => $event['city'] ?? '',
                            'fecha_evento' => $eventDate->toIso8601String(),
                        ],
                        'asiento' => [
                            'fila' => $seat['row'],
                            'numero' => $seat['number'],
                            'zona' => [
                                'nombre_zona' => $seat['zone'] ?? 'General',
                            ],
                        ],
                    ],
                ];
            })->all(),
        ];

        Cache::forever($this->purchaseKey($reference), $purchase);

        $userReferences = collect(Cache::get($this->userPurchasesKey($userId), []));
        $userReferences->push($reference);
        Cache::forever($this->userPurchasesKey($userId), $userReferences->unique()->values()->all());

        return $this->hydratePurchase($purchase);
    }

    public function findPurchaseForUser(string $reference, int $userId): ?Fluent
    {
        $purchase = Cache::get($this->purchaseKey($reference));

        if (! is_array($purchase) || (int) ($purchase['user_id'] ?? 0) !== $userId) {
            return null;
        }

        return $this->hydratePurchase($purchase);
    }

    public function ticketsForUser(int $userId): Collection
    {
        $references = collect(Cache::get($this->userPurchasesKey($userId), []));

        return $references
            ->map(fn (string $reference) => Cache::get($this->purchaseKey($reference)))
            ->filter(fn ($purchase) => is_array($purchase))
            ->flatMap(fn (array $purchase) => $purchase['tickets'] ?? [])
            ->map(fn (array $ticket) => $this->hydrateTicket($ticket))
            ->values();
    }

    public function findTicketForUser(string $code, int $userId): ?Fluent
    {
        return $this->ticketsForUser($userId)->firstWhere('codigo_unico', $code);
    }

    public function eventFromApi(array $event): Fluent
    {
        return new Fluent([
            'slug' => $event['slug'],
            'nombre_evento' => $event['title'],
            'fecha_evento' => $this->eventDate($event),
            'venue' => $event['venue'] ?? 'Por confirmar',
            'city' => $event['city'] ?? '',
        ]);
    }

    public function seatCollection(Collection $seats): Collection
    {
        return $seats->map(fn (array $seat) => new Fluent([
            'precio' => (float) ($seat['price'] ?? 0),
            'asiento' => new Fluent([
                'fila' => $seat['row'],
                'numero' => $seat['number'],
                'zona' => new Fluent([
                    'nombre_zona' => $seat['zone'] ?? 'General',
                ]),
            ]),
        ]))->values();
    }

    private function hydratePurchase(array $purchase): Fluent
    {
        return new Fluent([
            'user_id' => $purchase['user_id'],
            'user' => new Fluent($purchase['user']),
            'referencia_interna' => $purchase['referencia_interna'],
            'subtotal' => $purchase['subtotal'],
            'cargo_servicio' => $purchase['cargo_servicio'],
            'total' => $purchase['total'],
            'tickets' => collect($purchase['tickets'] ?? [])->map(fn (array $ticket) => $this->hydrateTicket($ticket)),
        ]);
    }

    private function hydrateTicket(array $ticket): Fluent
    {
        return new Fluent([
            'codigo_unico' => $ticket['codigo_unico'],
            'qr_token' => $ticket['qr_token'],
            'estadoTicket' => new Fluent($ticket['estadoTicket']),
            'eventoAsiento' => new Fluent([
                'precio' => $ticket['eventoAsiento']['precio'],
                'evento' => new Fluent([
                    'slug' => $ticket['eventoAsiento']['evento']['slug'],
                    'nombre_evento' => $ticket['eventoAsiento']['evento']['nombre_evento'],
                    'poster_color' => $ticket['eventoAsiento']['evento']['poster_color'],
                    'venue' => $ticket['eventoAsiento']['evento']['venue'],
                    'city' => $ticket['eventoAsiento']['evento']['city'],
                    'fecha_evento' => Carbon::parse($ticket['eventoAsiento']['evento']['fecha_evento']),
                ]),
                'asiento' => new Fluent([
                    'fila' => $ticket['eventoAsiento']['asiento']['fila'],
                    'numero' => $ticket['eventoAsiento']['asiento']['numero'],
                    'zona' => new Fluent([
                        'nombre_zona' => $ticket['eventoAsiento']['asiento']['zona']['nombre_zona'],
                    ]),
                ]),
            ]),
        ]);
    }

    private function eventDate(array $event): Carbon
    {
        $showtime = $event['showtimes'][0] ?? null;

        if (is_array($showtime) && filled($showtime['date'] ?? null) && filled($showtime['time'] ?? null)) {
            return Carbon::parse($showtime['date'] . ' ' . $showtime['time']);
        }

        return now()->addWeek();
    }

    private function purchaseKey(string $reference): string
    {
        return "purchases:reference:{$reference}";
    }

    private function userPurchasesKey(int $userId): string
    {
        return "purchases:user:{$userId}";
    }
}
