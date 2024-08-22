<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class anaquelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // datos de prueba.
        $data = [
            [
                'id_pasillo' => 1,
                'nombre' => 'Anaquel 1',
                'capacidad' => 100,
                'paquetes_actuales' => 0,
                'estado' => 1,
            ],
            [
                'id_pasillo' => 1,
                'nombre' => 'Anaquel 2',
                'capacidad' => 100,
                'paquetes_actuales' => 0,
                'estado' => 1,
            ],
        ];

        DB::table('anaqueles')->insert($data);
    }
}
