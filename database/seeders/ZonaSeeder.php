<?php

namespace Database\Seeders;

use App\Models\Zona;
use Illuminate\Database\Seeder;

class ZonaSeeder extends Seeder
{
    public function run(): void
    {
        $zonas = [
            ['nombre_zona' => 'Platea Central', 'color_hex' => '#4CAF50'],
            ['nombre_zona' => 'Platea Alta',    'color_hex' => '#2196F3'],
            ['nombre_zona' => 'Palco',          'color_hex' => '#9C27B0'],
            ['nombre_zona' => 'Balcón',         'color_hex' => '#FF9800'],
        ];

        foreach ($zonas as $zona) {
            Zona::firstOrCreate(['nombre_zona' => $zona['nombre_zona']], $zona);
        }
    }
}
