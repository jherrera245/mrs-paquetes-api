<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehiculosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('vehiculos')->insert([
            [
                'id_empleado_conductor' => 1, 
                'id_empleado_apoyo' => 2, 
                'placa' => 'C-0069',
                'capacidad_carga' => 1500, 
                'id_bodega' => 2, 
                'id_estado' => 1, 
                'id_marca' => 1, // Isuzu
                'id_modelo' => 1, // Isuzu N-Series
                'year_fabricacion' => 2020,
                'created_at' => now(),
                'updated_at' => now(),
                'tipo' => 'camion',
            ],
            [
                'id_empleado_conductor' => 3, 
                'id_empleado_apoyo' => 4,
                'placa' => 'C-0001',
                'capacidad_carga' => 3000, 
                'id_bodega' => 3, 
                'id_estado' => 1, 
                'id_marca' => 2, 
                'id_modelo' => 2, 
                'year_fabricacion' => 2021,
                'created_at' => now(),
                'updated_at' => now(),
                'tipo' => 'camion',
            ],
            [
                'id_empleado_conductor' => 5, 
                'id_empleado_apoyo' => 6, 
                'placa' => 'M-0001',
                'capacidad_carga' => 100, 
                'id_bodega' => 1,
                'id_estado' => 1, 
                'id_marca' => 3, // Honda
                'id_modelo' => 3, // Honda navy
                'year_fabricacion' => 2022,
                'created_at' => now(),
                'updated_at' => now(),
                'tipo' => 'moto',
            ],
        ]);
    }
}
