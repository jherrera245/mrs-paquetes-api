<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoPaquetesSeeder extends Seeder
{
    public function run()
    {
        DB::table('estado_paquetes')->insert([
            ['nombre' => 'En Tránsito', 'descripcion' => 'El paquete está en tránsito'],
            ['nombre' => 'Entregado', 'descripcion' => 'El paquete ha sido entregado'],
            ['nombre' => 'Devuelto', 'descripcion' => 'El paquete ha sido devuelto'],
            ['nombre' => 'Perdido', 'descripcion' => 'El paquete está perdido'],
        ]);
    }
}

