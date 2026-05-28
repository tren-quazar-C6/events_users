<?php

namespace Tests\Unit;

use App\Services\EventService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EventServiceTest extends TestCase
{
    public function test_it_normalizes_events_from_the_api(): void
    {
        config(['services.events_api.url' => 'http://events-api.test']);

        Http::fake([
            '*' => Http::response([
                [
                    'id' => 15,
                    'nombre' => 'Noche de Teatro',
                    'categoria' => 'Drama',
                    'descripcion' => 'Una funcion de prueba.',
                    'precio' => 55000,
                    'color' => '#123456',
                    'funciones' => [
                        ['fecha' => '2026-06-01', 'hora' => '20:00'],
                    ],
                ],
            ]),
        ]);

        $event = app(EventService::class)->all()->first();

        $this->assertSame([
            'id' => 15,
            'slug' => 'noche-de-teatro',
            'title' => 'Noche de Teatro',
            'category' => 'Drama',
            'synopsis' => 'Una funcion de prueba.',
            'poster_color' => '#123456',
            'image_url' => null,
            'price_from' => 55000,
            'available_seats' => 0,
            'showtimes' => [
                ['date' => '2026-06-01', 'time' => '20:00'],
            ],
        ], $event);
    }

    public function test_it_normalizes_the_current_backend_event_shape(): void
    {
        config(['services.events_api.url' => 'http://events-api.test']);

        Http::fake([
            '*' => Http::response([
                [
                    'idEvento' => 1,
                    'nombreEvento' => 'Morat en Vivo',
                    'descripcion' => 'Concierto oficial',
                    'fechaEvento' => '2025-12-20T20:00:00',
                    'tipoEvento' => 'CONCIERTO',
                    'imagenPrincipal' => null,
                    'asientosDisponibles' => 3,
                ],
            ]),
        ]);

        $event = app(EventService::class)->all()->first();

        $this->assertSame('morat-en-vivo', $event['slug']);
        $this->assertSame('Morat en Vivo', $event['title']);
        $this->assertSame('CONCIERTO', $event['category']);
        $this->assertSame('Concierto oficial', $event['synopsis']);
        $this->assertNull($event['image_url']);
        $this->assertSame(3, $event['available_seats']);
        $this->assertSame([
            ['date' => '2025-12-20', 'time' => '20:00'],
        ], $event['showtimes']);
    }
}
