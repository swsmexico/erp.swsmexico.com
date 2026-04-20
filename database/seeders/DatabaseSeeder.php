<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SistemaBaseSeeder::class,
            RolesSeeder::class,
            // AnualidadesSeeder::class,  // pendiente — requiere revisión del reporte de migración
        ]);
    }
}
