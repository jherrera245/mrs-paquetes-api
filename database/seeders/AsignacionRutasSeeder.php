<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AsignacionRutasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $asignaciones = [
            [
                'codigo_unico_asignacion' => 'ASG0001',
                'id_ruta' => 1,
                'id_vehiculo' => 1,
                'id_paquete' => 1,
                'fecha' => Carbon::now(),
                'id_estado' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'codigo_unico_asignacion' => 'ASG0002',
                'id_ruta' => 2,
                'id_vehiculo' => 2,
                'id_paquete' => 2,
                'fecha' => Carbon::now(),
                'id_estado' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'codigo_unico_asignacion' => 'ASG0003',
                'id_ruta' => 3,
                'id_vehiculo' => 3,
                'id_paquete' => 3,
                'fecha' => Carbon::now(),
                'id_estado' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'codigo_unico_asignacion' => 'ASG0004',
                'id_ruta' => 4,
                'id_vehiculo' => 4,
                'id_paquete' => 4,
                'fecha' => Carbon::now(),
                'id_estado' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'codigo_unico_asignacion' => 'ASG0005',
                'id_ruta' => 5,
                'id_vehiculo' => 5,
                'id_paquete' => 5,
                'fecha' => Carbon::now(),
                'id_estado' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'codigo_unico_asignacion' => 'ASG0006',
                'id_ruta' => 6,
                'id_vehiculo' => 6,
                'id_paquete' => 6,
                'fecha' => Carbon::now(),
                'id_estado' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'codigo_unico_asignacion' => 'ASG0007',
                'id_ruta' => 7,
                'id_vehiculo' => 7,
                'id_paquete' => 7,
                'fecha' => Carbon::now(),
                'id_estado' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'codigo_unico_asignacion' => 'ASG0008',
                'id_ruta' => 8,
                'id_vehiculo' => 8,
                'id_paquete' => 8,
                'fecha' => Carbon::now(),
                'id_estado' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'codigo_unico_asignacion' => 'ASG0009',
                'id_ruta' => 9,
                'id_vehiculo' => 9,
                'id_paquete' => 9,
                'fecha' => Carbon::now(),
                'id_estado' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'codigo_unico_asignacion' => 'ASG0010',
                'id_ruta' => 10,
                'id_vehiculo' => 10,
                'id_paquete' => 10,
                'fecha' => Carbon::now(),
                'id_estado' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'codigo_unico_asignacion' => 'ASG0011',
                'id_ruta' => 11,
                'id_vehiculo' => 11,
                'id_paquete' => 11,
                'fecha' => Carbon::now(),
                'id_estado' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        DB::table('asignacion_rutas')->insert($asignaciones);
    }
}
