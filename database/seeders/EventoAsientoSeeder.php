<?php

namespace Database\Seeders;

use App\Models\Asiento;
use App\Models\Evento;
use App\Models\EventoAsiento;
use Illuminate\Database\Seeder;

class EventoAsientoSeeder extends Seeder
{
    public function run(): void
    {
        $eventos  = Evento::all();
        $asientos = Asiento::with('zona')->get();

        // Multiplicadores de precio por zona respecto a price_from del evento
        $precios = [
            'Platea Central' => 1.0,
            'Platea Alta'    => 0.8,
            'Palco'          => 1.5,
            'Balcón'         => 0.6,
        ];

        foreach ($eventos as $evento) {
            foreach ($asientos as $asiento) {
                $factor = $precios[$asiento->zona->nombre_zona] ?? 1.0;
                $precio = round($evento->price_from * $factor, -3); // redondea a miles

                EventoAsiento::firstOrCreate(
                    [
                        'evento_id'  => $evento->id,
                        'asiento_id' => $asiento->id,
                    ],
                    [
                        'precio' => $precio,
                        'estado' => 'DISPONIBLE',
                    ]
                );
            }
        }
    }
}
