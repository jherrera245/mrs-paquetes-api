<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RutasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rutas = [
            [
                'id_destino' => 1,
                'nombre' => 'Ruta001',
                'id_bodega' => 1,
                'estado' => 1,
                'distancia_km' => '1.00',
                'duracion_aproximada' => '1.00',
                'fecha_programada' => '2024-05-01',
                'created_at' => '2024-08-27 20:34:06',
                'updated_at' => '2024-08-27 20:34:06'
            ],
            [
                'id_destino' => 2,
                'nombre' => 'Ruta002',
                'id_bodega' => 1,
                'estado' => 1,
                'distancia_km' => '1.00',
                'duracion_aproximada' => '1.00',
                'fecha_programada' => '2024-05-01',
                'created_at' => '2024-08-27 20:34:27',
                'updated_at' => '2024-08-27 20:34:27'
            ],
            [
                'id_destino' => 3,
                'nombre' => 'Ruta003',
                'id_bodega' => 1,
                'estado' => 1,
                'distancia_km' => '1.00',
                'duracion_aproximada' => '1.00',
                'fecha_programada' => '2024-08-28',
                'created_at' => '2024-08-27 20:35:16',
                'updated_at' => '2024-08-27 20:35:16'
            ],
            [
                'id_destino' => 4,
                'nombre' => 'Ruta004',
                'id_bodega' => 1,
                'estado' => 1,
                'distancia_km' => '1.00',
                'duracion_aproximada' => '1.00',
                'fecha_programada' => '2024-08-28',
                'created_at' => '2024-08-27 20:38:02',
                'updated_at' => '2024-08-27 20:38:02'
            ],
            [
                'id_destino' => 5,
                'nombre' => 'Ruta005',
                'id_bodega' => 1,
                'estado' => 1,
                'distancia_km' => '1.00',
                'duracion_aproximada' => '1.00',
                'fecha_programada' => '2024-08-28',
                'created_at' => '2024-08-27 20:38:14',
                'updated_at' => '2024-08-27 20:38:14'
            ],
            [
                'id_destino' => 8,
                'nombre' => 'Ruta006',
                'id_bodega' => 1,
                'estado' => 1,
                'distancia_km' => '1.00',
                'duracion_aproximada' => '1.00',
                'fecha_programada' => '2024-08-28',
                'created_at' => '2024-08-27 20:38:30',
                'updated_at' => '2024-08-27 20:38:30'
            ],
            [
                'id_destino' => 9,
                'nombre' => 'Ruta007',
                'id_bodega' => 1,
                'estado' => 1,
                'distancia_km' => '1.00',
                'duracion_aproximada' => '1.00',
                'fecha_programada' => '2024-08-28',
                'created_at' => '2024-08-27 20:38:41',
                'updated_at' => '2024-08-27 20:38:41'
            ],
            [
                'id_destino' => 10,
                'nombre' => 'Ruta008',
                'id_bodega' => 1,
                'estado' => 1,
                'distancia_km' => '1.00',
                'duracion_aproximada' => '1.00',
                'fecha_programada' => '2024-08-28',
                'created_at' => '2024-08-27 20:39:03',
                'updated_at' => '2024-08-27 20:39:03'
            ],
            [
                'id_destino' => 11,
                'nombre' => 'Ruta009',
                'id_bodega' => 1,
                'estado' => 1,
                'distancia_km' => '1.00',
                'duracion_aproximada' => '1.00',
                'fecha_programada' => '2024-08-28',
                'created_at' => '2024-08-27 20:39:15',
                'updated_at' => '2024-08-27 20:39:15'
            ],
            [
                'id_destino' => 12,
                'nombre' => 'Ruta010',
                'id_bodega' => 1,
                'estado' => 1,
                'distancia_km' => '15.00',
                'duracion_aproximada' => '1.00',
                'fecha_programada' => '2024-08-28',
                'created_at' => '2024-08-27 20:39:33',
                'updated_at' => '2024-08-27 20:39:33'
            ],
            [
                'id_destino' => 13,
                'nombre' => 'Ruta0011',
                'id_bodega' => 1,
                'estado' => 1,
                'distancia_km' => '15.00',
                'duracion_aproximada' => '1.00',
                'fecha_programada' => '2024-08-28',
                'created_at' => '2024-08-27 20:41:07',
                'updated_at' => '2024-08-27 20:41:07'
            ],
            [
                'id_destino' => 5,
                'nombre' => 'Ruta0012',
                'id_bodega' => 1,
                'estado' => 1,
                'distancia_km' => '15.00',
                'duracion_aproximada' => '1.00',
                'fecha_programada' => '2024-08-28',
                'created_at' => '2024-08-27 20:41:31',
                'updated_at' => '2024-08-27 20:41:31'
            ]
        ];

        // Usamos insert para añadir múltiples registros
        DB::table('rutas')->insert($rutas);
    }
}
