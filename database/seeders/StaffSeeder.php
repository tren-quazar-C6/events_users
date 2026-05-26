<?php

namespace Database\Seeders;

use App\Models\RolStaff;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $adminRol = RolStaff::where('nombre_rol', 'ADMIN')->first();

        Staff::firstOrCreate(
            ['email' => 'admin@tickify.local'],
            [
                'rol_staff_id' => $adminRol->id,
                'nombre'       => 'Admin Tickify',
                'password_hash'=> Hash::make('admin1234'),
            ]
        );
    }
}
