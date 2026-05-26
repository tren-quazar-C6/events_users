<?php

namespace Database\Seeders;

use App\Models\Asiento;
use App\Models\Zona;
use Illuminate\Database\Seeder;

class AsientoSeeder extends Seeder
{
    public function run(): void
    {
        // Layout estándar Teatro Metropolitano:
        // Platea Central: A-D, 12 asientos/fila  (48 total)
        // Platea Alta:    E-G, 12 asientos/fila  (36 total)
        // Palco:          H-I,  8 asientos/fila  (16 total)
        // Balcón:         J-K, 10 asientos/fila  (20 total)

        $layout = [
            'Platea Central' => ['rows' => ['A','B','C','D'], 'seats' => 12],
            'Platea Alta'    => ['rows' => ['E','F','G'],     'seats' => 12],
            'Palco'          => ['rows' => ['H','I'],         'seats' => 8],
            'Balcón'         => ['rows' => ['J','K'],         'seats' => 10],
        ];

        $posY = 1;
        foreach ($layout as $zonaNombre => $config) {
            $zona = Zona::where('nombre_zona', $zonaNombre)->first();

            foreach ($config['rows'] as $fila) {
                for ($num = 1; $num <= $config['seats']; $num++) {
                    $codigo = $fila . str_pad($num, 2, '0', STR_PAD_LEFT);

                    Asiento::firstOrCreate(
                        ['codigo_asiento' => $codigo],
                        [
                            'zona_id' => $zona->id,
                            'fila'    => $fila,
                            'numero'  => $num,
                            'pos_x'   => $num,
                            'pos_y'   => $posY,
                        ]
                    );
                }
                $posY++;
            }
        }
    }
}
