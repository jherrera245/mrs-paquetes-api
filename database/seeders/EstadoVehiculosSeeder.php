<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoVehiculosSeeder extends Seeder
{
    public function run()
    {
        DB::table('estado_vehiculos')->insert([
            ['estado' => 'Disponible'],
            ['estado' => 'En Mantenimiento'],
            ['estado' => 'Asignado'],
            ['estado' => 'Fuera de Servicio'],
        ]);
    }
}
