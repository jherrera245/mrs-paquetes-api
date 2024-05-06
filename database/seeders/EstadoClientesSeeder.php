<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoClientesSeeder extends Seeder
{
    public function run()
    {
        // Insertar datos en la tabla estado_clientes
        DB::table('estado_clientes')->insert([
            ['estado' => 'Activo'],
            ['estado' => 'Inactivo'],
            ['estado' => 'Moroso'],
        ]);
    }
}
