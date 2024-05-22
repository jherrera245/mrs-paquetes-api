<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TipoIncidenciaSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('tipo_incidencia')->insert([
            ['nombre' => 'Retraso', 'descripcion' => 'El paquete ha sufrido un retraso', 'id_estado' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Daño', 'descripcion' => 'El paquete ha sufrido daños', 'id_estado' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Pérdida', 'descripcion' => 'El paquete está perdido', 'id_estado' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Error en la dirección', 'descripcion' => 'La dirección del paquete es incorrecta', 'id_estado' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
