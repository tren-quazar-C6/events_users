<?php

namespace Database\Seeders;

use App\Models\RolStaff;
use Illuminate\Database\Seeder;

class RolStaffSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['ADMIN', 'TAQUILLA', 'ACCESO'] as $rol) {
            RolStaff::firstOrCreate(['nombre_rol' => $rol]);
        }
    }
}
