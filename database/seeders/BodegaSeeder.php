<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BodegaSeeder extends Seeder
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
                'nombre' => 'Bodega Central',
                'direccion' => 'Avenida Roosevelt y 7ª Calle Poniente, San Miguel, El Salvador.',
                'id_departamento' => 12,
                'id_municipio' => 203,
            ],
            
        ];

        DB::table('bodegas')->insert($data);
    }
}
