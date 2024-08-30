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
        // datos de prueba
        $data = [
            [
                'id_bodega' => 1,
                'nombre' => 'Pasillo 1',
            ],
            [
                'id_bodega' => 1,
                'nombre' => 'Pasillo 2',
            ],
        ];

        DB::table('pasillos')->insert($data);
    }
}

