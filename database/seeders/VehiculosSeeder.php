<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class VehiculosSeeder extends Seeder
{
    public function run()
    {
        DB::table('vehiculos')->insert([
            [
                'id_empleado_conductor' => 1,
                'id_empleado_apoyo' => 2,
                'placa' => '234',
                'capacidad_carga' => 20.00,
                'id_estado' => 1,
                'id_marca' => 1,
                'id_modelo' => 1,
                'year_fabricacion' => 2017,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_empleado_conductor' => 3,
                'id_empleado_apoyo' => 4,
                'placa' => '443',
                'capacidad_carga' => 555.00,
                'id_estado' => 1,
                'id_marca' => 1,
                'id_modelo' => 2,
                'year_fabricacion' => 2015,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_empleado_conductor' => 13,
                'id_empleado_apoyo' => 14,
                'placa' => '2443',
                'capacidad_carga' => 44.00,
                'id_estado' => 1,
                'id_marca' => 1,
                'id_modelo' => 2,
                'year_fabricacion' => 2013,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_empleado_conductor' => 3,
                'id_empleado_apoyo' => 24,
                'placa' => '11',
                'capacidad_carga' => 11.00,
                'id_estado' => 1,
                'id_marca' => 1,
                'id_modelo' => 2,
                'year_fabricacion' => 2020,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_empleado_conductor' => 23,
                'id_empleado_apoyo' => 24,
                'placa' => '657',
                'capacidad_carga' => 57777.00,
                'id_estado' => 1,
                'id_marca' => 5,
                'id_modelo' => 24,
                'year_fabricacion' => 2000,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_empleado_conductor' => 13,
                'id_empleado_apoyo' => 4,
                'placa' => '567',
                'capacidad_carga' => 66.00,
                'id_estado' => 1,
                'id_marca' => 1,
                'id_modelo' => 2,
                'year_fabricacion' => 2015,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_empleado_conductor' => 1,
                'id_empleado_apoyo' => 2,
                'placa' => '274',
                'capacidad_carga' => 20.00,
                'id_estado' => 1,
                'id_marca' => 1,
                'id_modelo' => 1,
                'year_fabricacion' => 2017,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_empleado_conductor' => 3,
                'id_empleado_apoyo' => 4,
                'placa' => '113',
                'capacidad_carga' => 555.00,
                'id_estado' => 1,
                'id_marca' => 1,
                'id_modelo' => 2,
                'year_fabricacion' => 2015,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_empleado_conductor' => 13,
                'id_empleado_apoyo' => 14,
                'placa' => '249',
                'capacidad_carga' => 44.00,
                'id_estado' => 1,
                'id_marca' => 1,
                'id_modelo' => 2,
                'year_fabricacion' => 2013,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_empleado_conductor' => 3,
                'id_empleado_apoyo' => 24,
                'placa' => '114',
                'capacidad_carga' => 11.00,
                'id_estado' => 1,
                'id_marca' => 1,
                'id_modelo' => 2,
                'year_fabricacion' => 2020,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_empleado_conductor' => 23,
                'id_empleado_apoyo' => 24,
                'placa' => '157',
                'capacidad_carga' => 57777.00,
                'id_estado' => 1,
                'id_marca' => 5,
                'id_modelo' => 24,
                'year_fabricacion' => 2000,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_empleado_conductor' => 13,
                'id_empleado_apoyo' => 4,
                'placa' => '577',
                'capacidad_carga' => 66.00,
                'id_estado' => 1,
                'id_marca' => 1,
                'id_modelo' => 2,
                'year_fabricacion' => 2015,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]);

    }
}
