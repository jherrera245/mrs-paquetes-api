<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TipoPaqueteSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('tipo_paquete')->insert([
            ['nombre' => 'Documentos', 'descripcion' => 'Paquete de documentos', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Electrónicos', 'descripcion' => 'Paquete de dispositivos electrónicos', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Ropa', 'descripcion' => 'Paquete de ropa', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Alimentos', 'descripcion' => 'Paquete de alimentos', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
