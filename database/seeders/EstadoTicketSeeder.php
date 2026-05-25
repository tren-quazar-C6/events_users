<?php

namespace Database\Seeders;

use App\Models\EstadoTicket;
use Illuminate\Database\Seeder;

class EstadoTicketSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['CONFIRMADO', 'USADO', 'CANCELADO'] as $estado) {
            EstadoTicket::firstOrCreate(['nombre_estado' => $estado]);
        }
    }
}
