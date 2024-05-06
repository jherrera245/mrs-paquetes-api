<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoIncidenciasSeeder extends Seeder
{
    public function run()
    {
        DB::table('estado_incidencias')->insert([
            ['estado' => 'Abierta'],
            ['estado' => 'En Proceso'],
            ['estado' => 'Cerrada'],
        ]);
    }
}
