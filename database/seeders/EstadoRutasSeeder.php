<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoRutasSeeder extends Seeder
{
    public function run()
    {
        DB::table('estado_rutas')->insert([
            ['estado' => 'Activo'],
            ['estado' => 'Inactivo'],
            ['estado' => 'Pendiente'],
            ['estado' => 'Cancelada'],
        ]);
    }
}

