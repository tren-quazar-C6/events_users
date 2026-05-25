<?php

namespace Database\Seeders;

use App\Models\Evento;
use App\Models\TipoEvento;
use Illuminate\Database\Seeder;

class EventoSeeder extends Seeder
{
    public function run(): void
    {
        $raw = json_decode(file_get_contents(database_path('mocks/events.json')), true);

        foreach ($raw as $e) {
            $tipo = TipoEvento::where('nombre_tipo', $e['category'])->first()
                ?? TipoEvento::first();

            $firstShowtime = $e['showtimes'][0];
            $fechaEvento   = $firstShowtime['date'] . ' ' . $firstShowtime['time'] . ':00';

            Evento::updateOrCreate(
                ['slug' => $e['slug']],
                [
                    'tipo_evento_id'      => $tipo->id,
                    'nombre_evento'       => $e['title'],
                    'synopsis'            => $e['synopsis'],
                    'author'              => $e['author'],
                    'duration'            => $e['duration'],
                    'poster_color'        => $e['poster_color'],
                    'venue'               => $e['venue'],
                    'city'                => $e['city'],
                    'price_from'          => $e['price_from'],
                    'fecha_evento'        => $fechaEvento,
                    'fecha_inicio_ventas' => now(),
                    'fecha_fin_ventas'    => now()->addMonths(3),
                    'capacidad_total'     => 120,
                    'publicado'           => true,
                    'activo'              => true,
                ]
            );
        }
    }
}
