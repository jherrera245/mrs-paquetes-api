<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PasilloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Datos de prueba para pasillos en la Bodega 1 
        $data = [
            [
                'id_bodega' => 1,
                'nombre' => 'P1__SANMIGUEL',  // Pasillo 1 para San Miguel
                'capacidad' => 150,
                'estado' => 1,
            ],
            [
                'id_bodega' => 1,
                'nombre' => 'P2__SANTAROSA',  // Pasillo 2 para Santa Rosa
                'capacidad' => 150,
                'estado' => 1,
            ],
            [
                'id_bodega' => 1,
                'nombre' => 'P3__MORAZAN',  // Pasillo 3 para Morazán
                'capacidad' => 150,
                'estado' => 1,
            ],
            [
                'id_bodega' => 1,
                'nombre' => 'P4__USULUTLAN',  // Pasillo 4 para Usulután
                'capacidad' => 150,
                'estado' => 1,
            ],
            [
                'id_bodega' => 1,
                'nombre' => 'P5__LAUNION',  // Pasillo 5 para La Unión
                'capacidad' => 150,
                'estado' => 1,
            ],
            [
                'id_bodega' => 1,
                'nombre' => 'P6__DA',  // Pasillo  6 para La Unión
                'capacidad' => 100,
                'estado' => 1,
            ],
        ];

        // Insertar los datos en la tabla 'pasillos'
        DB::table('pasillos')->insert($data);
    }
}
