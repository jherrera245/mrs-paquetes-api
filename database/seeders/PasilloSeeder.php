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
        // Datos de prueba para tres bodegas, cada una con tres pasillos
        $data = [
            // Pasillos para Bodega 1
            [
                'id_bodega' => 1,
                'nombre' => 'Pasillo 1',
            ],
            [
                'id_bodega' => 1,
                'nombre' => 'Pasillo 2',
            ],
            [
                'id_bodega' => 1,
                'nombre' => 'Pasillo 3',
            ],
            
            // Pasillos para Bodega 2
            [
                'id_bodega' => 2,
                'nombre' => 'Pasillo 1',
            ],
            [
                'id_bodega' => 2,
                'nombre' => 'Pasillo 2',
            ],
            [
                'id_bodega' => 2,
                'nombre' => 'Pasillo 3',
            ],
            
            // Pasillos para Bodega 3
            [
                'id_bodega' => 3,
                'nombre' => 'Pasillo 1',
            ],
            [
                'id_bodega' => 3,
                'nombre' => 'Pasillo 2',
            ],
            [
                'id_bodega' => 3,
                'nombre' => 'Pasillo 3',
            ],
        ];
    
        // Insertar los datos en la tabla 'pasillos'
        DB::table('pasillos')->insert($data);
    }
    
}

