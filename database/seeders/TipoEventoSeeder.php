<?php

namespace Database\Seeders;

use App\Models\TipoEvento;
use Illuminate\Database\Seeder;

class TipoEventoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = ['Musical', 'Drama', 'Comedia', 'Infantil', 'Danza', 'Ópera', 'Concierto'];

        foreach ($tipos as $tipo) {
            TipoEvento::firstOrCreate(['nombre_tipo' => $tipo]);
        }
    }
}
