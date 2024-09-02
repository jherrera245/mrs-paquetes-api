<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TamanoPaqueteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ["nombre"=>'Pequeño'],
            ["nombre"=>'Mediano'],
            ["nombre"=>'Grande'],
            ];
    
            DB::table('tamano_paquete')->insert($data);
    }
}
