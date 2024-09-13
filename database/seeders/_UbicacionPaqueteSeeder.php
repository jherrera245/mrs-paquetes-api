<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UbicacionPaqueteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ubicacionesPaquetes = [
            ['id_paquete' => 1, 'id_ubicacion' => 1, 'estado' => 1],
            ['id_paquete' => 2, 'id_ubicacion' => 2, 'estado' => 1],
            ['id_paquete' => 3, 'id_ubicacion' => 3, 'estado' => 1],
            ['id_paquete' => 4, 'id_ubicacion' => 4, 'estado' => 1],
            ['id_paquete' => 5, 'id_ubicacion' => 5, 'estado' => 1],
            ['id_paquete' => 6, 'id_ubicacion' => 6, 'estado' => 1],
            ['id_paquete' => 7, 'id_ubicacion' => 7, 'estado' => 1],
            ['id_paquete' => 8, 'id_ubicacion' => 8, 'estado' => 1],
            ['id_paquete' => 9, 'id_ubicacion' => 9, 'estado' => 1],
            ['id_paquete' => 10, 'id_ubicacion' => 10, 'estado' => 1],
            ['id_paquete' => 11, 'id_ubicacion' => 11, 'estado' => 1],
            ['id_paquete' => 12, 'id_ubicacion' => 12, 'estado' => 1],
            ['id_paquete' => 13, 'id_ubicacion' => 13, 'estado' => 1],
            ['id_paquete' => 14, 'id_ubicacion' => 14, 'estado' => 1],
            ['id_paquete' => 15, 'id_ubicacion' => 15, 'estado' => 1],
            ['id_paquete' => 16, 'id_ubicacion' => 16, 'estado' => 1],
            ['id_paquete' => 17, 'id_ubicacion' => 17, 'estado' => 1],
            ['id_paquete' => 18, 'id_ubicacion' => 18, 'estado' => 1],
            ['id_paquete' => 19, 'id_ubicacion' => 19, 'estado' => 1],
            ['id_paquete' => 20, 'id_ubicacion' => 20, 'estado' => 1],
   
        ];

        DB::table('ubicaciones_paquetes')->insert($ubicacionesPaquetes);
    }
}
