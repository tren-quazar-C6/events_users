<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TipoEventoSeeder::class,
            EstadoTicketSeeder::class,
            RolStaffSeeder::class,
            ZonaSeeder::class,
            StaffSeeder::class,
            EventoSeeder::class,
            AsientoSeeder::class,
            EventoAsientoSeeder::class,
        ]);
    }
}
