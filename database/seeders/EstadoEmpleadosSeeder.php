<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoEmpleadosSeeder extends Seeder
{
    public function run()
    {
        DB::table('estado_empleados')->insert([
            ['estado' => 'Activo'],
            ['estado' => 'Inactivo'],
            ['estado' => 'Suspendido'],
        ]);
    }
}

