<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventoZonaPreciosSeeder extends Seeder
{
    public function run(): void
    {
        $preciosConfig = [
            3  => ['zona_1' => 200000, 'zona_2' => 120000, 'zona_3' => 75000,  'zona_5' => 75000],
            9  => ['zona_1' => 350000, 'zona_2' => 200000, 'zona_3' => 120000, 'zona_5' => 120000],
            16 => ['zona_1' => 280000, 'zona_2' => 160000, 'zona_3' => 100000, 'zona_5' => 100000],
            20 => ['zona_1' => 250000, 'zona_2' => 130000, 'zona_3' => 80000],
            37 => ['zona_1' => 200000, 'zona_2' => 110000, 'zona_3' => 65000],
            40 => ['zona_1' => 220000, 'zona_2' => 130000, 'zona_3' => 70000],
            43 => ['zona_1' => 150000, 'zona_2' => 90000,  'zona_3' => 55000],
        ];

        $zonas = DB::table('ZONAS')->select('id_zona', 'nombre_zona')->get()->keyBy('nombre_zona');

        foreach ($preciosConfig as $idEvento => $preciosPorZona) {
            foreach ($preciosPorZona as $nombreZona => $precio) {
                $idZona = match ($nombreZona) {
                    'zona_1' => $zonas->firstWhere('nombre_zona', 'Zona 1') ? $zonas->firstWhere('nombre_zona', 'Zona 1')->id_zona : 1,
                    'zona_2' => $zonas->firstWhere('nombre_zona', 'Zona 2') ? $zonas->firstWhere('nombre_zona', 'Zona 2')->id_zona : 2,
                    'zona_3' => $zonas->firstWhere('nombre_zona', 'Zona 3') ? $zonas->firstWhere('nombre_zona', 'Zona 3')->id_zona : 3,
                    'zona_5' => $zonas->firstWhere('nombre_zona', 'Zona 5') ? $zonas->firstWhere('nombre_zona', 'Zona 5')->id_zona : 5,
                    default => null,
                };

                if (! $idZona) {
                    continue;
                }

                $exists = DB::table('EVENTO_ZONA')
                    ->where('id_evento', $idEvento)
                    ->where('id_zona', $idZona)
                    ->exists();

                if ($exists) {
                    DB::table('EVENTO_ZONA')
                        ->where('id_evento', $idEvento)
                        ->where('id_zona', $idZona)
                        ->update(['precio' => $precio]);
                } else {
                    DB::table('EVENTO_ZONA')->insert([
                        'id_evento'      => $idEvento,
                        'id_zona'        => $idZona,
                        'precio'         => $precio,
                        'cargo_servicio' => (int) ($precio * 0.1),
                        'capacidad'      => 100,
                        'activo'         => 1,
                    ]);
                }
            }
        }
    }
}
